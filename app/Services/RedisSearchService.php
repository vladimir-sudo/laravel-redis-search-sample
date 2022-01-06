<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class RedisSearchService
{
    const PREFIX = 'search_cache';

    /**
     * @param string $tableName
     * @return string
     */
    private function getTablePrefix(string $tableName): string
    {
        return self::PREFIX . ':' . $tableName . ':';
    }

    /**
     * @param string $varName
     * @return string
     */
    private function getVarKey(string $varName): string
    {
        return self::PREFIX . ':' . $varName;
    }

    /**
     * @param $tableName
     * @param $data
     * @return bool
     */
    public function refreshByData($tableName, $data)
    {
        $prefix = $this->getTablePrefix($tableName);

        $currentKeyList = Redis::keys($prefix . '*');

        if ($currentKeyList) {
            Redis::del($currentKeyList);
        }

        $totalCount = 0;

        foreach ($data as $item) {
            if (isset($item['id'])) {
                $id = $item['id'];
                $totalCount++;

                foreach ($item as $field => $val) {
                    Redis::set($prefix . $field . ':' . urlencode($this->strtolower($val)) . ':' . $id, $id);
                }
            }
        }

        Redis::set(self::PREFIX . ':' . $tableName . ':total_count', $totalCount);

        return true;
    }

    /**
     * @param $tableName
     * @param $partText
     * @param null $fieldName
     * @param bool $fullMatch
     * @return mixed
     */
    public function search($tableName, $partText, $fieldName = null, $fullMatch = false)
    {
        $prefix = $this->getTablePrefix($tableName);

        if ($fieldName) {
            $prefix .= $fieldName . ':';
        } else {
            $prefix .= '*:';
        }

        $postfix = $fullMatch ? ':*' : '*';
        $keys = Redis::keys($prefix . urlencode($this->strtolower($partText)) . $postfix);

        $idList = [];
        foreach ($keys as $key) {
            $idList[] = Redis::get($key);
        }

        return $idList;
    }

    /**
     * @param $tableName
     * @param $partText
     * @param null $fieldName
     * @return mixed
     */
    public function searchFull($tableName, $partText, $fieldName = null)
    {
        $prefix = $this->getTablePrefix($tableName);

        if ($fieldName) {
            $prefix = $prefix . $fieldName . ':';
        } else {
            $prefix = $prefix . '*:';
        }

        $keys = Redis::keys($prefix . '*' . urlencode($this->strtolower($partText)) . '*');

        $idList = [];
        foreach ($keys as $key) {
            $idList[] = Redis::get($key);
        }

        return $idList;
    }

    /**
     * @param $tableName
     * @return mixed
     */
    public function totalCount($tableName)
    {
        return Redis::get(self::PREFIX . ':' . $tableName . ':total_count');
    }

    /**
     * @param $tableName
     * @param $id
     * @return mixed
     */
    public function delete($tableName, $id)
    {
        $keys = Redis::keys(self::PREFIX . ':' . $tableName . ':*:*:' . $id);

        if ($keys) {
            return Redis::del($keys);
        }

        return true;
    }

    /**
     * @param $tableName
     * @param $id
     * @param $data
     * @return bool
     */
    public function addOrUpdate($tableName, $id, $data)
    {
        $prefix = $this->getTablePrefix($tableName);

        $this->delete($tableName, $id);

        foreach ($data as $field => $val) {
            Redis::set($prefix . $field . ':' . urlencode($this->strtolower($val)) . ':' . $id, $id);
        }

        return true;
    }

    /**
     * @param $tableName
     * @param $id
     * @param $data
     * @return bool
     */
    public function addOrUpdateRepeatField($tableName, $id, $data)
    {
        $prefix = $this->getTablePrefix($tableName);

        $this->delete($tableName, $id);

        foreach ($data as $key => $value) {
            foreach ($value as $val) {
                Redis::set($prefix . $key . ':' . urlencode($this->strtolower($val)) . ':' . $id, $id);
            }
        }

        return true;
    }

    /**
     * @param $str
     * @return bool|false|mixed|string|string[]|null
     */
    public function strtolower($str)
    {
        return mb_strtolower($str);
    }

    /**
     * @param string $key
     * @param $data
     * @return mixed
     */
    public function refreshJSONString(string $key, $data)
    {
        $redisKey = $this->getVarKey($key);

        return Redis::set($redisKey, json_encode($data));
    }

    /**
     * @param string $varName
     * @return mixed
     */
    public function getJsonData(string $varName)
    {
        $redisKey = $this->getVarKey($varName);

        return json_decode(Redis::get($redisKey), true);
    }

    /**
     * @param array $data
     * @param string $search
     * @return array
     */
    public function filterDataByString(array $data, string $search): array
    {
        $filteredData = [];

        foreach ($data as $item) {
            foreach ($item as $value) {
                if (strpos($value, $search) !== false) {
                    $filteredData[] = $item;
                    break;
                }
            }
        }

        return $filteredData;
    }

    /**
     * @param $tableName
     * @param $id
     * @param $field
     * @param $value
     * @return bool
     */
    public function updateField($tableName, $id, $field, $value)
    {
        $prefix = $this->getTablePrefix($tableName);

        $this->deleteField($tableName, $id, $field);

        Redis::set($prefix . $field . ':' . urlencode($this->strtolower($value)) . ':' . $id, $id);

        return true;
    }

    /**
     * @param $tableName
     * @param $id
     * @param $field
     */
    private function deleteField($tableName, $id, $field)
    {
        $prefix = $this->getTablePrefix($tableName);

        $keys = Redis::keys($prefix . $field . ':*:' . $id);
        if ($keys) {
            return Redis::del($keys);
        }
    }

    /**
     * Clear all cache
     */
    public function clearAll()
    {
        Redis::flushAll();
    }

    /**
     * @param null $table
     * @return mixed
     */
    public function getAll($table = null)
    {
        if (empty($table)) {
            return Redis::keys('*');
        } else {
            $prefix = $this->getTablePrefix($table);

            return Redis::keys($prefix . ':*');
        }
    }
}

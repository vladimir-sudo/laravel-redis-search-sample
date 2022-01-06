<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\RedisSearchService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $redisService;

    /**
     *
     */
    public function __construct()
    {
        $this->redisService = new RedisSearchService();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $redisCacheAll = $this->redisService->getAll();

        $productsIds = Product::all()->pluck('id')->toArray();


        $useSearch = false;

        $searchIds = [];
        foreach ($request->all() as $key => $value) {
            if (!empty($value)) {
                $useSearch = true;
                $searchIds = $this->redisService->search('products', $value, $key);

                $productsIds = array_merge($productsIds, $searchIds);
            }
        }

        return view('products.index', [
            'products' => Product::whereIn('id', $useSearch ? $searchIds : $productsIds)->get(),
            'cache' => $redisCacheAll,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addProduct(Request $request)
    {
        $product = new Product();
        $product->name = $request->name;
        $product->color = $request->color;
        $product->type = $request->type;
        $product->width = $request->width;
        $product->save();

        $this->redisService->addOrUpdate('products', $product->id, $product->toArray());

        return redirect()->route('products.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pullToCache()
    {
        $products = Product::all();

        $this->redisService->refreshByData('products', $products->toArray());

        return redirect()->route('products.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCache()
    {
        $this->redisService->clearAll();

        return redirect()->route('products.index');
    }
}

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <div class="d-flex mt-5 mb-3 justify-content-center">
        <a href="{{ route('products.clear') }}" class="btn btn-primary m-1">Clear cache</a>
        <a href="{{ route('products.cache') }}" class="btn btn-primary m-1">Pull to cache</a>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Add product</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Table</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Redis</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
            <form action="{{ route('products.create') }}" method="post" class="container">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="color">
                </div>
                <div class="mb-3">
                    <label class="form-label">Width</label>
                    <input type="text" class="form-control" name="width">
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" class="form-control" name="type">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <form class="container mt-5" action="{{ route('products.index') }}" method="get">
                <div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">
                            Name
                            <input type="text" class="form-control" name="name" value="{{ request()->get('name') }}">
                        </th>
                        <th scope="col">
                            Color
                            <input type="text" class="form-control" name="color" value="{{ request()->get('color') }}">
                        </th>
                        <th scope="col">
                            Width
                            <input type="text" class="form-control" name="width" value="{{ request()->get('width') }}">
                        </th>
                        <th scope="col">
                            Type
                            <input type="text" class="form-control" name="type" value="{{ request()->get('type') }}">
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <th>{{ $product->name }}</th>
                            <td>{{ $product->color }}</td>
                            <td>{{ $product->width }}</td>
                            <td>{{ $product->type }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </form>
        </div>
        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            <div>
                <pre>
                    @foreach($cache as $c)
                        {{ json_encode($c) }}
                    @endforeach
                </pre>
            </div>
        </div>
    </div>
</div>
</body>
</html>

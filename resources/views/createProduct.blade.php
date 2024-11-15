@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Product</div>
                <div class="pl-4 pt-4 mr-auto">
                    <a href="Javascript:void(0)" class="btn btn-success">Add New Product</a>
                    @if (session('status'))
                        <div class="text-{{ session('status') }} mt-2">
                            <strong>{{ session('message') }}</strong>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('StoreProduct') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="productname">Product Name</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="name" id="productname" class="form-control" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="brand">Select Brand</label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="brand" name="brand">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->brand_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="category">Select Category</label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="Price">Price</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="price" id="Price" class="form-control" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="weight">Weight</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="weight" id="weight" class="form-control" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="weight_type">Weight Type</label>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" id="weight_type" name="weight_type" required/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="weight_type">Product Image</label>
                            </div>
                            <div class="col-md-4">
                                <input type="file" name="image" accept="image/png, image/gif, image/jpeg">
                            </div>
                        </div>
                        <div class="text-center pt-3">
                            <button type="submit" class="btn btn-info">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

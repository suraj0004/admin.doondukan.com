@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Brand</div>
                <div class="pl-4 pt-4 mr-auto">
                    <a href="Javascript:void(0)" class="btn btn-success">Add New Product</a>
                    @if (session('status'))
                        <div class="text-{{ session('status') }} mt-2">
                            <strong>{{ session('message') }}</strong>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateProduct',$data->id ) }}" method="POST">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="productname">Product Name</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="name" id="productname" class="form-control" value="{{ $data->name}}" required>  
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="brand">Select Brand</label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="brand" name="brand" required>
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}"  @if($data->brand->id==$brand->id ) selected @endif>{{ $brand->brand_name}}</option>
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
                                    <option value="{{ $category->id }}" @if($data->category->id==$category->id ) selected @endif >{{ $category->category_name }}</option>
                                    @endforeach
                                </select>  
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="weight">Weight</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="weight" id="weight" class="form-control" value="{{ $data->weight }}" required>  
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="weight_type">Weight Type</label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="weight_type" name="weight_type" required>
                                    <option value="Kg" @if($data->weight_type=="Kg" ) selected @endif >Kg</option>
                                    <option value="gm" @if($data->weight_type=="gm" ) selected @endif >Gm</option>
                                    <option value="l"  @if($data->weight_type=="l" ) selected @endif >L</option>
                                    <option value="ml" @if($data->weight_type=="ml" ) selected @endif>Ml</option>
                                </select> 
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
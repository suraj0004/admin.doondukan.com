@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Product</div>
                <div class="pl-4 pt-4 mr-auto">
                    <a href="{{ route('CreateProduct') }}" class="btn btn-outline-primary">Add new product</a>
                    @if(session('status'))
                    <div class="text-{{ session('status') }} mt-2">
                        <strong> {{ session('message') }}</strong>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Brand Name</th>
                                <th>Category Name</th>
                                <th>Weight</th>
                                <th>Weight Type</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ( $data as $product )
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                                <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                <td>{{ $product->weight }}</td>
                                <td>{{ $product->weight_type }}</td>
                                <td>{{ $product->created_at }}</td>
                                <td><a href="{{ route('DeleteProduct',$product->id) }}" class="text-danger" onclick="return confirm('Are you sure? You want to delete this.')"s><strong>Delete</strong></a> / <a href="{{ route('EditProduct',$product->id) }}" class="text-primary"><strong>Edit</strong></a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">No data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="row">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Brands</div>
                <div class="pl-4 pt-4 mr-auto">
                    <a href="{{ route('CreateBrand') }}" class="btn btn-outline-primary">Add new brand</a>
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
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ( $data as $brand )
                            <tr>
                                <td>{{ $brand->brand_name }}</td>
                                <td>{{ $brand->created_at }}</td>
                                <td><a href="{{ route('DeleteBrand',$brand->id) }}" class="text-danger" onclick="return confirm('Are you sure? You want to delete this.')"><strong>Delete</strong></a> / <a href="{{ route('EditBrand',$brand->id) }}" class="text-primary"><strong>Edit</strong></a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">No data found</td>
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
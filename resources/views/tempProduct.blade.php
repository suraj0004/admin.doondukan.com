@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Temp Products ( {{ $data->total() }} )</div>
                <div class="pl-4 pt-4 mr-auto">
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
                                <th>Weight</th>
                                <th>Weight Type</th>
                                <th>User Name</th>
                                <th>User Number</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ( $data as $tempProduct )
                            <tr>
                                <td>{{ $tempProduct->name }}</td>
                                <td>{{ $tempProduct->brand_name ?? 'N/A' }}</td>
                                <td>{{ $tempProduct->weight }}</td>
                                <td>{{ $tempProduct->weight_type }}</td>
                                <td>{{ $tempProduct->user->name }}</td>
                                <td>{{ $tempProduct->user->phone }}</td>
                                <td>{{ $tempProduct->created_at }}</td>
                                <td> <a href="{{ route('AddTempProductToProduct',$tempProduct->id) }}" class="text-success"><strong>Add To Product</strong></a> | <a href="{{ route('DeleteTempProduct',$tempProduct->id) }}" class="text-danger" onclick="return confirm('Are you sure? You want to delete this.')"><strong>Delete</strong></a>
                                </td>
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
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Category</div>
                <div class="pl-4 pt-4 mr-auto">
                    <a href="Javascript:void(0)" class="btn btn-success">Edit Category</a>
                    @if (session('status'))
                        <div class="text-{{ session('status') }} mt-2">
                            <strong>{{ session('message') }}</strong>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateCategory',$data->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label for="cat-name">Category Name</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="name" id="cat-name" class="form-control" value="{{ $data->category_name }}" required>  
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
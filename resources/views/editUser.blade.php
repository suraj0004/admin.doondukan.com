@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">User</div>
                <div class="pl-4 pt-4 mr-auto">
                    <a href="Javascript:void(0)" class="btn btn-success">Edit User</a>
                    @if (session('status'))
                        <div class="text-{{ session('status') }} mt-2">
                            <strong>{{ session('message') }}</strong>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdateUser',$data->id) }}" method="POST" onsubmit="return validate_password()" enctype="multipart/form-data">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="name">Name</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="name" id="name" class="form-control" value="{{ $data->name ?? '' }}" required>  
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="email">Email</label>
                            </div>
                            <div class="col-md-4">
                                <input type="email" name="email" class="form-control" value="{{ $data->email ?? '' }}" id="email">
                            </div>
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="number">Phone Number</label>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="phone" id="number" class="form-control"value="{{ $data->phone ?? '' }}" required>  
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="password">Password</label>
                            </div>
                            <div class="col-md-4">
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="c_password">Confirm Password</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="c_password" id="c_password" class="form-control" onchange="validate_password()">
                            </div>
                            <span class="text-danger" id="error"></span>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="profile_picture">Profile Picture</label>
                            </div>
                            <div class="col-md-4">
                                <input type="file" id="profile_picture" name="image" class="form-control" style="padding-bottom: 35px;"> 
                            </div>
                            @if(!empty( $data->image) )
                            <div class="col-md-4">
                                <img src="{{ URL('/') }}/profileimages/{{ $data->id }}/thumb_{{ $data->image }}" style="width: 18%; height: 80%;">
                            </div>
                            @endif
                        </div>
                        <div class="row form-group">
                            <div><strong class="text-primary">Add Store Details</strong></div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="store_name">Store Name</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="store_name" id="store_name" value="{{ $data->store->name ?? '' }}" class="form-control">  
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="store_email">Store Email</label>
                            </div>
                            <div class="col-md-4">
                                <input type="email" name="store_email" id="store_email" value="{{ $data->store->email ?? '' }}" class="form-control"> 
                            </div>
                            @error('store_email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="store_number">Store Contact Number</label>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="store_number" value="{{ $data->store->mobile ?? '' }}" id="store_number" class="form-control"> 
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="registration_date">Registration Date</label>
                            </div>
                            <div class="col-md-4">
                                <input type="date" id="registration_date" value="{{ $data->store->registration_date ?? '' }}" name="registration_date" class="form-control"> 
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="valid_upto_date">Valid Upto Date</label>
                            </div>
                            <div class="col-md-4">
                                <input type="date" id="valid_upto_date" value="{{ $data->store->valid_upto ?? '' }}" name="valid_upto_date" class="form-control"> 
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="store_address">Store Address</label>
                            </div>
                            <div class="col-md-4">
                                <textarea rows="4" cols="6" id="store_address" name="store_address" class="form-control">{{ $data->store->address ?? '' }}</textarea> 
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="store_about">Store About</label>
                            </div>
                            <div class="col-md-4">
                                <textarea rows="4" cols="6" id="store_about" name="store_about" class="form-control">{{ $data->store->about ?? '' }}</textarea> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="store_logo">Store Logo</label>
                            </div>
                            <div class="col-md-4">
                                <input type="file" id="store_logo" name="logo" class="form-control" style="padding-bottom: 35px;"> 
                            </div>
                            @if(!empty( $data->store->logo) )
                            <div class="col-md-4">
                                <img src="{{ URL('/') }}/shopimages/{{ $data->id }}/thumb_{{ $data->store->logo }}" style="width: 18%; height: 80%;">
                            </div>
                            @endif
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
@push('script')
<script type="text/javascript" defer>
    function validate_password() 
    {
        var pass = $("#password").val();
        var c_password = $("#c_password").val();
        if(pass != c_password && pass != "") 
        {
            $("#error").text("Confirm Password does not match");
            return false;
        }
        else 
        {
            $("#error").text("");
            return true;
        }
    }
</script>
@endpush
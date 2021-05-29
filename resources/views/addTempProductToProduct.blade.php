@extends('layouts.app')
@push('style')
<style type="text/css">
    a:hover{
        color: black;
    }
</style>
@endpush
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Temp Products ( {{ $data->name }} )</div>
                <div class="pl-4 pt-4 mr-auto">
                    @if(session('status'))
                    <div class="text-{{ session('status') }} mt-2">
                        <strong> {{ session('message') }}</strong>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="col-md-8">
                        <form action="{{ route('StoreTempProductToMainProduct',$data->id) }}" method="POST">
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
                                <button type="submit" class="btn btn-info">Add to main</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <span class="font-weight-bold text-primary">Or Search Product</span>
                    </div>
                    <div class="col-md-8 pt-2">
                        <form action="{{ route('StoreTempProductToMainProduct',$data->id) }}" method="POST">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="productname">Product Name</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" onkeyup="getProduct(this.value)" class="form-control" placeholder="Search..." id="pname" required>
                                <div class="bg-light" id="searchresult" style="max-height: 130px;overflow-y: auto;"></div>
                                <input type="hidden" name="tempMainProductId" id="tempMainProductId">
                            </div>
                        </div>
                        <div class="text-center pt-3">
                            <button type="submit" class="btn btn-info">Assign Product</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script type="text/javascript">
    function getProduct(value) 
    {
        var url = "{{ route('SearchProduct') }}";
         $.ajax({
            url: url,
            data: {name:value},
            success: function (data) {
               if(data.success==false) {
                    $("#searchresult").html('<a href="javascript:void(0)" class="d-block p-1">Not Product Found.</a>');
                } else {
                    var i;
                    $("#searchresult").html('');
                    for (i = 0; i < data.data.length; i++) {
                        $("#searchresult").append("<a href='javascript:void(0)' class='d-block p-1' onclick=setProduct("+data.data[i].id+",'"+data.data[i].name+"')>"+data.data[i].name+"</a>"); 
                    }
               }
            },
        });
    }

    function setProduct(id,name) 
    {
        $("#pname").val(name);
        $("#tempMainProductId").val(id);
    }
</script>
@endpush
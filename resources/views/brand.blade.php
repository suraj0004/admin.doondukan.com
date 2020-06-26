@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Brands ( {{ $data->total() }} )</div>
                <div class="pl-4 pt-4 mr-auto">
                    <a href="{{ route('CreateBrand') }}" class="btn btn-outline-primary">Add New Brand</a>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#import-brands" class="btn btn-outline-success">Import From Excel</a>
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
                                <th>Country</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ( $data as $brand )
                            <tr>
                                <td>{{ $brand->brand_name }}</td>
                                <td>{{ $brand->country ?? 'N/A' }}</td>
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
<!-- Import Excel Modal -->
<div class="modal" id="import-brands">
  <div class="modal-dialog">
    <div class="modal-content">
       <form action="{{ route('import-excel') }}" method="POST" enctype="multipart/form-data"> 
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Import Brands</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <div>
                @csrf
                <input type="file" id="excelfile" name="excelfile" accept=".xlsx, .xls, .csv" required>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Upload</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          </div>
      </form>
    </div>
  </div>
</div>
<!-- End of import excel modal -->
@endsection
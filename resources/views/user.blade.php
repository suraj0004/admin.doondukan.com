@extends('layouts.app')
@push('style')
<style type="text/css">
    th,.white-space 
    {
        white-space: nowrap;
    }
</style>
@endpush
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Users</div>
                <div class="pl-4 pt-4 mr-auto">
                    <a href="{{ route('CreateUser') }}" class="btn btn-outline-primary">Add new user</a>
                    @if(session('status'))
                    <div class="text-{{ session('status') }} mt-2">
                        <strong> {{ session('message') }}</strong>
                    </div>
                    @endif
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Address</th>
                                <th>Registration Date</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ( $data as $user )
                            <tr>
                                <td  class="white-space">{{ $user->name }}</td>
                                <td class="white-space">{{ $user->email ?? 'N/A' }}</td>
                                <td class="white-space">{{ $user->phone }}</td>
                                <td>{{ $user->store->address ?? 'N/A' }}</td>
                                <td class="white-space">
                                    @if(!empty($user->store->registration_date)) 
                                    {{ date("d-m-Y", strtotime($user->store->registration_date)) }}
                                    @else
                                    {{ 'N/A' }}
                                    @endif 
                                </td>
                                <td class="white-space">{{ date("d-m-Y", strtotime($user->created_at)) }}</td>
                                <td class="white-space"><a href="{{ route('DeleteUser',$user->id) }}" class="text-danger" onclick="return confirm('Are you sure? You want to delete this.')"s><strong>Delete</strong></a> / <a href="{{ route('EditUser',$user->id) }}" class="text-primary"><strong>Edit</strong></a></td>
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
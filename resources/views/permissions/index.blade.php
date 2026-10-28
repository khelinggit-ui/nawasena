@extends('layouts.master')
@section('title', 'Permissions Management')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
             <div class="page-title-box mt-3">
                 <h4 class="mb-sm-0">Permissions Management</h4>
             </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card">
                <div class="card-header">
                    @can('create permission')
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary">Create Permission</a>
                    @endcan
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>
                                    @can('edit permission')
                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @endcan
                                    @can('delete permission')
                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this permission?')">Delete</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

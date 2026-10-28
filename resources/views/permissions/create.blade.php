@extends('layouts.master')
@section('title', 'Create Permission')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
             <div class="page-title-box mt-3">
                 <h4 class="mb-sm-0">Create Permission</h4>
             </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Permission Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Save</button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.admin')

@section('title', 'Edit Seller')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3"><i class="bi bi-pencil-square me-2"></i>Edit Seller</h5>

            <form action="{{ route('admin.sellers.update', $seller) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-shop"></i> Business Name</label>
                    <input type="text" name="name" value="{{ $seller->name }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person"></i> Contact Person</label>
                    <input type="text" name="contact_person" value="{{ $seller->contact_person }}" class="form-control"
                        required>
                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                        <input type="email" name="email" value="{{ $seller->email }}" class="form-control" required>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label"><i class="bi bi-telephone"></i> Phone</label>
                        <input type="text" name="phone" value="{{ $seller->phone }}" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('admin.sellers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
            </form>
        </div>
    </div>
@endsection

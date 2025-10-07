@extends('admin.layouts.admin')

@section('title', 'Customer Details')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="bi bi-person-badge"></i> Customer Details</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>First Name:</strong></p>
                    <p class="text-muted">{{ $customer->first_name }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Last Name:</strong></p>
                    <p class="text-muted">{{ $customer->last_name }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Email:</strong></p>
                    <p class="text-muted">{{ $customer->email }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Phone:</strong></p>
                    <p class="text-muted">{{ $customer->phone ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Gender:</strong></p>
                    <p class="text-muted">{{ ucfirst($customer->gender) }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Date of Birth:</strong></p>
                    <p class="text-muted">{{ $customer->dob ? \Carbon\Carbon::parse($customer->dob)->format('d M Y') : '-' }}</p>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>
@endsection

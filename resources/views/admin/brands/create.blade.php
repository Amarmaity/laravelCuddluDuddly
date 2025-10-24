@extends('admin.layouts.admin')
@section('title', isset($brand) ? 'Edit Brand' : 'New Brand')

@section('content')
    <div class="container-fluid px-2 px-md-3 py-2">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-semibold text-primary">
                <i class="bi bi-tags me-2"></i> {{ isset($brand) ? 'Edit Brand' : 'New Brand' }}
            </h4>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <form action="{{ isset($brand) ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($brand))
                        @method('PUT')
                    @endif

                    @include('admin.brands._form', [
                        'submit' => isset($brand) ? 'Update Brand' : 'Create Brand',
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection

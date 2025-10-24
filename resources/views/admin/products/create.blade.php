@extends('admin.layouts.admin')

@section('title', 'Add New Product')

@section('content')
    <div class="container-fluid py-1">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-box2-heart me-2"></i> Add New Product</h5>
            </div>

            <div class="card-body">
                <form id="productForm" action="{{ route('admin.products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">

                        <!-- 🧑‍💼 Seller -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Seller</label>
                            <select name="seller_id" class="form-select" required>
                                <option value="">Select Seller</option>
                                @foreach ($sellers as $seller)
                                    <option value="{{ $seller->id }}">{{ $seller->name ?? $seller->shop_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 🏷 Category Multi-select -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Categories</label>
                            <select name="master_category_section_id[]" id="categorySelect" multiple required>
                                @foreach ($categoryTree as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->masterCategory->name ?? 'N/A' }} →
                                        {{ $item->sectionType->name ?? 'N/A' }} →
                                        {{ $item->category->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">You can select multiple categories (search supported).</small>
                        </div>

                        <!-- 📝 Product Name -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Product Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Product name" required>
                        </div>

                        <!-- 💰 Price -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00"
                                required>
                        </div>

                        <!-- 📦 Stock -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock</label>
                            <input type="number" name="stock" class="form-control" placeholder="Quantity in stock"
                                required>
                        </div>

                        <!-- 📝 Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Product description"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Product Images</label>

                            <div id="dropZone" class="dropzone-area text-center">
                                <div class="dropzone-content" id="dropzoneContent">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-primary"></i>
                                    <p class="mt-2 mb-1 fw-semibold">Drag & Drop Images Here or Click to Browse</p>
                                    <small class="text-muted">Upload at least 3 images (JPEG, PNG, max 500KB each).</small>
                                </div>
                                <div id="imagePreviews" class="preview-container mt-3 d-none"></div>
                                <div id="uploadProgress" class="upload-progress mt-3 d-none">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="upload-status">
                                        Uploading: <span class="upload-count">0</span>/<span class="total-count">0</span>
                                    </small>
                                </div>
                            </div>

                            <input type="file" id="imageInput" name="images[]" accept="image/*" multiple hidden>
                        </div>
                        <div id="errorMessage" class="text-danger mt-2 small fw-semibold d-none"></div>
                    </div>

                    <!-- 🎯 Submit -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('css/product-form.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/product-form.js') }}"></script>
@endpush

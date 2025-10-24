@extends('admin.layouts.admin')

@section('content')
    <div class="container-fluid py-1">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Edit Product</h5>
            </div>

            <div class="card-body">
                <form id="productEditForm" action="{{ route('admin.products.update', $product->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- 🧑‍💼 Seller -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Seller</label>
                            <select name="seller_id" class="form-select" required>
                                <option value="">Select Seller</option>
                                @foreach ($sellers as $seller)
                                    <option value="{{ $seller->id }}"
                                        {{ $seller->id == $product->seller_id ? 'selected' : '' }}>
                                        {{ $seller->name ?? $seller->shop_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 🏷 Categories -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Categories</label>
                            <select name="master_category_section_id[]" id="categorySelect" multiple required>
                                @foreach ($categoryTree as $item)
                                    <option value="{{ $item->id }}"
                                        {{ in_array($item->id, $selectedCategoryIds) ? 'selected' : '' }}>
                                        {{ $item->masterCategory->name ?? 'N/A' }} →
                                        {{ $item->sectionType->name ?? 'N/A' }} →
                                        {{ $item->category->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">You can select multiple categories (search supported).</small>
                        </div>

                        <!-- 📝 Product Info -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Product Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control"
                                value="{{ $product->price }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock</label>
                            <input type="number" name="stock" class="form-control" value="{{ $product->stock }}"
                                required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ $product->description }}</textarea>
                        </div>

                        <!-- 📸 Product Image Upload Zone -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Images</label>
                            <!-- Dropzone -->
                            <div id="dropZone" class="dropzone-area">
                                <div id="dropzoneContent" class="dropzone-content text-center">
                                    <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>
                                    <p class="mt-2 mb-1 fw-semibold">Drag & Drop Images Here or Click to Browse</p>
                                    <small class="text-muted">Upload at least 3 images (JPEG, PNG, max 500KB each).</small>
                                </div>

                                <!-- Image Previews -->
                                <div id="imagePreviews" class="preview-container mt-3">
                                    {{-- Show existing images here --}}
                                    @foreach ($product->images as $img)
                                        <div class="preview-item existing" data-id="{{ $img->id }}">
                                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="Existing Image">
                                            <button type="button" class="preview-remove" title="Remove">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- Upload Progress (same as create) -->
                                <div id="uploadProgress" class="upload-progress mt-3 d-none">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="upload-status">
                                        Uploading: <span class="upload-count">0</span>/<span class="total-count">0</span>
                                    </small>
                                </div>
                            </div>

                            <!-- Hidden inputs -->
                            <input type="file" id="imageInput" name="images[]" accept="image/*" multiple hidden>
                            <input type="hidden" id="removedImages" name="removed_images" value="">
                        </div>
                        <div id="errorMessage" class="text-danger mt-2 small fw-semibold d-none"></div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Update Product
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
    <link href="{{ asset('css/product-edit.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/product-edit.js') }}"></script>
@endpush

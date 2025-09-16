@extends('admin.layouts.admin')

@section('title', 'Manage Categories')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/css/categories-index.css') }}">
@endpush

@section('content')
    <div class="container-fluid py-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 mb-0">Manage Categories</h2>
            <button type="button" class="btn btn-success btn-sm action-btn" data-bs-toggle="modal"
                data-bs-target="#actionModal" data-action="add" data-type="master">
                <i class="bi bi-plus-circle me-1"></i> Add Master Category
            </button>
        </div>

        <form id="bulkForm" action="{{ route('admin.categories.bulkAction') }}" method="POST">
            @csrf
            <div class="mb-2">
                <button type="submit" name="action" value="delete" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash3 me-1"></i> Delete Selected
                </button>
            </div>

            @if ($masterCategories->isEmpty())
                <div class="alert alert-info">No master categories found. Start by adding one!</div>
            @else
                <ul class="category-tree">
                    @foreach ($masterCategories as $masterCategory)
                        <li class="tree-item">
                            <div class="item-content level-0">
                                <i class="bi bi-chevron-right tree-toggler @if ($masterCategory->sectionTypes->isEmpty()) invisible @endif"
                                    aria-expanded="false"></i>
                                <input type="checkbox" class="form-check-input parent-checkbox" name="selected[master][]"
                                    value="{{ $masterCategory->id }}">
                                <div class="image-wrapper" data-id="{{ $masterCategory->id }}" data-type="master">
                                    <img src="{{ asset('storage/' . $masterCategory->image_url) }}"
                                        class="preview-{{ $masterCategory->id }}" alt="{{ $masterCategory->name }}">
                                    <div class="image-overlay">
                                        <i class="bi bi-pencil-fill"></i>
                                        <input type="file" class="image-input" data-id="{{ $masterCategory->id }}"
                                            data-type="master" accept="image/*">
                                    </div>
                                    <div class="upload-progress" id="progress-{{ $masterCategory->id }}">0%</div>
                                </div>

                                <span class="item-name">{{ $masterCategory->name }}</span>
                                <div class="action-icons">
                                    <a href="#" class="action-btn" title="Add Section" data-bs-toggle="modal"
                                        data-bs-target="#actionModal" data-action="add" data-type="section"
                                        data-parent-id="{{ $masterCategory->id }}"
                                        data-parent-name="{{ $masterCategory->name }}">
                                        <i class="bi bi-plus-circle-fill"></i>
                                    </a>
                                    <a href="#" class="action-btn" title="Edit Master Category" data-bs-toggle="modal"
                                        data-bs-target="#actionModal" data-action="edit" data-type="master"
                                        data-id="{{ $masterCategory->id }}" data-name="{{ $masterCategory->name }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- FIXED PART: Changed div to ul for semantic HTML --}}
                            @if ($masterCategory->sectionTypes->isNotEmpty())
                                <ul class="nested">
                                    @foreach ($masterCategory->sectionTypes as $sectionType)
                                        <li class="tree-item @if ($sectionType->categories->isNotEmpty()) no-bottom-border @endif">
                                            <div class="item-content level-1">
                                                <i class="bi bi-chevron-right tree-toggler @if ($sectionType->categories->isEmpty()) invisible @endif"
                                                    aria-expanded="false"></i>
                                                {{-- <input type="checkbox"
                                                    class="form-check-input child-checkbox parent-checkbox"
                                                    name="selected[section][]" value="{{ $sectionType->id }}"> --}}
                                                <input type="checkbox"
                                                    class="form-check-input child-checkbox parent-checkbox"
                                                    name="selected[section][]"
                                                    value="{{ $sectionType->id }}:{{ $masterCategory->id }}">
                                                <span class="item-name">{{ $sectionType->name }}</span>
                                                <div class="action-icons">
                                                    <a href="#" class="action-btn" title="Add Category"
                                                        data-bs-toggle="modal" data-bs-target="#actionModal"
                                                        data-action="add" data-type="category"
                                                        data-parent-id="{{ $sectionType->id }}"
                                                        data-parent-name="{{ $sectionType->name }}">
                                                        <i class="bi bi-plus-circle-fill"></i>
                                                    </a>
                                                    <a href="#" class="action-btn" title="Edit Section"
                                                        data-bs-toggle="modal" data-bs-target="#actionModal"
                                                        data-action="edit" data-type="section"
                                                        data-id="{{ $sectionType->id }}"
                                                        data-name="{{ $sectionType->name }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            @if ($sectionType->categories->isNotEmpty())
                                                <div class="nested category-grid-container">
                                                    <div
                                                        class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 row-cols-xl-5 g-2">
                                                        @foreach ($sectionType->categories as $category)
                                                            <div class="col">
                                                                <div class="card category-card h-100">
                                                                    {{-- <input type="checkbox"
                                                                        class="form-check-input child-checkbox"
                                                                        name="selected[category][]"
                                                                        value="{{ $category->id }}"> --}}
                                                                    <input type="checkbox"
                                                                        class="form-check-input child-checkbox"
                                                                        name="selected[category][]"
                                                                        value="{{ $category->id }}:{{ $masterCategory->id }}">
                                                                    <a href="#" class="action-icon action-btn"
                                                                        title="Edit Category" data-bs-toggle="modal"
                                                                        data-bs-target="#actionModal" data-action="edit"
                                                                        data-type="category"
                                                                        data-id="{{ $category->id }}"
                                                                        data-name="{{ $category->name }}">
                                                                        <i class="bi bi-pencil-square"></i>
                                                                    </a>

                                                                    <div class="image-wrapper"
                                                                        data-id="{{ $category->id }}"
                                                                        data-type="category">
                                                                        <img src="{{ asset('storage/' . $category->image_url) }}"
                                                                            class="preview-{{ $category->id }}"
                                                                            alt="{{ $category->name }}">
                                                                        <div class="image-overlay">
                                                                            <i class="bi bi-pencil-fill"></i>
                                                                            <input type="file" class="image-input"
                                                                                data-id="{{ $category->id }}"
                                                                                data-type="category" accept="image/*">
                                                                        </div>
                                                                        <div class="upload-progress"
                                                                            id="progress-{{ $category->id }}">0%</div>
                                                                    </div>

                                                                    <div class="card-body">
                                                                        <h6 class="card-title">{{ $category->name }}</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </form>
    </div>

    {{-- Unified Add/Edit Modal (No changes needed) --}}
    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <form id="actionForm" method="POST" action="">
                    @csrf
                    <div id="method-field"></div>
                    <input type="hidden" name="type" id="actionType">
                    <input type="hidden" name="parent_id" id="actionParentId">
                    <div class="modal-header">
                        <h5 class="modal-title fs-6" id="actionModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="actionName" class="form-label visually-hidden">Name</label>
                            <input type="text" name="name" id="actionName" class="form-control form-control-sm"
                                required placeholder="Enter name...">
                        </div>
                    </div>
                    <div class="modal-footer p-1">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="actionSubmitButton">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Pagination -->
    <div class="card-footer">
        {{ $masterCategories->links('pagination::bootstrap-5') }}
    </div>
    <div class="toast-container"></div>
@endsection

@push('scripts')
    <script>
        window.appRoutes = {
            storeCategory: "{{ route('admin.categories.store') }}",
            indexCategory: "{{ route('admin.categories.index') }}",
            bulkAction: "{{ route('admin.categories.bulkAction') }}",
            uploadImage: "{{ route('admin.categories.uploadImage') }}"
        };
    </script>
    <script src="{{ asset('public/js/categories-index.js') }}" defer></script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Tree Toggler Logic (FIXED) ---
            document.querySelectorAll('.tree-toggler').forEach(toggler => {
                toggler.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Use a more robust selector to find the common parent `<li>`
                    const parentItem = this.closest('.tree-item');
                    const nestedList = parentItem.querySelector('.nested');

                    if (nestedList) {
                        const isExpanded = this.getAttribute('aria-expanded') === 'true';
                        this.setAttribute('aria-expanded', !isExpanded);
                        this.classList.toggle('bi-chevron-right', isExpanded);
                        this.classList.toggle('bi-chevron-down', !isExpanded);
                        nestedList.classList.toggle('active');
                    }
                });
            });

            // --- Unified Modal Logic (No changes needed) ---
            const actionModalEl = document.getElementById('actionModal');
            const actionModal = new bootstrap.Modal(actionModalEl);
            const modalForm = document.getElementById('actionForm');
            const modalTitle = document.getElementById('actionModalLabel');
            const nameInput = document.getElementById('actionName');
            const typeInput = document.getElementById('actionType');
            const parentIdInput = document.getElementById('actionParentId');
            const submitButton = document.getElementById('actionSubmitButton');
            const methodFieldContainer = document.getElementById('method-field');

            document.querySelectorAll('.action-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action;
                    const type = this.dataset.type;
                    const typeTitleCase = type.charAt(0).toUpperCase() + type.slice(1);

                    modalForm.reset();
                    methodFieldContainer.innerHTML = '';
                    parentIdInput.value = '';
                    submitButton.textContent = 'Save';
                    submitButton.classList.remove('btn-success');
                    submitButton.classList.add('btn-primary');

                    typeInput.value = type;

                    if (action === 'add') {
                        modalForm.action = "{{ route('admin.categories.store') }}";
                        modalTitle.textContent = `Add New ${typeTitleCase}`;
                        if (type !== 'master') {
                            modalTitle.textContent += ` to "${this.dataset.parentName}"`;
                        }
                        parentIdInput.value = this.dataset.parentId || '';
                        submitButton.textContent = 'Create';
                        submitButton.classList.replace('btn-primary', 'btn-success');
                    } else if (action === 'edit') {
                        const id = this.dataset.id;
                        modalForm.action = `/admin/categories/${id}`;
                        methodFieldContainer.innerHTML = `@method('PUT')`;
                        modalTitle.textContent = `Edit ${typeTitleCase}`;
                        nameInput.value = this.dataset.name;
                        submitButton.textContent = 'Save Changes';
                    }
                });
            });

            actionModalEl.addEventListener('shown.bs.modal', () => {
                nameInput.focus();
            });

            // --- Checkbox Propagation Logic (No changes needed) ---
            document.querySelectorAll('.parent-checkbox').forEach(parentCheckbox => {
                parentCheckbox.addEventListener('change', function() {
                    const parentItem = this.closest('.tree-item');
                    const nestedContainer = parentItem.querySelector('.nested');
                    if (nestedContainer) {
                        nestedContainer.querySelectorAll('.child-checkbox').forEach(
                            childCheckbox => {
                                childCheckbox.checked = this.checked;
                            });
                    }
                });
            });

        });
        // --- Bulk Delete Confirmation ---
        function confirmBulkDelete() {
            const selectedCount = document.querySelectorAll('#bulkForm input[type="checkbox"]:checked').length;
            if (selectedCount === 0) {
                alert("Please select at least one item to delete.");
                return false;
            }
            return confirm(
                `Are you sure you want to delete the ${selectedCount} selected item(s)? This action cannot be undone.`);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.image-input').forEach(input => {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (!file) return;

                    const id = this.dataset.id;
                    const type = this.dataset.type;
                    const progressEl = document.getElementById(`progress-${id}`);
                    const previewEl = document.querySelector(`.preview-${id}`);

                    let formData = new FormData();
                    formData.append('image', file);
                    formData.append('id', id);
                    formData.append('type', type);
                    formData.append('_token', '{{ csrf_token() }}');

                    let xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route('admin.categories.uploadImage') }}', true);

                    // ✅ Show progress %
                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            let percent = Math.round((e.loaded / e.total) * 100);
                            progressEl.style.display = 'block';
                            progressEl.textContent = percent + '%';
                        }
                    });

                    xhr.onload = function() {
                        progressEl.style.display = 'none';
                        progressEl.textContent = '0%';

                        if (xhr.status === 200) {
                            let response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                // ✅ Force fresh image (avoid caching)
                                previewEl.src = response.url + '?v=' + new Date().getTime();

                                if (typeof showToast === 'function') {
                                    showToast(response.message || "Image uploaded", 'success');
                                }
                            } else {
                                if (typeof showToast === 'function') {
                                    showToast(response.message || "Upload failed", 'danger');
                                }
                            }
                        } else {
                            if (typeof showToast === 'function') {
                                showToast("Upload error", 'danger');
                            }
                        }
                    };

                    xhr.onerror = function() {
                        progressEl.style.display = 'none';
                        progressEl.textContent = '0%';
                        if (typeof showToast === 'function') {
                            showToast("Network error", 'danger');
                        }
                    };

                    xhr.send(formData);
                });
            });
        });
    </script> --}}
@endpush

<div class="row g-4">
    <div class="col-md-5 text-center">
        @if ($product->images->count())
            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                class="img-fluid rounded shadow-sm mb-2" alt="">
            <div class="d-flex justify-content-center flex-wrap gap-2">
                @foreach ($product->images as $img)
                    <img src="{{ asset('storage/' . $img->image_path) }}" class="img-thumbnail rounded"
                        style="width: 60px; height: 60px; object-fit: cover;">
                @endforeach
            </div>
        @else
            <p class="text-muted small">No images uploaded.</p>
        @endif
    </div>

    <div class="col-md-7">
        <h5 class="mb-2">{{ $product->name }}</h5>
        <p class="text-muted small">{{ $product->description ?: 'No description available' }}</p>
        <p><strong>Price:</strong> ₹{{ number_format($product->price, 2) }}</p>
        <p><strong>Stock:</strong> {{ $product->stock }}</p>
        <p><strong>Seller:</strong> {{ $product->seller->name ?? '—' }}</p>
        <p><strong>Category Chain:</strong><br>
            @foreach ($product->categorySections as $sec)
                <small class="text-muted d-block">
                    {{ $sec->masterCategory->name ?? '' }} →
                    {{ $sec->sectionType->name ?? '' }} →
                    {{ $sec->category->name ?? '' }}
                </small>
            @endforeach
        </p>

        {{-- <div class="mt-3 d-flex gap-2">
            <button class="btn btn-success btn-sm"><i class="bi bi-check2-circle"></i></button>
            <button class="btn btn-warning btn-sm"><i class="bi bi-star"></i></button>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-info btn-sm"><i
                    class="bi bi-pencil"></i></a>
            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
        </div> --}}

        <div class="d-flex justify-content-center gap-2 mt-3">
            <button class="btn btn-success btn-sm btn-approve" data-id="{{ $product->id }}">
                <i class="bi bi-check2-circle"></i>
            </button>
            <button class="btn btn-warning btn-sm btn-feature" data-id="{{ $product->id }}">
                <i class="bi bi-star"></i>
            </button>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-info btn-sm"><i
                    class="bi bi-pencil"></i></a>
            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $product->id }}">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</div>

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Sellers;
use App\Models\MasterCategorySection;
use App\Models\ProductImage;
use App\Models\ProductCategorySection;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sellers = Sellers::select('id', 'name', 'contact_person')->get();
        $query = Products::with([
            'seller',
            'approvedBy',
            'categorySections.masterCategory',
            'categorySections.sectionType',
            'categorySections.category'
        ])->select('products.*');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('approval_status')) {
            if ($request->approval_status === 'approved') {
                $query->where('is_approved', 1);
            } elseif ($request->approval_status === 'pending') {
                $query->where('is_approved', 0);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('featured')) {
            $query->where('featured', $request->featured ? 1 : 0);
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        switch ($request->get('sort')) {
            case 'oldest':
                $query->oldest();
                break;

            case 'price_low_high':
                $query->orderBy('price', 'asc');
                break;

            case 'price_high_low':
                $query->orderBy('price', 'desc');
                break;

            case 'stock_low_high':
                $query->orderBy('stock', 'asc');
                break;

            case 'stock_high_low':
                $query->orderBy('stock', 'desc');
                break;

            case 'name':
                $query->orderBy('name');
                break;

            default: // latest
                $query->latest();
        }

        $products = $query->paginate(5)->withQueryString();

        return view('admin.products.index', compact('products', 'sellers'));
    }

    public function create()
    {
        $sellers = Sellers::select('id', 'name')->get();

        $categoryTree = MasterCategorySection::with(['masterCategory', 'sectionType', 'category'])
            ->get();
        return view('admin.products.create', compact('sellers', 'categoryTree'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'master_category_section_id' => 'required|array|min:1',
            'master_category_section_id.*' => 'exists:master_category_sections,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'images' => 'required|array|min:3',
            'images.*' => 'image|mimes:jpeg,jpg,png|max:500', // max size in KB
        ], [
            'images.*.mimes' => 'Only JPG, JPEG, or PNG formats are allowed.',
            'images.*.max' => 'Each image must be less than 500KB.',
            'images.required' => 'Upload at least 3 product images.',
        ]);

        DB::beginTransaction();

        try {
            $product = Products::create([
                'seller_id' => $validated['seller_id'],
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']) . '-' . uniqid(),
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'featured' => 0,
                'is_approved' => 0,
            ]);

            if ($request->hasFile('images')) {
                $isFirst = true;
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $isFirst ? 1 : 0,
                    ]);

                    $isFirst = false;
                }
            }

            foreach ($validated['master_category_section_id'] as $categoryId) {
                ProductCategorySection::create([
                    'product_id' => $product->id,
                    'master_category_section_id' => $categoryId,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while saving the product. ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $ids = is_array($request->ids)
            ? $request->ids
            : json_decode($request->ids, true);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No products selected for deletion.']);
        }

        DB::beginTransaction();

        try {
            $products = Products::with('images')->whereIn('id', $ids)->get();

            foreach ($products as $product) {
                // 🖼️ Delete images from storage
                foreach ($product->images as $image) {
                    if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    $image->delete();
                }

                // 🔗 Delete related category mappings
                ProductCategorySection::where('product_id', $product->id)->delete();

                // 🧾 Finally delete product
                $product->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Selected products deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error deleting products: ' . $e->getMessage(),
            ]);
        }
    }

    public function bulkFeature(Request $request)
    {
        $products = Products::whereIn('id', $request->ids)->get();
        foreach ($products as $product) {
            $product->featured = !$product->featured;
            $product->save();
        }
        return response()->json(['success' => true, 'message' => 'Feature status toggled successfully.']);
    }

    public function bulkApprove(Request $request)
    {
        $products = Products::whereIn('id', $request->ids)->get();
        foreach ($products as $product) {
            $product->is_approved = !$product->is_approved;
            $product->save();
        }
        return response()->json(['success' => true, 'message' => 'Approval status toggled successfully.']);
    }

    public function edit($id)
    {
        $product = Products::with(['images', 'categorySections'])->findOrFail($id);
        $sellers = Sellers::select('id', 'name')->get();
        $categoryTree = MasterCategorySection::with(['masterCategory', 'sectionType', 'category'])->get();

        // Collect already linked category IDs
        $selectedCategoryIds = $product->categorySections->pluck('master_category_section_id')->toArray();

        return view('admin.products.edit', compact('product', 'sellers', 'categoryTree', 'selectedCategoryIds'));
    }


    public function update(Request $request, $id)
    {
        $product = Products::with(['images', 'categorySections'])->findOrFail($id);

        // Convert removed_images string ("1,2,3") into array before validation
        $removedImages = [];
        if ($request->filled('removed_images')) {
            $removedImages = array_filter(explode(',', $request->input('removed_images')));
        }

        $validated = $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'master_category_section_id' => 'required|array|min:1',
            'master_category_section_id.*' => 'exists:master_category_sections,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png|max:500',
        ]);

        DB::beginTransaction();

        try {
            // 📝 Update basic product fields
            $product->update([
                'seller_id'   => $validated['seller_id'],
                'name'        => $validated['name'],
                'slug'        => Str::slug($validated['name']) . '-' . uniqid(),
                'description' => $validated['description'] ?? null,
                'price'       => $validated['price'],
                'stock'       => $validated['stock'],
            ]);

            // 🗑️ Delete removed images if any
            if (!empty($removedImages)) {
                $imagesToDelete = ProductImage::where('product_id', $product->id)
                    ->whereIn('id', $removedImages)
                    ->get();

                foreach ($imagesToDelete as $img) {
                    if (Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                    $img->delete();
                }
            }

            // 📤 Upload newly added images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => 0,
                    ]);
                }
            }

            // 🧩 Rebuild category-section relations
            $product->categorySections()->delete();

            foreach ($validated['master_category_section_id'] as $catId) {
                ProductCategorySection::create([
                    'product_id' => $product->id,
                    'master_category_section_id' => $catId,
                ]);
            }

            // 🧮 Final validation: must have at least 3 images
            $finalCount = $product->images()->count();
            if ($finalCount < 3) {
                throw new \Exception('At least 3 images are required for a product.');
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function quickView($id)
    {
        $product = Products::with(['images', 'seller', 'categorySections.masterCategory', 'categorySections.sectionType', 'categorySections.category'])
            ->findOrFail($id);

        return response()->json([
            'html' => view('admin.partials.quick-view-card', compact('product'))->render()
        ]);
    }
}

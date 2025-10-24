<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brands::select('id', 'name', 'description', 'logo', 'is_active', 'created_at', 'updated_at');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $brands->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === '1') {
                $brands->where('is_active', 1);
            } elseif ($request->status === '0') {
                $brands->where('is_active', 0);
            }
        }

        $brands = $brands->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.brands.index', compact('brands'));
    }


    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        // Prepare data
        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->is_active,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $data['logo'] = $path;
        }

        // Store the new brand
        Brands::create($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit(Brands $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brands $brand)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        // If validation fails, redirect back with errors and old input
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare clean data
        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->is_active,
        ];

        // Handle logo update
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }

            $path = $request->file('logo')->store('brands', 'public');
            $data['logo'] = $path;
        }

        // Update the brand
        $brand->update($data);

        // Redirect back with success message
        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brands $brand)
    {
        if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}

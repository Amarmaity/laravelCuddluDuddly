<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class ReviewController extends Controller
{
    // List all reviews with filters
    public function index(Request $request)
    {
        $query = Review::with(['product', 'customer'])->whereHas('customer');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%");
                })
                    ->orWhereHas('product', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })
                    ->orWhere('comment', 'like', "%$search%");
            });
        }


        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sorting
        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            default:
                $query->latest(); // latest first
        }

        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.customers.reviews', compact('reviews'));
    }





    // Show single review
    public function show(Review $review)
    {
        $review->load(['products', 'customers']);
        return view('admin.reviews.show', compact('review'));
    }

    // Show create form
    public function create()
    {
        $products = Product::all();
        $customers = User::all();
        return view('admin.reviews.create', compact('products', 'customers'));
    }

    // Store new review
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        Review::create($request->all());

        return redirect()->route('admin.reviews.index')->with('success', 'Review added successfully.');
    }

    // Show edit form
    public function edit(Review $review)
    {
        $products = Product::all();
        $customers = User::all();
        return view('admin.reviews.edit', compact('review', 'products', 'customers'));
    }

    // Update review
    public function update(Request $request, Review $review)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($request->all());

        return redirect()->route('admin.reviews.index')->with('success', 'Review updated successfully.');
    }

    // Delete review
    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
    }

    // Bulk delete reviews
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reviews,id',
        ]);

        Review::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected reviews deleted successfully.',
        ]);
    }
}

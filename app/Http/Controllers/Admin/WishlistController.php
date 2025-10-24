<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $query = Wishlist::with(['product.primaryImage', 'product.images', 'user'])->latest();

        if ($search = $request->input('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $wishlist = $query->get();

        return view('admin.customers.wishlist', compact('wishlist'));
    }

    public function destroy($id)
    {
        Wishlist::destroy($id);
        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        Wishlist::whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true]);
    }
}

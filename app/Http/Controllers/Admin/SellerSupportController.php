<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerSupport;
use Illuminate\Http\Request;

class SellerSupportController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = SellerSupport::with(['seller', 'admin', 'product.primaryImage', 'product.images'])->latest();

    //     @dd($query);
    //     if ($search = $request->input('search')) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('subject', 'like', "%{$search}%")
    //                 ->orWhereHas('seller', function ($q2) use ($search) {
    //                     $q2->where('name', 'like', "%{$search}%")
    //                         ->orWhere('email', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('product', function ($q3) use ($search) {
    //                     $q3->where('name', 'like', "%{$search}%");
    //                 });
    //         });
    //     }
    //     if ($status = $request->input('status')) {
    //         $query->where('status', $status);
    //     }

    //     $supports = $query->paginate(15);

    //     return view('admin.support.index', compact('supports'));
    // }



    public function index(Request $request)
{
    $query = SellerSupport::with(['seller', 'admin', 'product.primaryImage', 'product.images'])
                ->latest();

    if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('subject', 'like', "%{$search}%")
              ->orWhereHas('seller', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
              })
              ->orWhereHas('product', function ($q3) use ($search) {
                  $q3->where('name', 'like', "%{$search}%");
              });
        });
    }

    if ($status = $request->input('status')) {
        $query->where('status', $status);
    }

    $supports = $query->paginate(15);

    // Debug the query builder (optional)
    // dd($query->toSql(), $query->getBindings());

    return view('admin.support.index', compact('supports'));
}


    public function show($id)
    {
        $support = SellerSupport::with(['seller', 'admin'])->findOrFail($id);
        return view('admin.support.show', compact('support'));
    }

    public function updateStatus(Request $request, $id)
    {
        $support = SellerSupport::findOrFail($id);
        $support->status = $request->input('status');
        $support->admin_id = auth()->id();
        $support->save();

        return redirect()->back()->with('success', 'Support ticket updated successfully.');
    }

    public function destroy($id)
    {
        SellerSupport::destroy($id);
        return response()->json(['success' => true]);
    }
}

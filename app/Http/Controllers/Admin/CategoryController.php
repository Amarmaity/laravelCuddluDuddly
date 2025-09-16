<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\MasterCategory;
use App\Models\MasterCategorySection;
use App\Models\SectionType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        $masterCategories = MasterCategory::with([
            'categories',
            'sectionTypes',
            'categories.sectionTypes',
        ])->paginate(perPage: 6);

        return view('admin.categories.index', compact('masterCategories'));
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name'      => 'required|string|max:255',
    //         'type'      => 'required|in:master,section,category',
    //         'parent_id' => 'nullable|integer',
    //     ]);

    //     $slug = Str::slug($validated['name']);

    //     if ($validated['type'] === 'master') {
    //         MasterCategory::create([
    //             'name'      => $validated['name'],
    //             'slug'      => $slug,
    //             'image_url' => null,
    //             'status'    => 1,
    //         ]);
    //     }

    //     if ($validated['type'] === 'section') {
    //         $section = SectionType::create([
    //             'name' => $validated['name'],
    //             'slug' => $slug,
    //         ]);

    //         MasterCategorySection::create([
    //             'master_category_id' => $validated['parent_id'],
    //             'section_type_id'    => $section->id,
    //         ]);
    //     }

    //     if ($validated['type'] === 'category') {
    //         $category = Category::create([
    //             'name'        => $validated['name'],
    //             'slug'        => $slug,
    //             'description' => null,
    //             'image_url'   => null,
    //         ]);

    //         MasterCategorySection::create([
    //             'section_type_id' => $validated['parent_id'],
    //             'category_id'     => $category->id,
    //         ]);
    //     }

    //     return redirect()->route('admin.categories.index')
    //         ->with('success', ucfirst($validated['type']) . ' created successfully.');
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'type'      => 'required|in:master,section,category',
            'parent_id' => 'nullable|integer',
        ]);

        $slug = Str::slug($validated['name']);
        $created = null;

        if ($validated['type'] === 'master') {
            $created = MasterCategory::create([
                'name'      => $validated['name'],
                'slug'      => $slug,
                'image_url' => null,
                'status'    => 1,
            ]);
        }

        if ($validated['type'] === 'section') {
            $section = SectionType::create([
                'name' => $validated['name'],
                'slug' => $slug,
            ]);
            MasterCategorySection::create([
                'master_category_id' => $validated['parent_id'],
                'section_type_id'    => $section->id,
            ]);
            $created = $section;
        }

        if ($validated['type'] === 'category') {
            $category = Category::create([
                'name'        => $validated['name'],
                'slug'        => $slug,
                'description' => null,
                'image_url'   => null,
            ]);
            MasterCategorySection::create([
                'section_type_id' => $validated['parent_id'],
                'category_id'     => $category->id,
            ]);
            $created = $category;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($validated['type']) . ' created successfully.',
                'type'    => $validated['type'],
                'data'    => $created,
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', ucfirst($validated['type']) . ' created successfully.');
    }

    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'type' => 'required|in:master,section,category',
    //     ]);

    //     $slug = Str::slug($validated['name']);

    //     if ($validated['type'] === 'master') {
    //         MasterCategory::where('id', $id)->update([
    //             'name' => $validated['name'],
    //             'slug' => $slug,
    //         ]);
    //     }

    //     if ($validated['type'] === 'section') {
    //         SectionType::where('id', $id)->update([
    //             'name' => $validated['name'],
    //             'slug' => $slug,
    //         ]);
    //     }

    //     if ($validated['type'] === 'category') {
    //         Category::where('id', $id)->update([
    //             'name' => $validated['name'],
    //             'slug' => $slug,
    //         ]);
    //     }

    //     return redirect()->route('admin.categories.index')
    //         ->with('success', ucfirst($validated['type']) . ' updated successfully.');
    // }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:master,section,category',
        ]);

        $slug = Str::slug($validated['name']);
        $updated = null;

        if ($validated['type'] === 'master') {
            $updated = MasterCategory::where('id', $id)->update([
                'name' => $validated['name'],
                'slug' => $slug,
            ]);
        }

        if ($validated['type'] === 'section') {
            $updated = SectionType::where('id', $id)->update([
                'name' => $validated['name'],
                'slug' => $slug,
            ]);
        }

        if ($validated['type'] === 'category') {
            $updated = Category::where('id', $id)->update([
                'name' => $validated['name'],
                'slug' => $slug,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($validated['type']) . ' updated successfully.',
                'type'    => $validated['type'],
                'id'      => $id,
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', ucfirst($validated['type']) . ' updated successfully.');
    }

    // public function bulkAction(Request $request)
    // {
    //     $action = $request->input('action');
    //     $selected = $request->input('selected', []);
    //     if ($action !== 'delete') {
    //         return back()->with('error', 'Invalid action.');
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // --- Master categories ---
    //         if (!empty($selected['master'])) {
    //             $masters = MasterCategory::with(['categories', 'sectionTypes'])
    //                 ->whereIn('id', $selected['master'])
    //                 ->get();

    //             foreach ($masters as $master) {
    //                 // delete master image
    //                 if ($master->image_url && Storage::disk('public')->exists($master->image_url)) {
    //                     Storage::disk('public')->delete($master->image_url);
    //                 }

    //                 // detach and cleanup pivot rows
    //                 MasterCategorySection::where('master_category_id', $master->id)->delete();

    //                 // delete related categories (if they are not linked to other masters)
    //                 foreach ($master->categories as $cat) {
    //                     if ($cat->masterCategories()->count() === 1) {
    //                         if ($cat->image_url && Storage::disk('public')->exists($cat->image_url)) {
    //                             Storage::disk('public')->delete($cat->image_url);
    //                         }
    //                         $cat->delete();
    //                     }
    //                 }

    //                 // delete related sections (if they are not linked to other masters)
    //                 foreach ($master->sectionTypes as $sec) {
    //                     if ($sec->masterCategories()->count() === 1) {
    //                         $sec->delete();
    //                     }
    //                 }

    //                 // finally delete master
    //                 $master->delete();
    //             }
    //         }

    //         // --- Standalone sections (needs master_category_id context) ---
    //         if (!empty($selected['section'])) {
    //             foreach ($selected['section'] as $combo) {
    //                 [$sectionId, $masterId] = explode(':', $combo);

    //                 MasterCategorySection::where('master_category_id', $masterId)
    //                     ->where('section_type_id', $sectionId)
    //                     ->delete();

    //                 $section = SectionType::find($sectionId);
    //                 if ($section && $section->masterCategories()->count() === 0) {
    //                     $section->delete();
    //                 }
    //             }
    //         }

    //         // --- Standalone categories (needs master_category_id context) ---
    //         if (!empty($selected['category'])) {
    //             foreach ($selected['category'] as $combo) {
    //                 [$catId, $masterId] = explode(':', $combo);

    //                 MasterCategorySection::where('master_category_id', $masterId)
    //                     ->where('category_id', $catId)
    //                     ->delete();

    //                 $cat = Category::find($catId);
    //                 if ($cat && $cat->masterCategories()->count() === 0) {
    //                     if ($cat->image_url && Storage::disk('public')->exists($cat->image_url)) {
    //                         Storage::disk('public')->delete($cat->image_url);
    //                     }
    //                     $cat->delete();
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         return back()->with('success', 'Selected items deleted successfully.');
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error('Bulk delete failed: ' . $e->getMessage());
    //         return back()->with('error', 'Bulk delete failed. Please check logs.');
    //     }
    // }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $selected = $request->input('selected', []);

        if ($action !== 'delete') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid action.'], 422);
            }
            return back()->with('error', 'Invalid action.');
        }

        DB::beginTransaction();
        try {
            // --- Master categories ---
            if (!empty($selected['master'])) {
                $masters = MasterCategory::with(['categories', 'sectionTypes'])
                    ->whereIn('id', $selected['master'])
                    ->get();

                foreach ($masters as $master) {
                    if ($master->image_url && Storage::disk('public')->exists($master->image_url)) {
                        Storage::disk('public')->delete($master->image_url);
                    }

                    MasterCategorySection::where('master_category_id', $master->id)->delete();

                    foreach ($master->categories as $cat) {
                        if ($cat->masterCategories()->count() === 1) {
                            if ($cat->image_url && Storage::disk('public')->exists($cat->image_url)) {
                                Storage::disk('public')->delete($cat->image_url);
                            }
                            $cat->delete();
                        }
                    }

                    foreach ($master->sectionTypes as $sec) {
                        if ($sec->masterCategories()->count() === 1) {
                            $sec->delete();
                        }
                    }

                    $master->delete();
                }
            }

            // --- Standalone sections ---
            if (!empty($selected['section'])) {
                foreach ($selected['section'] as $combo) {
                    [$sectionId, $masterId] = explode(':', $combo);

                    MasterCategorySection::where('master_category_id', $masterId)
                        ->where('section_type_id', $sectionId)
                        ->delete();

                    $section = SectionType::find($sectionId);
                    if ($section && $section->masterCategories()->count() === 0) {
                        $section->delete();
                    }
                }
            }

            // --- Standalone categories ---
            if (!empty($selected['category'])) {
                foreach ($selected['category'] as $combo) {
                    [$catId, $masterId] = explode(':', $combo);

                    MasterCategorySection::where('master_category_id', $masterId)
                        ->where('category_id', $catId)
                        ->delete();

                    $cat = Category::find($catId);
                    if ($cat && $cat->masterCategories()->count() === 0) {
                        if ($cat->image_url && Storage::disk('public')->exists($cat->image_url)) {
                            Storage::disk('public')->delete($cat->image_url);
                        }
                        $cat->delete();
                    }
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Selected items deleted successfully.'
                ]);
            }

            return back()->with('success', 'Selected items deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Bulk delete failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bulk delete failed. Please check logs.'
                ], 500);
            }

            return back()->with('error', 'Bulk delete failed. Please check logs.');
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'id'   => 'required|integer',
                'type' => 'required|in:master,category',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // model + folder
            if ($request->type === 'master') {
                $item = MasterCategory::findOrFail($request->id);
                $folder = 'images/master_category_banners';
            } else {
                $item = Category::findOrFail($request->id);
                $folder = 'images/category_banners';
            }

            // file name = slug.ext
            $slug = Str::slug($item->name);
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = $slug . '.' . $extension;

            // relative path for DB
            $dbPath = $folder . '/' . $filename;

            // ✅ delete old image if exists
            if ($item->image_url && Storage::disk('public')->exists($item->image_url)) {
                Storage::disk('public')->delete($item->image_url);
            }

            // ✅ store new image
            $request->file('image')->storeAs($folder, $filename, 'public');

            // update DB
            $item->image_url = $dbPath;
            $item->save();

            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $dbPath),
                'message' => 'Image uploaded successfully!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
                'type' => 'danger'
            ], 500);
        }
    }
}

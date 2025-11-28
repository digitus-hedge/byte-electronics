<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        if (!is_null($request->keyword)) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }
        $categories = $query->latest()->paginate(10)->appends(request()->query());

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
{
            $validated = $request->validate([
                'name' => [
                    'required',
                    Rule::unique('categories', 'name')->whereNull('deleted_at'),
                ],
                'slug' => [
                    'required',
                    Rule::unique('categories', 'slug'),
                ],
                'description' => ['nullable'],
                'image' => [
                    'nullable',
                    'dimensions:min_width=109,min_height=79,max_width=500,max_height=500',
                ],
                'meta_description' => ['nullable'],
                'meta_tag' => ['nullable'],

                
            ]);

            $path = public_path('uploads/category');

            // Set default status
            $validated['status'] = $request->has('status') ? 1 : 0;

            // Create category
            $category = Category::create($validated);

            // Handle image upload if exists
            if ($request->hasFile('image')) {
                $category->storeMedia($request, $path);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category has been created!');
        }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

   
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                Rule::unique('categories', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($category->id),
            ],
            'slug' => [
                'required',
                Rule::unique('categories', 'slug')->ignore($category->id),
            ],
            'description' => ['nullable'],
            'image' => [
                'nullable',
                'dimensions:min_width=109,min_height=79,max_width=500,max_height=500',
            ],
            'meta_description' => ['nullable'],
            'meta_tag' => ['nullable'],
        ]);
    
        $path = public_path('uploads/category');
        $validated['status'] = $request->has('status') ? 1 : 0;
    
        // If new image is uploaded, delete old image
        if ($request->hasFile('image')) {
            if ($category->file_name) {
                $oldImage = $path . '/' . $category->file_name;
                if (File::exists($oldImage)) {
                    File::delete($oldImage);
                }
            }
            $category->storeMedia($request, $path);
        }
    
        $category->update($validated);
    
        return response()->json([
            'success' => true,
            'redirect_url' => route('admin.categories.index'),
        ]);
    }
    

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category has been deleted!');
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();
        return redirect()->route('admin.categories.index')->with('success', 'Category has been permanently deleted!');
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        return redirect()->route('admin.categories.index')->with('success', 'Category has been restored!');
    }

    public function addFeatured(Request $request)
    {
        $request->validate(['id' => 'required|exists:categories,id']);
        $category = Category::findOrFail($request->id);
        $category->update(['featured' => 1]);

        return response()->json(['success' => true, 'message' => 'Category marked as featured']);
    }

    public function removeFeatured(Request $request)
    {
        $request->validate(['id' => 'required|exists:categories,id']);
        $category = Category::findOrFail($request->id);
        $category->update(['featured' => 0]);

        return response()->json(['success' => true, 'message' => 'Category removed from featured']);
    }
}

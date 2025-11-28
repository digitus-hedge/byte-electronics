<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;
use App\Models\Brands;
use Illuminate\Support\Facades\File;
use Cviebrock\EloquentSluggable\Sluggable;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\Catch_;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $query = Brands::query();

            if ($request->ajax() && $request->has('keyword')) {
                $query->where('name', 'like', '%' . $request->keyword . '%');
                $brands = $query->paginate(10);

                return view('brands.partials.table', compact('brands'))->render(); // return only table HTML
            }

            $brands = $query->latest()->paginate(10);
            return view('brands.index', compact('brands'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Brands $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug',
            'meta_tag' => 'required|string',
            'meta_description' => 'required|string',
            'image' => 'required|image|dimensions:min_width=109,min_height=79,max_width=111,max_height=81',
            'secondary_logo' => 'nullable|image|dimensions:min_width=109,min_height=79,max_width=150,max_height=110',
            'banner' => 'nullable|image|dimensions:min_width=1349,min_height=270,max_width=1350,max_height=271',
            'description' => 'nullable|string',
        ]);
        $path = public_path('uploads/brand');
        $pathBaanner = public_path('uploads/brand/banner');
        $pathSecondaryLogo = public_path('uploads/brand/secondary_logo');

        $validated['status'] = $request->has('status') ? 1 : 0;
        // $input = $request->validated();
        // $input['status'] = $status;
        $brand = $brand->create($validated);

        if ($request->has('image')) {
            $brand->storeMedia($request, $path, 'image');
        }
        if ($request->has('banner')) {
            $brand->storeMedia($request, $pathBaanner, 'banner');
        }
        if ($request->has('secondary_logo')) {
            $brand->storeMedia($request, $pathSecondaryLogo, 'secondary_logo');
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Brand has been created!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Brands $brand)
    {
        return view('brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Brands $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brands $brand)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'slug' => 'required|string|max:255|unique:brands,slug,' . $brand->id,
            'meta_tag' => 'required|string',
            'meta_description' => 'required|string',
            'image' => 'nullable|image|dimensions:min_width=109,min_height=79,max_width=111,max_height=81',
            'secondary_logo' => 'nullable|image|dimensions:min_width=109,min_height=79,max_width=150,max_height=110',
            'banner' => 'nullable|image|dimensions:min_width=1349,min_height=270,max_width=1350,max_height=271',
            'description' => 'nullable|string',
        ]);
        
   
        $input = $validated;
        $input['status'] = $request->has('status') ? 1 : 0;
        $path = public_path('uploads/brand');
        $pathBanner = public_path('uploads/brand/banner');
        $pathSecondaryLogo = public_path('uploads/brand/secondary_logo');

        if ($request->hasFile('image')) {
            
            $image_path = $path . '/' . $request->image;
            if (File::exists($image_path)) {
                File::delete($image_path);
            }
            try {
              
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($path, $fileName);
                $input['image'] = $fileName;
            } catch (\Exception $e) {
                Log::error('Failed to store brand image: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to store brand image'
                ], 500);
            }
        }

        if ($request->hasFile('banner')) {
            $banner_path = $pathBanner . '/' . $brand->banner;
            if (File::exists($banner_path)) {
                File::delete($banner_path);
            }
            try {
                $file = $request->file('banner');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($pathBanner, $fileName);
                $input['banner'] = $fileName;
            } catch (\Exception $e) {
                Log::error('Failed to store banner image: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to store banner image'
                ], 500);
            }
        }

        if ($request->hasFile('secondary_logo')) {
            $oldSecondaryLogoPath = $pathSecondaryLogo . '/' . $brand->secondary_logo;
            if (File::exists($oldSecondaryLogoPath)) {
                File::delete($oldSecondaryLogoPath);
            }
            try {
                $file = $request->file('secondary_logo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($pathSecondaryLogo, $fileName);
                $input['secondary_logo'] = $fileName;
            } catch (\Exception $e) {
                Log::error('Failed to store secondary logo: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to store secondary logo'
                ], 500);
            }
        }
        //   dd($input);
        try {
            if (!$brand->update($input)) {
                throw new \Exception('Failed to update brand: ' . ($brand->getConnection()->getPdo()->errorInfo()[2] ?? 'Unknown error'));
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Brand has been updated!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Brand update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update brand: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addFeatured(Request $request)
    {
        // dd($request);
        try {
            $request->validate(['id' => 'required|exists:brands,id']);
            $brand = Brands::findOrFail($request->id);
            $brand->update(['featured' => 1]);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }
    }

    public function removeFeatured(Request $request)
    {
        $request->validate(['id' => 'required|exists:brands,id']);
        $brand = Brands::findOrFail($request->id);
        $brand->update(['featured' => 0]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brands $brand)
    {
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Brand has been deleted!');
    }

    /*
    * Seach brand
    */
    public function search(Request $request)
    {
       return $request;
    }
}

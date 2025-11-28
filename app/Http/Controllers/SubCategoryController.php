<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SubCategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Null_;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = SubCategory::query();
        if(!is_null($request->keyword)){
            $query->where('name','like','%'.$request->keyword.'%');
        }
        if(!is_null($request->category_id)){
            $query->where('category_id',$request->category_id);
        }
        $subcategories = $query->latest()->paginate(10)->appends(request()->query());
        Session::put('current_page',request()->fullUrl());
        //echo Session::get('current_page');

        return view('subcategories.index',compact('subcategories','categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all(); // Fetch all categories
        $subcategories = Subcategory::all(); // Fetch all subcategories

        return view('subcategories.create', compact('categories', 'subcategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
 
    public function store(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('sub_categories', 'name')
                    ->where('category_id', $request->category_id)
                    ->whereNull('deleted_at'),
            ],
            'slug' => [
                'required',
                Rule::unique('sub_categories', 'slug')
                    ->where('category_id', $request->category_id),
            ],
            'category_id' => ['required'],
            'parent_id' => ['nullable'],
            'description' => ['nullable'],
            'image' => ['nullable', 'image', 'dimensions:min_width=109,min_height=79,max_width=111,max_height=81'],
            'default_image' => ['nullable', 'image', 'dimensions:min_width=799,min_height=799,max_width=801,max_height=801'],
            'meta_tag' => ['nullable'],
            'meta_description' => ['nullable'],
            'status' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['status'] = $request->has('status') ? 1 : 0;

        $path = public_path('uploads/subcategory');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Handle image uploads
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $file->move($path, $fileName);
            $data['image_sub_cat'] = 'uploads/subcategory/' . $fileName;
        }

        if ($request->hasFile('default_image')) {
            $file = $request->file('default_image');
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $file->move($path, $fileName);
            $data['product_default_sub_cat'] = 'uploads/subcategory/' . $fileName;
        }

        SubCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Sub Category has been created!',
            'redirect_url' => route('admin.subcategories.index'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(SubCategory $subcategory)
    {
        return view('subcategories.show',compact('subcategory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SubCategory $subcategory )
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        //$page = request()->url();
        //print_r($page); die();
        // dd($subcategory);
        return view('subcategories.edit',compact('categories','subcategory','subcategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//     public function update(SubCategoryRequest $request, SubCategory $subcategory)
//     {
         
          
// try {
//     // Process the request and update the subcategory
//     $path = public_path('uploads/subcategory');
//             $status = !$request->status ? 0 : 1;
//             $input = $request->validated();
//             $input['status'] = $status;
//     //            $page = $request->url();
//     //            print_r(\session('current_page')); die();
//     $input = $request->except('image', 'default_image');
//             if ($request->hasFile('image')) {
//                 $image_path = public_path($subcategory->image_sub_cat);
//                 if (File::exists($image_path)) {
//                     File::delete($image_path);
//                 }
//                 $file = $request->file('image');
//                 $fileName = uniqid() . '_' . $file->getClientOriginalName();
//                 $file->move($path, $fileName);
//                 $input['image_sub_cat'] = 'uploads/subcategory/' . $fileName;
                
//             }

//             if ($request->hasFile('default_image')) {
//                 $image_path1 = public_path($subcategory->product_default_sub_cat);
//                 if (File::exists($image_path1)) {
//                     File::delete($image_path1);
//                 }
//                 $file = $request->file('default_image');
//                 $fileName = uniqid() . '_' . $file->getClientOriginalName();
//                 $file->move($path, $fileName);
//                 $input['product_default_sub_cat'] = 'uploads/subcategory/' . $fileName;
               
//             }
//             // dd($input);
//             $input = array_merge($input, $request->except(['image', 'default_image', '_token']));
//             // dd($input , $subcategory);
//             $subcategory->update($input);
//                 return response()->json([
//                     'success' => true,
//                     'redirect_url' =>  route('admin.subcategories.index'),
//                     'message' => 'Sub Category has been updated successfully!'
//                 ]);
//             } catch (\Exception $e) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'An error occurred: ' . $e->getMessage()
//                 ], 500);
//             }
//     }
public function update(Request $request, SubCategory $subcategory)
{
    $validator = Validator::make($request->all(), [
        'name' => [
            'required',
            Rule::unique('sub_categories', 'name')
                ->where('category_id', $request->category_id)
                ->whereNull('deleted_at')
                ->ignore($subcategory->id),
        ],
        'slug' => [
            'required',
            Rule::unique('sub_categories', 'slug')
                ->where('category_id', $request->category_id)
                ->ignore($subcategory->id),
        ],
        'category_id' => ['required'],
        'parent_id' => ['nullable'],
        'description' => ['nullable'],
        'image' => ['nullable', 'image', 'dimensions:min_width=109,min_height=79,max_width=111,max_height=81'],
        'default_image' => ['nullable', 'image', 'dimensions:min_width=799,min_height=799,max_width=801,max_height=801'],
        'meta_tag' => ['nullable'],
        'meta_description' => ['nullable'],
        'status' => ['required'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $data = $validator->validated();
    $data['status'] = $request->has('status') ? 1 : 0;

    $path = public_path('uploads/subcategory');
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    // Handle image uploads
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $file->move($path, $fileName);
        $data['image_sub_cat'] = 'uploads/subcategory/' . $fileName;
    }

    if ($request->hasFile('default_image')) {
        $file = $request->file('default_image');
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $file->move($path, $fileName);
        $data['product_default_sub_cat'] = 'uploads/subcategory/' . $fileName;
    }

    $subcategory->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Sub Category has been updated!',
        'redirect_url' => route('admin.subcategories.index'),
    ]);
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubCategory $subcategory)
    {  
        // Check if there are any child subcategories
        $hasChildren = SubCategory::where('parent_id', $subcategory->id)->exists();
        
        if ($hasChildren) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete subcategory that has child subcategories!'
            ], 422);
        }
        
        $subcategory->delete();
        // return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory has been deleted!');
    return response()->json([
        'success' => true,
        'message' => 'Subcategory has been deleted!'
    ]);
    }

    public function getSubcategoriesByCategory(Request $request){
        // dd($request);
        if($request->selectedCategoryId)
        {
            try {
                        $subcategories = SubCategory::where('category_id', $request->selectedCategoryId)
                            ->where('id', '!=', $request->currentSubcategoryId) // Exclude the current subcategory
                            ->get(['id', 'name']);

                        return response($subcategories);
                    } catch (\Exception $e) {
                        // Log the error and return a generic error message
                       // Log::error('Error fetching subcategories: ' . $e->getMessage());
                        return response()->json(['error' => 'Unable to fetch subcategories'], 500);
                    }
        }
        else{
        $category_id = $request->category_id;
        $subcategories = SubCategory::where('category_id',$category_id)->active()->get();
        return response()->json($request->category_id);
        // currentSubcategoryId
        }


    }

    public function getSubcategories($categoryId)
{
    $subcategories = Subcategory::where('category_id', $categoryId)->get();

    return response()->json($subcategories);
}
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Brands;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductAttrDocument;
use App\Models\ProductAttribute;
use App\Models\ProductExtended;
use App\Models\ProductHeader;
use App\Models\ProductPrice;
use App\Models\Products;
use App\Models\SubCategory;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;
use App\Models\RequestQuote;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use ParagonIE\Sodium\Compat;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Imports\ProductPriceImport;
use App\Imports\ProductSpecificationImport;


class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $requests)
    {

        $query = Products::query()->orderBy('id', 'desc');
        // $categories = Category::all();
        $categories = Category::with('subcategories.children')->get();
        $subcategories = SubCategory::all();
        $brands = Brands::all();
        // dd($categories);
        if (!is_null($requests->keyword)) {
            $query->where('name', 'like', '%' . $requests->keyword . '%');
        }

        $products = $query->latest()->paginate(10)->appends(request()->query());
        // dd($products);
        return view('productManage.index', compact('products', 'categories', 'brands')); // 'subcategories',

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


        try {
            $categories = Category::all();
            $subcategories = SubCategory::all();
            $brands = Brands::all();
            $attributeHeadings = ProductHeader::all();
            $attribute = ProductAttribute::all();
            // $attributeExtended = ProductExtended::all();
            // $attributeDocument = ProductAttrDocument::all();
            $currencies = Currency::all();

            return view('productManage.create', compact(
                'categories',
                'subcategories',
                'brands',
                'attributeHeadings',
                'attribute',
                // 'attributeExtended',
                // 'attributeDocument',
                'currencies'

            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        // Validate the request


        // Unique rules for name and slug
        $rule_name_unique = Rule::unique('products', 'name')->where(function ($query) {
            $query->whereNull('deleted_at');
        });

        $rule_slug_unique = Rule::unique('products', 'slug')->where(function ($query) {
            $query->whereNull('deleted_at');
        });

        if (in_array($request->method(), ['PUT', 'PATCH'])) {
            $rule_name_unique->ignore($request->product->id);
            $rule_slug_unique->ignore($request->product->id);
        }

        // Validate request
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', $rule_name_unique],
            'slug' => ['required', 'string', 'max:255', $rule_slug_unique],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sub_category_id' => ['required', 'integer', 'exists:sub_categories,id'],
            //  'price' => ['nullable', 'string', 'max:255'],
            'file_name' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'org_name' => ['nullable', 'string', 'max:255'],
            'manufacturers_no' => ['nullable', 'string', 'max:255'],
            //  'minimum_qty' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'more_description' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string'],
            'is_repairable' => ['required', 'boolean'],
            'featured' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
            'tag' => ['nullable', 'array'],
            'tag.*' => ['string'],
            'attributes' => ['required', 'array'],
            'attributes.*.heading' => ['required', 'string'],
            'attributes.*.attribute' => ['required', 'string'],
            'attributes.*.value_type' => ['required', 'string'],
            'attributes.*.value' => ['nullable', 'string'],
            'attributes.*.file' => ['nullable', 'file', 'max:51200'],
            'pricing' => ['nullable', 'array'], // Add validation for pricing array
            'pricing.*.unit_name' => ['required', 'string', 'max:255'],
            'pricing.*.quantity' => ['required', 'integer', 'min:1'],
            'pricing.*.unit_price' => ['required', 'numeric', 'min:0'],
            'pricing.*.bulk_price' => ['required', 'numeric', 'min:0'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
        ]);

        // Convert tags to a comma-separated string
        if ($request->has('tag')) {
            $validatedData['tag'] = implode(',', $request->input('tag'));
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $slugName = $request->input('slug') . '.' . $extension;
            $partNo = $request->manufacturers_no . '.' . $extension;

            $destinationPath = public_path('uploads/products');
            $file->move($destinationPath, $partNo);

            $validatedData['file_name'] = $partNo;
            $validatedData['org_name'] = $originalName;
        }

        try {
            DB::beginTransaction(); // Start transaction

            // Create product
            $product = Products::create($validatedData);
            $product->save();

            // Save attributes
            $attributes = $request->input('attributes');
            foreach ($attributes as $index => $attribute) {
                $productExtended = ProductExtended::create([
                    'product_id' => $product->id,
                    'product_attr_id' => $attribute['attribute_id'],
                    'value_type' => $attribute['value_type'] ?? null,
                    'prd_header_id' => $attribute['heading_id'] ?? null,
                ]);

                // Handle file uploads
                $fileInputName = "attributes.{$index}.file";
                $value = $attribute['value'] ?? null;
                if ($request->hasFile($fileInputName)) {
                    $file = $request->file($fileInputName);
                    $folder = $attribute['value_type'] === 'image' ? 'products/attributes' : 'products/docs';
                    $destinationPath = public_path($folder);

                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move($destinationPath, $fileName);
                    $filePath = "$folder/$fileName";
                    $value = $filePath;
                }

                // Save to ProductAttrDocument
                ProductAttrDocument::create([
                    'product_ext_id' => $productExtended->id,
                    'name' => $attribute['attribute'],
                    'value' => $value,
                    'attrib_count' => 1,
                ]);
            }

            // Save pricing details
            if ($request->has('pricing')) {

                foreach ($request->input('pricing') as $pricing) {
                    ProductPrice::create([
                        'product_id' => $product->id,
                        'unit_name' => $pricing['unit_name'],
                        'qty' => $pricing['quantity'],
                        'single_price' => $pricing['unit_price'],
                        'total_price' => $pricing['bulk_price'],
                        'currency_name' => trim(strstr($request->currency, '-', true)),
                        'currency_symbol' => trim(strstr($request->currency, '-', false)),
                        'currency_id' => $request->currency_id,
                    ]);
                }
            }

            DB::commit(); // Commit transaction

            return response()->json([
                'message' => 'Product created successfully!',
                'product' => $product,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if any error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(Products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $product)
    {

        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Brands::all();
        $attributeHeadings = ProductHeader::all();
        $attributes = ProductAttribute::all();
        $currencies = Currency::all();
        $pricingDetails = ProductPrice::where('product_id', $product->id)->get();
        $defaultCurrency = Currency::where('is_default', true)->first(); // Get the default currency
        $selectedCurrency = $product->prices()->with('currency')->first()?->currency;

        $conversionRate = $selectedCurrency && $selectedCurrency->id !== $defaultCurrency->id
            ? $selectedCurrency->conversion_rate : 1; // Use 1 if it's already the default currency
        // Convert prices if the selected currency is not the default
        foreach ($pricingDetails as $pricing) {
            $pricing->converted_single_price = $pricing->single_price * $conversionRate;
            $pricing->converted_total_price = ($pricing->converted_single_price) * $pricing->qty;
        }


        // Fetch all product extended attributes for the given product
        $attributeExtended = ProductExtended::where('product_id', $product->id)->get();

        // Fetch associated attribute documents
        $attributeDocuments = ProductAttrDocument::whereIn('product_ext_id', $attributeExtended->pluck('id'))->get();
        $product = Products::find($product->id);
        // Prepare structured data
        $productAttributes = $attributeExtended->map(function ($extended) use ($attributeHeadings, $attributes, $attributeDocuments) {
            return [
                'header' => $attributeHeadings->firstWhere('id', $extended->prd_header_id)?->name ?? 'N/A',
                'headerId' => $extended->prd_header_id,
                'attributeId' => $extended->product_attr_id,
                'attribute_name' => $attributes->firstWhere('id', $extended->product_attr_id)?->name ?? 'N/A',
                'attribute_value' => $attributeDocuments->firstWhere('product_ext_id', $extended->id)?->value ?? 'N/A',
                'value_type' => $extended->value_type,
            ];
        });
        //   dd($selectedCurrency);

        return view('productManage.edit', compact(
            'product',
            'categories',
            'subcategories',
            'brands',
            'attributeHeadings',
            'attributeExtended',
            'attributeDocuments',
            'attributes',
            'productAttributes',
            'currencies',
            'pricingDetails',
            'selectedCurrency',
            'defaultCurrency',
            'conversionRate'
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Products $product)
    {
        // dd('request',$request->all());
        $rule_name_unique = Rule::unique('products', 'name')
            ->whereNull('deleted_at')
            ->ignore($product->id);

        $rule_slug_unique = Rule::unique('products', 'slug')
            ->whereNull('deleted_at')
            ->ignore($product->id);

        // Validate request
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', $rule_name_unique],
            'slug' => ['required', 'string', 'max:255',  $rule_slug_unique],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sub_category_id' => ['required', 'integer', 'exists:sub_categories,id'],
            'file_name' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'org_name' => ['nullable', 'string', 'max:255'],
            'manufacturers_no' => ['nullable', 'string', 'max:255'],
            // 'minimum_qty' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'more_description' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string'],
            'is_repairable' => ['required', 'boolean'],
            'featured' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
            'tag' => ['nullable', 'array'],
            'tag.*' => ['string'],
            'attributes' => ['required', 'array'],
            'attributes.*.heading' => ['required', 'string'],
            'attributes.*.attribute' => ['required', 'string'],
            'attributes.*.value_type' => ['required', 'string'],
            'attributes.*.value' => ['nullable', 'string'],
            'attributes.*.file' => ['nullable', 'file', 'max:51200'],
            'pricing' => ['nullable', 'array'], // Add validation for pricing array
            'pricing.*.unit_name' => ['required', 'string', 'max:255'],
            'pricing.*.quantity' => ['required', 'integer', 'min:1'],
            'pricing.*.unit_price' => ['required', 'numeric', 'min:0'],
            'pricing.*.bulk_price' => ['required', 'numeric', 'min:0'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
        ]);

        if ($request->has('tag')) {
            $validatedData['tag'] = implode(',', $request->input('tag'));
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $slugName = $request->input('slug') . '.' . $extension;
            $partNo = $request->manufacturers_no . '.' . $extension;
            $destinationPath = public_path('uploads/products');
            $file->move($destinationPath, $partNo);

            $validatedData['file_name'] = $partNo;
            $validatedData['org_name'] = $originalName;
        }

        try {
            DB::beginTransaction();

            $product->update($validatedData);

            $attributes = $request->input('attributes');

            foreach ($attributes as $index => $attribute) {
                $productExtended = ProductExtended::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_attr_id' => $attribute['attribute_id'],
                    ],
                    [
                        'value_type' => $attribute['value_type'] ?? null,
                        'prd_header_id' => $attribute['heading_id'] ?? null,
                    ]
                );

                $fileInputName = "attributes.{$index}.file";
                $value = $attribute['value'] ?? null;

                if ($request->hasFile($fileInputName)) {
                    $file = $request->file($fileInputName);
                    $folder = $attribute['value_type'] === 'image' ? 'uploads/products/attributes' : 'uploads/products/docs';
                    $destinationPath = public_path($folder);

                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move($destinationPath, $fileName);

                    $value = "$folder/$fileName";
                }

                ProductAttrDocument::updateOrCreate(
                    [
                        'product_ext_id' => $productExtended->id,
                    ],
                    [
                        'name' => $attribute['attribute'],
                        'value' => $value,
                        'attrib_count' => 1,
                    ]
                );
            }

            // Update pricing details
            if ($request->has('pricing')) {
                foreach ($request->input('pricing') as $pricing) {
                    ProductPrice::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'unit_name' => $pricing['unit_name'],
                            'qty' => $pricing['quantity'],
                        ],
                        [
                            'single_price' => $pricing['unit_price'],
                            'total_price' => $pricing['bulk_price'],
                            'currency_name' => trim(strstr($request->currency, '-', true)),
                            'currency_symbol' => trim(strstr($request->currency, '-', false)),
                            'currency_id' => $request->currency_id,
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Product updated successfully!',
                'product' => $product,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Products $products)
    {
        // dd($products);
        try {
            if ($products->delete()) {
                return response()->json(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete product']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting product: ' . $e->getMessage()], 500);
        }
    }

    public function getSubCategories($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)->get(['id', 'name']);
        return response()->json($subCategories);
    }

    function getProductsUnderCategory($categoryId)
    {
        // Fetch subcategory IDs under the category
        $subCategoryIds = SubCategory::where('category_id', $categoryId)
            ->with('descendants')
            ->get()
            ->flatMap(function ($subCategory) {
                return $subCategory->descendants->pluck('id')->push($subCategory->id);
            })
            ->unique()
            ->toArray();

        // Fetch products linked to these subcategories
        return Products::whereIn('sub_category_id', $subCategoryIds)->get();
    }

    // Fetch products under a specific subcategory (including its children)
    function getProductsUnderSubCategory($subCategoryId)
    {
        // Fetch descendant subcategory IDs
        $subCategoryIds = SubCategory::where('id', $subCategoryId)
            ->with('descendants')
            ->get()
            ->flatMap(function ($subCategory) {
                return $subCategory->descendants->pluck('id')->push($subCategory->id);
            })
            ->unique()
            ->toArray();

        // Fetch products linked to these subcategories
        return Products::whereIn('sub_category_id', $subCategoryIds)->get();
    }

    public function addFeatured(Request $request)
    {

        $product = Products::find($request->id);
        // dd($request);
        if ($product) {
            $product->featured = 1;
            $product->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
    public function removeFeatured(Request $request)
    {
        $product = Products::find($request->id);
        if ($product) {
            $product->featured = 0;
            $product->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
    public function addBestSeller(Request $request)
    {

        $product = Products::find($request->id);
        // dd($request);
        if ($product) {
            $product->best_sellers = 1;
            $product->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
    public function removeBestSeller(Request $request)
    {
        $product = Products::find($request->id);
        if ($product) {
            $product->best_sellers = 0;
            $product->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    //function to product bulk import
    public function productBulkImport()
    {

        $size = ini_get('post_max_size');

        return view('productManage.bulkImport.view', compact('size'));
    }
    // public function productBulkImport(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls,csv',
    //     ]);

    //     $file = $request->file('file');
    //     $path = $file->storeAs('imports', 'products.xlsx'); // stores in storage/app/imports/products.xlsx
    // dd($path);
    //     return back()->with('success', 'File uploaded. Run the import with: php artisan import:products');
    // }
    /**
     * Store the excel sheet data in to data base
     * product details
     * 
     */
    public function productBulkImportStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480'
        ]);
        $file = $request->file('file');
        $path = $file->storeAs('imports', 'products_to_live.csv'); // stores in storage/app/imports/products.xlsx

        return back()->with('success', 'File uploaded. Run the import with: php artisan import:products');

        // Excel::import(new ProductsImport, $request->file('file'));


        // return back()->with('success', 'Product data imported successfully.');

    // return back()->with('success', 'Product data imported successfully.');
}

/**
 * Product price bulk import
 * 
 */
public function productPriceImport(Request $request)
{
    $request->validate([
        'price_file' => 'required|mimes:xlsx,xls,csv'
    ]);


    $file = $request->file('price_file');
   $path = $file->storeAs('imports', 'product_prices.csv'); // stores in storage/app/imports/products.xlsx

        return back()->with('success', 'File uploaded. Run the import with: php artisan import:products');
    // Excel::import(new ProductPriceImport, $request->file('price_file'));
    // return back()->with('success', 'Product price data imported successfully.');

}

/**
 * product specification bulk import
 * 
 */
public function productSpecificationImportStore(Request $request)
{
    $request->validate([
        'specification_file' => 'required|mimes:xlsx,xls,csv'
    ]);
    $file = $request->file('specification_file');
    $path = $file->storeAs('imports', 'product_extended.csv'); // stores in storage/app/imports/products.xlsx

        return back()->with('success', 'File uploaded. Run the import with: php artisan import:products');

    // Excel::import(new ProductSpecificationImport, $request->file('specification_file'));
    // return back()->with('success','Product Specification data imported successfully.');
    
}

    /**
     * product more information bulk import
     * 
     */
    public function productMoreInfoImportStore(Request $request){
        $request->validate([
            'more_info_file' => 'required|mimes:xlsx,xls,csv'
        ]);
        $file = $request->file('more_info_file');
        $path = $file->storeAs('imports', 'product_more_Information.csv');
        return back()->with('success', 'File uploaded. Run the import with: php artisan import:product-more-info product_more_Information.csv');
    }

/**
 * Product Search
 * 
 */
public function search(Request $request)
{
    $query = $request->input('search');

    $products = Products::where('name', 'like', "%{$query}%")
        ->orWhere('manufacturers_no	', 'like', "%{$query}%")
        ->take(10) // Limit results
        ->get(['id', 'name','manufacturers_no']); // Select only necessary columns

    // If you also need price, include it in the query (adjust according to your pricing structure)
    foreach ($products as $product) {
        $product->price = optional($product->prices()->first())->total_price ?? null;

    }
}

   

   


    /*
    * List Product RFQ
    * 
    */

    public function requestQuote(Request $request)
    {
        $rfqs = RequestQuote::with(['product', 'user'])
            ->where('status', 1)
            ->when($request->keyword, function ($query, $keyword) {
                $query->whereHas('product', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
                    // ->orWhereHas('user', function ($q) use ($keyword) {
                    //     $q->where('name', 'like', "%{$keyword}%")
                    //         ->orWhere('email', 'like', "%{$keyword}%");
                    // });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // If it's an AJAX request, return partial HTML
        if (request()->ajax()) {
            return view('productManage.partials.table', compact('rfqs'))->render();
        }

        return view('productManage.productRfq.view', compact('rfqs'));
    }
}

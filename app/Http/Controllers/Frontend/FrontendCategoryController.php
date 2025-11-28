<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Brands;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Log;

class FrontendCategoryController extends Controller
{
    // public function index()
    // {

    // $categories=Category::all();
    // $subcategories=SubCategory::all();
    //     $dataArray= [];

    // foreach ($categories as $category) {
    // // Prepare the main category data
    // $categoryData = [
    //     'name' => $category->name,
    //     'url' => 'details/' . $category->slug, // Use the slug for the URL
    //     'items' => []
    // ];

    // // Get subcategories for the current category
    // $subcategories = $category->subcategories;

    // // Add subcategories to the items array
    // foreach ($subcategories as $subcategory) {
    //     $categoryData['items'][] = [
    //         'name' => $subcategory->name,
    //         'url' => 'details/' . $category->slug . '/' . $subcategory->slug // Use slugs for the URL
    //     ];
    // }

    // // Add the category data to the main array
    // $dataArray[] = $categoryData;
    // }
    //     return Inertia::render('Category/CategoryList', [
    //         'categories' => $dataArray
    //     ]);
    // }

    public function index()
    {
        ini_set('memory_limit', '1G');
        $categories = Category::with(['subcategories', 'products' => function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }])->get();
        $brands = Brands::where('status', 1)->select('id', 'name')->get()->toArray();
        $dataArray = [];

        foreach ($categories as $category) {
            $categoryData = [
                'name'        => $category->name,
                'url'         => 'details/' . $category->slug,
                'items'       => [],
                'active'      => $category->status == 1 ? true : false,
                'newProducts' => $category->products()->where('created_at', '>=', Carbon::now()->subDays(30))->exists(),
                'brand_ids'   => $category->products()->pluck('brand_id')->unique()->values()->toArray(),
            ];

            foreach ($category->subcategories->whereNull('parent_id') as $subcategory) {
                $categoryData['items'][] = $this->formatSubcategory($subcategory, $category);
            }

            $dataArray[] = $categoryData;
        }

        return Inertia::render('Category/CategoryList', [
            'categories' => $dataArray,
            'brands'     => $brands,
        ]);
    }

    private function formatSubcategory($subcategory, $category)
    {
        $data = [
            'name'        => $subcategory->name,
            'url'         => 'details/' . $category->slug . '/' . $subcategory->slug, // Correct URL format
            'items'       => [],
            'active'      => $subcategory->status == 1 ? true : false,
            'newProducts' => $subcategory->created_at->diffInDays() <= 30 ? true : false,
            'brand_ids'   => $subcategory->products->pluck('brand_id')->unique()->values()->toArray(),
        ];

        // Recursively add child subcategories
        foreach ($subcategory->children as $childSubcategory) {
            $data['items'][] = $this->formatSubcategory($childSubcategory, $category);
        }

        return $data;
    }


    public function show($any)
    {
        // Split the URL path into slugs
        $slugs      = explode('/', $any);
        $parent     = null;
        $path       = '';
        $categories = [];

        foreach ($slugs as $index => $slug) {
            if ($index === 0) {
                // First level must be a Category
                $parent = Category::where('slug', $slug)->first();
            } else {
                // Debug: Output the parent and slug being processed
                Log::info("Processing slug: $slug at level: $index", [
                    'parent' => $parent,
                    'query'  => SubCategory::where('slug', $slug)
                        ->where(function ($query) use ($parent) {
                            if ($parent instanceof Category) {
                                // If parent is a Category, fetch subcategory with matching category_id
                                $query->where('category_id', $parent->id);
                            } else {
                                // If parent is a SubCategory, fetch subcategory with matching parent_id
                                $query->where('parent_id', $parent->id);
                            }
                        })
                        ->whereNull('deleted_at')
                        ->toSql(), // Output the raw SQL query
                ]);

                // Find the correct SubCategory based on hierarchy
                $parent = SubCategory::where('slug', $slug)
                    ->where(function ($query) use ($parent) {
                        if ($parent instanceof Category) {
                            // If parent is a Category, fetch subcategory with matching category_id
                            $query->where('category_id', $parent->id);
                        } else {
                            // If parent is a SubCategory, fetch subcategory with matching parent_id
                            $query->where('parent_id', $parent->id);
                        }
                    })
                    ->whereNull('deleted_at')
                    ->first();

                // Debug: Output the result of the query
                \Log::info("SubCategory query result:", ['parent' => $parent]);
            }

            // If no parent is found, abort with a 404 error
            if (! $parent) {
                \Log::error("Category/Subcategory not found at level: $index", [
                    'slug'   => $slug,
                    'parent' => $parent,
                ]);
                abort(404, 'Category or Subcategory not found');
            }

            // Append to path
            $path .= '/' . $parent->slug;

            // Store categories/subcategories for frontend
            $categories[] = [
                'id'   => $parent->id,
                'name' => $parent->name,
                'type' => 'category',
                'url'  => url("products/filter?productType%5B0%5D=" . $parent->id),
            ];
        }

        // Get the final category or subcategory (last parent in the loop)
        $finalEntity = $parent;
        // Determine the image path based on the type of the final entity
        $image = $finalEntity instanceof Category
            ? asset('uploads/category/' . $finalEntity->file_name)
            : asset($finalEntity->image_sub_cat);
        // dd($image);
        // Fetch subcategories based on the type of the final entity
        $subCategories = $finalEntity instanceof Category
            ? $finalEntity->subcategories()->whereNull('deleted_at')->get()
            : $finalEntity->children()->whereNull('deleted_at')->get();

        // Prepare the categories array for the frontend
        $categoriesForFrontend = $subCategories->isEmpty()
            ? ""
            : $subCategories->map(function ($subCategory) {
                // dd($subCategory);
                return [
                    'id'   => $subCategory->id,
                    'name' => $subCategory->name,
                    'type' => 'subcategory',
                    'url'  => url("products/filter?productType%5B0%5D=" . $subCategory->parent_id),
                ];
            });
        $filterUrl = $subCategories->isEmpty()
            ? url("products/filter?productType%5B0%5D=" . $categories[0]['id']) // Category URL if no subcategories
            : [
                'category'    => url("products/filter?productType%5B0%5D=" . $categories[0]['id'] . '&subCategory%5B0%5D=' . $finalEntity->id),
            ];
        dd([
            'image'           => $image,
            'title'           => $finalEntity->name,
            'description'     => $finalEntity->description,
            'current_categories' => $categoriesForFrontend,
            'subCategories'   => $subCategories,
            // 'productCategory' => $categories[0]['url'],
            'filterUrl'       => $filterUrl,
        ]);
        // Return the Inertia.js response with the desired format
        return Inertia::render('Category/Details', [
            'image'           => $image,
            'title'           => $finalEntity->name,
            'description'     => $finalEntity->description,
            'current_categories' => $categoriesForFrontend,
            'subCategories'   => $subCategories,
            // 'productCategory' => $categories[0]['url'],
            'filterUrl'       => $filterUrl,
        ]);
    }

    public function view($categorySlug)
    {
        $category      = Category::where('slug', $categorySlug)->firstOrFail();
        $subcategories = $category->subcategories;

        $categories = [
            [
                'name'  => $category->name,
                'items' => $subcategories->map(fn($subcategory) => [
                    'id'   => $subcategory->id,
                    'name' => $subcategory->name,
                ])->toArray(),
            ],
        ];

        return Inertia::render('Category/Details', [
            'image'       => asset('uploads/category/' . $category->file_name),
            'title'       => $category->name,
            'description' => $category->description,
            'categories'  => $categories,
        ]);
    }
    // {
    //     $cats=Category::all();
    //     $subCats=SubCategory::with('category')->get()  ;
    //       dd($cats->all());
    //     $categories = [
    //         [
    //             'name'  => 'Discrete Semiconductors',
    //             'items' => [
    //                 ['id' => 1, 'name' => 'Diodes & Rectifiers'],
    //                 ['id' => 2, 'name' => 'Transistors'],
    //                 ['id' => 3, 'name' => 'Thyristors'],
    //                 ['id' => 4, 'name' => 'Voltage Regulators'],
    //             ],
    //         ],
    //         [
    //             'name'  => 'Embedded Processors & Controllers',
    //             'items' => [
    //                 ['id' => 5, 'name' => 'CPLD - Complex Programmable Logic Devices'],
    //                 ['id' => 6, 'name' => 'FPGA - Configuration Memory'],
    //                 ['id' => 7, 'name' => 'Processors - Application Specialised'],
    //                 ['id' => 8, 'name' => 'Systems on a Chip - SoC'],
    //                 ['id' => 9, 'name' => 'EEPLD - Electronically Erasable Programmable Logic Devices'],
    //                 ['id' => 10, 'name' => 'FPGA - Field Programmable Gate Array'],
    //                 ['id' => 11, 'name' => 'RF System on a Chip - SoC'],
    //                 ['id' => 12, 'name' => 'Microprocessors - MPU'],
    //                 ['id' => 13, 'name' => 'CPU - Central Processing Units'],
    //                 ['id' => 14, 'name' => 'Microcontrollers - MCU'],
    //                 ['id' => 15, 'name' => 'SPLD - Simple Programmable Logic Devices'],
    //                 ['id' => 16, 'name' => 'Digital Signal Processors & Controllers - DSP, DSC'],
    //             ],
    //         ],
    //     ];

    // return Inertia::render('Category/Details', [
    //     'image'       => asset('assets/images/11.png'),
    //     'title'       => 'Semiconductors',
    //     'description' => 'Semiconductors from industry-leading manufacturers are available from Mouser Electronics. Mouser is an authorized distributor for many semiconductor manufacturers, including Analog Devices, Infineon, Intel, Microchip, Micron, NXP, onsemi, Renesas, STMicroelectronics, Texas Instruments, Xilinx & many more. See our full selection of semiconductors below.',
    //     'categories'  => $categories,
    // ]);
    // }

    public function handleAction(Request $request)
    {
        $productId = $request->input('product_id');
        $action    = $request->input('action');

        // dd("Product ID: $productId, Action: $action");
    }
}

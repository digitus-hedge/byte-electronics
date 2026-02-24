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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FrontendCategoryController extends Controller
{

    public function index()
    {

        $data = Cache::remember('category_list_page', 86400, function () {
            $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateTimeString();

            $categories = Category::select('id', 'name', 'slug', 'status')->get();
            $categoryIds = $categories->pluck('id');

            if ($categoryIds->isEmpty()) {
                return ['categories' => [], 'brands' => []];
            }

            $allSubs = DB::table('sub_categories')
                ->select('id', 'category_id', 'name', 'slug', 'parent_id', 'status', 'created_at')
                ->whereNull('deleted_at')
                ->get();

            $topLevel = [];
            $childrenByParent = [];

            foreach ($allSubs as $sub) {
                if ($sub->parent_id == $sub->category_id) {
                    $topLevel[$sub->category_id][] = $sub;
                } else {
                    $childrenByParent[$sub->parent_id][] = $sub;
                }
            }

            // $productInfo = DB::select("
            //     SELECT category_id, brand_id,
            //         MAX(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS has_new
            //     FROM products FORCE INDEX (products_category_brand_index)
            //     WHERE deleted_at IS NULL AND status = 1
            //     GROUP BY category_id, brand_id
            // ", [$thirtyDaysAgo]);
            $productInfo = DB::select("CALL GetCategoryBrandSummary()");
            $productInfo = collect($productInfo);

            $brandsByCategory = [];
            $categoriesWithNewProducts = [];
            $allBrandIds = [];

            foreach ($productInfo as $row) {
                $catId = $row->category_id;
                $brandId = $row->brand_id;
                $brandsByCategory[$catId][$brandId] = true;
                $allBrandIds[$brandId] = true;
                if ($row->has_new == 1) {
                    $categoriesWithNewProducts[$catId] = true;
                }
            }

            $brands = DB::table('brands')
                ->where('status', 1)
                ->whereIn('id', array_keys($allBrandIds))
                ->select('id', 'name')
                ->get()
                ->map(fn($b) => ['id' => $b->id, 'name' => $b->name])
                ->toArray();

            $dataArray = [];
            foreach ($categories as $category) {
                $subs = $topLevel[$category->id] ?? [];
                $catBrands = isset($brandsByCategory[$category->id])
                    ? array_map('intval', array_keys($brandsByCategory[$category->id]))
                    : [];

                $dataArray[] = [
                    'name'        => $category->name,
                    'url'         => 'details/' . $category->slug,
                    'active'      => $category->status == 1,
                    'newProducts' => isset($categoriesWithNewProducts[$category->id]),
                    'brand_ids'   => $catBrands,
                    'items'       => array_map(function ($sub) use ($category, &$childrenByParent, $thirtyDaysAgo) {
                        return $this->buildSubTree($sub, $category, $childrenByParent, $thirtyDaysAgo);
                    }, $subs),
                ];
            }

            return ['categories' => $dataArray, 'brands' => $brands];
        });

        return Inertia::render('Category/CategoryList', $data);
    }

    private function buildSubTree($sub, $category, &$childrenByParent, $thirtyDaysAgo)
    {
        $children = $childrenByParent[$sub->id] ?? [];

        return [
            'name'        => $sub->name,
            'url'         => 'details/' . $category->slug . '/' . $sub->slug,
            'active'      => $sub->status == 1,
            'newProducts' => $sub->created_at >= $thirtyDaysAgo,
            'brand_ids'   => [],
            'items'       => array_map(function ($child) use ($category, &$childrenByParent, $thirtyDaysAgo) {
                return $this->buildSubTree($child, $category, $childrenByParent, $thirtyDaysAgo);
            }, $children),
        ];
    }

    public function index_cache()
    {

        //Just make sure you clear the cache when products or categories are updated:
        //Cache::forget('category_list_page_data');
        $data = Cache::remember('category_list_page_data', 86400, function () {
            $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateTimeString();

            $categories = Category::select('id', 'name', 'slug', 'status')
                ->with([
                    'subcategories' => fn($q) => $q->whereNull('parent_id')
                        ->select('id', 'category_id', 'name', 'slug', 'parent_id'),
                    'subcategories.children' => fn($q) =>
                    $q->select('id', 'category_id', 'name', 'slug', 'parent_id', 'sub_category_id'),
                    'subcategories.children.children' => fn($q) =>
                    $q->select('id', 'category_id', 'name', 'slug', 'parent_id', 'sub_category_id'),
                ])->get();

            $categoryIds = $categories->pluck('id');

            if ($categoryIds->isEmpty()) {
                return ['categories' => [], 'brands' => []];
            }

            $productInfo = DB::table('products')
                ->select(
                    'category_id',
                    'brand_id',
                    DB::raw("MAX(CASE WHEN created_at >= '{$thirtyDaysAgo}' THEN 1 ELSE 0 END) AS has_new")
                )
                ->whereIn('category_id', $categoryIds)
                ->whereNull('deleted_at')
                ->where('status', 1)
                ->groupBy('category_id', 'brand_id')
                ->get();

            $brandsByCategory = $productInfo->groupBy('category_id');
            $categoriesWithNewProducts = $productInfo
                ->where('has_new', 1)
                ->pluck('category_id')
                ->unique()
                ->flip()
                ->toArray();

            $relevantBrandIds = $productInfo->pluck('brand_id')->unique()->toArray();
            $brands = Brands::where('status', 1)
                ->whereIn('id', $relevantBrandIds)
                ->select('id', 'name')
                ->get()
                ->toArray();

            $dataArray = $categories->map(function ($category) use ($brandsByCategory, $categoriesWithNewProducts) {
                return [
                    'name'        => $category->name,
                    'url'         => 'details/' . $category->slug,
                    'active'      => $category->status == 1,
                    'newProducts' => isset($categoriesWithNewProducts[$category->id]),
                    'brand_ids'   => isset($brandsByCategory[$category->id])
                        ? $brandsByCategory[$category->id]->pluck('brand_id')->unique()->values()->toArray()
                        : [],
                    'items'       => $category->subcategories->map(function ($subcategory) use ($category) {
                        return $this->formatSubcategory($subcategory, $category);
                    })->toArray(),
                ];
            })->toArray();

            return ['categories' => $dataArray, 'brands' => $brands];
        });

        return Inertia::render('Category/CategoryList', $data);
    }





    public function index_old()
    {
        ini_set('memory_limit', '2G');
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
            'url'         => 'details/' . $category->slug . '/' . $subcategory->slug,
            'items'       => [],
            'active'      => $subcategory->status == 1,
            'newProducts' => $subcategory->created_at->diffInDays() <= 30,
            'brand_ids'   => [], // Don't load per subcategory — too expensive
        ];

        foreach ($subcategory->children as $childSubcategory) {
            $data['items'][] = $this->formatSubcategory($childSubcategory, $category);
        }

        return $data;
    }


    // private function formatSubcategory($subcategory, $category)
    // {
    //     $data = [
    //         'name'        => $subcategory->name,
    //         'url'         => 'details/' . $category->slug . '/' . $subcategory->slug, // Correct URL format
    //         'items'       => [],
    //         'active'      => $subcategory->status == 1 ? true : false,
    //         'newProducts' => $subcategory->created_at->diffInDays() <= 30 ? true : false,
    //         'brand_ids'   => $subcategory->products->pluck('brand_id')->unique()->values()->toArray(),
    //     ];

    //     // Recursively add child subcategories
    //     foreach ($subcategory->children as $childSubcategory) {
    //         $data['items'][] = $this->formatSubcategory($childSubcategory, $category);
    //     }

    //     return $data;
    // }


    public function show($any)
    {

        // $slugs      = explode('/', $any);
        // $parent     = null;
        // $path       = '';
        // $categories = [];

        // foreach ($slugs as $index => $slug) {
        //     if ($index === 0) {

        //         $parent = Category::where('slug', $slug)->first();
        //     } 

        //     else 
        //     {

        //         $parent = SubCategory::where('slug', $slug)
        //             ->where(function ($query) use ($parent) {
        //                 if ($parent instanceof Category) {

        //                     $query->where('category_id', $parent->id);
        //                 } else {
        //                     $query->where('parent_id', $parent->id);
        //                 }
        //             })
        //             ->whereNull('deleted_at')
        //             ->first();

        //         \Log::info("SubCategory query result:", ['parent' => $parent]);
        //     }

        //     if (! $parent) {
        //         \Log::error("Category/Subcategory not found at level: $index", [
        //             'slug'   => $slug,
        //             'parent' => $parent,
        //         ]);
        //         abort(404, 'Category or Subcategory not found');
        //     }

        //     $path .= '/' . $parent->slug;

        //     $categories[] = [
        //         'id'   => $parent->id,
        //         'name' => $parent->name,
        //         'type' => 'category',
        //         'url'  => url("products/filter?productType%5B0%5D=" . $parent->id),
        //     ];
        // }

        // $finalEntity = $parent;

        // $image = $finalEntity instanceof Category
        //     ? asset('uploads/category/' . $finalEntity->file_name)
        //     : asset($finalEntity->image_sub_cat);


        // $subCategories = $finalEntity instanceof Category
        //     ? $finalEntity->subcategories()->whereNull('deleted_at')->get()
        //     : $finalEntity->children()->whereNull('deleted_at')->get();

        // $categoriesForFrontend = $subCategories->isEmpty()
        //     ? ""
        //     : $subCategories->map(function ($subCategory) {
        //         return [
        //             'id'   => $subCategory->id,
        //             'name' => $subCategory->name,
        //             'type' => 'subcategory',
        //             'url'  => url("products/filter?productType%5B0%5D=" . $subCategory->parent_id),
        //         ];
        //     });
        // $filterUrl = $subCategories->isEmpty()
        //     ? url("products/filter?productType%5B0%5D=" . $categories[0]['id']) 
        //     : [
        //         'category'    => url("products/filter?productType%5B0%5D=" . $categories[0]['id'] . '&subCategory%5B0%5D=' . $finalEntity->id),
        //     ];

        // return Inertia::render('Category/Details', [
        //     'image'           => $image,
        //     'title'           => $finalEntity->name,
        //     'description'     => $finalEntity->description,
        //     'current_categories' => $categoriesForFrontend,
        //     'subCategories'   => $subCategories,
        //     'filterUrl'       => $filterUrl,
        // ]);


        $slugs = explode('/', $any);
        $parent = null;
        $categories = [];

        foreach ($slugs as $index => $slug) {

            if ($index === 0) {
                $parent = Category::select('id', 'name', 'slug', 'file_name', 'description')
                    ->where('slug', $slug)
                    ->first();
            } else {
                $parent = SubCategory::select('id', 'name', 'slug', 'parent_id', 'category_id', 'image_sub_cat', 'description')
                    ->where('slug', $slug)
                    ->whereNull('deleted_at')
                    ->where(function ($query) use ($parent) {
                        $query->when(
                            $parent instanceof Category,
                            fn($q) => $q->where('category_id', $parent->id),
                            fn($q) => $q->where('parent_id', $parent->id)
                        );
                    })
                    ->first();
            }

            if (!$parent) {
                abort(404);
            }

            $categories[] = [
                'id'   => $parent->id,
                'name' => $parent->name,
                'type' => $parent instanceof Category ? 'category' : 'subcategory',
                'url'  => url('products/filter') . '?' . http_build_query(
                    $parent instanceof Category
                        ? ['productType' => [$parent->id]]
                        : [
                            'productType' => [$parent->category_id],
                            'subCategory' => [$parent->id]
                        ]
                ),
            ];
        }

        $finalEntity = $parent;

        $subCategories = $finalEntity instanceof Category
            ? $finalEntity->subcategories()
            ->select('id', 'name', 'parent_id')
            ->whereNull('deleted_at')
            ->get()
            : $finalEntity->children()
            ->select('id', 'name', 'parent_id')
            ->whereNull('deleted_at')
            ->get();


        $image = $finalEntity instanceof Category
            ? asset("uploads/category/{$finalEntity->file_name}")
            : asset($finalEntity->image_sub_cat);

        $categoriesForFrontend = $subCategories->isEmpty()
            ? ""
            : $subCategories->map(function ($sub) use ($categories) {
                return [
                    'id'   => $sub->id,
                    'name' => $sub->name,
                    'type' => 'subcategory',
                    'url'  => url('products/filter') . '?' . http_build_query([
                        'productType' => [$categories[0]['id']],
                        'subCategory' => [$sub->id]
                    ]),
                ];
            });

        $filterUrl = $subCategories->isEmpty()
            ? url('products/filter') . '?' . http_build_query([
                'productType' => [$categories[0]['id']]
            ])
            : url('products/filter') . '?' . http_build_query([
                'productType' => [$categories[0]['id']],
                'subCategory' => [$finalEntity->id]
            ]);

        return Inertia::render('Category/Details', [
            'image'              => $image,
            'title'              => $finalEntity->name,
            'description'        => $finalEntity->description,
            'current_categories' => $categoriesForFrontend,
            'subCategories'      => $subCategories,
            'filterUrl'          => $filterUrl,
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

<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class FrontendCategoryController extends Controller
{

    public function index()
    {
        $categories = Category::select('id', 'name', 'slug', 'status')
            ->get();

        if ($categories->isEmpty()) {
            return Inertia::render('Category/CategoryList', ['categories' => [], 'brands' => []]);
        }

        $allSubs = DB::table('sub_categories')
            ->select('id', 'category_id', 'name', 'slug', 'parent_id', 'status')
            ->whereNull('deleted_at')
            ->get()
            ->groupBy(fn($sub) => $sub->parent_id == $sub->category_id ? 'top_' . $sub->category_id : 'child_' . $sub->parent_id);

        $dataArray = $categories->map(function ($category) use ($allSubs) {
            $topLevel = $allSubs->get('top_' . $category->id, collect());

            return [
                'id'          => $category->id,
                'name'        => $category->name,
                'url'         => '/categories/' . $category->slug,
                'active'      => $category->status == 1,
                'newProducts' => false,
                'brand_ids'   => [],
                'items'       => $topLevel->map(fn($sub) => $this->buildSubTree($sub, $category, $allSubs))->values()->toArray(),
            ];
        })->toArray();

        return Inertia::render('Category/CategoryList', [
            'categories' => $dataArray,
            'brands'     => [],
        ]);
    }
    private function buildSubTree($sub, $category, $allSubs)
    {
        $children = $allSubs->get('child_' . $sub->id, collect());

        return [
            'id'          => $sub->id,
            'category_id' => $sub->category_id,
            'name'        => $sub->name,
            'url'         => '/' . $category->slug . '/' . $sub->slug,
            'active'      => $sub->status == 1,
            'newProducts' => false,
            'brand_ids'   => [],
            'items'       => $children->map(fn($child) => $this->buildSubTree($child, $category, $allSubs))->values()->toArray(),
        ];
    }

    // private function buildSubTree($sub, $category, &$childrenByParent, $thirtyDaysAgo)
    // {
    //     $children = $childrenByParent[$sub->id] ?? [];

    //     return [
    //         'name'        => $sub->name,
    //         'url'         => 'details/' . $category->slug . '/' . $sub->slug,
    //         'active'      => $sub->status == 1,
    //         'newProducts' => $sub->created_at >= $thirtyDaysAgo,
    //         'brand_ids'   => [],
    //         'items'       => array_map(function ($child) use ($category, &$childrenByParent, $thirtyDaysAgo) {
    //             return $this->buildSubTree($child, $category, $childrenByParent, $thirtyDaysAgo);
    //         }, $children),
    //     ];
    // }

    public function index_cache()
    {

        //Just make sure you clear the cache when products or categories are updated:
        //Cache::forget('category_list_page_data');
        $data = Cache::remember('category_list_page_data', 86400, function () {
            $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateTimeString();

            $categories = Category::select('id', 'name', 'slug', 'status')
                ->with([
                    'subcategories'                   => fn($q)                   => $q->whereNull('parent_id')
                        ->select('id', 'category_id', 'name', 'slug', 'parent_id'),
                    'subcategories.children'          => fn($q)          =>
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

            $brandsByCategory          = $productInfo->groupBy('category_id');
            $categoriesWithNewProducts = $productInfo
                ->where('has_new', 1)
                ->pluck('category_id')
                ->unique()
                ->flip()
                ->toArray();

            $relevantBrandIds = $productInfo->pluck('brand_id')->unique()->toArray();
            $brands           = Brands::where('status', 1)
                ->whereIn('id', $relevantBrandIds)
                ->select('id', 'name')
                ->get()
                ->toArray();

            $dataArray = $categories->map(function ($category) use ($brandsByCategory, $categoriesWithNewProducts) {
                return [
                    'name'        => $category->name,
                    'url'         => '/categories/' . $category->slug,
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
        $brands    = Brands::where('status', 1)->select('id', 'name')->get()->toArray();
        $dataArray = [];

        foreach ($categories as $category) {
            $categoryData = [
                'name'        => $category->name,
                'url'         => '/categories/' . $category->slug,
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
            'id'          => $subcategory->id,
            'category_id' => $subcategory->category_id,
            'name'        => $subcategory->name,
            'url'         => '/' . $category->slug . '/' . $subcategory->slug,
            'active'      => $subcategory->status == 1,
            'newProducts' => $subcategory->created_at->diffInDays() <= 30,
            'brand_ids'   => [],
            'items'       => [],
        ];

        foreach ($subcategory->children as $childSubcategory) {
            $data['items'][] = $this->formatSubcategory($childSubcategory, $category);
        }

        return $data;
    }

    public function show($any, $subcategory = null)
    {
        if ($subcategory !== null) {
            $any = $any . '/' . $subcategory;
        }
        $slugs      = explode('/', $any);
        $parent     = null;
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
                        if ($parent instanceof Category) {
                            $query->where('category_id', $parent->id)
                                ->orWhereHas('children', function ($q) use ($parent) {
                                    $q->where('category_id', $parent->id);
                                });
                        } else {
                            $query->where('parent_id', $parent->id);
                        }
                    })
                    ->first();
            }

            if (! $parent) {
                Log::error('Category/SubCategory not found in show()', [
                    'slug'  => $slug,
                    'index' => $index,
                ]);
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
                        'subCategory' => [$parent->id],
                    ]
                ),
            ];
        }

        $finalEntity = $parent;

        $subCategories = $finalEntity instanceof Category
            ? $finalEntity->subcategories()
            ->select('id', 'name', 'slug', 'parent_id')
            ->whereNull('deleted_at')
            ->get()
            : $finalEntity->children()
            ->select('id', 'name', 'slug', 'parent_id')
            ->whereNull('deleted_at')
            ->get();

        $image = $finalEntity instanceof Category
            ? asset("uploads/category/{$finalEntity->file_name}")
            : asset($finalEntity->image_sub_cat);

        $categoriesForFrontend = $subCategories->isEmpty()
            ? ""
            : $subCategories->map(function ($sub) use ($slugs) {
            return [
                'id'   => $sub->id,
                'name' => $sub->name,
                'type' => 'subcategory',
                'url'  => '/' . $slugs[0] . '/' . $sub->slug,
            ];
        });

        // $categoriesForFrontend = $subCategories->isEmpty()
        //     ? ""
        //     : $subCategories->map(function ($sub) use ($categories) {
        //     return [
        //         'id'   => $sub->id,
        //         'name' => $sub->name,
        //         'type' => 'subcategory',
        //         'url'  => url('products/filter') . '?' . http_build_query([
        //             'productType' => [$categories[0]['id']],
        //             'subCategory' => [$sub->id],
        //         ]),
        //     ];
        // });
        if ($finalEntity instanceof SubCategory) {
            request()->merge([
                'productType' => [$finalEntity->category_id],
                'subCategory' => [$finalEntity->id],
            ]);
            return app()->make(\App\Http\Controllers\Frontend\ProductController::class)
                ->filter(request());
        }
        $filterUrl = $subCategories->isEmpty()
            ? url('products/filter') . '?' . http_build_query([
            'productType' => [$categories[0]['id']],
        ])
            : url('products/filter') . '?' . http_build_query([
            'productType' => [$categories[0]['id']],
            'subCategory' => [$finalEntity->id],
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

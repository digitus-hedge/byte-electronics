<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\ProductPrice;
use App\Models\Products;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index()
    {
        // $products          = Products::all();
        // Fetch all products with their extended attributes and documents (if any)
        $products      = Products::with('attributes.documents')->select('id', 'name', 'slug', 'file_name', 'status', 'created_at')->orderby('id', 'desc')->paginate(40);
        $ProductBanner = asset('assets/banners/banner.jpg');

        $brands = Brands::select('id', 'name')->get();

        // Fetch categories dynamically with ID
        $categories    = Category::select('id', 'name')->get();
        $subCategories = SubCategory::select('id', 'name')->get();
        // Product Page Filters
        $productPageFilter = [
            [
                'head' => 'Manufacturer',
                'data' => $brands,
            ],
            [
                'head' => 'Product Type',
                'data' => $categories,
            ],
            [
                'head' => 'Sub Product Type',
                'data' => $subCategories,
            ],
            [
                'head' => 'Attributes',
                'data' => [''],
            ],
        ];

        $formattedProducts = $products->through(function ($product) {
            $rohsDocument  = $product->attributes->flatMap->documents->firstWhere('name', 'RoHS');
            $rohsCompliant = $rohsDocument ? true : false;

            return [
                'id'             => $product->id,
                'image'          => asset($product->file_name),
                'name'           => $product->name,
                'slug'           => $product->slug,
                'active'         => (bool) $product->status,
                'rohs_compliant' => $rohsCompliant,
                'created_at'     => $product->created_at->toDateTimeString(),
            ];
        });

        return Inertia::render('Products/List', [
            'ProductBanner'     => $ProductBanner,
            'productPageFilter' => $productPageFilter,
            'products'          => $formattedProducts,
        ]);
    }

    public function details($slug)
    {
        $productDetails = products::where('slug', $slug)
            ->with(['brands', 'category', 'subcategories', 'extendedAttributes', 'extendedAttributes.attributes', 'extendedAttributes.attrDocuments', 'extendedAttributes.headers'])
            ->first();



        // Fetch dynamic pricing from ProductPrice model
        $productPrices = ProductPrice::where('product_id', $productDetails->id)->get();
        $hasPrices = $productPrices->isNotEmpty();
        // Structure pricing data
        $pricing = [
            'currency' => $productPrices->first()->currency_name ?? 'AED', // Default to AED if no prices
            'pricing'  => [],
        ];

        // Group prices by unit_name and format the data
        $groupedPrices = $productPrices->groupBy('unit_name');
        foreach ($groupedPrices as $unitName => $prices) {
            $pricing['pricing'][$unitName] = $prices->map(function ($price) {
                return [
                    'qty'        => $price->qty,
                    'unit_price' => $price->single_price,
                    'ext_price'  => $price->total_price,
                ];
            })->all();
        }

        // If no prices exist, provide default structure
        // if (empty($pricing['pricing'])) {
        //     $pricing['pricing'] = [
        //         'unit1' => [
        //             [
        //                 'qty'        => 1,
        //                 'unit_price' => 222,
        //                 'ext_price'  => 222,
        //             ],
        //         ],
        //     ];
        // }
        // dd($productDetails);
        $product = [
            'id'                      => $productDetails->id,
            'name'                    => $productDetails->name,
            'description'             => $productDetails->description,
            'part_no'                 => $productDetails->manufacturers_no ?? 'N/A',
            'mfr_no'                  => $productDetails->manufacturers_no ?? 'N/A',
            'manufacturer_id'         => $productDetails->brands ? $productDetails->brands->id : 0,
            'manufacturer'            => $productDetails->brands ? $productDetails->brands->name : 'Unknown',
            'customer_no_placeholder' => 'Type here...',
            'price'                   => $productDetails->price,
            'datasheet_link'          => $productDetails->datasheet ? asset('uploads/documents/' . $productDetails->datasheet) : null,
            'category_id'          => $productDetails->category ? $productDetails->category->id : 0,
            'sub_category_id'      => $productDetails->subcategories ? $productDetails->subcategories->id : 0,

            'images'                  => [
                ['id' => 1, 'src' => asset("uploads/products/" . ($productDetails->file_name ?? ""))],
                ['id' => 2, 'src' => asset("uploads/products/" . ($productDetails->file_name ?? ""))], // Placeholder
                ['id' => 3, 'src' => asset("uploads/products/" . ($productDetails->file_name ?? ""))], // Placeholder
            ],
        ];

        // Group attributes by their header
        $groupedAttributes = $productDetails->extendedAttributes
            ->sortBy(fn($attribute) => $attribute->headers->id ?? 0)
            ->groupBy(fn($attribute) => $attribute->headers->name ?? 'General');

        // $groupedAttributes = $productDetails->extendedAttributes->groupBy(function ($attribute) {
        //     return $attribute->headers->name ?? 'General';
        // });

        // Generate dynamic accordion data
        $dynamicAccordionData = [];
        $baseUrl              = url('/'); // Get the base URL dynamically
        foreach ($groupedAttributes as $headerName => $attributes) {

            $formattedAttributes = [];

            foreach ($attributes as $attribute) {
                // dd($attribute->attrDocuments);
                // Fetch document value (if available)
                $value = $attribute->attrDocuments->first()->value ?? 'N/A';

                $formattedAttributes[] = [
                    'name'       => $attribute->attributes->name, // Attribute name
                    'value'      => in_array($attribute->value_type, ['image', 'document'])
                        ? $baseUrl . '/' . ltrim($value, '/') // Ensure correct URL format
                        : $value,
                    'value_type' => $attribute->value_type,
                    'part_no'    => $productDetails->manufacturers_no ?? 'N/A',
                ];
            }

            $dynamicAccordionData[] = [
                'title'      => $headerName, // Use header name
                'attributes' => $formattedAttributes,
            ];
        }

        // Assign only dynamic data
        $product['accordionData'] = $dynamicAccordionData;

        $allCategories     = category::take(10)->get();
        // dd($allCategories);
        $productCategories = [
            'title'      => 'PRODUCT CATEGORIES',
            'categories' => $allCategories->map(function ($category) {
                return [
                    'id'   => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }),
        ];

        $relatedProducts = products::with('prices')->where('category_id', $productDetails->category_id)
            ->where('id', '!=', $productDetails->id)
            ->take(15)
            ->get()
            ->map(function ($relatedProducts) {
                return [
                    'id'          => $relatedProducts->id,
                    'image'       => asset($relatedProducts->file_name),
                    'name'        => $relatedProducts->name,
                    'slug'        => $relatedProducts->slug,
                    'description' => $relatedProducts->description,
                    'isNew'       => false,
                    'has_price' => $relatedProducts->prices && $relatedProducts->prices->count() > 0,

                ];
            });



        $FeaturedProduct = Products::select('name', 'description', 'slug', 'file_name')
            ->where('brand_id', $productDetails->brand_id)
            ->where('id', '!=', $productDetails->id)
            // ->where('featured',1)->where('status',1)
            ->limit(10)->orderBy('created_at', 'asc')
            ->get();


        return Inertia::render(
            'Products/ProductDetails',
            [
                'product'           => $product,
                'productCategories' => $productCategories,
                'relatedProducts'   => $relatedProducts,
                'pricing'           => $pricing,
                'hasPrices'         => $hasPrices,
                'FeaturedProduct'   => $FeaturedProduct,
            ]
        );
    }

    public function dynamicAttributes(Request $request)
    {
        $manufacturer = $request->input('manufacturer', []);
        $productType = $request->input('productType', []);
        $subCategory = $request->input('subCategory', []);
        $attributeFilters = $request->input('attributeFilters', []);

        $categoryIds = !empty($productType) ? implode(',', array_map('intval', $productType)) : '';
        $subCategoryIds = !empty($subCategory) ? implode(',', array_map('intval', $subCategory)) : '';

        $attributes = DB::select('CALL GetProductAttributes(?, ?)', [$categoryIds, $subCategoryIds]);

        $validAttributeNames = [];
        if (!empty($productType) || !empty($attributeFilters)) {
            $productQuery = Products::query()->whereNull('deleted_at');
            if (!empty($manufacturer)) {
                $productQuery->whereIn('brand_id', $manufacturer);
            }
            if (!empty($productType)) {
                $productQuery->whereIn('category_id', $productType);
            }
            if (!empty($subCategory)) {
                $productQuery->whereIn('sub_category_id', $subCategory);
            }
            if (!empty($attributeFilters)) {
                foreach ($attributeFilters as $attributeName => $values) {
                    if (!empty($values)) {
                        $productQuery->whereHas('attributes.documents', function ($q) use ($attributeName, $values) {
                            $q->where('name', $attributeName)->whereIn('value', $values);
                        });
                    }
                }
            }

            $validAttributeNames = DB::table('products as p')
                ->join('product_extended as pe', 'p.id', '=', 'pe.product_id')
                ->join('product_attr_documents as pad', 'pe.id', '=', 'pad.product_ext_id')
                ->whereIn('p.id', $productQuery->select('id'))
                ->whereNull('p.deleted_at')
                ->distinct()
                ->pluck('pad.name')
                ->toArray();
        }

        $productPageFilter = array_filter(array_map(function ($attribute) use ($validAttributeNames) {
            if (empty($validAttributeNames) || in_array($attribute->attribute_name, $validAttributeNames)) {
                return [
                    'head' => $attribute->attribute_name,
                    'data' => array_map(function ($value) {
                        return [
                            'id' => $value->document_id,
                            'name' => $value->value,
                            'enabled' => true, // Default to enabled
                        ];
                    }, json_decode($attribute->attribute_values)),
                ];
            }
            return null;
        }, $attributes), fn($item) => !is_null($item));

        return response()->json(['productPageFilter' => $productPageFilter]);
    }


    public function filter(Request $request)
{
    $search       = $request->input('search', '');
    $manufacturer = $request->input('manufacturer', []);
    $productType  = $request->input('productType', []);
    $subCategory  = $request->input('subCategory', []);
    $page         = $request->input('page', 1);
    $active       = filter_var($request->input('active', false), FILTER_VALIDATE_BOOLEAN);
    $rohsCompliant = filter_var($request->input('rohsCompliant', false), FILTER_VALIDATE_BOOLEAN);
    $newProducts  = filter_var($request->input('newProducts', false), FILTER_VALIDATE_BOOLEAN);
    $attributeFilters = $request->input('attributeFilters', []);

    $normalizedAttributeFilters = [];
    foreach ($attributeFilters as $key => $values) {
        $normalizedValues = [];
        if (is_string($values) && !empty($values)) {
            $normalizedValues = [$values];
        } elseif (is_array($values) || is_object($values)) {
            $valuesArray = is_object($values) ? get_object_vars($values) : $values;
            $normalizedValues = array_filter(
                array_values($valuesArray),
                fn($val) => is_string($val) && !empty($val)
            );
        }
        if (!empty($normalizedValues)) {
            $normalizedAttributeFilters[$key] = array_values($normalizedValues);
        }
    }

    $filters = $request->only(['manufacturer', 'productType', 'subCategory', 'page', 'search', 'active', 'rohsCompliant', 'newProducts']);
    $filters['attributeFilters'] = $normalizedAttributeFilters;
    $categoryIds = !empty($filters['productType']) ? implode(',', $filters['productType']) : '';
    $subCategoryIds = !empty($filters['subCategory']) ? implode(',', $filters['subCategory']) : '';

    // Base query — NO eager loading
    $query = Products::query()
        ->select('id', 'name', 'slug', 'file_name', 'status', 'created_at', 'category_id', 'sub_category_id', 'brand_id')
        ->whereNull('deleted_at');

    if ($search !== '') {
        $query->where('name', 'like', "%{$search}%");
    }
    if (!empty($filters['manufacturer'])) {
        $query->whereIn('brand_id', $filters['manufacturer']);
    }
    if (!empty($filters['productType'])) {
        $query->whereIn('category_id', $filters['productType']);
    }
    if (!empty($filters['subCategory'])) {
        $query->whereIn('sub_category_id', $filters['subCategory']);
    }
    if ($active) {
        $query->where('status', 1);
    }
    if ($rohsCompliant) {
        $query->whereHas('attributes.documents', function ($q) {
            $q->where('name', 'RoHS');
        });
    }
    if ($newProducts) {
        $query->where('created_at', '>=', now()->subDays(30));
    }
    if (!empty($filters['attributeFilters'])) {
        foreach ($filters['attributeFilters'] as $attributeName => $values) {
            if (!empty($values)) {
                $query->whereHas('attributes.documents', function ($q) use ($attributeName, $values) {
                    $q->where('name', $attributeName)->whereIn('value', $values);
                });
            }
        }
    }

    // ---- COUNT with FORCE INDEX ----
    // Build WHERE conditions for raw count
    $countWhere = ['deleted_at IS NULL'];
    $countBindings = [];

    if (!empty($filters['productType'])) {
        $placeholders = implode(',', array_fill(0, count($filters['productType']), '?'));
        $countWhere[] = "category_id IN ($placeholders)";
        $countBindings = array_merge($countBindings, $filters['productType']);
    }
    if (!empty($filters['manufacturer'])) {
        $placeholders = implode(',', array_fill(0, count($filters['manufacturer']), '?'));
        $countWhere[] = "brand_id IN ($placeholders)";
        $countBindings = array_merge($countBindings, $filters['manufacturer']);
    }
    if (!empty($filters['subCategory'])) {
        $placeholders = implode(',', array_fill(0, count($filters['subCategory']), '?'));
        $countWhere[] = "sub_category_id IN ($placeholders)";
        $countBindings = array_merge($countBindings, $filters['subCategory']);
    }
    if ($search !== '') {
        $countWhere[] = "name LIKE ?";
        $countBindings[] = "%{$search}%";
    }
    if ($active) {
        $countWhere[] = "status = 1";
    }
    if ($newProducts) {
        $countWhere[] = "created_at >= ?";
        $countBindings[] = now()->subDays(30)->toDateTimeString();
    }

    $whereClause = implode(' AND ', $countWhere);
    $countResult = DB::select(
        "SELECT COUNT(*) as total FROM products FORCE INDEX (idx_products_cat_status_del_brand) WHERE {$whereClause}",
        $countBindings
    );
    $total = $countResult[0]->total;

    // ---- PAGINATED PRODUCTS ----
    $products = $query->forPage($page, 20)->get();

    // Check if page exceeds last page
    $lastPage = max(1, ceil($total / 20));
    if ($page > $lastPage) {
        $page = $lastPage;
        $products = $query->forPage($page, 20)->get();
    }

    $prod = new \Illuminate\Pagination\LengthAwarePaginator(
        $products, $total, 20, $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    // ---- RoHS for only 20 products ----
    $productIds = $products->pluck('id');
    $rohsProductIds = [];
    if ($productIds->isNotEmpty()) {
        $rohsProductIds = DB::table('product_extended as pe')
            ->join('product_attr_documents as pad', 'pe.id', '=', 'pad.product_ext_id')
            ->whereIn('pe.product_id', $productIds)
            ->where('pad.name', 'RoHS')
            ->pluck('pe.product_id')
            ->toArray();
    }

    $formattedProducts = $prod->through(function ($product) use ($rohsProductIds) {
        return [
            'id' => $product->id,
            'image' => $product->file_name ? asset('uploads/products/' . $product->file_name) : asset('assets/images/dummy_product.webp'),
            'name' => $product->name,
            'slug' => $product->slug,
            'active' => (bool) $product->status,
            'rohs_compliant' => in_array($product->id, $rohsProductIds),
            'created_at' => $product->created_at->toDateTimeString(),
        ];
    });

    // ---- Cached filter dropdowns ----
    $brands = Cache::remember('filter_brands', 3600, fn() =>
        Brands::select('id', 'name')->get()->toArray()
    );
    $categories = Cache::remember('filter_categories', 3600, fn() =>
        Category::select('id', 'name')->get()->toArray()
    );
    $subCategories = Cache::remember('filter_subcategories', 3600, fn() =>
        SubCategory::select('id', 'name')->get()->toArray()
    );

    // ---- Valid attribute names ----
    $validAttributeNames = [];
if (!empty($filters['productType']) || !empty($filters['attributeFilters'])) {
    $cacheKey = 'valid_attr_names_' . $categoryIds . '_' . $subCategoryIds;
    $validAttributeNames = Cache::remember($cacheKey, 3600, function () use ($filters) {
        return DB::table('product_attr_documents as pad')
            ->join('product_extended as pe', 'pad.product_ext_id', '=', 'pe.id')
            ->join('products as p', 'pe.product_id', '=', 'p.id')
            ->whereIn('p.category_id', $filters['productType'] ?? [])
            ->whereNull('p.deleted_at')
            ->distinct()
            ->pluck('pad.name')
            ->toArray();
    });
}

    // ---- Cached attributes ----
    $attributes = Cache::remember(
        "product_attributes_{$categoryIds}_{$subCategoryIds}",
        2592000,
        fn() => DB::select('CALL GetProductAttributes(?, ?)', [$categoryIds, $subCategoryIds])
    );

    return inertia('Products/List', [
        'ProductBanner' => asset('assets/banners/banner.jpg'),
        'productPageFilter' => [
            ['head' => 'Manufacturer', 'data' => array_map(fn($b) => ['id' => $b['id'], 'name' => $b['name']], $brands)],
            ['head' => 'Product Type', 'data' => array_map(fn($c) => ['id' => $c['id'], 'name' => $c['name']], $categories)],
            ['head' => 'Sub Product Type', 'data' => array_map(fn($s) => ['id' => $s['id'], 'name' => $s['name']], $subCategories)],
            ...array_filter(array_map(function ($attribute) use ($validAttributeNames) {
                if (empty($validAttributeNames) || in_array($attribute->attribute_name, $validAttributeNames)) {
                    return [
                        'head' => $attribute->attribute_name,
                        'data' => array_map(fn($value) => [
                            'id' => $value->document_id,
                            'name' => $value->value,
                        ], json_decode($attribute->attribute_values ?? '[]') ?: []),
                    ];
                }
                return null;
            }, $attributes), fn($item) => !is_null($item)),
        ],
        'products' => $formattedProducts,
        'brands' => $brands,
        'categories' => $categories,
        'subCategories' => $subCategories,
        'selectedFilters' => [
            'manufacturer' => $filters['manufacturer'] ?? [],
            'productType' => $filters['productType'] ?? [],
            'subCategory' => $filters['subCategory'] ?? [],
            'page' => $page ?? 1,
            'search' => $filters['search'] ?? '',
            'active' => $filters['active'] ?? false,
            'rohsCompliant' => $filters['rohsCompliant'] ?? false,
            'newProducts' => $filters['newProducts'] ?? false,
            'attributeFilters' => $filters['attributeFilters'] ?? [],
        ],
    ]);
}

    public function filter_old(Request $request)
    {
        // Extract filter inputs with defaults
        $search       = $request->input('search', '');
        $manufacturer = $request->input('manufacturer', []);
        $productType  = $request->input('productType', []);
        $subCategory  = $request->input('subCategory', []);
        $page         = $request->input('page', 1);
        $active       = filter_var($request->input('active', false), FILTER_VALIDATE_BOOLEAN);
        $rohsCompliant = filter_var($request->input('rohsCompliant', false), FILTER_VALIDATE_BOOLEAN);
        $newProducts  = filter_var($request->input('newProducts', false), FILTER_VALIDATE_BOOLEAN);
        $attributeFilters = $request->input('attributeFilters', []);
        $normalizedAttributeFilters = [];
        foreach ($attributeFilters as $key => $values) {
            $normalizedValues = [];
            if (is_string($values) && !empty($values)) {
                $normalizedValues = [$values];
            } elseif (is_array($values) || is_object($values)) {
                $valuesArray = is_object($values) ? get_object_vars($values) : $values;
                $normalizedValues = array_filter(
                    array_values($valuesArray),
                    fn($val) => is_string($val) && !empty($val)
                );
            }
            if (!empty($normalizedValues)) {
                $normalizedAttributeFilters[$key] = array_values($normalizedValues);
            }
        }

        $ProductBanner = asset('assets/banners/banner.jpg');
        $brands        = Brands::select('id', 'name')->get();
        $categories    = Category::select('id', 'name')->get();
        $subCategories = SubCategory::select('id', 'name')->get();
        $filters = $request->only([
            'manufacturer',
            'productType',
            'subCategory',
            'page',
            'search',
            'active',
            'rohsCompliant',
            'newProducts'
        ]);

        $filters['attributeFilters'] = $normalizedAttributeFilters;
        $categoryIds = !empty($filters['productType']) ? implode(',', $filters['productType']) : '';
        $subCategoryIds = !empty($filters['subCategory']) ? implode(',', $filters['subCategory']) : '';


        $validAttributeNames = [];

        if (!empty($filters['productType']) || !empty($filters['attributeFilters'])) {
            $productQuery = Products::query()->whereNull('deleted_at');
            if (!empty($filters['manufacturer'])) {
                $productQuery->whereIn('brand_id', $filters['manufacturer']);
            }
            if (!empty($filters['productType'])) {
                $productQuery->whereIn('category_id', $filters['productType']);
            }
            if (!empty($filters['subCategory'])) {
                $productQuery->whereIn('sub_category_id', $filters['subCategory']);
            }
            if ((isset($filters['search'])) && $filters['search'] !== '') {
                $productQuery->where('name', 'like', "%{$filters['search']}%");
            }
            if (isset($filters['active']) && $filters['active']) {
                $productQuery->where('status', 1);
            }
            if (isset($filters['rohsCompliant']) && $filters['rohsCompliant']) {
                $productQuery->whereHas('attributes.documents', function ($q) {
                    $q->where('name', 'RoHS');
                });
            }
            if (isset($filters['newProducts']) && $filters['newProducts']) {
                $productQuery->where('created_at', '>=', now()->subDays(30));
            }
            if (!empty($filters['attributeFilters'])) {
                foreach ($filters['attributeFilters'] as $attributeName => $values) {
                    if (!empty($values)) {
                        $productQuery->whereHas('attributes.documents', function ($q) use ($attributeName, $values) {
                            $q->where('name', $attributeName)->whereIn('value', $values);
                        });
                    }
                }
            }

            $validAttributeNames = DB::table('products as p')
                ->join('product_extended as pe', 'p.id', '=', 'pe.product_id')
                ->join('product_attr_documents as pad', 'pe.id', '=', 'pad.product_ext_id')
                ->whereIn('p.id', $productQuery->select('id'))
                ->whereNull('p.deleted_at')
                ->distinct()
                ->pluck('pad.name')
                ->toArray();
        }

        // Format productPageFilter
        // $productPageFilter = [
        //     ['head' => 'Manufacturer', 'data' => $brands->map(fn($brand) => ['id' => $brand->id, 'name' => $brand->name])->toArray()],
        //     ['head' => 'Product Type', 'data' => $categories->map(fn($cat) => ['id' => $cat->id, 'name' => $cat->name])->toArray()],
        //     ['head' => 'Sub Product Type', 'data' => $subCategories->map(fn($sub) => ['id' => $sub->id, 'name' => $sub->name])->toArray()],
        //     ...array_filter(array_map(function ($attribute) use ($validAttributeNames) {
        //         if (empty($validAttributeNames) || in_array($attribute->attribute_name, $validAttributeNames)) {
        //             return [
        //                 'head' => $attribute->attribute_name,
        //                 'data' => array_map(function ($value) {
        //                     return [
        //                         'id' => $value->document_id,
        //                         'name' => $value->value,
        //                     ];
        //                 }, json_decode($attribute->attribute_values ?? '[]') ?: []),
        //             ];
        //         }
        //         return null;
        //     }, DB::select('CALL GetProductAttributes(?, ?)', [$categoryIds, $subCategoryIds])), fn($item) => !is_null($item)),
        // ];

        // Base query with eager loading
        $query = Products::query()->with('attributes.documents')->whereNull('deleted_at');

        // Apply filters
        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }
        if (!empty($filters['manufacturer'])) {
            $query->whereIn('brand_id', $filters['manufacturer']);
        }
        if (!empty($filters['productType'])) {
            $query->whereIn('category_id', $filters['productType']);
        }
        if (!empty($filters['subCategory'])) {
            $query->whereIn('sub_category_id', $filters['subCategory']);
        }
        if ($active) {
            $query->where('status', 1);
        }
        if ($rohsCompliant) {
            $query->whereHas('attributes.documents', function ($q) {
                $q->where('name', 'RoHS');
            });
        }
        if ($newProducts) {
            $query->where('created_at', '>=', now()->subDays(30));
        }
        if (!empty($filters['attributeFilters'])) {
            foreach ($filters['attributeFilters'] as $attributeName => $values) {
                if (!empty($values)) {
                    $query->whereHas('attributes.documents', function ($q) use ($attributeName, $values) {
                        $q->where('name', $attributeName)->whereIn('value', $values);
                    });
                }
            }
        }

        // Paginate results
        $prod = $query->paginate(20, ['*'], 'page', $page);

        if ($prod->lastPage() < $page) {
            $page = $prod->lastPage();
            $prod = $query->paginate(20, ['*'], 'page', $page);
        }

        $products = $prod;

        $formattedProducts = $products->through(function ($product) {
            $rohsDocument = $product->attributes->flatMap->documents->firstWhere('name', 'RoHS');
            $rohsCompliant = $rohsDocument ? true : false;

            return [
                'id' => $product->id,
                'image' => $product->file_name ? asset('uploads/products/' . $product->file_name) : asset('assets/images/dummy_product.webp'),
                'name' => $product->name,
                'slug' => $product->slug,
                'active' => (bool) $product->status,
                'rohs_compliant' => $rohsCompliant,
                'created_at' => $product->created_at->toDateTimeString(),
            ];
        });
        $attributes = Cache::remember(
            "product_attributes_{$categoryIds}_{$subCategoryIds}",
            2592000, // 300 seconds = 5 minutes
            fn() => DB::select('CALL GetProductAttributes(?, ?)', [$categoryIds, $subCategoryIds])
        );
        // $attributes = DB::select('CALL GetProductAttributes(?, ?)', [$categoryIds, $subCategoryIds]);
        return inertia('Products/List', [
            'ProductBanner' => $ProductBanner,
            'productPageFilter' => [
                ['head' => 'Manufacturer', 'data' => $brands->map(fn($brand) => ['id' => $brand->id, 'name' => $brand->name])->toArray()],
                ['head' => 'Product Type', 'data' => $categories->map(fn($cat) => ['id' => $cat->id, 'name' => $cat->name])->toArray()],
                ['head' => 'Sub Product Type', 'data' => $subCategories->map(fn($sub) => ['id' => $sub->id, 'name' => $sub->name])->toArray()],
                ...array_filter(array_map(function ($attribute) use ($validAttributeNames) {
                    if (empty($validAttributeNames) || in_array($attribute->attribute_name, $validAttributeNames)) {
                        return [
                            'head' => $attribute->attribute_name,
                            'data' => array_map(function ($value) {
                                return [
                                    'id' => $value->document_id,
                                    'name' => $value->value,
                                ];
                            }, json_decode($attribute->attribute_values ?? '[]') ?: []),
                        ];
                    }
                    return null;
                }, $attributes), fn($item) => !is_null($item)),
            ],
            'products' => $formattedProducts,
            'brands' => $brands->toArray(),
            'categories' => $categories->toArray(),
            'subCategories' => $subCategories->toArray(),
            'selectedFilters' => [
                'manufacturer' => $filters['manufacturer'] ?? [],
                'productType' => $filters['productType'] ?? [],
                'subCategory' => $filters['subCategory'] ?? [],
                'page' => $page ?? 1,
                'search' => $filters['search'] ?? '',
                'active' => $filters['active'] ?? false,
                'rohsCompliant' => $filters['rohsCompliant'] ?? false,
                'newProducts' => $filters['newProducts'] ?? false,
                'attributeFilters' => $filters['attributeFilters'] ?? [],
            ],
        ]);
    }

    public function getSimilarProductsCount(Request $request)
    {
        // dd($request->all());
        // Validate the request
        $request->validate([
            'attributes' => 'required|array',
            'attributes.*.name' => 'required|string',
            'attributes.*.value' => 'required|string',
            'product_id' => 'required|exists:products,id', // Exclude the current product
        ]);

        $attributes = $request->input('attributes', []);
        $productId = $request->input('product_id');
        $categoryId = $request->input('category_id'); // Optional: to narrow down by category
        $subCategoryId = $request->input('sub_category_id'); // Optional: to narrow down by subcategory
        $manufacturerId = $request->input('manufacturer_id'); // Optional: to narrow down by manufacturer

        Log::info('ProductController::getSimilarProductsCount - Received attributes:', [
            'attributes' => $attributes,
            'product_id' => $productId,
            'category_id' => $categoryId,
            'sub_category_id' => $subCategoryId,
            'manufacturer_id' => $manufacturerId,
        ]);

        try {
            // Start building the query
            $query = Products::query()
                ->whereNull('deleted_at')
                ->where('id', '!=', $productId); // Exclude the current product

            // Apply category and subcategory filters if provided
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
            if ($subCategoryId) {
                $query->where('sub_category_id', $subCategoryId);
            }
            if ($manufacturerId) {
                $query->where('brand_id', $manufacturerId);
            }

            // Apply attribute filters
            foreach ($attributes as $attribute) {
                $attributeName = $attribute['name'];
                $attributeValue = $attribute['value'];

                $query->whereHas('attributes.documents', function ($q) use ($attributeName, $attributeValue) {
                    $q->where('name', $attributeName)
                        ->where('value', $attributeValue);
                });
            }

            // Get the count of matching products
            $count = $query->count();

            Log::info('ProductController::getSimilarProductsCount - Products found:', [
                'count' => $count,
            ]);

            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('ProductController::getSimilarProductsCount - Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the product count.',
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $categoryId = $request->input('category_id');

        // Product search
        $productQuery = Products::query()
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                    ->orWhere('products.manufacturers_no', 'like', "%{$search}%");
            });

        if ($categoryId) {
            $productQuery->where('products.category_id', $categoryId);
        }

        $products = $productQuery->select(
            'products.id',
            'products.name',
            'products.slug',
            'products.file_name',
            'products.manufacturers_no',
            'brands.name as brand_name'
        )
            ->limit(10)
            ->get()
            ->map(function ($product) {
                $imagePath = 'Uploads/products/' . $product->file_name;
                $image = file_exists(public_path($imagePath)) && $product->file_name
                    ? asset($imagePath)
                    : asset('assets/images/dummy_product.webp');

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $image,
                    'part_no' => $product->manufacturers_no,
                    'brand' => $product->brand_name,
                    'type' => 'product',
                ];
            });

        $results = ['products' => $products];

        // Only fetch categories, subcategories, and brands if no category is selected
        if (!$categoryId) {
            // Category search
            $categories = Category::query()
                ->where('name', 'like', "%{$search}%")
                ->select('id', 'name', 'slug')
                ->limit(5)
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'type' => 'category',
                    ];
                });

            // Subcategory search (first-level only)
            $subcategories = SubCategory::query()
                ->where('name', 'like', "%{$search}%")
                ->whereNull('parent_id')
                ->select('id', 'name', 'slug', 'category_id')
                ->limit(5)
                ->get()
                ->map(function ($subcategory) {
                    return [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                        'slug' => $subcategory->slug,
                        'category_id' => $subcategory->category_id,
                        'type' => 'subcategory',
                    ];
                });

            // Brand search
            $brands = Brands::query()
                ->where('name', 'like', "%{$search}%")
                ->select('id', 'name', 'slug')
                ->limit(5)
                ->get()
                ->map(function ($brand) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->name,
                        'slug' => $brand->slug,
                        'type' => 'brand',
                    ];
                });

            $results['categories'] = $categories;
            $results['subcategories'] = $subcategories;
            $results['brands'] = $brands;
        } else {
            // Ensure empty arrays for categories, subcategories, and brands when category is selected
            $results['categories'] = [];
            $results['subcategories'] = [];
            $results['brands'] = [];
        }

        return response()->json($results);
    }

    /**
     * Product Request for Quote
     * * This function handles the request for a quote for a product.
     *
     */
    public function requestQuote(Request $request, $productId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required_without:phone|nullable|email|max:255',
            'phone' => 'required_without:email|nullable',
            'quantity' => 'required|integer|min:1',
            'file'     => 'nullable|file|max:2048',
        ]);

        try {
            // Handle file upload if provided
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('quotes', 'public');
            }

            // Get logged-in user id or null
            $userId = Auth::check() ? Auth::id() : null;

            // Insert into database
            DB::table('request_quotes')->insert([
                'name'       => $validated['name'],
                'email'      => $validated['email'] ?? null,
                'phone'      => $validated['phone'] ?? null,
                'product_id' => $productId,
                'user_id'   =>  $userId,
                'quantity'   => $validated['quantity'],
                'file'       => $filePath,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            // Log the error for debugging
            Log::error('Quote Request Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Optionally delete uploaded file if DB insert failed
            if (!empty($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->with('error', 'Something went wrong while processing your request. Please try again.');
        }
    }
    public function getProductAttributes($categoryIds = '', $subCategoryIds = '')
    {
        $attributes = DB::table('products as p')
            ->leftJoin('categories as c', function ($join) {
                $join->on('p.category_id', '=', 'c.id')
                    ->whereNull('c.deleted_at');
            })
            ->leftJoin('product_extended as pe', 'p.id', '=', 'pe.product_id')
            ->leftJoin('product_attributes as pa', 'pe.product_attr_id', '=', 'pa.id')
            ->leftJoin('product_headers as ph', 'pa.prd_header_id', '=', 'ph.id')
            ->leftJoin('product_attr_documents as pad', 'pe.id', '=', 'pad.product_ext_id')
            ->select([
                'pad.name as attribute_name',
                DB::raw('CONCAT(
                "[",
                GROUP_CONCAT(
                    CONCAT(
                        "{\\"document_id\\":", JSON_QUOTE(CAST(pad.id AS CHAR)),
                        ",\\"value\\":", JSON_QUOTE(pad.value),
                        "}"
                    ) ORDER BY pad.value SEPARATOR ","
                ),
                "]"
            ) as attribute_values')
            ])
            ->whereNull('p.deleted_at')
            ->where('ph.name', '=', 'Specifications')
            ->whereNotNull('pad.name')
            ->whereNotNull('pad.value')
            ->whereNotIn('pad.name', [
                "Product Image",
                "Manufacturer",
                "Product Type",
                "Product Category",
                "Product",
                "Application Notes",
                "Brand",
                "Description",
                "Datasheet",
                "Image",
                "Image Alt text",
                "Models",
                "Title",
                "Tradename",
                "Factory Pack Quantity"
            ])
            ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereRaw("FIND_IN_SET(p.category_id, ?)", [$categoryIds]);
            })
            ->when(!empty($subCategoryIds), function ($query) use ($subCategoryIds) {
                $query->where(function ($q) use ($subCategoryIds) {
                    $q->whereRaw("FIND_IN_SET(p.sub_category_id, ?)", [$subCategoryIds])
                        ->orWhere('p.sub_category_id', '=', 0);
                });
            })
            ->groupBy('pad.name')
            ->orderBy('pad.name')
            ->get();

        return $attributes;
    }
}

<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FrontendBrandController extends Controller
{
    public function index()
    {
        $results = DB::select("
        SELECT
            letter_group,
            JSON_ARRAYAGG(
                JSON_OBJECT(
                    'name',   name,
                    'slug',   slug,
                    'banner', banner,
                    'url',    CONCAT('/brands/details/', slug)
                )
            ) AS brands
        FROM (
            SELECT
                name, slug, banner,
                CASE
                    WHEN UPPER(SUBSTRING(name, 1, 1)) REGEXP '^[A-Z]$'
                    THEN UPPER(SUBSTRING(name, 1, 1))
                    ELSE '#'
                END AS letter_group
            FROM brands
            WHERE deleted_at IS NULL AND status=1
            ORDER BY name
        ) AS sorted_brands
        GROUP BY letter_group
        ORDER BY letter_group
     ");

        $groupedBrands = collect($results)->mapWithKeys(function ($row) {
            return [$row->letter_group => json_decode($row->brands, true)];
        });

        // Get first brand's banner from first letter group
        $firstBrand  = $groupedBrands->first()[0] ?? null;
        $brandBanner = isset($firstBrand['banner'])
            ? asset('uploads/brand/banner/' . $firstBrand['banner'])
            : null;

        return Inertia::render('Brands/List', [
            'brandBanner'   => $brandBanner,
            'manufacturers' => $groupedBrands,
        ]);
    }

    public function index_org()
    {
        //$brandBanner = asset('assets/banners/Frame 10.png');

        // $manufacturers = collect([
        //     '1BitSquared', '3M', '4D Systems', '0xDA', '1Global', '4D LCD',
        //     'Apple', 'AMD', 'Asus', 'Acer',
        //     'Bose', 'Bosch', 'Brother',
        //     'Canon', 'Cisco', 'Corsair',
        //     'Dell', 'D-Link', 'Dyson',
        //     'Epson', 'Energizer', 'Eizo',
        //     'Fujitsu', 'Fender', 'Fitbit',
        //     'Google', 'Gigabyte', 'GoPro',
        //     'HP', 'Huawei', 'Harman Kardon',
        //     'Intel', 'IBM', 'Ikea',
        //     'Jabra', 'JBL', 'Joy-Con',
        //     'Kingston', 'Kodak', 'Kaspersky',
        //     'LG', 'Lenovo', 'Logitech',
        //     'Microsoft', 'Motorola', 'MSI',
        //     'Nvidia', 'Nikon', 'Netgear',
        //     'Oppo', 'OnePlus', 'Olympus',
        //     'Panasonic', 'Philips', 'PlayStation',
        //     'Qualcomm', 'QNAP', 'Quanta',
        //     'Razer', 'Ricoh', 'Roku',
        //     'Samsung', 'Sony', 'Seagate',
        //     'Toshiba', 'TP-Link', 'Tesla',
        //     'Ubiquiti', 'Uber', 'Uniden',
        //     'Vivo', 'ViewSonic', 'Vizio',
        //     'Western Digital', 'Wacom', 'Whirlpool',
        //     'Xiaomi', 'Xerox', 'XFX',
        //     'Yamaha', 'Yealink', 'Yubico',
        //     'Zotac', 'ZTE', 'Zebra',
        // ]);
        // Fetch the brand data from the database
        // Fetch only the `name` and `slug` fields from the Brands table
        //$brands = Brands::select('name', 'slug')->get();

        // Already

        $brands = DB::table('brands')
            ->select('name', 'slug', 'banner')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $brands->transform(function ($brand) {
            $brand->url = url("brands/details/{$brand->slug}");
            return $brand;
        });

        $groupedBrands = $brands->groupBy(function ($brand) {
            $firstLetter = strtoupper(substr($brand->name, 0, 1));
            return preg_match('/[A-Z]/', $firstLetter) ? $firstLetter : '#';
        });

        $bannerFileName = $brands->isNotEmpty() ? $brands->first()->banner : null;

        $brandBanner = $bannerFileName ? asset('uploads/brand/banner/' . $bannerFileName) : null;

        return Inertia::render('Brands/List', [
            'brandBanner'   => $brandBanner,
            'manufacturers' => $groupedBrands,
        ]);

        // $brands = DB::table('brands')
        //     ->select('name', 'slug', 'banner')
        //     ->whereNull('deleted_at')
        //     ->orderBy('name')
        //     ->get()
        //     ->map(function ($brand) {
        //         return [
        //             'name' => $brand->name,
        //             'slug' => $brand->slug,
        //             'banner' => $brand->banner,
        //             'url' => route('brands.details', $brand->slug),
        //         ];
        //     });

        // $groupedBrands = $brands->groupBy(function ($brand) {
        //     $firstLetter = strtoupper(substr($brand['name'], 0, 1));
        //     return preg_match('/[A-Z]/', $firstLetter) ? $firstLetter : '#';
        // });

        // $bannerFileName = DB::table('brands')
        //     ->whereNull('deleted_at')
        //     ->whereNotNull('banner')
        //     ->value('banner');

        // $brandBanner = $bannerFileName
        //     ? asset('uploads/brand/banner/' . $bannerFileName)
        //     : null;

        // return Inertia::render('Brands/List', [
        //     'brandBanner'   => $brandBanner,
        //     'manufacturers' => $groupedBrands,
        // ]);

    }

    public function details($slug)
    {

        $brandDetails = Brands::select('id', 'name', 'slug', 'file_name', 'description')
            ->where('slug', $slug)
            ->firstOrFail();

        $brandId = $brandDetails->id;

        // Step 1: Get all category_ids that have products for this brand (single fast query)
        $categoryIds = DB::table('products')
            ->where('brand_id', $brandId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('category_id');

        if ($categoryIds->isEmpty()) {
            return Inertia::render('Brands/Details', [
                'brand' => $this->formatBrandResponse($brandDetails, [])
            ]);
        }

        // Step 2: Get categories
        $categories = Category::select('id', 'name', 'slug')
            ->where('status', 1)
            ->whereIn('id', $categoryIds)
            ->get();

        // Step 3: Get all subcategories for these categories
        $allSubs = DB::table('sub_categories')
            ->select('id', 'category_id', 'name', 'slug', 'parent_id', 'status')
            ->whereIn('category_id', $categoryIds)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get();

        $topLevel         = [];
        $childrenByParent = [];

        foreach ($allSubs as $sub) {
            if ($sub->parent_id == $sub->category_id) {
                $topLevel[$sub->category_id][] = $sub;
            } else {
                $childrenByParent[$sub->parent_id][] = $sub;
            }
        }

        // Step 4: Get all products for this brand in these categories (single query)
        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('sub_categories', 'products.sub_category_id', '=', 'sub_categories.id')
            ->select('products.id', 'products.name', 'products.slug', 'products.file_name', 'products.price', 'products.description', 'products.category_id', 'products.sub_category_id', 'categories.slug as category_slug', 'sub_categories.slug as subcategory_slug')
            ->where('products.brand_id', $brandId)
            ->where('products.status', 1)
            ->whereNull('products.deleted_at')
            ->whereIn('products.category_id', $categoryIds)
            ->get();

        // Group products by sub_category_id and category_id
        $productsBySub      = [];
        $productsByCategory = [];

        foreach ($products as $product) {
            if ($product->sub_category_id) {
                $productsBySub[$product->sub_category_id][] = [
                    'name' => $product->name,
                    'url'  => '/' . $product->category_slug . '/' . $product->subcategory_slug . '/' . $product->slug,
                ];
            } else {
                $productsByCategory[$product->category_id][] = [
                    'name' => $product->name,
                    'url'  => '/' . $product->category_slug . '/' . ($product->subcategory_slug ?? 'uncategorized') . '/' . $product->slug,
                ];
            }
        }

        // Step 5: Build response
        $categories_brand = [];

        foreach ($categories as $category) {
            $subs  = $topLevel[$category->id] ?? [];
            $items = [];

            foreach ($subs as $sub) {
                $subItem = $this->buildBrandSubTree($sub, $childrenByParent, $productsBySub, $category->slug);
                if (! empty($subItem['items'])) {
                    $items[] = $subItem;
                }
            }

            // Category-level products
            if (! empty($productsByCategory[$category->id])) {
                $items[] = [
                    'name'  => 'General Products',
                    'url'   => '/categories/' . $category->slug,
                    'items' => $productsByCategory[$category->id],
                ];
            }

            if (! empty($items)) {
                $categories_brand[] = [
                    'name'  => $category->name,
                    'url'   => '/categories/' . $category->slug,
                    'items' => $items,
                ];
            }
        }

        return Inertia::render('Brands/Details', [
            'brand' => $this->formatBrandResponse($brandDetails, $categories_brand),
        ]);
    }

    private function buildBrandSubTree($sub, &$childrenByParent, &$productsBySub, $categorySlug = '')
    {
        $children = $childrenByParent[$sub->id] ?? [];
        $items    = [];

        foreach ($children as $child) {
            $childItem = $this->buildBrandSubTree($child, $childrenByParent, $productsBySub, $categorySlug);
            if (! empty($childItem['items'])) {
                $items[] = $childItem;
            }
        }

        // Add products for this subcategory
        if (! empty($productsBySub[$sub->id])) {
            $items = array_merge($items, $productsBySub[$sub->id]);
        }

        return [
            'name'  => $sub->name,
            'url'   => '/' . $categorySlug . '/' . $sub->slug,
            'items' => $items,
        ];
    }

    private function formatBrandResponse($brandDetails, $categories_brand)
    {
        return [
            'name'  => $brandDetails->name,
            'slug'  => $brandDetails->slug,
            'image' => ($brandDetails->file_name && file_exists(public_path('uploads/brand/' . $brandDetails->file_name)))
                ? asset('uploads/brand/' . rawurlencode($brandDetails->file_name))
                : asset('assets/images/dummy_product.webp'),
            'tabs'  => [
                [
                    'key'     => 'about',
                    'label'   => 'About',
                    'content' => $brandDetails->description ?? 'No description available.',
                ],
                [
                    'key'     => 'product',
                    'label'   => 'Product Line',
                    'content' => $categories_brand,
                ],
                [
                    'key'     => 'support',
                    'label'   => 'Resources & Support',
                    'content' => 'Need help? Browse our support resources and contact our team for assistance.',
                ],
            ],
        ];
    }

    public function details_old($slug)
    {
        //$brandDetails = Brands::where('slug', $slug)->firstOrFail();
        $brandDetails = Brands::select('id', 'name', 'slug', 'file_name', 'description')
            ->where('slug', $slug)
            ->firstOrFail();

        // Fetch categories that have products or subcategories with products under the current brand
        $categories_brand = Category::whereHas('products', function ($query) use ($brandDetails) {
            $query->where('brand_id', $brandDetails->id)->where('status', 1);
        })
            ->orWhereHas('subcategories.products', function ($query) use ($brandDetails) {
                $query->where('brand_id', $brandDetails->id)->where('status', 1);
            })
            ->with([
                'subcategories' => function ($query) use ($brandDetails) {
                    $query->whereNull('parent_id')
                        ->where('status', 1)
                        ->with(['descendants' => function ($query) use ($brandDetails) {
                            $query->where('status', 1)
                                ->with(['products' => function ($query) use ($brandDetails) {
                                    $query->where('brand_id', $brandDetails->id)
                                        ->where('status', 1)
                                        ->select('id', 'name', 'slug', 'file_name', 'price', 'description');
                                }]);
                        }, 'products' => function ($query) use ($brandDetails) {
                            $query->where('brand_id', $brandDetails->id)
                                ->where('status', 1)
                                ->select('id', 'name', 'slug', 'file_name', 'price', 'description');
                        }]);
                },
                'products'      => function ($query) use ($brandDetails) {
                    $query->where('brand_id', $brandDetails->id)
                        ->where('status', 1)
                        ->select('id', 'name', 'slug', 'file_name', 'price', 'description');
                },
            ])
            ->where('status', 1)
            ->get()
            ->map(function ($category) {
                $categoryData = [
                    'name'  => $category->name,
                    'url'   => '/products/filter?category=' . $category->id,
                    'items' => [],
                ];

                // Add top-level subcategories with their products and nested subcategories
                foreach ($category->subcategories as $subcategory) {
                    $categoryData['items'][] = $this->formatSubcategory($subcategory);
                }

                // Add category-level products as a pseudo-subcategory
                if ($category->products->isNotEmpty()) {
                    $categoryData['items'][] = [
                        'name'  => 'General Products',
                        'url'   => '/products/filter?category=' . $category->id,
                        'items' => $category->products->map(function ($product) {
                            return [
                                'name' => $product->name,
                                'url'  => '/products/' . $product->slug,
                            ];
                        })->toArray(),
                    ];
                }

                return $categoryData;
            })->filter(function ($category) {
            return ! empty($category['items']);
        })->values()->toArray();

        $brand = [
            'name'  => $brandDetails->name,
            'image' => asset('uploads/brand/' . $brandDetails->file_name),
            'id'    => $brandDetails->id,
            'tabs'  => [
                [
                    'key'     => 'about',
                    'label'   => 'About',
                    'content' => $brandDetails->description ?? 'No description available.',
                ],
                [
                    'key'     => 'product',
                    'label'   => 'Product Line',
                    'content' => $categories_brand,
                ],
                [
                    'key'     => 'support',
                    'label'   => 'Resources & Support',
                    'content' => 'Need help? Browse our support resources and contact our team for assistance.',
                ],
            ],
        ];

        return Inertia::render('Brands/Details', compact('brand'));
    }

    /**
     * Format a subcategory with its products and nested subcategories
     */
    private function formatSubcategory($subcategory)
    {
        $subcategoryData = [
            'name'  => $subcategory->name,
            'url'   => '/products/filter?subcategory=' . $subcategory->id,
            'items' => [],
        ];

        // Add products under this subcategory
        if ($subcategory->products->isNotEmpty()) {
            $subcategoryData['items'] = array_merge(
                $subcategoryData['items'],
                $subcategory->products->map(function ($product) {
                    return [
                        'name' => $product->name,
                        'url'  => '/products/' . $product->slug,
                    ];
                })->toArray()
            );
        }

        // Add nested subcategories (descendants)
        if ($subcategory->descendants->isNotEmpty()) {
            foreach ($subcategory->descendants as $descendant) {
                $subcategoryData['items'][] = $this->formatSubcategory($descendant);
            }
        }

        return $subcategoryData;
    }

    public function prodductLine()
    {
        $productLineBanner = asset('assets/banners/BOSCH-1 1.png');
        return Inertia::render('Brands/ProductLine', compact('productLineBanner'));
    }

    public function brandProducts($slug)
    {
        $brand   = Brands::where('slug', $slug)->firstOrFail();
        $page    = max(1, (int) request()->input('page', 1));
        $perPage = 20;

        $products = \App\Models\Products::with(['category:id,slug', 'subcategories:id,slug'])
            ->select('id', 'name', 'slug', 'file_name', 'status', 'created_at', 'category_id', 'sub_category_id', 'brand_id')
            ->where('brand_id', $brand->id)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedProducts = $products->through(function ($product) {
            return [
                'id'               => $product->id,
                'image'            => $product->file_name ? asset('uploads/products/' . $product->file_name) : asset('assets/images/dummy_product.webp'),
                'name'             => $product->name,
                'slug'             => $product->slug,
                'active'           => (bool) $product->status,
                'rohs_compliant'   => false,
                'created_at'       => $product->created_at->toDateTimeString(),
                'category_slug'    => $product->category->slug ?? 'uncategorized',
                'subcategory_slug' => $product->subcategories->slug ?? 'uncategorized',
            ];
        });

        $brands        = \App\Models\Brands::select('id', 'name')->get()->toArray();
        $categories    = \App\Models\Category::select('id', 'name')->get()->toArray();
        $subCategories = \App\Models\SubCategory::select('id', 'name')->get()->toArray();

        $productPageFilter = [
            ['head' => 'Manufacturer', 'data' => array_map(fn($b) => ['id' => $b['id'], 'name' => $b['name']], $brands)],
            ['head' => 'Product Type', 'data' => array_map(fn($c) => ['id' => $c['id'], 'name' => $c['name']], $categories)],
            ['head' => 'Sub Product Type', 'data' => array_map(fn($s) => ['id' => $s['id'], 'name' => $s['name']], $subCategories)],
        ];

        return Inertia::render('Products/List', [
            'ProductBanner'     => asset('assets/banners/banner.jpg'),
            'productPageFilter' => $productPageFilter,
            'products'          => $formattedProducts,
            'brands'            => $brands,
            'categories'        => $categories,
            'subCategories'     => $subCategories,
            'selectedFilters'   => [
                'manufacturer'     => [$brand->id],
                'productType'      => [],
                'subCategory'      => [],
                'page'             => $page,
                'search'           => '',
                'active'           => false,
                'rohsCompliant'    => false,
                'newProducts'      => false,
                'attributeFilters' => [],
            ],
        ]);
    }
}

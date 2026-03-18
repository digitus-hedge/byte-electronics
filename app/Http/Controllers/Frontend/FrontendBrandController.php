<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Brands;
use App\Models\Category;
use Illuminate\Support\Facades\DB;


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
    $firstBrand    = $groupedBrands->first()[0] ?? null;
    $brandBanner   = isset($firstBrand['banner'])
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

        $topLevel = [];
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
            ->select('id', 'name', 'slug', 'file_name', 'price', 'description', 'category_id', 'sub_category_id')
            ->where('brand_id', $brandId)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->whereIn('category_id', $categoryIds)
            ->get();

        // Group products by sub_category_id and category_id
        $productsBySub = [];
        $productsByCategory = [];

        foreach ($products as $product) {
            if ($product->sub_category_id) {
                $productsBySub[$product->sub_category_id][] = [
                    'name' => $product->name,
                    'url'  => '/products/' . $product->slug,
                ];
            } else {
                $productsByCategory[$product->category_id][] = [
                    'name' => $product->name,
                    'url'  => '/products/' . $product->slug,
                ];
            }
        }

        // Step 5: Build response
        $categories_brand = [];

        foreach ($categories as $category) {
            $subs = $topLevel[$category->id] ?? [];
            $items = [];

            foreach ($subs as $sub) {
                $subItem = $this->buildBrandSubTree($sub, $childrenByParent, $productsBySub);
                if (!empty($subItem['items'])) {
                    $items[] = $subItem;
                }
            }

            // Category-level products
            if (!empty($productsByCategory[$category->id])) {
                $items[] = [
                    'name'  => 'General Products',
                    'url'   => '/products/filter?category=' . $category->id,
                    'items' => $productsByCategory[$category->id],
                ];
            }

            if (!empty($items)) {
                $categories_brand[] = [
                    'name'  => $category->name,
                    'url'   => '/products/filter?category=' . $category->id,
                    'items' => $items,
                ];
            }
        }

        return Inertia::render('Brands/Details', [
            'brand' => $this->formatBrandResponse($brandDetails, $categories_brand)
        ]);
    }

    private function buildBrandSubTree($sub, &$childrenByParent, &$productsBySub)
    {
        $children = $childrenByParent[$sub->id] ?? [];
        $items = [];

        // Add child subcategories
        foreach ($children as $child) {
            $childItem = $this->buildBrandSubTree($child, $childrenByParent, $productsBySub);
            if (!empty($childItem['items'])) {
                $items[] = $childItem;
            }
        }

        // Add products for this subcategory
        if (!empty($productsBySub[$sub->id])) {
            $items = array_merge($items, $productsBySub[$sub->id]);
        }

        return [
            'name'  => $sub->name,
            'url'   => '/products/filter?productType%5B0%5D=' . $sub->id,
            'items' => $items,
        ];
    }

    private function formatBrandResponse($brandDetails, $categories_brand)
    {
        return [
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
                'products' => function ($query) use ($brandDetails) {
                    $query->where('brand_id', $brandDetails->id)
                        ->where('status', 1)
                        ->select('id', 'name', 'slug', 'file_name', 'price', 'description');
                }
            ])
            ->where('status', 1)
            ->get()
            ->map(function ($category) {
                $categoryData = [
                    'name' => $category->name,
                    'url' => '/products/filter?category=' . $category->id,
                    'items' => []
                ];

                // Add top-level subcategories with their products and nested subcategories
                foreach ($category->subcategories as $subcategory) {
                    $categoryData['items'][] = $this->formatSubcategory($subcategory);
                }

                // Add category-level products as a pseudo-subcategory
                if ($category->products->isNotEmpty()) {
                    $categoryData['items'][] = [
                        'name' => 'General Products',
                        'url' => '/products/filter?category=' . $category->id,
                        'items' => $category->products->map(function ($product) {
                            return [
                                'name' => $product->name,
                                'url' => '/products/' . $product->slug,
                            ];
                        })->toArray()
                    ];
                }

                return $categoryData;
            })->filter(function ($category) {
                return !empty($category['items']);
            })->values()->toArray();

        $brand = [
            'name' => $brandDetails->name,
            'image' => asset('uploads/brand/' . $brandDetails->file_name),
            'id' => $brandDetails->id,
            'tabs' => [
                [
                    'key' => 'about',
                    'label' => 'About',
                    'content' => $brandDetails->description ?? 'No description available.',
                ],
                [
                    'key' => 'product',
                    'label' => 'Product Line',
                    'content' => $categories_brand,
                ],
                [
                    'key' => 'support',
                    'label' => 'Resources & Support',
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
            'name' => $subcategory->name,
            'url' => '/products/filter?subcategory=' . $subcategory->id,
            'items' => []
        ];

        // Add products under this subcategory
        if ($subcategory->products->isNotEmpty()) {
            $subcategoryData['items'] = array_merge(
                $subcategoryData['items'],
                $subcategory->products->map(function ($product) {
                    return [
                        'name' => $product->name,
                        'url' => '/products/' . $product->slug,
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
}

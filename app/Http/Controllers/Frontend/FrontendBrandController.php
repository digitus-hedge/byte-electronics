<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Brands;
use App\Models\Category;

class FrontendBrandController extends Controller
{
    public function index()
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
   $brands = Brands::select('name', 'slug')->get();
   $brands->transform(function ($brand) {
    $brand->url = url("brands/details/{$brand->slug}");
    return $brand;
});
   // Group the brands by the first letter of their name
   $groupedBrands = $brands->groupBy(function ($brand) {
       $firstLetter = strtoupper(substr($brand->name, 0, 1));
       return preg_match('/[A-Z]/', $firstLetter) ? $firstLetter : '#';
   });

   // Fetch the banner file name from the first brand (or any specific logic you need)
   $bannerFileName = $brands->isNotEmpty() ? $brands->first()->banner : null;

   // Construct the full URL for the banner
   $brandBanner = $bannerFileName ? asset('uploads/brand/banner/' . $bannerFileName) : null;

   return Inertia::render('Brands/List', [
       'brandBanner'   => $brandBanner,
       'manufacturers' => $groupedBrands,
   ]);
}

public function details($slug)
{
    $brandDetails = Brands::where('slug', $slug)->firstOrFail();

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
        return Inertia::render('Brands/ProductLine',compact('productLineBanner'));
    }

}

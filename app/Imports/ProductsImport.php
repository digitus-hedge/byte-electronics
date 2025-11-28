<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brands;
use App\Models\Products;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProductsImport implements ToModel ,WithHeadingRow
{
   
public function model(array $row)
{
    try {
        // Start DB transaction for this row
        DB::beginTransaction();

        // 1. Handle brand
        $brandName = trim($row['brand_id'] ?? '');
        if (empty($brandName)) {
            throw new \Exception("Brand name missing");
        }

        $brand = Brands::firstOrCreate(
            ['name' => $brandName],
            [
                'slug' => Str::slug($brandName),
                'file_name' => $brandName . '.png',
                'org_name' => 'brands/' . $brandName . '.png',
                'type' => 'null',
                'description' => 'description',
                'meta_tag' => 'meta_tag',
                'meta_description' => 'meta_description',
                'status' => 1,
                'featured' => 0,
                'banner' => null,
                'secondary_logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $brandId = $brand->id;

        // 2. Handle categories
        $categoryId = null;
        $subCategoryId = null;
        $breadcrumb = json_decode($row['category_id'] ?? '', true);

        if (is_array($breadcrumb) && count($breadcrumb) > 1) {
            $mainCategoryName = trim($breadcrumb[1]);
            $mainCategory = Category::firstOrCreate(
                ['name' => $mainCategoryName],
                [
                    'slug' => Str::slug($mainCategoryName),
                    'file_name' => $mainCategoryName . '.png',
                    'org_name' => 'categories/' . $mainCategoryName . '.png',
                    'type' => 'null',
                    'description' => 'description',
                    'meta_tag' => 'meta_tag',
                    'meta_description' => 'meta_description',
                    'status' => 1,
                    'featured' => 0,
                ]
            );
            $categoryId = $mainCategory->id;

            $parentId = $categoryId;

            for ($i = 2; $i < count($breadcrumb); $i++) {
                $subCatName = trim($breadcrumb[$i]);
                $subCategory = SubCategory::firstOrCreate(
                    ['name' => $subCatName],
                    [
                        'slug' => Str::slug($subCatName),
                        'parent_id' => $parentId,
                        'level' => 1,
                        'category_id' => $categoryId,
                        'description' => 'description',
                        'status' => 1,
                        'meta_tag' => 'meta_tag',
                        'meta_description' => 'meta_description',
                        'image_sub_cat' => null,
                        'product_sub_cat' => null,
                    ]
                );
                $subCategoryId = $subCategory->id;
                $parentId = $subCategoryId;
            }
        } else {
            throw new \Exception("Invalid or missing category breadcrumb");
        }

        // 3. Check for duplicate product
        $productName = trim($row['name']);
        $productSlug = Str::slug($productName);
        
        if (Products::where('slug', $productSlug)->exists()) {
            throw new \Exception("Duplicate product slug: $productSlug");
        }

      
        $product = Products::updateOrCreate(
            ['slug' => $productSlug],
            [
                'name' => $productName,
                'brand_id' => $brandId,
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'manufacturers_no' => trim($row['name']),
                'price' => null,
                'file_name' => $productName . '.png',
                'org_name' => 'products/' . $productName . '.png',
                'description' => 'static description',
                'more_description' => 'static more description',
                'meta_tag' => 'meta_tag',
                'meta_description' => 'meta_description',
                'meta_title' => 'meta_title',
                'tag' => 'tag',
                'minimum_qty' => 1,
                'status' => 1,
                'featured' => 0,
                'is_repairable' => 0,
                'best_seller' => 0,
            ]
        );
        Log::info("Saved product data:", $product->toArray());

        DB::commit(); // All good, commit transaction
        return $product;

    } catch (\Throwable $e) {
        DB::rollBack(); // Something went wrong, rollback changes

        // Optionally log the row or error
        Log::error("Import error in row: " . json_encode($row));
        Log::error($e->getMessage());

        return null; // skip this row
    }
}

   
}
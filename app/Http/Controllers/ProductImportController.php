<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Products;
use App\Models\ProductExtended;
use App\Models\ProductAttribute;
use App\Models\ProductHeader;
use App\Models\ProductPrice;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brands;
use App\Models\Currency;
use App\Models\ProductAttrDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductImportController extends Controller
{
    /**
     * Import all products from product_import_data without downloading images/datasheets.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function importProducts()
    {
        Log::info('Starting product import process from product_import_data (Phase 1)');

        $totalProducts = DB::table('product_import_data')->count();
        Log::info('Total products to process', ['total' => $totalProducts]);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        if ($totalProducts === 0) {
            Log::warning('No products found in product_import_data');
            return response()->json(['message' => 'No products to import'], 200);
        }

        try {
            // Process products in chunks of 100
            DB::table('product_import_data')->orderBy('product_id')->chunk(100, function ($products) use (&$successCount, &$errorCount, &$errors, $totalProducts) {
                Log::info('Processing chunk', ['chunk_size' => count($products)]);
                foreach ($products as $index => $productData) {
                    $productArray = (array)$productData;
                    Log::info('Processing product', [
                        'index' => $index + 1,
                        'total' => $totalProducts,
                        'product_id' => $productArray['product_id'] ?? 'unknown',
                        'manufacturer_no' => $productArray['manufacturer_part_number'] ?? 'unknown',
                        'description' => $productArray['description'] ?? 'unknown',
                        'breadcrumb' => $productArray['breadcrumb'] ?? 'unknown',
                        'manufacturer' => $productArray['manufacturer'] ?? 'unknown'
                    ]);

                    // Validate required fields
                    if (empty($productArray['manufacturer_part_number']) || $productArray['manufacturer_part_number'] === 'UNKNOWN') {
                        $errorCount++;
                        $errorMsg = 'Missing or invalid manufacturer_part_number';
                        Log::error($errorMsg, ['product_id' => $productArray['product_id'] ?? 'unknown']);
                        $errors[] = ['product_id' => $productArray['product_id'] ?? 'unknown', 'error' => $errorMsg];
                        continue;
                    }

                    $result = $this->processProduct($productArray);
                    if ($result['success']) {
                        $successCount++;
                        Log::info('Product processed successfully', [
                            'product_id' => $productArray['product_id'],
                            'manufacturer_no' => $productArray['manufacturer_part_number']
                        ]);
                    } else {
                        $errorCount++;
                        Log::error('Failed to import product', [
                            'product_id' => $productArray['product_id'] ?? 'unknown',
                            'manufacturer_no' => $productArray['manufacturer_part_number'] ?? 'unknown',
                            'error' => $result['message']
                        ]);
                        $errors[] = [
                            'product_id' => $productArray['product_id'] ?? 'unknown',
                            'manufacturer_no' => $productArray['manufacturer_part_number'] ?? 'unknown',
                            'error' => $result['message']
                        ];
                    }
                }
            });

            $message = "Product import completed (Phase 1): {$successCount} succeeded, {$errorCount} failed";
            Log::info($message, ['total_products' => $totalProducts]);
            return response()->json(['message' => $message, 'errors' => $errors], $errorCount > 0 ? 207 : 200);
        } catch (\Exception $e) {
            Log::error('Import failed due to exception (Phase 1)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download images and datasheets for all products and update relevant tables.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateImagesAndDatasheets()
    {
        Log::info('Starting image and datasheet update process (Phase 2)');

        $products = Products::with(['category', 'subCategory', 'brand'])->get();
        $totalProducts = $products->count();
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        if ($totalProducts === 0) {
            Log::warning('No products found in products table');
            return response()->json(['message' => 'No products to process'], 200);
        }

        try {
            foreach ($products as $index => $product) {
                Log::info('Processing images and datasheet', [
                    'index' => $index + 1,
                    'total' => $totalProducts,
                    'product_id' => $product->id,
                    'manufacturer_no' => $product->manufacturers_no
                ]);

                // Fetch imageUrls and datasheetUrl
                $imageAttr = ProductAttrDocument::whereHas('extended', function ($query) use ($product) {
                    $query->where('product_id', $product->id)
                          ->whereHas('attribute', function ($q) {
                              $q->where('name', 'Product Image')
                                ->whereHas('header', function ($h) {
                                    $h->where('name', 'Images');
                                });
                          });
                })->first();

                $datasheetAttr = ProductAttrDocument::whereHas('extended', function ($query) use ($product) {
                    $query->where('product_id', $product->id)
                          ->whereHas('attribute', function ($q) {
                              $q->where('name', 'Datasheet')
                                ->whereHas('header', function ($h) {
                                    $h->where('name', 'Documents');
                                });
                          });
                })->first();

                $imageUrl = $imageAttr->value ?? null;
                $datasheetUrl = $datasheetAttr->value ?? null;

                // Download image
                $imagePath = null;
                if ($imageUrl && $imageUrl !== 'pending') {
                    $imagePath = $this->downloadImage(
                        json_encode([$imageUrl]),
                        $product->manufacturers_no,
                        'products'
                    );
                    if ($imagePath) {
                        $product->file_name = $imagePath;
                        $product->org_name = $imagePath;
                        $product->save();

                        if ($product->category) {
                            $this->storeCategoryImage($product->category->id, $product->category->name, $imagePath);
                        }
                        if ($product->subCategory) {
                            $this->storeSubCategoryImage($product->subCategory->id, $product->subCategory->name, $imagePath);
                        }
                        if ($product->brand) {
                            $this->storeBrandImage($product->brand->id, $product->brand->name, $imagePath);
                        }

                        $imageAttr->value = $imagePath;
                        $imageAttr->save();

                        Log::info('Image updated successfully', [
                            'product_id' => $product->id,
                            'image_path' => $imagePath
                        ]);
                    } else {
                        Log::warning('Image download failed', [
                            'product_id' => $product->id,
                            'image_url' => $imageUrl
                        ]);
                        $errors[] = [
                            'product_id' => $product->id,
                            'manufacturer_no' => $product->manufacturers_no,
                            'error' => 'Failed to download image: ' . $imageUrl
                        ];
                    }
                } else {
                    Log::warning('No image URL available', ['product_id' => $product->id]);
                    $product->file_name = 'Uploads/products/default.jpg';
                    $product->org_name = 'Uploads/products/default.jpg';
                    $product->save();
                }

                // Download datasheet
                $datasheetPath = null;
                if ($datasheetUrl && $datasheetUrl !== 'pending') {
                    $datasheetPath = $this->downloadDatasheet(
                        $datasheetUrl,
                        $product->manufacturers_no
                    );
                    if ($datasheetPath) {
                        $datasheetAttr->value = $datasheetPath;
                        $datasheetAttr->save();
                        Log::info('Datasheet updated successfully', [
                            'product_id' => $product->id,
                            'datasheet_path' => $datasheetPath
                        ]);
                    } else {
                        Log::warning('Datasheet download failed', [
                            'product_id' => $product->id,
                            'datasheet_url' => $datasheetUrl
                        ]);
                        $errors[] = [
                            'product_id' => $product->id,
                            'manufacturer_no' => $product->manufacturers_no,
                            'error' => 'Failed to download datasheet: ' . $datasheetUrl
                        ];
                    }
                } else {
                    Log::warning('No datasheet URL available', ['product_id' => $product->id]);
                }

                $successCount++;
            }

            $message = "Image and datasheet update completed (Phase 2): {$successCount} succeeded, {$errorCount} failed";
            Log::info($message, ['total_products' => $totalProducts]);
            return response()->json(['message' => $message, 'errors' => $errors], $errorCount > 0 ? 207 : 200);
        } catch (\Exception $e) {
            Log::error('Image and datasheet update failed due to exception (Phase 2)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Process a single product and save to the live database.
     *
     * @param array $productData
     * @return array
     */
    private function processProduct(array $productData)
    {
        try {
            Log::info('Starting processProduct', [
                'product_id' => $productData['product_id'] ?? 'unknown',
                'manufacturer_no' => $productData['manufacturer_part_number'] ?? 'unknown',
                'description' => $productData['description'] ?? 'unknown',
                'breadcrumb' => $productData['breadcrumb'] ?? 'unknown',
                'manufacturer' => $productData['manufacturer'] ?? 'unknown'
            ]);

            // Step 1: Process Brand
            $brandName = $productData['manufacturer'] ?? 'Unknown Brand';
            Log::info('Processing brand', ['brand_name' => $brandName]);
            $brand = Brands::firstOrCreate(
                ['name' => $brandName],
                [
                    'slug' => $this->generateUniqueSlug($brandName, Brands::class),
                    'description' => '',
                    'status' => 1,
                    'file_name' => 'uploads/brand/default.jpg',
                    'org_name' => 'uploads/brand/default.jpg'
                ]
            );
            Log::info('Brand processed', ['brand_id' => $brand->id, 'brand_name' => $brandName]);

            // Step 2: Process Category and SubCategory
            $breadcrumbParts = explode(' / ', $productData['breadcrumb'] ?? '');
            array_shift($breadcrumbParts); // Remove first part
            array_pop($breadcrumbParts); // Remove last part
            $mainCategoryName = $breadcrumbParts[0] ?? 'Uncategorized';
            $subCategoryName = isset($breadcrumbParts[1]) ? $breadcrumbParts[1] : null;

            Log::info('Processing category', ['category_name' => $mainCategoryName]);
            $category = Category::firstOrCreate(
                ['name' => $mainCategoryName],
                [
                    'slug' => $this->generateUniqueSlug($mainCategoryName, Category::class),
                    'description' => '',
                    'status' => 1,
                    'file_name' => 'Uploads/category/default.jpg',
                    'org_name' => 'Uploads/category/default.jpg'
                ]
            );
            Log::info('Category processed', ['category_id' => $category->id, 'category_name' => $mainCategoryName]);

            $subCategoryId = 0;
            if ($subCategoryName) {
                Log::info('Processing subcategory', ['subcategory_name' => $subCategoryName]);
                $subCategory = SubCategory::firstOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $subCategoryName
                    ],
                    [
                        'slug' => $this->generateUniqueSlug($subCategoryName, SubCategory::class),
                        'description' => '',
                        'status' => 1,
                        'image_sub_cat' => 'Uploads/subcategory/default.jpg'
                    ]
                );
                $subCategoryId = $subCategory->id;
                Log::info('SubCategory processed', ['subcategory_id' => $subCategoryId, 'subcategory_name' => $subCategoryName]);
            }

            // Step 3: Process Product
            $basePrice = $this->getBasePrice($productData['pricing'] ?? '[]');
            Log::info('Calculated base price', [
                'product_id' => $productData['product_id'],
                'base_price' => $basePrice
            ]);

            $productDataForInsert = [
                'name' => $productData['description'] ?? 'Untitled Product',
                'slug' => $this->generateUniqueSlug($productData['description'] ?? 'untitled-product', Products::class),
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'sub_category_id' => $subCategoryId,
                'file_name' => 'pending',
                'org_name' => $this->cleanImageUrl($productData['imageUrls'] ?? '[]'),
                'manufacturers_no' => $productData['manufacturer_part_number'] ?? 'UNKNOWN',
                'price' => $basePrice,
                'description' => $productData['description'] ?? '',
                'more_description' => $productData['more_info_description'] ?? '',
                'meta_title' => $productData['more_info_title'] ?? $productData['description'] ?? '',
                'meta_description' => $productData['more_info_description'] ?? $productData['description'] ?? '',
                'tag' => '',
                'minimum_qty' => 1,
                'is_repairable' => 0,
                'featured' => 0,
                'status' => 1
            ];

            Log::info('Attempting to create product', [
                'product_id' => $productData['product_id'],
                'data' => $productDataForInsert
            ]);

            $product = Products::create($productDataForInsert);

            Log::info('Product created successfully', [
                'product_id' => $product->id,
                'manufacturer_no' => $productData['manufacturer_part_number'],
                'db_id' => $product->id
            ]);

            // Step 4: Process Related Data
            $this->processImages($product, $productData['imageUrls'] ?? '[]', $productData['imageDetails'] ?? '');
            $this->processSpecifications($product, $productData['specifications'] ?? '', $productData);
            $this->processDocuments($product, $productData['documents'] ?? '[]', $productData['datasheetUrl'] ?? '');
            $this->processCompliance($product, $productData['compliance'] ?? '');
            $this->processPricing($product, $productData['pricing'] ?? '[]');
            $this->processMoreInformation($product, $productData);
            $this->processPartInformation($product, $productData);

            return ['success' => true, 'message' => 'Product processed successfully'];
        } catch (\Exception $e) {
            Log::error('Exception in processProduct', [
                'product_id' => $productData['product_id'] ?? 'unknown',
                'manufacturer_no' => $productData['manufacturer_part_number'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'Failed to process product: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate a unique slug for a model.
     *
     * @param string $name
     * @param string $model
     * @return string
     */
    private function generateUniqueSlug($name, $model)
    {
        Log::info('Generating slug', ['name' => $name, 'model' => $model]);
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while ($model::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        Log::info('Generated slug', ['slug' => $slug]);
        return $slug;
    }

    /**
     * Process product images (store URLs, defer downloading).
     *
     * @param Products $product
     * @param string $imageUrls
     * @param string $imageDetails
     * @return void
     */
    private function processImages($product, $imageUrls, $imageDetails)
    {
        try {
            Log::info('Processing images', ['product_id' => $product->id]);
            $header = ProductHeader::firstOrCreate(['name' => 'Images']);
            Log::info('ProductHeader created', [
                'header_id' => $header->id,
                'product_id' => $product->id
            ]);

            $imageUrl = $this->cleanImageUrl($imageUrls);
            if (!empty($imageUrl)) {
                $attribute = ProductAttribute::firstOrCreate(
                    [
                        'prd_header_id' => $header->id,
                        'name' => 'Product Image'
                    ],
                    ['value_type' => 'Image']
                );
                $extended = ProductExtended::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_attr_id' => $attribute->id,
                        'prd_header_id' => $header->id
                    ],
                    [
                        'value_type' => 'Image',
                        'value' => $imageUrl
                    ]
                );
                $attributeCounts = [];
                $attributeCounts['Product Image'] = ($attributeCounts['Product Image'] ?? 0) + 1;
                ProductAttrDocument::updateOrCreate(
                    [
                        'product_ext_id' => $extended->id,
                        'name' => 'Product Image',
                        'attrib_count' => $attributeCounts['Product Image']
                    ],
                    [
                        'value' => $imageUrl
                    ]
                );
                Log::info('Image URL stored', ['product_id' => $product->id, 'image_url' => $imageUrl]);
            } else {
                Log::warning('No image URL provided', ['product_id' => $product->id]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process images', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process product specifications and pricing metadata.
     *
     * @param Products $product
     * @param string $specifications
     * @param array $productData
     * @return void
     */
    private function processSpecifications($product, $specifications, $productData)
    {
        try {
            Log::info('Processing specifications', ['product_id' => $product->id]);
            $header = ProductHeader::firstOrCreate(['name' => 'Specifications']);
            Log::info('ProductHeader created', [
                'header_id' => $header->id,
                'product_id' => $product->id
            ]);

            $specsArray = $this->parseKeyValueString($specifications);
            $attributeCounts = [];

            foreach ($specsArray as $key => $value) {
                if ($key === '' || $value === '') {
                    Log::info('Skipping specification', [
                        'product_id' => $product->id,
                        'key' => $key
                    ]);
                    continue;
                }

                $attributeName = trim($key, ':');
                $attributeValue = ($attributeName === 'RoHS' && empty($value)) ? 'Yes' : trim($value, "': ");

                $attribute = ProductAttribute::firstOrCreate(
                    [
                        'prd_header_id' => $header->id,
                        'name' => $attributeName
                    ],
                    ['value_type' => 'Text']
                );
                $extended = ProductExtended::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_attr_id' => $attribute->id,
                        'prd_header_id' => $header->id
                    ],
                    [
                        'value_type' => 'Text',
                        'value' => $attributeValue
                    ]
                );
                $attributeCounts[$attributeName] = ($attributeCounts[$attributeName] ?? 0) + 1;
                ProductAttrDocument::updateOrCreate(
                    [
                        'product_ext_id' => $extended->id,
                        'name' => $attributeName,
                        'attrib_count' => $attributeCounts[$attributeName]
                    ],
                    [
                        'value' => $attributeValue
                    ]
                );
                Log::info('Specification stored', [
                    'product_id' => $product->id,
                    'attribute' => $attributeName
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process specifications', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process product documents (store URLs, defer downloading).
     *
     * @param Products $product
     * @param string $documents
     * @param string $datasheetUrl
     * @return void
     */
    private function processDocuments($product, $documents, $datasheetUrl)
    {
        try {
            Log::info('Processing documents', ['product_id' => $product->id]);
            $header = ProductHeader::firstOrCreate(['name' => 'Documents']);
            Log::info('ProductHeader created', [
                'header_id' => $header->id,
                'product_id' => $product->id
            ]);

            $attributeCounts = [];
            $docs = json_decode($documents, true);
            if (is_array($docs) && !empty($docs)) {
                foreach ($docs as $index => $doc) {
                    $attributeName = $doc['docType'] ?? 'Document';
                    $docUrl = $doc['docUrl'] ?? '';
                    if (empty($docUrl)) {
                        Log::warning('Skipping document with empty URL', [
                            'product_id' => $product->id,
                            'doc_index' => $index
                        ]);
                        continue;
                    }

                    $attributeCounts[$attributeName] = ($attributeCounts[$attributeName] ?? 0) + 1;
                    $attribute = ProductAttribute::firstOrCreate(
                        [
                            'prd_header_id' => $header->id,
                            'name' => $attributeName
                        ],
                        ['value_type' => 'Document']
                    );
                    $extended = ProductExtended::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'product_attr_id' => $attribute->id,
                            'prd_header_id' => $header->id
                        ],
                        ['value_type' => 'Document']
                    );
                    ProductAttrDocument::updateOrCreate(
                        [
                            'product_ext_id' => $extended->id,
                            'name' => $attributeName,
                            'attrib_count' => $attributeCounts[$attributeName]
                        ],
                        [
                            'value' => $docUrl
                        ]
                    );
                    Log::info('Document URL stored', [
                        'product_id' => $product->id,
                        'doc_type' => $attributeName
                    ]);
                }
            }

            if (!empty($datasheetUrl)) {
                $attributeName = 'Datasheet';
                $attributeCounts[$attributeName] = ($attributeCounts[$attributeName] ?? 0) + 1;
                $attribute = ProductAttribute::firstOrCreate(
                    [
                        'prd_header_id' => $header->id,
                        'name' => $attributeName
                    ],
                    ['value_type' => 'Document']
                );
                $extended = ProductExtended::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_attr_id' => $attribute->id,
                        'prd_header_id' => $header->id
                    ],
                    ['value_type' => 'Document']
                );
                ProductAttrDocument::updateOrCreate(
                    [
                        'product_ext_id' => $extended->id,
                        'name' => $attributeName,
                        'attrib_count' => $attributeCounts[$attributeName]
                    ],
                    [
                        'value' => $datasheetUrl
                    ]
                );
                Log::info('Datasheet URL stored', [
                    'product_id' => $product->id,
                    'datasheet_url' => $datasheetUrl
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process documents', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process product compliance data.
     *
     * @param Products $product
     * @param string $compliance
     * @return void
     */
    private function processCompliance($product, $compliance)
    {
        try {
            Log::info('Processing compliance', ['product_id' => $product->id]);
            $header = ProductHeader::firstOrCreate(['name' => 'Product Compliance']);
            Log::info('ProductHeader created', [
                'header_id' => $header->id,
                'product_id' => $product->id
            ]);

            $complianceArray = $this->parseKeyValueString($compliance);
            if (empty($complianceArray)) {
                Log::warning('No compliance data parsed', ['product_id' => $product->id]);
                return;
            }

            $attributeCounts = [];
            foreach ($complianceArray as $key => $value) {
                if ($key === '' || $value === '') {
                    Log::info('Skipping compliance', [
                        'product_id' => $product->id,
                        'key' => $key
                    ]);
                    continue;
                }

                $attributeName = trim($key, ':');
                $attributeValue = trim($value, "': ");
                $attribute = ProductAttribute::firstOrCreate(
                    [
                        'prd_header_id' => $header->id,
                        'name' => $attributeName
                    ],
                    ['value_type' => 'Text']
                );
                $extended = ProductExtended::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_attr_id' => $attribute->id,
                        'prd_header_id' => $header->id
                    ],
                    [
                        'value_type' => 'Text',
                        'value' => $attributeValue
                    ]
                );
                $attributeCounts[$attributeName] = ($attributeCounts[$attributeName] ?? 0) + 1;
                ProductAttrDocument::updateOrCreate(
                    [
                        'product_ext_id' => $extended->id,
                        'name' => $attributeName,
                        'attrib_count' => $attributeCounts[$attributeName]
                    ],
                    [
                        'value' => $attributeValue
                    ]
                );
                Log::info('Compliance stored', [
                    'product_id' => $product->id,
                    'attribute' => $attributeName
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process compliance', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process product pricing data.
     *
     * @param Products $product
     * @param string $pricingData
     * @return void
     */
    private function processPricing($product, $pricingData)
    {
        try {
            Log::info('Processing pricing', ['product_id' => $product->id]);
            if (empty($pricingData) || $pricingData === '[]') {
                Log::warning('Empty pricing data', ['product_id' => $product->id]);
                return;
            }

            $cleanedPricingData = stripslashes(trim($pricingData, '"'));
            $prices = json_decode($cleanedPricingData, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($prices) || empty($prices)) {
                Log::warning('Failed to decode pricing JSON, attempting text parsing', [
                    'product_id' => $product->id,
                    'json_error' => json_last_error_msg()
                ]);
                $prices = $this->parsePricingText($cleanedPricingData);
                if (empty($prices)) {
                    Log::warning('No pricing data extracted', ['product_id' => $product->id]);
                    return;
                }
            }

            foreach ($prices as $index => $price) {
                if (!isset($price['quantity']) || !isset($price['unitPrice']) || !isset($price['extendedPrice']) || !isset($price['unitType'])) {
                    Log::warning('Incomplete pricing entry', [
                        'product_id' => $product->id,
                        'index' => $index
                    ]);
                    continue;
                }

                $quantity = $price['quantity'];
                $unitPrice = str_replace('$', '', $price['unitPrice']);
                $totalPrice = str_replace('$', '', $price['extendedPrice']);
                $unitType = $price['unitType'];

                $priceRecord = ProductPrice::create([
                    'product_id' => $product->id,
                    'unit_name' => $unitType,
                    'qty' => $quantity,
                    'single_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'currency_name' => 'USD',
                    'currency_symbol' => '$',
                    'currency_id' => 2
                ]);
                Log::info('ProductPrice created', [
                    'price_id' => $priceRecord->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process pricing', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process more information data.
     *
     * @param Products $product
     * @param array $productData
     * @return void
     */
    private function processMoreInformation($product, $productData)
    {
        try {
            Log::info('Processing more information', ['product_id' => $product->id]);
            $header = ProductHeader::firstOrCreate(['name' => 'More Information']);
            Log::info('ProductHeader created', [
                'header_id' => $header->id,
                'product_id' => $product->id
            ]);

            $attributeCounts = [];
            $moreInfoFields = [
                'Title' => $productData['more_info_title'] ?? '',
                'Description' => $productData['more_info_description'] ?? '',
                'Image Alt Text' => $productData['more_info_image_alt'] ?? ''
            ];

            foreach ($moreInfoFields as $key => $value) {
                if (empty($value)) {
                    Log::info('Skipping more information field', [
                        'product_id' => $product->id,
                        'key' => $key
                    ]);
                    continue;
                }

                $attribute = ProductAttribute::firstOrCreate(
                    [
                        'prd_header_id' => $header->id,
                        'name' => $key
                    ],
                    ['value_type' => 'Text']
                );
                $extended = ProductExtended::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_attr_id' => $attribute->id,
                        'prd_header_id' => $header->id
                    ],
                    [
                        'value_type' => 'Text',
                        'value' => $value
                    ]
                );
                $attributeCounts[$key] = ($attributeCounts[$key] ?? 0) + 1;
                ProductAttrDocument::updateOrCreate(
                    [
                        'product_ext_id' => $extended->id,
                        'name' => $key,
                        'attrib_count' => $attributeCounts[$key]
                    ],
                    [
                        'value' => $value
                    ]
                );
                Log::info('More information stored', [
                    'product_id' => $product->id,
                    'attribute' => $key
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process more information', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process part information (lifecycle).
     *
     * @param Products $product
     * @param array $productData
     * @return void
     */
    private function processPartInformation($product, $productData)
    {
        try {
            Log::info('Processing part information', ['product_id' => $product->id]);
            $header = ProductHeader::firstOrCreate(['name' => 'Part Information']);
            Log::info('ProductHeader created', [
                'header_id' => $header->id,
                'product_id' => $product->id
            ]);

            $attributeCounts = [];
            if (!empty($productData['lifecycle'])) {
                $attribute = ProductAttribute::firstOrCreate(
                    [
                        'prd_header_id' => $header->id,
                        'name' => 'Lifecycle'
                    ],
                    ['value_type' => 'Text']
                );
                $extended = ProductExtended::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_attr_id' => $attribute->id,
                        'prd_header_id' => $header->id
                    ],
                    [
                        'value_type' => 'Text',
                        'value' => $productData['lifecycle']
                    ]
                );
                $attributeCounts['Lifecycle'] = ($attributeCounts['Lifecycle'] ?? 0) + 1;
                ProductAttrDocument::updateOrCreate(
                    [
                        'product_ext_id' => $extended->id,
                        'name' => 'Lifecycle',
                        'attrib_count' => $attributeCounts['Lifecycle']
                    ],
                    [
                        'value' => $productData['lifecycle']
                    ]
                );
                Log::info('Part information stored', [
                    'product_id' => $product->id,
                    'attribute' => 'Lifecycle'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process part information', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Parse pricing data from text when JSON decode fails.
     *
     * @param string $pricingData
     * @return array
     */
    private function parsePricingText($pricingData)
    {
        Log::info('Parsing pricing text', ['pricingData' => substr($pricingData, 0, 100)]);
        $prices = [];
        $pattern = "/\{'quantity':\s*'([^']+)',\s*'unitType':\s*'([^']+)',\s*'unitPrice':\s*'\$([\d,.]+)',\s*'extendedPrice':\s*'\$([\d,.]+)'\}/";
        preg_match_all($pattern, $pricingData, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $quantity = $match[1];
            $unitType = $match[2];
            $unitPrice = $match[3];
            $extendedPrice = $match[4];

            $unitPrice = str_replace(',', '', $unitPrice);
            $extendedPrice = str_replace(',', '', $extendedPrice);

            $prices[] = [
                'quantity' => $quantity,
                'unitType' => $unitType,
                'unitPrice' => '$' . $unitPrice,
                'extendedPrice' => '$' . $extendedPrice
            ];
        }

        Log::info('Parsed pricing', ['prices' => $prices]);
        return $prices;
    }

    /**
     * Download product image.
     *
     * @param string $imageUrls
     * @param string $manufacturerPartNo
     * @param string $type
     * @return string|null
     */
    private function downloadImage($imageUrls, $manufacturerPartNo, $type = 'products')
    {
        Log::info('Downloading image', [
            'imageUrls' => $imageUrls,
            'type' => $type,
            'manufacturer_no' => $manufacturerPartNo
        ]);

        $cleanedImageUrls = trim($imageUrls, "[]'\"");
        if (empty($cleanedImageUrls)) {
            Log::warning('Empty image URLs', [
                'type' => $type,
                'manufacturer_no' => $manufacturerPartNo
            ]);
            return null;
        }

        $urls = json_decode($imageUrls, true);
        $imageUrl = (json_last_error() === JSON_ERROR_NONE && is_array($urls) && !empty($urls)) ? $urls[0] : $cleanedImageUrls;

        if (empty($imageUrl)) {
            Log::warning('No valid image URL', [
                'type' => $type,
                'manufacturer_no' => $manufacturerPartNo
            ]);
            return null;
        }

        $baseUrl = 'https://www.mouser.com';
        $fullUrl = str_starts_with($imageUrl, 'http') ? $imageUrl : $baseUrl . (str_starts_with($imageUrl, '/') ? $imageUrl : '/' . $imageUrl);

        $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $filename = Str::slug($manufacturerPartNo) . '.' . $extension;

        $path = match ($type) {
            'products' => 'uploads/products/' . $filename,
            'attribute' => 'uploads/products/attribute/' . $filename,
            'brand' => 'uploads/brand/' . $filename,
            'category' => 'uploads/category/' . $filename,
            'subcategory' => 'uploads/subcategory/' . $filename,
            default => 'uploads/products/' . $filename,
        };

        try {
            $cacertPath = storage_path('app/cacert.pem');
            $httpOptions = ['timeout' => 30];
            if (!file_exists($cacertPath)) {
                Log::warning('CA bundle not found', ['path' => $cacertPath]);
                $httpOptions['verify'] = false;
            } else {
                $httpOptions['verify'] = $cacertPath;
            }

            $response = Http::retry(3, 100)->withOptions($httpOptions)->get($fullUrl);

            if ($response->successful()) {
                Storage::disk('public')->put($path, $response->body());
                Log::info('Image downloaded', [
                    'path' => $path,
                    'url' => $fullUrl
                ]);
                return $path;
            }

            Log::warning('Image download failed', [
                'url' => $fullUrl,
                'status' => $response->status()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Image download exception', [
                'url' => $fullUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Download product datasheet.
     *
     * @param string $datasheetUrl
     * @param string $manufacturerPartNo
     * @return string|null
     */
    private function downloadDatasheet($datasheetUrl, $manufacturerPartNo)
    {
        Log::info('Downloading datasheet', [
            'url' => $datasheetUrl,
            'manufacturer_no' => $manufacturerPartNo
        ]);

        if (!$datasheetUrl) {
            Log::warning('No datasheet URL', ['manufacturer_no' => $manufacturerPartNo]);
            return null;
        }

        $fullUrl = str_starts_with($datasheetUrl, 'http') ? $datasheetUrl : 'https://www.mouser.com' . $datasheetUrl;
        $extension = pathinfo($datasheetUrl, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = Str::slug($manufacturerPartNo) . '_datasheet.' . $extension;
        $path = 'Uploads/documents/' . $filename;

        try {
            $cacertPath = storage_path('app/cacert.pem');
            $httpOptions = ['timeout' => 30];
            if (!file_exists($cacertPath)) {
                Log::warning('CA bundle not found', ['path' => $cacertPath]);
                $httpOptions['verify'] = false;
            } else {
                $httpOptions['verify'] = $cacertPath;
            }

            $response = Http::retry(3, 100)->withOptions($httpOptions)->get($fullUrl);

            if ($response->successful()) {
                Storage::disk('public')->put($path, $response->body());
                Log::info('Datasheet downloaded', [
                    'path' => $path,
                    'url' => $fullUrl
                ]);
                return $path;
            }

            Log::warning('Datasheet download failed', [
                'url' => $fullUrl,
                'status' => $response->status()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Datasheet download exception', [
                'url' => $fullUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Store category image.
     *
     * @param int $categoryId
     * @param string $categoryName
     * @param string $imagePath
     * @return void
     */
    private function storeCategoryImage($categoryId, $categoryName, $imagePath)
    {
        try {
            Log::info('Storing category image', ['category_id' => $categoryId]);
            $category = Category::find($categoryId);
            if ($category && $imagePath) {
                $category->file_name = $imagePath;
                $category->org_name = $imagePath;
                $category->save();
                Log::info('Category image stored', [
                    'category_id' => $categoryId,
                    'file_name' => $imagePath
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to store category image', [
                'category_id' => $categoryId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store subcategory image.
     *
     * @param int|null $subCategoryId
     * @param string $subCategoryName
     * @param string $imagePath
     * @return void
     */
    private function storeSubCategoryImage($subCategoryId, $subCategoryName, $imagePath)
    {
        try {
            Log::info('Storing subcategory image', ['subcategory_id' => $subCategoryId]);
            if ($subCategoryId) {
                $subCategory = SubCategory::find($subCategoryId);
                if ($subCategory && $imagePath) {
                    $subCategory->image_sub_cat = $imagePath;
                    $subCategory->save();
                    Log::info('Subcategory image stored', [
                        'subcategory_id' => $subCategoryId,
                        'image_sub_cat' => $imagePath
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to store subcategory image', [
                'subcategory_id' => $subCategoryId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store brand image.
     *
     * @param int $brandId
     * @param string $brandName
     * @param string $imagePath
     * @return void
     */
    private function storeBrandImage($brandId, $brandName, $imagePath)
    {
        try {
            Log::info('Storing brand image', ['brand_id' => $brandId]);
            $brand = Brands::find($brandId);
            if ($brand && $imagePath) {
                $brand->file_name = $imagePath;
                $brand->org_name = $imagePath;
                $brand->save();
                Log::info('Brand image stored', [
                    'brand_id' => $brandId,
                    'file_name' => $imagePath
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to store brand image', [
                'brand_id' => $brandId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clean image URL for storage.
     *
     * @param string $imageUrls
     * @return string
     */
    private function cleanImageUrl($imageUrls)
    {
        Log::info('Cleaning image URL', ['imageUrls' => $imageUrls]);
        $cleaned = trim($imageUrls, "[]'\"");
        $urls = json_decode($imageUrls, true);
        $result = (json_last_error() === JSON_ERROR_NONE && is_array($urls) && !empty($urls)) ? $urls[0] : ($cleaned ?: 'pending');
        Log::info('Cleaned image URL', ['result' => $result]);
        return $result;
    }

    /**
     * Get base price from pricing data.
     *
     * @param string $pricingData
     * @return string
     */
    private function getBasePrice($pricingData)
    {
        try {
            Log::info('Getting base price', ['pricingData' => substr($pricingData, 0, 100)]);
            if (empty($pricingData) || $pricingData === '[]') {
                Log::warning('Empty pricing data');
                return '0.00';
            }

            $cleanedPricingData = stripslashes(trim($pricingData, '"'));
            $prices = json_decode($cleanedPricingData, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($prices) || empty($prices)) {
                $prices = $this->parsePricingText($cleanedPricingData);
                if (empty($prices)) {
                    Log::warning('No pricing data extracted');
                    return '0.00';
                }
            }

            foreach ($prices as $price) {
                if (isset($price['quantity']) && $price['quantity'] == '1' && isset($price['unitPrice'])) {
                    $cleanedPrice = str_replace('$', '', $price['unitPrice']);
                    if (!empty($cleanedPrice)) {
                        Log::info('Base price found', ['price' => $cleanedPrice]);
                        return $cleanedPrice;
                    }
                }
            }

            Log::warning('No unitPrice for quantity: 1');
            return '0.00';
        } catch (\Exception $e) {
            Log::error('Failed to get base price', [
                'error' => $e->getMessage()
            ]);
            return '0.00';
        }
    }

    /**
     * Parse key-value string into an array.
     *
     * @param string $string
     * @return array
     */
    private function parseKeyValueString($string)
    {
        Log::info('Parsing key-value string', ['string' => substr($string, 0, 100)]);
        if (empty($string)) {
            return [];
        }

        try {
            $string = trim($string, "{}");
            $pairs = preg_split('/,\s*(?=(?:(?:[^"]*"){2})*[^"]*$)/', $string);
            $result = [];

            foreach ($pairs as $pair) {
                if (preg_match('/^([^:]+):(.*)$/', $pair, $matches)) {
                    $key = trim($matches[1], "'\": ");
                    $value = trim($matches[2], "'\" ");
                    if ($key !== '') {
                        $result[$key] = $value;
                    }
                }
            }

            Log::info('Parsed key-value string', ['result' => $result]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to parse key-value string', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}

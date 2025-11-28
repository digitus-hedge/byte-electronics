<?php

namespace App\Imports;

use App\Models\ProductExtended;
use App\Models\ProductHeader;
use App\Models\Products;
use App\Models\ProductAttribute;
use App\Models\ProductAttrDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductSpecificationImport implements ToModel, WithHeadingRow
{
    protected ?Command $command;
    protected array $productNameCache = [];

    public function __construct(?Command $command = null)
    {
        $this->command = $command;
    }

    public function model(array $row)
    {
        try {
            // Validate required fields
            $productName = trim($row['product_id'] ?? '');
            $headerName = trim($row['prd_header_id'] ?? '');
            $attributeName = trim($row['product_attr_id'] ?? '');
            $value = trim($row['arr_value'] ?? '');

            if (empty($productName) || empty($headerName) || empty($attributeName) || empty($value)) {
                if ($this->command) {
                    $this->command->warn("⚠️ Skipping row with missing required fields: " . json_encode($row));
                }
                return null;
            }

            if ($this->command) {
                $this->command->info("🔄 Processing: Product - {$productName}, Header - {$headerName}, Attr - {$attributeName}, Value - {$value}");
            }

            // Cache product lookup to reduce queries
            if (!isset($this->productNameCache[$productName])) {
                $product = Products::where('name', $productName)->first();
                if (!$product) {
                    if ($this->command) {
                        $this->command->error("❌ Product not found: {$productName}");
                    }
                    return null;
                }
                $this->productNameCache[$productName] = $product;
            }

            $product = $this->productNameCache[$productName];

            // Get or create the specification header
            $header = ProductHeader::firstOrCreate(['name' => $headerName]);

            // Get or create the attribute under this header
            $attribute = ProductAttribute::firstOrCreate(
                [
                    'name' => $attributeName,
                    'prd_header_id' => $header->id,
                ],
                [
                    'value_type' => 'Text',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Check if this value already exists for this product + attribute
            $existingValue = ProductExtended::where('product_id', $product->id)
                ->where('prd_header_id', $header->id)
                ->where('product_attr_id', $attribute->id)
                ->whereHas('attrDocuments', function ($query) use ($value) {
                    $query->where('value', $value);
                })
                ->exists();

            if ($existingValue) {
                if ($this->command) {
                    $this->command->warn("⚠️ Duplicate value '{$value}' for attribute '{$attributeName}' on product '{$productName}' — skipping.");
                }
                return null;
            }

            // Create a new ProductExtended record
            $extended = ProductExtended::create([
                'product_id' => $product->id,
                'prd_header_id' => $header->id,
                'product_attr_id' => $attribute->id,
                'value_type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert the attribute value
            ProductAttrDocument::create([
                'product_ext_id' => $extended->id,
                'name' => $attribute->name,
                'value' => $value,
                'attribute_count' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($this->command) {
                $this->command->info("✅ Successfully added: {$attributeName} = {$value} to {$productName}");
            }

            return null;

        } catch (\Throwable $e) {
            Log::error('ProductSpecificationImport Error: ' . $e->getMessage(), [
                'row' => $row,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($this->command) {
                $this->command->error("❌ Error processing row: " . $e->getMessage());
            }

            return null;
        }
    }
}

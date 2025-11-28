<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use App\Models\Products;
use App\Models\ProductExtended;
use App\Models\ProductAttrDocument;
use App\Models\ProductAttribute;
use App\Models\ProductHeader;
use DB;

class ProductCompliance implements ToModel , WithHeadingRow
{
    protected ? Command $command;
    protected array $productNameCache = [];
    public function __construct(?Command $command = null)
    {
        $this->command = $command;
    }
    public function model(array $row)
    {
        try {
            $productName = trim($row['product_id'] ?? '');
            $product_attr = trim($row['product_attr'] ?? '');
            $value = trim($row['value'] ?? '');
            if (empty($productName)) return null;
            $product = Products::where('name', $productName)->first();
            if (!$product) {
                return null;
            }
            $header = ProductHeader::firstOrCreate(['name' => 'Product Compliance']);
            $attribute = ProductAttribute::firstOrCreate(
                [
                    'prd_header_id' => $header->id,
                    'name' => $product_attr,
                ],
                [
                    'value_type' => 'Text',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
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

            $extended = ProductExtended::create([
                'product_id' => $product->id,
                'prd_header_id' => $header->id,
                'product_attr_id' => $attribute->id,
                'value_type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            ProductAttrDocument::create([
                'product_ext_id' => $extended->id,
                'name' => $product_attr,
                'value' => $value,
                'attribute_count' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Log::info($extended);
        } catch (\Throwable $e) {
            Log::error('Error importing product compliance: ' . $e->getMessage());
            return null;
        }
    }
}





              
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

class ProductMoreInformation implements ToModel , WithHeadingRow
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
            if (empty($productName)) return null;
            $product = Products::where('name', $productName)->first();
            if (!$product) {
                return null;
            }
            $header = ProductHeader::firstOrCreate(['name' => 'More Information']);
            $array = [
                'image' => 'Image',
                'title' => 'text',
                'description' => 'text'
            ];
            $array = ['image' => 'Image','title' => 'text','description' => 'text'];
            foreach($array as $key => $_attributes){

                $ProductAttribute = ProductAttribute::firstOrCreate([
                    'prd_header_id' => $header->id,
                    'name' => $key,
                    'value_type' => $_attributes
                ]);

                $value =  '';
                if($ProductAttribute->name == 'image'){
                   $value = '/assets/images/dummy_product.webp';
                }
                if($ProductAttribute->name == 'title'){
                   $value =  $row['title'];
                }
                if($ProductAttribute->name == 'description'){
                   $value =  $row['description'];
                }
                // $existingValue = ProductExtended::where('product_id', $product->id)
                // ->where('prd_header_id', $header->id)
                // ->where('product_attr_id', $ProductAttribute->id)
                // ->whereHas('attrDocuments', function ($query) use ($value) {
                //     $query->where('value', $value);
                // })
                // ->exists();

                // if ($existingValue) {
                //     if ($this->command) {
                //         $this->command->warn("⚠️ Duplicate value '{$value}' for attribute '{$ProductAttribute->name}' on product '{$productName}' — skipping.");
                //     }
                //     return null;
                // }
                $productExtended = ProductExtended::create([
                    'product_id' => $product->id,
                    'product_attr_id' => $ProductAttribute->id,
                    'value_type' => $ProductAttribute->value_type,
                    'prd_header_id' => $ProductAttribute->prd_header_id,
                ]);
                $ProductAttrDocument =  ProductAttrDocument::create([
                   'product_ext_id' => $productExtended->id,
                   'name' => $ProductAttribute->name,
                   'value' => $value,
                   'attrib_count' => 1,
                ]);
               Log::info('ProductAttrDocument id : ' . $ProductAttrDocument->id);
            }
        } catch (\Throwable $e) {
            Log::error('Error importing product More information: ' . $e->getMessage());
            return null;
        }
    }
}





              
<?php

namespace App\Imports;



use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use App\Models\ProductPrice;
use App\Models\Products;
use Illuminate\Console\Command;


class ProductPriceImport implements ToModel, WithHeadingRow
{
    protected static $insertedRows = [];
    protected ?Command $command;


    public function __construct(?Command $command = null)
    {
        $this->command = $command;
    }

    public function model(array $row)
    {
        Log::info($row);
        try {
            $productName = trim($row['product_id'] ?? '');
            if (empty($productName)) return null;

            $product = Products::where('name', $productName)->first();
            if (!$product) {
                // Log::warning("Product not found for: {$productName}");
                // $this->console("❌ Product not found: {$productName}", 'warn');
                return null;
            }

            $productId = $product->id;
            $unitName = 'Normal';
            $qty = $row['qty'] ?? 0;

            // ✅ Extract exactly what's in the Excel, no recalculation
            $rawSingle = trim((string)($row['single_price'] ?? ''));
            $rawTotal  = trim((string)($row['total_price'] ?? ''));

            // Clean values like "$3.74", "USD 3.74", "3,829.76"
            $singlePrice = $this->parsePrice($rawSingle);
            $totalPrice  = $this->parsePrice($rawTotal);

            $currencyName = $row['currency_name'] ?? 'USD';
            $currencySymbol = $row['currency_symbol'] ?? '$';

            $rowKey = "{$productId}-{$unitName}-{$qty}-{$singlePrice}-{$totalPrice}-{$currencyName}";
            if (in_array($rowKey, self::$insertedRows)) {
                // Log::info("⏭️ Skipped duplicate in Excel: {$rowKey}");
                // $this->console("⏭️ Duplicate row skipped (Excel): {$productName}, Qty: {$qty}", 'info');
                return null;
            }
            self::$insertedRows[] = $rowKey;

            $duplicateInDB = ProductPrice::where('product_id', $productId)
                ->where('unit_name', $unitName)
                ->where('qty', $qty)
                ->where('single_price', $singlePrice)
                ->where('total_price', $totalPrice)
                ->where('currency_name', $currencyName)
                ->exists();

            if ($duplicateInDB) {
                // Log::info("⚠️ Skipped DB duplicate: {$rowKey}");
                // $this->console("⚠️ Already in DB: {$productName}, Qty: {$qty}", 'info');
                return null;
            }

            // $this->console("✅ Imported: {$productName}, Qty: {$qty}, Single: {$singlePrice}", 'info');

            return new ProductPrice([
                'product_id'      => $productId,
                'unit_name'       => $unitName,
                'qty'             => $qty,
                'single_price'    => $singlePrice,
                'total_price'     => $totalPrice,
                'currency_name'   => $currencyName,
                'currency_symbol' => $currencySymbol,
                'currency_id'     => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Error importing product price: ' . $e->getMessage(), [
                'row' => $row
            ]);
            // $this->console("❌ Exception: " . $e->getMessage(), 'error');
            return null;
        }
    }

    protected function console(string $message, string $type = 'info')
    {
        if (!$this->command) return;

        match ($type) {
            'error' => $this->command->error($message),
            'warn', 'warning' => $this->command->warn($message),
            default => $this->command->info($message),
        };
    }

    protected function parsePrice(string $price): float
    {
        // Remove symbols, spaces, and commas (e.g., "$ 3,829.76" => "3829.76")
        $clean = str_replace(',', '', $price); // remove commas first
        $clean = preg_replace('/[^\d.]/', '', $clean); // remove everything except numbers and dot
        return round((float)$clean, 4);
    }
}


    





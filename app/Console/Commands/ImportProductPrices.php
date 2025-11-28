<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductPriceImport;

class ImportProductPrices extends Command
{
    /**
     * The name and signature of the console command.
     * Example usage: php artisan import:product-prices prices.csv
     */
    protected $signature = 'import:product-prices {filename}';

    /**
     * The console command description.
     */
    protected $description = 'Import product price data from a CSV, XLSX, or XLS file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = $this->argument('filename');
        $filePath = storage_path("app/private/imports/{$filename}");

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Starting price import from: {$filename}");

        try {
            Excel::import(new ProductPriceImport, $filePath);
            // $this->info("Product price import completed successfully.");
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
        }

        return 0;
    }
}

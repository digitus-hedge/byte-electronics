<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductSpecificationImport;

class ImportProductSpecifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage: php artisan import:product-specs yourfile.xlsx
     */
    protected $signature = 'import:product-specs {filename}';

    /**
     * The console command description.
     */
    protected $description = 'Import product specification data from a CSV, XLSX, or XLS file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = $this->argument('filename');
        $filePath = storage_path("app/private/imports/{$filename}");

        if (!file_exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return 1;
        }

        $this->info("📦 Importing product specifications from: {$filename}");

        try {
            Excel::import(new ProductSpecificationImport($this), $filePath);

            $this->info("✅ Product specification import completed successfully.");
        } catch (\Exception $e) {
            $this->error("🚨 Import failed: " . $e->getMessage());
        }

        return 0;
    }
}

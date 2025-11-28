<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ProductImportController;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from the products_details table into the application database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = $this->argument('filename');
        $filePath = storage_path("app/private/imports/{$filename}");

        if (!file_exists($filePath)) {
            $this->error("File not found at: storage/app/private/imports/{$filename}");
            return 1;
        }

        $this->info("Starting import from {$filename}...");

        try {
            Excel::import(new ProductsImport, $filePath);
            $this->info("Import completed successfully.");
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
        }

        return 0;
    }
}
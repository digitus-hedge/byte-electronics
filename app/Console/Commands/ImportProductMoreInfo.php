<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Imports\ProductMoreInformation;
use DB;
class ImportProductMoreInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:product-more-info {filename}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
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
            
            Excel::import(new ProductMoreInformation, $filePath);
            $this->info("Product more info import completed successfully.");
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
        }
        return 0;
    }
}

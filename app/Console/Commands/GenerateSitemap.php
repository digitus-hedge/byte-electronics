<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Products;
use App\Models\Category;
use App\Models\Brands;
use App\Models\Blog;
use Carbon\Carbon;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the XML sitemap for byte-electronics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create()
            ->add(Url::create('/')
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0))
            ->add(Url::create('/contact-us')
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8))
            ->add(Url::create('/about-us')
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8))
            ->add(Url::create('/faq')
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8))
            ->add(Url::create('/terms-conditions')
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8))
            ->add(Url::create('/privacy-policy')
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8))
            ->add(Url::create(url('/products/filter'))
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(0.8)
            );

        //add products
        Products::where('status', 1)->chunk(100, function($products) use ($sitemap) {
            foreach($products as $product) {
                $sitemap->add(
                    Url::create(url("/products/details/{$product->slug}"))
                        ->setLastModificationDate($product->updated_at ?? now())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7)
                );
            }
        });
        
        // Add all brand detail pages dynamically
            Brands::where('status', 1)->chunk(100, function ($brands) use ($sitemap) {
                foreach ($brands as $brand) {
                    $sitemap->add(
                        Url::create(url("/brands/details/{$brand->slug}"))
                            ->setLastModificationDate($brand->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                }
            });
         
            // Add all category detail pages dynamically
            Category::where('status', 1)->chunk(100, function ($categories) use ($sitemap) {
                foreach ($categories as $category) {
                    $sitemap->add(
                        Url::create(url("/categories/details/{$category->slug}"))
                            ->setLastModificationDate($category->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                }
            });
            Blog::where('status', 1)->chunk(100, function ($blogs) use ($sitemap) {
                foreach ($blogs as $blog) {
                    $sitemap->add(
                        Url::create(url("/blogs/details/{$blog->slug}"))
                            ->setLastModificationDate($blog->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                }
            });

           // Save the sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
        
    }
}

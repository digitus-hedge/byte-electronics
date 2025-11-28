<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banners;
use App\Models\Products;
use Inertia\Inertia;
use App\Models\Blog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;


class BlogController extends Controller
{
    public function index()
    {
        // $blogs = [
        //     [
        //         'imgSrc' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
        //         'text'   => "Byte electricals journal keeps you up to date on the latest innovations and engineering design.",
        //     ],
        //     [
        //         'imgSrc' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
        //         'text'   => "Byte electricals journal keeps you up to date on the latest innovations and engineering design.",
        //     ],
        //     [
        //         'imgSrc' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
        //         'text'   => "Byte electricals journal keeps you up to date on the latest innovations and engineering design.",
        //     ],
        //     [
        //         'imgSrc' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
        //         'text'   => "Byte electricals journal keeps you up to date on the latest innovations and engineering design.",
        //     ],
        // ];
        // $blogBanner = asset('assets/banners/banner.jpg');
        // $blogCategory = [
        //     ['name' => "Newest Products"],
        //     ['name' => "Circuit Protection"],
        //     ['name' => "Connectors"],
        //     ['name' => "Electromechanical"],
        //     ['name' => "Embedded Solutions"],
        //     ['name' => "Enclosures"],
        //     ['name' => "Engineering Tools"],
        //     ['name' => "Industrial Automation"],
        //     ['name' => "LED Lighting"],
        //     ['name' => "Memory & Data Storage"],
        //     ['name' => "Opto-electronics"],
        //     ['name' => "Passive Components"],
        //     ['name' => "Power"],
        //     ['name' => "RF & Wireless"],
        //     ['name' => "Semiconductors"],
        //     ['name' => "Sensors"],
        //     ['name' => "Test & Measurement"],
        //     ['name' => "Thermal Management"],
        //     ['name' => "Tools & Supplies"],
        //     ['name' => "Wire & Cable"],
        // ];

        // return Inertia::render('Blog/Blogs', ['blogs' => $blogs, 'blogBanner' => $blogBanner,'blogCategory' => $blogCategory]);

        $query = Banners::query();

        $query->where('pagename','blog')
              ->where('type', 'blog_banner')
              ->where('status', 1);

        $query->orderBy('type')
              ->orderBy('priority');

        $banners = $query->get() ?? collect([]);
        Session::put('current_page',request()->fullUrl());


        $blogs = Blog::latest()->get(); // Fetch all blogs sorted by latest

        // $firstBlog = $blogs->first();
        $categories = Blog::distinct()->pluck('categories')->filter()->flatten()->unique();
        //  dd($categories);
        return Inertia::render('Blog/Blogs', [
            'blogs' => $blogs,
            'blogBanner' => !$banners->isEmpty() ?  asset('storage/' . $banners->first()->url1) : null,
            'blogCategory' => $blogs->pluck('categories')->flatten()->unique()->map(function($category) {
                return ['name' => $category];
            })->values()->toArray()
        ]);
    }


    public function show($slug)
    {
        $blog=Blog::where('slug',$slug)->first();
        $otherBlogs = Blog::latest()->limit(5)->get();
        $banner = Banners::where('pagename','blog') ->where('type', 'blog_banner')
        ->where('status', 1)->first();
        $blogDetailsMainBanner = $banner ? asset('storage/' . $banner->url1) : null;
        $blogDetailsSubBanner = $blog->featured_image ? asset('storage/' . $blog->featured_image) : null;
        // $blogDetailsMainBanner = asset('assets/banners/Group342.png');
        // $blogDetailsSubBanner  = asset('assets/blogs/fff.jpeg');

        // $pageTitle       = 'DC Microgrids Offer a Smarter More Efficient Energy Solution';
        $pageTitle       = $blog->title;
        // $pageSubtitle    = 'New electronic monday';
        // $mainHeading     = 'byte';
        // $blogTitle       = 'New Tech Tuesdays';
        // $blogIntro       = 'Join Rudy Ramos for a weekly look at all things interesting, new, and noteworthy for design engineers.';
        $publishDate     = Carbon::parse($blog->published_date)->format('d-m-Y');
        // $blogDescription = 'On a recent trip to South Texas, I was amazed to see how many wind turbines dot the landscape, taking advantage of the prevailing Gulf winds (Figure 1). These huge, three-bladed horizontal-axis wind turbines (HAWT), along with solar, are rapidly becoming considerable renewable energy sources driving substantial change to the traditional power grid. Today, over 72,000 wind turbines across the US generate clean, reliable power, accounting for 151GW of capacity, making it the fourth-largest electricity generation source. This wind power serves the equivalent of 46 million American homes.[1]';
        // $blogIntro       = 'One promising technology that could play a significant role in this shift is the direct current (DC) microgrid. Moving to grids operating on direct current and away from traditional alternating current (AC) grids might hold the key to greater efficiency, reliability, and cost-effectiveness, especially with the push towards decentralized, renewable-powered energy systems.';
        // $blogContent     = [
        //     [
        //         'title'      => 'What Is a DC Microgrid?',
        //         'content'    => 'Microgrid generally refers to a completely localized energy system...',
        //         'subTitles'  => [
        //             'Benefits of DC Microgrids',
        //             'DC Microgrid Challenges',
        //         ],
        //         'subContent' => [
        //             'DC microgrids are more efficient than traditional AC grids because they eliminate the need for AC and DC energy conversions while seamlessly integrating renewable energy sources like solar and wind. Battery storage systems designed for DC power provide enhanced energy storage without requiring energy conversion. Additionally, DC microgrids offer localized control over power distribution, making them suitable for remote areas, and can be scaled easily to meet growing energy needs.',
        //             'One major obstacle when transitioning to DC microgrids is the compatibility of DC systems with the existing AC-powered infrastructure. Most homes and commercial buildings are designed for AC power, requiring careful integration or complete retrofitting to support DC power.',
        //         ],

        //     ],
        //     [
        //         'title'   => 'The Newest Products for Your Newest Designs®',
        //         'content' => 'Microgrid generally refers to a completely localized energy system more than its size. A key feature of a microgrid is its ability to operate autonomously, either in islanding mode (independent of the main grid) or grid-connected mode. Microgrids typically include renewable energy generation sources, battery storage systems, power electronics, and DC loads—devices and appliances that run on DC power, like laptops, smartphones, and battery storage systems. In a DC microgrid, all components are optimized for DC power. Systems of varying scales—from small setups of a few kilowatts (kW) for individual homes to larger megawatt (MW) installations for remote communities or extensive facilities—can qualify as microgrids if they integrate the essential elements: power generation, storage, control, and local load management.',
        //     ],
        //     [
        //         'title'   => 'Tuesday’s Takeaway',
        //         'content' => 'Microgrid generally refers to a completely localized energy system more than its size. A key feature of a microgrid is its ability to operate autonomously, either in islanding mode (independent of the main grid) or grid-connected mode. Microgrids typically include renewable energy generation sources, battery storage systems, power electronics, and DC loads—devices and appliances that run on DC power, like laptops, smartphones, and battery storage systems. In a DC microgrid, all components are optimized for DC power. Systems of varying scales—from small setups of a few kilowatts (kW) for individual homes to larger megawatt (MW) installations for remote communities or extensive facilities—can qualify as microgrids if they integrate the essential elements: power generation, storage, control, and local load management.',
        //     ],

        // ];
        $blogContent=$blog->content;
        $blogContent_1=$blog->content_1;
        $blogBanners = $blog->image_1 ? [
            $blog->image_4 ? [
                ['imgSrc' => asset('storage/' . $blog->image_1), 'text' => $blog->image_1_text],
                ['imgSrc' => asset('storage/' . $blog->image_2), 'text' => $blog->image_2_text],
                ['imgSrc' => asset('storage/' . $blog->image_3), 'text' => $blog->image_3_text],
                ['imgSrc' => asset('storage/' . $blog->image_4), 'text' => $blog->image_4_text]
            ] : [
                ['imgSrc' => asset('storage/' . $blog->image_1), 'text' => $blog->image_1_text]
            ]
        ] : [];
        // dd($blogContent);
        $blogBanners = $otherBlogs->map(function($blog) {
            return [
                'imgSrc' => $blog->featured_image ? asset('storage/' . $blog->featured_image) : null,
                'text' => $blog->title
            ];
        })->toArray();
        // $blogBanners = [
        //     [
        //         'imgSrc' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
        //         'text'   => "Microgrid generally refers to a completely localized energy system more than its size.",
        //     ],
        //     [
        //         'imgSrc' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
        //         'text'   => "Microgrid generally refers to a completely localized energy system more than its size. ",
        //     ],
        //     [
        //         'imgSrc' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
        //         'text'   => "Microgrid generally refers to a completely localized energy system more than its size. ",
        //     ],
        //     [
        //         'imgSrc' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
        //         'text'   => "Microgrid generally refers to a completely localized energy system more than its size. ",
        //     ],
        // ];
        $otherContent=[
            [
                'image' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
                'description'   => "Microgrid generally refers to a completely localized energy system more than its size.",
                'readArticle' => 'Read Article',
            ],
            [
                'image' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
                'description'   => "Microgrid generally refers to a completely localized energy system more than its size. ",
                'readArticle' => 'Read Article',

            ],
            [
                'image' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
                'description'   => "Microgrid generally refers to a completely localized energy system more than its size. ",
                'readArticle' => 'Read Article',

            ],
            [
                'image' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
                'description'   => "Microgrid generally refers to a completely localized energy system more than its size. ",
                'watchVideo' => 'Watch Video',
            ],
            [
                'image' => asset('assets/blogs/WhatsApp Image 2025-01-08 at 10.42.14_c3c73052.jpg'),
                'description'   => "Microgrid generally refers to a completely localized energy system more than its size. ",
                'watchVideo' => 'Watch Video',

            ],
        ];
        // dd($otherContent);

        return Inertia::render('Blog/BlogDetails', [
            'blogdetailsMainBanner' => $blogDetailsMainBanner,
            'blogDetailsSubBanner'  => $blogDetailsSubBanner,
            'bannerTitle'           => $pageTitle,
            // 'bannerSubtitle'        => $pageSubtitle,
            // 'mainHeading'           => $mainHeading,
            // 'blogTitle'             => $blogTitle,
            // 'blogIntro'             => $blogIntro,
            'publishDate'           => $publishDate,
            'blogContent_1'           => $blogContent_1,
            'blogContent'           => $blogContent,
            'blogImage_1'           => $blog->image_1 ? asset('storage/' . $blog->image_1) : null,
            'blogImage_2'           => $blog->image_2 ? asset('storage/' . $blog->image_2) : null,
            'blogImage_3'           => $blog->image_3 ? asset('storage/' . $blog->image_3) : null,
            'blogImage_4'           => $blog->image_4 ? asset('storage/' . $blog->image_4) : null,
            // 'blogDescription'       => $blogDescription,
            'blogBanners'           => $blogBanners,
            'otherContent'          => $otherContent,
        ]);
    }

}

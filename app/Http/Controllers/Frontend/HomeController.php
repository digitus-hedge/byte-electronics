<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banners;
use App\Models\Brands;
use App\Models\Products;
use App\Models\Category;
use App\Models\CartItems;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ReCaptcha\ReCaptcha;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Log;


class HomeController extends Controller
{
    public function Home()
    {
        $formattedBanners = $this->formattedBanners();
        $newestProducts = $this->newestProducts();
     
        $bestSellers = $this->bestSellers();

        $categoriesShow = Category::select('id', 'name', 'slug', 'file_name')
            ->where('featured', 1)
            ->limit(10)
            ->get();

        $brands = Brands::limit(6)->get();

        return Inertia::render('Home', [
            'Banners' => $formattedBanners,
            'newestProducts' => $newestProducts,
            'bestSellers' => $bestSellers,
            'categoriesShow' => $categoriesShow,
            'imageUrl' => asset('uploads/category'),
            'imageBrandUrl' => asset('uploads/brand'),
            'Brands' => $brands,
            // 'cartCount' => auth()->check() ? auth()->user()->cartItems()->count() : 0,
        ]);
    }

    private function bestSellers()
    {
        return Products::orderBy('created_at', 'desc')->where('best_sellers',1)->take(20)->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' =>  strip_tags($product->description),
                'image' => $product->file_name ? url('uploads/products/' . $product->file_name) : asset('/uploads/default.png'),
                'isNew' => false,
                'slug' => "products/details/".$product->slug,
                'part_no' => $product->manufacturers_no,
            ];
        });
    }
    private function newestProducts()
    {
       return Products::with('prices','minQuantity')->orderBy('created_at', 'desc')->take(20)->get()->map(function ($product) {
        $firstPrice = $product->prices->first();
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => strip_tags($product->description),
                'image' => $product->file_name ? url('uploads/products/' . $product->file_name) : asset('/uploads/default.png'),
                'isNew' => true,
                'slug' => "products/details/".$product->slug,
                'part_no' => $product->manufacturers_no,
                'min_qty' => $product->minQuantity ? $product->minQuantity->qty : 0,
                'has_price' => $product->prices && $product->prices->count() > 0,
               'total_price' => $firstPrice ? $firstPrice->total_price : 0,
            ];
        });
    }

    private function formattedBanners()
    {
        $banners = Banners::where('pagename', 'home')
            ->where('status', 1)
            ->get()
            ->groupBy('type');

        $requiredTypes = collect(['main_banner', 'secondary_banner_1', 'secondary_banner_2', 'secondary_banner_3', 'secondary_banner_4']);
        $formattedBanners = collect();

        foreach ($requiredTypes as $type) {
            if ($banners->has($type) && $banners[$type]->isNotEmpty()) {
                // Use existing banner data
                $formattedBanners->push([
                    'type' => $type,
                    'images' => $banners[$type]->map(function ($banner) {
                        return [
                            'src' => $banner->url1 ? url('storage/' . $banner->url1) : asset('storage/uploads/default-banner.png'),
                            'alt' => $banner->bannername ?? 'Banner Image',
                            'link' => $banner->redirect_url ?? '#',
                            'priority' => $banner->priority ?? 1
                        ];
                    })->sortBy('priority')->values()
                ]);
            } else {
                // Use default banner for missing type
                $formattedBanners->push([
                    'type' => $type,
                    'images' => [
                        [
                            'src' => $type === 'main_banner'
                                ? asset('storage/uploads/default-main-banner.png')
                                : asset('storage/uploads/default-banner.png'),
                            'alt' => ucfirst(str_replace('_', ' ', $type)),
                            'link' => '#',
                            'priority' => 1
                        ]
                    ]
                ]);
            }
        }

        return $formattedBanners;
    }

    // private function formattedBanners()
    // {
    //     $banners = Banners::where('pagename', 'home')->where('status', 1)->get()->groupBy('type');
    //     try {
    //         if ($banners->isEmpty()) {
    //         return collect([
    //             [
    //             'type' => 'default',
    //             'images' => [
    //                 [
    //                 'src' => asset('storage/uploads/default-banner.png'),
    //                 'alt' => 'Default Banner',
    //                 'link' => '#',
    //                 'priority' => 1,
    //                 ]
    //             ]
    //             ]
    //         ]);
    //         }

    //         return $banners->map(function ($items, $type) {
    //         return [
    //             'type' => $type,
    //             'images' => $items->map(function ($banner) {
    //             return [
    //                 'src' => url('storage/' . $banner->url1),
    //                 'alt' => $banner->bannername ?? 'Banner Image',
    //                 'link' => $banner->redirect_url ?? '#',
    //                 'priority' => $banner->priority ?? 1,
    //             ];
    //             })->sortBy('priority')->values(),
    //         ];
    //         })->values();
    //     } catch (\Exception $e) {
    //         \Log::error('Banner Error: ' . $e->getMessage());
    //         return collect([
    //         [
    //             'type' => 'default',
    //             'images' => [
    //             [
    //                 'src' => asset('storage/uploads/default-banner.png'),
    //                 'alt' => 'Default Banner',
    //                 'link' => '#',
    //                 'priority' => 1,
    //             ]
    //             ]
    //         ]
    //         ]);
    //     }
    // }


    public function termsConditions()
    {
        $termsConditionBanner = asset('assets/banners/26284.png');
        // dd($termsConditionBanner);
        return Inertia::render('TermsOfService', ['termsConditionBanner' => $termsConditionBanner]);
    }

    public function faq()
    {
        return Inertia::render('Faq');
    }

    public function privacyPolicy()
    {
        $privacyPolicyBanner = asset('assets/banners/privacy-policy.png');
        return Inertia::render('PrivacyPolicy',['privacyPolicyBanner'=>$privacyPolicyBanner]);
    }
    public function contactUs()
    {
        $ContactUsBanner = asset('assets\images\contact\hedge-slider-bg.webp');

        return Inertia::render('ContactUs', ['ContactUsBanner' => $ContactUsBanner]);
    }
    public function about()
    {
        $aboutUsBanner = asset('assets/banners/26284.png');
        return Inertia::render('AboutUs',['aboutUsBanner'=>$aboutUsBanner]);

    }

    public function submitMail(Request $request)
    {
        // Log incoming request data
        Log::debug('reCAPTCHA token received: ' . $request->recaptchaToken);
        Log::debug('Request IP: ' . $request->ip());
        Log::debug('Request data: ' . json_encode($request->all()));

        // Validate reCAPTCHA
        $recaptcha = new ReCaptcha(config('services.recaptcha.secret'));
        $response = $recaptcha->verify($request->recaptchaToken, $request->ip());

        if (!$response->isSuccess()) 
        {
            Log::error('reCAPTCHA verification failed', [
                'errors' => $response->getErrorCodes(),
                'hostname' => $response->getHostname(),
                'challenge_ts' => $response->getChallengeTs(),
                'score' => method_exists($response, 'getScore') ? $response->getScore() : null,
                'action' => method_exists($response, 'getAction') ? $response->getAction() : null,
            ]);
            return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed'], 422);
        }

        // Validate form data
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'message' => 'required|string',
                'lastName' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors()));
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        // Construct plain text email body
        $emailBody = "New Feedback / Support Request\n\n";
        $emailBody .= "Name: {$validated['name']} " . ($validated['lastName'] ?? '') . "\n";
        $emailBody .= "Email: {$validated['email']}\n";
        $emailBody .= "Phone: {$validated['phone']}\n";
        $emailBody .= "Country: " . ($validated['country'] ?? 'Not specified') . "\n";
        $emailBody .= "Company: " . ($validated['company'] ?? 'Not specified') . "\n";
        $emailBody .= "Message: {$validated['message']}\n\n";
        $emailBody .= "Thanks";

        // Send email
        try {
            Mail::raw($emailBody, function ($message) {
                $message->to('info@byte-electronics.com')
                        ->subject('New Contact Form Submission');
            });

            return response()->json(['success' => true, 'message' => 'Message sent successfully']);
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }

}

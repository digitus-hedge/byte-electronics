<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Category;
use Tighten\Ziggy\Ziggy;
use Illuminate\Support\Facades\Cache;
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'previousUrl' => url()->previous(),
            'categoriesForMenu' => fn () => Cache::remember('categoriesForMenu', 3600, function () {
                return Category::with(['subcategories' => function ($query) {
                    $query->active()->select('id', 'name', 'slug', 'category_id');
                }])
                    ->active()
                    ->select('id', 'name', 'slug')
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                            'subcategories' => $category->subcategories->map(function ($subcategory) {
                                return [
                                    'id' => $subcategory->id,
                                    'name' => $subcategory->name,
                                    'slug' => $subcategory->slug,
                                    'url' => '/products/filter?productType%5B0%5D=' . $subcategory->id,
                                ];
                            })->toArray(),
                        ];
                    });
            }),
            // 'cartCount' => fn () => auth()->check() ? auth()->user()->cartItems()->count() : 0,
            'success' => fn () => $request->session()->get('success'),
        ];
    }
}

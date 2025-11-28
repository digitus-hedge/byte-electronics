<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banners;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class BannersController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Banners::query();

            if ($request->ajax() && $request->has('keyword')) {
                $query->where('bannername', 'like', '%' . $request->keyword . '%');
                $banners = $query->paginate(10);

                return view('banners.partials.table', compact('banners'))->render(); // return only table HTML
            }

            $banners = $query->paginate(10);
            session(['current_page' => request()->fullUrl()]);
            return view('banners.index', compact('banners'));
      
    }

    public function create()
    {
        return view('banners.create');
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'bannername' => 'required|string|max:255',
                'type' => 'required|in:main_banner,secondary_banner_1,secondary_banner_2,secondary_banner_3,secondary_banner_4,category_banner,promotion_banner,blog_banner',
                'priority' => 'required|integer',
                'status' => 'required|boolean',
                'image1' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', function ($attribute, $value, $fail) use ($request) {
                    $this->validateImageDimensions($value, $request->type, 'image1', $fail);
                }],
                'image2' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', function ($attribute, $value, $fail) use ($request) {
                    $this->validateImageDimensions($value, $request->type, 'image2', $fail);
                }],
                'image3' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', function ($attribute, $value, $fail) use ($request) {
                    $this->validateImageDimensions($value, $request->type, 'image3', $fail);
                }],
                'pagename' => 'required|in:home,Category,blog',
                'redirect_url' => 'nullable|url',
            ];

            $validator = Validator::make($request->all(), $rules);

            // Custom validation for at least one image
            if (!$request->hasFile('image1') && !$request->hasFile('image2') && !$request->hasFile('image3')) {
                $validator->errors()->add('images', 'At least one image is required.');
            }

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except('image1', 'image2', 'image3');

            if ($request->hasFile('image1')) {
                $data['url1'] = $request->file('image1')->store('banners', 'public');
            }
            if ($request->hasFile('image2')) {
                $data['url2'] = $request->file('image2')->store('banners', 'public');
            }
            if ($request->hasFile('image3')) {
                $data['url3'] = $request->file('image3')->store('banners', 'public');
            }

            $banner = Banners::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Banner created successfully',
                'data' => $banner
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create banner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Banners $banner)
    {
        return view('banners.show', compact('banner'));
    }

    public function edit(Banners $banner)
    {
        return view('banners.edit', compact('banner'));
    }

    public function update(Request $request, Banners $banner)
    {
        try {
            $rules = [
                'bannername' => 'required|string|max:255',
                'type' => 'required|in:main_banner,secondary_banner_1,secondary_banner_2,secondary_banner_3,secondary_banner_4,category_banner,promotion_banner,blog_banner',
                'priority' => 'required|integer',
                'status' => 'required|boolean',
                'image1' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', function ($attribute, $value, $fail) use ($request) {
                    $this->validateImageDimensions($value, $request->type, 'image1', $fail);
                }],
                'image2' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', function ($attribute, $value, $fail) use ($request) {
                    $this->validateImageDimensions($value, $request->type, 'image2', $fail);
                }],
                'image3' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', function ($attribute, $value, $fail) use ($request) {
                    $this->validateImageDimensions($value, $request->type, 'image3', $fail);
                }],
                'pagename' => 'required|in:home,Category,blog',
                'redirect_url' => 'nullable|url',
                'existing_image1' => 'nullable|string',
                'existing_image2' => 'nullable|string',
                'existing_image3' => 'nullable|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            // Custom validation for at least one image
            if (!$request->hasFile('image1') && !$request->hasFile('image2') && !$request->hasFile('image3') &&
                !$request->input('existing_image1') && !$request->input('existing_image2') && !$request->input('existing_image3')) {
                $validator->errors()->add('images', 'At least one image is required.');
            }

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except('image1', 'image2', 'image3', 'existing_image1', 'existing_image2', 'existing_image3');

            // Handle image1
            if ($request->hasFile('image1')) {
                if ($banner->url1) {
                    Storage::disk('public')->delete($banner->url1);
                }
                $data['url1'] = $request->file('image1')->store('banners', 'public');
            } elseif ($request->input('existing_image1')) {
                $data['url1'] = $request->input('existing_image1');
            } else {
                if ($banner->url1) {
                    Storage::disk('public')->delete($banner->url1);
                }
                $data['url1'] = null;
            }

            // Handle image2
            if ($request->hasFile('image2')) {
                if ($banner->url2) {
                    Storage::disk('public')->delete($banner->url2);
                }
                $data['url2'] = $request->file('image2')->store('banners', 'public');
            } elseif ($request->input('existing_image2')) {
                $data['url2'] = $request->input('existing_image2');
            } else {
                if ($banner->url2) {
                    Storage::disk('public')->delete($banner->url2);
                }
                $data['url2'] = null;
            }

            // Handle image3
            if ($request->hasFile('image3')) {
                if ($banner->url3) {
                    Storage::disk('public')->delete($banner->url3);
                }
                $data['url3'] = $request->file('image3')->store('banners', 'public');
            } elseif ($request->input('existing_image3')) {
                $data['url3'] = $request->input('existing_image3');
            } else {
                if ($banner->url3) {
                    Storage::disk('public')->delete($banner->url3);
                }
                $data['url3'] = null;
            }

            $banner->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Banner updated successfully',
                'data' => $banner
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update banner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function validateImageDimensions($image, $type, $imageField, $fail)
    {
        if (!$image) return;

        $dimensions = [
            'main_banner' => ['image1' => [630, 530], 'image2' => [360, 300], 'image3' => [540, 450]],
            'secondary_banner_1' => ['image1' => [320, 270], 'image2' => [180, 150], 'image3' => [260, 210]],
            'secondary_banner_2' => ['image1' => [320, 270], 'image2' => [180, 150], 'image3' => [260, 210]],
            'secondary_banner_3' => ['image1' => [320, 270], 'image2' => [180, 150], 'image3' => [260, 210]],
            'secondary_banner_4' => ['image1' => [320, 270], 'image2' => [180, 150], 'image3' => [260, 210]],
            'category_banner' => ['image1' => [600, 500], 'image2' => [450, 350], 'image3' => [600, 500]],
            'promotion_banner' => ['image1' => [550, 450], 'image2' => [400, 320], 'image3' => [550, 450]],
            'blog_banner' => ['image1' => [1600, 320], 'image2' => [360, 300], 'image3' => [540, 450]],
        ];

        if (!isset($dimensions[$type][$imageField])) return;

        $requiredWidth = $dimensions[$type][$imageField][0];
        $requiredHeight = $dimensions[$type][$imageField][1];

        $imageInfo = getimagesize($image->getPathname());
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        if ($width !== $requiredWidth || $height !== $requiredHeight) {
            $fail("The $imageField must be exactly ${requiredWidth} × ${requiredHeight} pixels.");
        }
    }

    public function destroy(Banners $banner)
    {
        // Delete associated images
        if ($banner->url1) {
            Storage::disk('public')->delete($banner->url1);
        }
        if ($banner->url2) {
            Storage::disk('public')->delete($banner->url2);
        }
        if ($banner->url3) {
            Storage::disk('public')->delete($banner->url3);
        }

        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully'
        ]);
    }
}

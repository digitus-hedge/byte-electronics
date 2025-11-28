<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
   **/
  public function index(Request $request)
  {
    
    $query = Blog::query();

            if ($request->ajax() && $request->has('keyword')) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
                $blogs= $query->paginate(10);

                return view('blogs.partials.table', compact('blogs'))->render(); // return only table HTML
            }

            $blogs = $query->paginate(10);
            return view('blogs.index', compact('blogs'));
  }

  public function create()
  {
      return view('blogs.create');
  }


    public function store(Request $request)
    {

         $validatedData = $request->validate([
                'title' => 'required|unique:blogs,title',
                'content' => 'required',
                'tags' => 'required|array',
                'tags.*' => 'string',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ], [
                'tags.*.string' => 'Each tag must be a string.',
            ], [
                'title' => 'Title',
                'tags' => 'Tags',
                'tags.*' => 'Tags',
        ]);
       
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('blogs', 'public'); // Save to storage/app/public/blogs
            $validatedData['featured_image'] = $imagePath; // Save path in database
        }
        else{
            $validatedData['featured_image'] = null;
        }
        if ($request->hasFile('image_1')) {
            $imagePath = $request->file('image_1')->store('blogs', 'public'); // Save to storage/app/public/blogs
            $validatedData['image_1'] = $imagePath; // Save path in database
        }
        else{
            $validatedData['image_1'] = null;
        }


        if ($request->hasFile('image_2')) {
            $imagePath = $request->file('image_2')->store('blogs', 'public'); // Save to storage/app/public/blogs
            $validatedData['image_2'] = $imagePath; // Save path in database
        }
        else{
            $validatedData['image_2'] = null;
        }


        if ($request->hasFile('image_3')) {
            $imagePath = $request->file('image_3')->store('blogs', 'public'); // Save to storage/app/public/blogs
            $validatedData['image_3'] = $imagePath; // Save path in database
        }
        else{
            $validatedData['image_3'] = null;
        }

        if ($request->hasFile('image_4')) {
            $imagePath = $request->file('image_4')->store('blogs', 'public'); // Save to storage/app/public/blogs
            $validatedData['image_4'] = $imagePath; // Save path in database
        }
        else{
            $validatedData['image_4'] = null;
        }

        // Convert tags array to a comma-separated string
        $validatedData['tags'] = implode(',', $request->tags);
     

        try {
            $blog = Blog::create([
                'title' => $validatedData['title'],
                'slug' => Str::slug($validatedData['title']),
                'author' => $request->author,
                'publish_date' => $request->publish_date,
                'content' => $validatedData['content'],
                'content_1' => $request->content_1,
                'image_1' => $validatedData['image_1'],
                'image_2' => $validatedData['image_2'],
                'image_3' => $validatedData['image_3'],
                'image_4' => $validatedData['image_4'],
                'categories' => $request->categories,
                'tags' => $validatedData['tags'],
                'summary' => $request->summary,
                'featured_image' => $validatedData['featured_image'],
                'seo_title' => $request->seo_title,
                'meta_description' => $request->meta_description,
                'status' => $request->blogstatus,
                'social_sharing' => $request->social_sharing ? true : false,
            ]);

            // Return a JSON response on success
            return response()->json(['success' => true, 'message' => 'Blog created successfully!']);
        } catch (\Exception $e) {
            // Return a JSON response on failure
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }



  public function edit(Blog $blog)
  {
      return view('blogs.edit', compact('blog'));
  }

  public function update(Request $request, Blog $blog)
  {
      $request->validate([
          'title' => 'required|unique:blogs,title,' . $blog->id,
          'content' => 'required',
          'tags' => 'required|array',
          'tags.*' => 'string',
          'featured_image' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
          'image_1' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
          'image_2' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
          'image_3' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
          'image_4' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
      ]);
      $imagePath = $blog->featured_image;
      if ($request->hasFile('featured_image')) {

        //$imagePath = $request->file('featured_image')->store('blogs', 'public'); // Save to storage/app/public/blogs
        // $request->featured_image = $imagePath; // Save path in database
        $imagePath = $request->file('featured_image')
        ? $request->file('featured_image')->store('blogs', 'public')
        : $request->existing_featured_image;
    }
    // if ($request->hasFile('existing_featured_image')) {

    //     // $imagePath = $request->file('featured_image')->store('blogs', 'public'); // Save to storage/app/public/blogs
    //     // $request->featured_image = $imagePath; // Save path in database
    //     $imagePath = $request->file('existing_featured_image')
    //     ? $request->file('existing_featured_image')->store('blogs', 'public')
    //     : $request->existing_featured_image;
    // }
    // else{
    //     $imagePath = null;
    // }
    $image1 = $request->existing_image_1;
    if ($request->hasFile('image_1')) {
        // $imagePath = $request->file('image_1')->store('blogs', 'public'); // Save to storage/app/public/blogs
        // $request->image_1 = $imagePath; // Save path in database
        $image1 = $request->file('image_1')
        ? $request->file('image_1')->store('blogs', 'public')
        : $request->existing_image_1;
    }


    $image2 = $request->existing_image_2;
    if ($request->hasFile('image_2')) {
        $image2 = $request->file('image_2')
        ? $request->file('image_2')->store('blogs', 'public')
        : $request->existing_image_2; // Save path in database
    }


    $image3 = $request->existing_image_3;
    if ($request->hasFile('image_3')) {
        $image3 = $request->file('image_3')
        ? $request->file('image_3')->store('blogs', 'public')
        : $request->existing_image_3; // Save path in database
    }

    $image4 = $request->existing_image_4; ;
    if ($request->hasFile('image_4')) {
        $image4 = $request->file('image_4')
        ? $request->file('image_4')->store('blogs', 'public')
        : $request->existing_image_4;
    }



    // $image1 = $request->file('image_1')
    // ? $request->file('image_1')->store('blogs', 'public')
    // : $request->existing_image_1;

    // $image2 = $request->file('image_2')
    //     ? $request->file('image_2')->store('blogs', 'public')
    //     : $request->existing_image_2;

    // $image3 = $request->file('image_3')
    //     ? $request->file('image_3')->store('blogs', 'public')
    //     : $request->existing_image_3;

    // $image4 = $request->file('image_4')
    //     ? $request->file('image_4')->store('blogs', 'public')
    //     : $request->existing_image_4;

// Save to database

      // Convert tags array to a comma-separated string
      $request->tags = implode(',', $request->tags);



      $blog->update([
          'title' => $request->title,
          'slug' => Str::slug($request->title),
          'author' => $request->author,
          'publish_date' => $request->publish_date,
          'content' => $request->content,
          'content_1' => $request->content_1,
          'categories' => $request->categories,
          'tags' =>$request->tags,
          'summary' => $request->summary,
          'featured_image' => $imagePath,
            'image_1' => $image1,
            'image_2' => $image2,
            'image_3' => $image3,
            'image_4' => $image4,
          'seo_title' => $request->seo_title,
          'meta_description' => $request->meta_description,
          'status' => $request->blogstatus,
          'social_sharing' => $request->social_sharing ? true : false,
      ]);

    //   return redirect()->route('admin.blogs.index')->with('success', 'Blog updated!');
try {
    return response()->json(['success' => true, 'message' => 'Blog updated successfully!','image1'=>$image1,'image2'=>$image2,'image3'=>$image3,'image4'=>$image4,'imagepath'=>$imagePath]);
} catch (\Exception $e) {

    return response()->json(['success' => false, 'error' => $e,'image1'=>$image1,'image2'=>$image2,'image3'=>$image3,'image4'=>$image4,'imagepath'=>$imagePath]);
}
  }

  public function destroy(Blog $blog)
{
    $blog->delete(); // This performs a soft delete
    return redirect()->route('admin.blogs.index')->with('success', 'Blog moved to trash!');
}


}

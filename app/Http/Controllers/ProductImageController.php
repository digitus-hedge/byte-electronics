<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductImage::query();
 
        if ($request->has('trashed') && $request->trashed === 'only') {
            $query->onlyTrashed(); // Fetch only soft-deleted records
        } elseif ($request->has('trashed') && $request->trashed === 'with') {
            $query->withTrashed(); // Fetch all records including soft-deleted
        }

        $productImages = $query->orderBy('id', 'desc')->paginate(10);

        return view('productManage.productImages.index', compact('productImages'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(productImage $productImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(productImage $productImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, productImage $productImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(productImage $productImage)
    {
        //
    }
}

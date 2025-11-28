<?php

namespace App\Http\Controllers;

use App\Models\ProductAttribute;
use App\Models\ProductHeader;
use Illuminate\Http\Request;


class ProductHeaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ProductHeader::with('attributes')->get());
    }

     // Get attributes based on the selected heading
     public function getAttributes($headingId)
     {
        // dd($headingId);
        //  $attributeHeader = ProductHeader::where('id', $headingId)->get();
       $attributesUnderHeader= ProductAttribute::where('prd_header_id', $headingId)->get();

         return response()->json($attributesUnderHeader);
     }
 
     // Add a new heading
     public function addHeading(Request $request)
     {
        // dd($request);
         $request->validate([
             'name' => 'required|unique:product_headers,name|max:255',
         ]);
 
         $heading = ProductHeader::create([
             'name' => $request->name,
         ]);
 
         return response()->json($heading);
     }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate(['name' => 'required|string']);
        // return ProductHeader::create($request->all());
        $request->validate([
            'name' => 'required|unique:product_headers,name|max:255',
        ]);

        $heading = ProductHeader::create([
            'name' => $request->name,
        ]);

        return response()->json($heading);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductHeader $productHeader)
    {
        return response()->json($productHeader->load('attributes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductHeader $productHeader)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductHeader $productHeader)
    {
        $productHeader->update($request->all());
        return response()->json($productHeader);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductHeader $productHeader)
    {
        $productHeader->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

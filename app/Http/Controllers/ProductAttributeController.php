<?php

namespace App\Http\Controllers;

use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('attributes.index', [
            'attributes' => ProductAttribute::with('header')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function addAttribute(Request $request)
    {
        // dd($request);
        $request->validate([
            'heading_id' => 'required|exists:product_headers,id',
            'name' => 'required|unique:product_attributes,name,NULL,id,prd_header_id,' . $request->heading_id,
            'value_type' => 'required|string'
        ]);

        $attribute = ProductAttribute::create([
            'prd_header_id' => $request->heading_id,
            'name' => $request->name,
            'value_type' => $request->value_type
        ]);

        return response()->json($attribute);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'prd_header_id' => 'required|exists:product_headers,id',
            'name' => 'required|string',
            'value_type' => 'required|string'
        ]);

        return ProductAttribute::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAttribute $productAttribute)
    {
        return response()->json($productAttribute->load('header'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductAttribute $productAttribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductAttribute $productAttribute)
    {
        $productAttribute->update($request->all());
        return response()->json($productAttribute);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAttribute $productAttribute)
    {
        $productAttribute->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

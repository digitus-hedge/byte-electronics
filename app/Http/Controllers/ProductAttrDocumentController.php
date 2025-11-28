<?php

namespace App\Http\Controllers;

use App\Models\ProductAttrDocument;
use Illuminate\Http\Request;

class ProductAttrDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ProductAttrDocument::with('productExtended')->get());
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
        $request->validate([
            'product_ext_id' => 'required|exists:product_extended,id',
            'name' => 'required|string',
            'value' => 'required|string'
        ]);

        return ProductAttrDocument::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAttrDocument $productAttrDocument)
    {
        return response()->json($productAttrDocument->load('productExtended'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductAttrDocument $productAttrDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductAttrDocument $productAttrDocument)
    {
        $productAttrDocument->update($request->all());
        return response()->json($productAttrDocument);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAttrDocument $productAttrDocument)
    {
        $productAttrDocument->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

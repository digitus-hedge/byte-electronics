<?php

namespace App\Http\Controllers;

use App\Models\ProductExtended;
use Illuminate\Http\Request;

class ProductExtendedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ProductExtended::with(['product', 'attribute'])->get());
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
            'product_id' => 'required|exists:products,id',
            'product_attr_id' => 'required|exists:product_attributes,id',
            'value_type' => 'required|string'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductExtended $productExtended)
    {
        return response()->json($productExtended->load(['product', 'attribute']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductExtended $productExtended)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductExtended $productExtended)
    {
        $productExtended->update($request->all());
        return response()->json($productExtended);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductExtended $productExtended)
    {
        $productExtended->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

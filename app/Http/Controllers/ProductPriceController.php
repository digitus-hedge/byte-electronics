<?php

namespace App\Http\Controllers;

use App\Models\ProductPrice;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ProductPrice::with('product')->get());
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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'unit_name' => 'required|string',
            'qty' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0'
        ]);

        // single_price is calculated automatically in the model
        $productPrice = ProductPrice::create($request->all());

        return response()->json($productPrice, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductPrice $productPrice)
    {
        return response()->json($productPrice->load('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductPrice $productPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductPrice $productPrice)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'unit_name' => 'required|string',
            'qty' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            
        ]);

        $productPrice->update($request->all());

        return response()->json($productPrice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPrice $productPrice)
    {
        $productPrice->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

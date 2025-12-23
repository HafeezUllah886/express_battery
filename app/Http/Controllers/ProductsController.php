<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\products;
use App\Models\units;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = products::with('category', 'unit')->paginate(1000);
        $cats = categories::orderBy('name', 'asc')->get();
        return view('products.product', compact('items', 'cats'));
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
        $request->validate(
            [
                'code' => "unique:products,code",
            ],
            [
            'code.unique' => "Product Code already In Use",
            ]
        );

        products::create($request->all());

        return back()->with('success', 'Product Created');
    }

    /**
     * Display the specified resource.
     */
    public function show(products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'code' => "unique:products,code,".$id,
            ],
            [
            'code.unique' => "Product code already in Use",
            ]
        );

        $product = products::find($id);
        $product->update($request->all());

        return back()->with('success', 'Product Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(products $products)
    {
        //
    }

    public function ajaxCreate(request $request)
    {
        $check = products::where('name', $request->name)->count();
        if($check > 0)
        {
            return response()->json(
                ['response' => "Exists",]
            );
        }
        $product = products::create($request->all());
        return response()->json(
            ['response' => $product->id,]
        );
    }

    public function barcodePrint($id)
    {
        $product = products::find($id);
        return view('products.barcode',compact('product'));
    }

    public function generateCode(){
        gen:
        $value = rand(1111111111, 9999999999);
        $check = products::where('code', "C$value")->count();
        if($check > 0){
            goto gen;
        }

        return "C$value";
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\categories;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sales;
use App\Models\stock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $accounts = accounts::Business()->get();
        $customers = accounts::Customer()->get();
        $products = products::all();
        foreach($products as $product)
        {
            $product->stock = getStock($product->id);
        }
        $categories = categories::all();
        return view('pos.index', compact('accounts', 'customers', 'products', 'categories'));
    }

    public function allproducts()
    {
        $products = products::orderBy('name', 'asc')->get();
        foreach($products as $product)
        {
            $product->stock = getStock($product->id);
        }

        return response()->json(
            [
                'products' => $products,
            ]
        );
    }
    public function bycategory($id)
    {
        $products = products::where('catID', $id)->orderBy('name', 'asc')->get();
        foreach($products as $product)
        {
            $product->stock = getStock($product->id);
        }

        return response()->json(
            [
                'products' => $products,
            ]
        );
    }
    public function getSingleProduct($id)
    {
        $product = products::find($id);
        $stocks = stock::select('batch', DB::raw('SUM(cr) - SUM(db) AS balance'))
                  ->where('productID', $product->id)
                  ->groupBy('batch')
                  ->get();
        $product->balance = getStock($product->id);
        $product->batches = $stocks;

        return response()->json(
            [
                'product' => $product,
            ]
        );
    }
    public function searchByCode($code)
    {
        $product = products::where('code', $code)->first();
        if($product)
        {
           return $product->id;
        }
        return "Not Found";
    }

    public function store(Request $request)
    {
       try
        {

            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }

            DB::beginTransaction();
            $ref = getRef();
            $sale = sales::create(
                [
                  'customerID'      => $request->customer,
                  'date'            => $request->date,
                  'notes'           => $request->notes,
                  'discount'        => $request->billDiscount,
                  'refID'           => $ref,
                ]
            );

            $ids = $request->id;

            $total = 0;
            foreach($ids as $key => $id)
            {
                if($request->amount[$key] > 0)
                {
                $qty = $request->qty[$key];
                $price = $request->price[$key];
                $total += $request->amount[$key];

                $stock = stock::where('productID', $id)->where('batch', $request->batch[$key])->orderBy('id', 'desc')->first();
                sale_details::create(
                    [
                        'salesID'       => $sale->id,
                        'productID'     => $id,
                        'price'         => $price,
                       'warehouseID'   => $request->warehouse[$key],
                        'qty'           => $qty,
                        'amount'        => $request->amount[$key],
                        'date'          => $request->date,
                        'batch'         => $stock->batch ?? null,
                        'expiry'        => $stock->expiry ?? null,
                        'refID'         => $ref,
                    ]
                );
                createStock($id,0, $qty, $request->date, "Sold in Inv # $sale->id", $ref, $request->warehouse[$key], $stock->batch ?? null, $stock->expiry ?? null);
                }

            }

            $discount = $request->discount;
            $net = $total - $discount;

            $sale->update(
                [
                    'total'   => $net,
                ]
            );
            createTransaction($request->account, $request->date, $net, 0, "Payment of Inv No. $sale->id", $ref, 'Sale');

          DB::commit();
            return $sale->id;
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return "Error";
        }
    }

    public function print($id)
    {
        $invoice = sales::find($id);
        return view('pos.print', compact('invoice'));
    }

    public function printlast()
    {
        $invoice = sales::orderBy('id', 'desc')->first();
        return view('pos.print', compact('invoice'));
    }
}



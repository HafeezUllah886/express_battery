<?php

namespace App\Http\Controllers;

use App\Models\expenses;
use App\Models\extraProfit;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sales;
use Illuminate\Http\Request;

class profitController extends Controller
{
    public function index()
    {
        return view('reports.profit.index');
    }

    public function data($from, $to)
    {
        $products = products::orderBy('name', 'asc')->get();
        foreach($products as $product)
        {
            
            $sales = sale_details::where('productID', $product->id)->whereBetween('date', [$from, $to])->get();
            $product->sales = $sales;
        }

        $expenses = expenses::whereBetween('date', [$from, $to])->sum('amount');
        $extraProfit = extraProfit::whereBetween('date', [$from, $to])->sum('amount');

        return view('reports.profit.details', compact('from', 'to', 'expenses', 'products', 'extraProfit'));
    }
}

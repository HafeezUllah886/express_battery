<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\claim_amount;
use App\Models\products;
use App\Models\purchase_details;
use App\Models\quotation;
use App\Models\quotationDetails;
use App\Models\sale_details;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quotations = quotation::orderBy('id', 'desc')->get();
        return view('quotation.index', compact('quotations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $sold = sale_details::select('purchase_id', DB::raw('SUM(qty) as total_sold_qty'))
            ->groupBy('purchase_id');

            $claimed = claim_amount::select('purchase_id', DB::raw('SUM(claim_product_qty) as total_claimed_qty'))
            ->groupBy('purchase_id');

         $products = purchase_details::select(
            'purchase_details.*',
            'products.name as name',
            DB::raw('(purchase_details.qty - COALESCE(sold.total_sold_qty, 0)) as avail_qty'),
            DB::raw('(purchase_details.qty - COALESCE(claimed.total_claimed_qty, 0)) as avail_qty')
        )
            ->leftJoin('products', 'purchase_details.productID', '=', 'products.id')
            ->leftJoinSub($sold, 'sold', function ($join) {
                $join->on('purchase_details.id', '=', 'sold.purchase_id');
            })
            ->leftJoinSub($claimed, 'claimed', function ($join) {
                $join->on('purchase_details.id', '=', 'claimed.purchase_id');
            })
            ->whereRaw('COALESCE(sold.total_sold_qty, 0) < purchase_details.qty AND COALESCE(claimed.total_claimed_qty, 0) < purchase_details.qty')
            ->orderBy('purchase_details.id', 'desc')
            ->limit(200)
            ->get();
        $cats = categories::orderBy('name', 'asc')->get();
        return view('quotation.create', compact('products', 'cats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }

            DB::beginTransaction();
            $quot = quotation::create(
                [
                  'customerName'        => $request->customer,
                  'customerAddress'     => $request->address,
                  'notes'               => $request->notes,
                  'date'                => $request->date,
                  'validTill'           => $request->valid,
                ]
            );

            $ids = $request->id;

            $total = 0;
            foreach($ids as $key => $id)
            {
                $qty = $request->qty[$key];
                $price = $request->price[$key];
                $retail = $request->retail[$key];
                $sale_percentage = $request->sale_percentage[$key];
                $total += $request->amount[$key];

                quotationDetails::create(
                    [
                        'quotID'                => $quot->id,
                        'productID'             => $request->productID[$key],
                        'retail'                => $retail,
                        'sale_percentage'       => $sale_percentage,
                        'extra_tax'             => $request->extra_tax[$key],
                        'price'                 => $price,
                        'qty'                   => $qty,
                        'amount'                => $request->amount[$key],
                    ]
                );

            }

            $quot->update(
                [
                    'total'   => $total,
                ]
            );

           DB::commit();
            return to_route('quotation.show', $quot->id)->with('success', "Quotation Created");

        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $quot = quotation::find($id);
        return view('quotation.view', compact('quot'));
    }

    public function pdf($id)
    {
        $quot = quotation::find($id);
        $pdf = Pdf::loadview('quotation.pdf', compact('quot'));
    return $pdf->download("Quotation No. $quot->id.pdf");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(quotation $quotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, quotation $quotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try
        {
            DB::beginTransaction();
            $quot = quotation::find($id);
            foreach($quot->details as $product)
            {
                $product->delete();
            }
            $quot->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('quotation.index')->with('success', "Quotation Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('quotation.index')->with('error', $e->getMessage());
        }
    }
}

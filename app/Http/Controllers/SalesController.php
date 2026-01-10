<?php

namespace App\Http\Controllers;

use App\Http\Middleware\confirmPassword;
use App\Models\accounts;
use App\Models\categories;
use App\Models\claim_amount;
use App\Models\products;
use App\Models\purchase_details;
use App\Models\sale_details;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\salesman;
use App\Models\scrapePurchase;
use App\Models\scrapStock;
use App\Models\stock;
use App\Models\transactions;
use App\Models\units;
use App\Models\warehouses;
use Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Illuminate\Routing\Controller;

class SalesController extends Controller
{

    public function __construct()
    {
        $this->middleware(confirmPassword::class)->only('edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $start = $request->start ?? firstDayOfMonth();
        $end = $request->end ?? now()->toDateString();
        $sales = sales::with('payments')->orderby('id', 'desc')->whereBetween("date", [$start, $end])->get();
        return view('sales.index', compact('sales', 'start', 'end'));
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
        $customers = accounts::customerVendor()->get();
        $accounts = accounts::business()->get();
        $cats = categories::orderBy('name', 'asc')->get();


        return view('sales.create', compact('products', 'customers', 'accounts', 'cats'));
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
            $ref = getRef();
            $sale = sales::create(
                [
                  'customerID'      => $request->customerID,
                  'date'            => $request->date,
                  'notes'           => $request->notes,
                  'payment_status'  => $request->status,
                  'scrap_amount'    => $request->scrap_amount,
                  'payment'         => $request->paid,
                  'customerName'    => $request->customerName,
                  'refID'           => $ref,
                  'userID'          => auth()->user()->id,
                ]
            );

            $ids = $request->id;

            $total = 0;

            $customer_name = accounts::find($request->customerID)->title;
            if($request->customerID == 2)
            {
                $customer_name = $request->customerName . " (Walk-in)";
            }

           /*  $note_details = ""; */
            
            foreach($ids as $key => $id)
            {
                if($request->amount[$key] > 0)
                {
                $qty = $request->qty[$key];
                $price = $request->price[$key];
                $retail = $request->retail[$key];
                $purchase_percentage = $request->purchase_percentage[$key];
                $sale_percentage = $request->sale_percentage[$key];
                $pprice = $request->pprice[$key];
                $profit = $request->profit[$key];
                $total += $request->amount[$key];

                sale_details::create(
                    [
                        'salesID'               => $sale->id,
                        'productID'             => $request->productID[$key],
                        'purchase_id'           => $id,
                        'retail'                => $retail,
                        'purchase_percentage'   => $purchase_percentage,
                        'sale_percentage'       => $sale_percentage,
                        'extra_tax'             => $request->extra_tax[$key],
                        'pprice'                => $pprice,
                        'price'                 => $price,
                        'qty'                   => $qty,
                        'amount'                => $request->amount[$key],
                        'profit'                => $profit,
                        'date'                  => $request->date,
                        'refID'                 => $ref,
                    ]
                );
                }

            }

            $scrap_amount = $request->scrap_amount;
            $net = $total - $scrap_amount;

            $sale->update(
                [
                    'total'   => $net,
                ]
            );

            
              if($request->has('file')){
            createAttachment($request->file('file'), $ref);
        }


            if($request->scrap_amount > 0)
            {
                scrapePurchase::create(
                    [
                        'weight' => $request->weight,
                        'rate' => $request->rate,
                        'amount' => $request->scrap_amount,
                        'type' => 'Bill Adjustment',
                        'date' => $request->date,
                        'refID' => $ref,
                        'notes' => "Scrap Purchased in Inv # $sale->id",
                    ]
                );
                scrapStock::create(
                    [
                        'date' => $request->date,
                        'cr' => $request->weight,
                        'refID' => $ref,
                        'notes' => "Scrap Purchased in Inv # $sale->id",
                    ]
            );   
            }

            if($request->status == 'paid')
            {
                sale_payments::create(
                    [
                        'salesID'       => $sale->id,
                        'accountID'     => $request->accountID,
                        'date'          => $request->date,
                        'amount'        => $net,
                        'notes'         => "Full Paid",
                        'refID'         => $ref,
                    ]
                );
                createTransaction($request->accountID, $request->date, $net, 0, "Payment of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                createTransaction($request->customerID, $request->date, $net, $net, "Payment of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
            }
            elseif($request->status == 'advanced')
            {
                $balance = getAccountBalance($request->customerID);
                if($net < $balance)
                {
                    createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                    DB::commit();
                    return back()->with('success', "Sale Created: Balance was not enough moved to unpaid / pending");
                }
                else
                {
                    sale_payments::create(
                        [
                            'salesID'       => $sale->id,
                            'accountID'     => $request->accountID,
                            'date'          => $request->date,
                            'amount'        => $net,
                            'notes'         => "Full Paid",
                            'refID'         => $ref,
                        ]
                    );

                    createTransaction($request->customerID, $request->date, $net, 0, "Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                }

            }
            elseif($request->status == 'partial')
            {
                $paid = $request->paid;
                if($paid < 1)
                {
                    createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                    DB::commit();
                    return back()->with('success', "Sale Created: Bill moved to unpaid / pending");
                }
                else
                {
                    sale_payments::create(
                        [
                            'salesID'       => $sale->id,
                            'accountID'     => $request->accountID,
                            'date'          => $request->date,
                            'amount'        => $paid,
                            'notes'         => "Parial Payment",
                            'refID'         => $ref,
                        ]
                    );

                    createTransaction($request->customerID, $request->date, $net, $paid, "Partial Payment of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                    createTransaction($request->accountID, $request->date, $paid, 0, "Partial Payment of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                }
            }
            else
            {
                createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
            }

           DB::commit();
            return to_route('sale.show', $sale->id)->with('success', "Sale Created");
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
    public function show(sales $sale)
    {
        return view('sales.view', compact('sale'));
    }

    public function pdf($id)
    {
        $sale = sales::find($id);
        $pdf = Pdf::loadview('sales.pdf', compact('sale'));
    return $pdf->download("Invoice No. $sale->id.pdf");
    }


    public function edit(sales $sale)
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

        $customers = accounts::customerVendor()->get();
        $accounts = accounts::business()->get();
        foreach($sale->details as $product)
        {
            $stocks = getStock($product->product->id);
            $product->stock = $stocks;
        }
        session()->forget('confirmed_password');
        return view('sales.edit', compact('products', 'customers', 'accounts', 'sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        dashboard();
        try
        {
            DB::beginTransaction();
            $sale = sales::find($id);
            foreach($sale->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            }
            foreach($sale->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $sale->refID)->delete();
            scrapePurchase::where('refID', $sale->refID)->delete();
            scrapStock::where('refID', $sale->refID)->delete();
            $ref = $sale->refID;
            $sale->update(
                [
                'customerID'  => $request->customerID,
                  'date'        => $request->date,
                  'notes'       => $request->notes,
                  'customerName'=> $request->customerName,
                  'payment_status'  => $request->status,
                  'scrap_amount'    => $request->scrap_amount,
                  'payment'     => $request->paid,
                  ]
            );

            $ids = $request->id;

            $total = 0;
           /*  $note_details = ''; */
            foreach($ids as $key => $id)
            {
                if($request->amount[$key] > 0)
                {
                $qty = $request->qty[$key];
                $price = $request->price[$key];
                $retail = $request->retail[$key];
                $purchase_percentage = $request->purchase_percentage[$key];
                $sale_percentage = $request->sale_percentage[$key];
                $pprice = $request->pprice[$key];
                $profit = $request->profit[$key];
                $total += $request->amount[$key];

                sale_details::create(
                    [
                        'salesID'               => $sale->id,
                        'productID'             => $request->productID[$key],
                        'purchase_id'           => $id,
                        'retail'                => $retail,
                        'purchase_percentage'   => $purchase_percentage,
                        'sale_percentage'       => $sale_percentage,
                        'extra_tax'             => $request->extra_tax[$key],
                        'pprice'                => $pprice,
                        'price'                 => $price,
                        'qty'                   => $qty,
                        'amount'                => $request->amount[$key],
                        'profit'                => $profit,
                        'date'                  => $request->date,
                        'refID'                 => $ref,
                    ]
                );
                }

            }

    
            $scrap_amount = $request->scrap_amount;
            $net = $total - $scrap_amount;

            $sale->update(
                [
                    'total'   => $net,
                ]
            );

            if($request->has('file')){
            createAttachment($request->file('file'), $ref);
        }

             if($request->scrap_amount > 0)
            {
                scrapePurchase::create(
                    [
                        'weight' => $request->weight,
                        'rate' => $request->rate,
                        'amount' => $request->scrap_amount,
                        'type' => 'Bill Adjustment',
                        'date' => $request->date,
                        'refID' => $ref,
                        'notes' => "Scrap Purchased in Inv # $sale->id",
                    ]
                );
                scrapStock::create(
                    [
                        'date' => $request->date,
                        'cr' => $request->weight,
                        'refID' => $ref,
                        'notes' => "Scrap Purchased in Inv # $sale->id",
                    ]
            );   
            }

            if($request->status == 'paid')
            {
                sale_payments::create(
                    [
                        'salesID'       => $sale->id,
                        'accountID'     => $request->accountID,
                        'date'          => $request->date,
                        'amount'        => $net,
                        'notes'         => "Full Paid",
                        'refID'         => $ref,
                    ]
                );
                createTransaction($request->accountID, $request->date, $net, 0, "Payment of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                createTransaction($request->customerID, $request->date, $net, $net, "Payment of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
            }
            elseif($request->status == 'advanced')
            {
                $balance = getAccountBalance($request->customerID);
                if($net < $balance)
                {
                    createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                    DB::commit();
                    return back()->with('success', "Sale Created: Balance was not enough moved to unpaid / pending");
                }
                else
                {
                    sale_payments::create(
                        [
                            'salesID'       => $sale->id,
                            'accountID'     => $request->accountID,
                            'date'          => $request->date,
                            'amount'        => $net,
                            'notes'         => "Full Paid",
                            'refID'         => $ref,
                        ]
                    );

                    createTransaction($request->customerID, $request->date, $net, 0, "Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                }

            }
            elseif($request->status == 'partial')
            {
                $paid = $request->paid;
                if($paid < 1)
                {
                    createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                    DB::commit();
                    return back()->with('success', "Sale Created: Bill moved to unpaid / pending");
                }
                else
                {
                    sale_payments::create(
                        [
                            'salesID'       => $sale->id,
                            'accountID'     => $request->accountID,
                            'date'          => $request->date,
                            'amount'        => $paid,
                            'notes'         => "Parial Payment",
                            'refID'         => $ref,
                        ]
                    );

                    createTransaction($request->customerID, $request->date, $net, $paid, "Partial Payment of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                    createTransaction($request->accountID, $request->date, $paid, 0, "Partial Payment of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
                }

            }
            else
            {
                createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Inv No. $sale->id Notes: $request->notes", $ref, 'Sale');
            }

            DB::commit();
            return to_route('sale.index')->with('success', "Sale Updated");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return to_route('sale.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try
        {
            DB::beginTransaction();
            $sale = sales::find($id);
            foreach($sale->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            }
            foreach($sale->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $sale->refID)->delete();
            scrapePurchase::where('refID', $sale->refID)->delete();
            scrapStock::where('refID', $sale->refID)->delete();
            $sale->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('sale.index')->with('success', "Sale Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('sale.index')->with('error', $e->getMessage());
        }
    }

    public function getSignleProduct($id)
    {
        $product = purchase_details::find($id);
        $product->stock = $product->qty - sale_details::where('purchase_id', $id)->sum('qty');
        $product_datails = products::find($product->productID);
        $product->name = $product_datails->name;
        $product->sale_percentage = $product_datails->sale_percentage;
        $product->extra_tax = $product_datails->extra_tax;
        return $product;
    }

    public function getProductByCode($code)
    {
        $product = products::where('code', $code)->first();
        if($product)
        {
           return $product->id;
        }
        return "Not Found";
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\claim_amount;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\products;
use App\Models\purchase_details;
use App\Models\sale_details;
use App\Models\scrapStock;
use App\Models\stock;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimAmountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $claims = claim_amount::all();
        return view('claim_amount.index', compact('claims'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = accounts::customer()->get();
        $sold = sale_details::select('purchase_id', DB::raw('SUM(qty) as total_sold_qty'))
            ->groupBy('purchase_id');

            $claimed = claim_amount::select('purchase_id', DB::raw('SUM(claim_product_qty) as total_claimed_qty'))
            ->groupBy('purchase_id');

         $claim_products = purchase_details::select(
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

            $products = products::all();
        $accounts = accounts::business()->get();
        $vendors = accounts::vendor()->get();
        return view('claim_amount.create', compact('customers', 'products', 'accounts', 'vendors', 'claim_products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customerID = $request->customerID;
        $productID = $request->productID;
        $qty = $request->qty;

        $claimType = $request->claimType;
        $amount = $request->amount;
        $isPaid = $request->isPaid;
        $paidFrom = $request->paidFrom;
        $claim_product_id = $request->claim_product_id;
        $claimQty = $request->claimQty;
        $extraAmount = $request->extraAmount;
        $receivedIn = $request->receivedIn;

        $stock = $request->stock;
        $weight = $request->weight;
        $scrap_notes = $request->scrap_notes;

        $vendorID = $request->vendorID;
        $date = $request->date;
        $notes = $request->notes;

        try {
            DB::beginTransaction();

            $ref = getRef();

            if($claimType == 'Item')
            {
                $product = purchase_details::find($claim_product_id);
                $product_id = $product->productID;
            }

            claim_amount::create([
                'customer_id' => $customerID,
                'product_id' => $productID,
                'qty' => $qty,
                'claim_type' => $claimType,
                'claim_product_id' => $product_id,
                'purchase_id' => $claim_product_id,
                'claim_product_qty' => $claimQty,
                'claim_product_extra_amount' => $extraAmount,
                'claim_product_received_in' => $receivedIn,
                'claim_amount' => $amount,
                'claim_amount_is_paid' => $isPaid,
                'claim_amount_paid_from' => $paidFrom,
                'stock_type' => $stock,
                'stock_weight' => $weight,
                'scrap_notes' => $scrap_notes,
                'vendor_id' => $vendorID,
                'date' => $date,
                'notes' => $notes,
                'status' => 'Pending',
                'refID' => $ref
            ]);

           

            if ($stock == 'Scrap') {
                scrapStock::create(
                    [
                        'date' => $date,
                        'cr' => $weight,
                        'refID' => $ref,    
                        'notes' => $notes,
                    ]
                );
            }

            if($claimType == 'Item') {
               
                if($extraAmount > 0)
                {
                    if($receivedIn == 0)
                    {
                        createTransaction($customerID,$date,$extraAmount,0,"Pending Claim Extra Amount Notes: $notes",$ref,'Claim Amount');
                    }
                    else
                    {
                        createTransaction($customerID,$date,$extraAmount, $extraAmount,"Paid Claim Extra Amount Notes: $notes",$ref,'Claim Amount');
                        createTransaction($receivedIn,$date,$extraAmount,0,"Received Claim Extra Amount Notes: $notes",$ref,'Claim Amount');
                    }
                }
            }

            if($claimType == 'Amount')
            {
               if($isPaid == 'Yes')
               {
                createTransaction($customerID,$date,$amount, $amount,"Claim Amount Paid to Customer Notes: $notes",$ref,'Claim Amount');
                createTransaction($paidFrom,$date,0,$amount,"Claim Amount Paid to Customer Notes: $notes",$ref,'Claim Amount');
               }
               else
               {
                createTransaction($customerID,$date,0, $amount,"Pending / Adjusted Claim Amount Notes: $notes",$ref,'Claim Amount');
               }
            }
            
            DB::commit();
            return redirect()->back()->with('success', 'Claim Amount Created Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(claim_amount $claim_amount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(claim_amount $claim_amount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, claim_amount $claim_amount)
    {
        //
    }

    public function receiving($id)
    {
        $claim = claim_amount::find($id);
        $products = products::all();
        $accounts = accounts::business()->get();
        return view('claim_amount.receiving', compact('claim', 'products', 'accounts'));
    }

    public function receivingStore(Request $request, $id)
    {
        $claim = claim_amount::find($id);
       try {
           DB::beginTransaction();
           $claim->update(
            [
                'status' => 'Completed'
            ]
           );

           if($request->claimType == 'Item')
           {
            createStock($request->claim_product_id, $request->claimQty,0,$request->date, "Claim Issued", $claim->refID, 1);
           }

           if($request->claimType == 'Amount')
           {
                if($request->isPaid == 'Yes')
                {
                    createTransaction($request->receivedIn,$request->date,$request->claimAmount,0,"Claim Amount Received from Vendor Notes: $request->notes",$claim->refID,'Claim Amount');
                    createTransaction($claim->vendor_id,$request->date,$request->claimAmount,$request->claimAmount,"Claim Amount Paid Notes: $request->notes",$claim->refID,'Claim Amount');
                }

                if($request->isPaid == 'No')
                {
                    createTransaction($claim->vendor_id,$request->date,$request->claimAmount,0,"Claim Amount Pending / Adjusted Notes: $request->notes",$claim->refID,'Claim Amount');
                }
           }
           DB::commit();
           return to_route('claim_amount.index')->with('success', 'Claim Received Successfully');
       } catch (\Exception $e) {
           DB::rollBack();
           return to_route('claim_amount.index')->with('error', $e->getMessage());
       }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $claim_amount = claim_amount::find($id);
        try {
            DB::beginTransaction();
            transactions::where('refID', $claim_amount->refID)->delete();
            stock::where('refID', $claim_amount->refID)->delete();
            scrapStock::where('refID', $claim_amount->refID)->delete();
            $claim_amount->delete();
            session()->forget('confirmed_password');
            DB::commit();
            return to_route('claim_amount.index')->with('success', 'Claim Amount Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('claim_amount.index')->with('error', $e->getMessage());
        }
    }
}

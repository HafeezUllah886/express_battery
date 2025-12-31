<?php

namespace App\Http\Controllers;

use App\Http\Middleware\confirmPassword;
use App\Models\accounts;
use App\Models\categories;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\purchase_payments;
use App\Models\stock;
use App\Models\transactions;
use App\Models\units;
use App\Models\warehouses;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class PurchaseController extends Controller
{
    public function __construct()
    {
        // Apply middleware to the edit method
        $this->middleware(confirmPassword::class)->only('edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = purchase::with('payments')->orderby('id', 'desc')->get();
        return view('purchase.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::orderby('name', 'asc')->get();
        $vendors = accounts::customerVendor()->get();
        $accounts = accounts::business()->get();
        $cats = categories::orderBy('name', 'asc')->get();
        return view('purchase.create', compact('products', 'vendors', 'accounts', 'cats'));
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
            $purchase = purchase::create(
                [
                  'vendorID'        => $request->vendorID,
                  'date'            => $request->date,
                  'notes'           => $request->notes,
                  'vendorName'      => $request->vendorName,
                  'payment_status'  => $request->status,
                  'inv'             => $request->inv,
                  'refID'           => $ref,
                ]
            );

            $ids = $request->id;
            dashboard();
            $total = 0;
            foreach($ids as $key => $id)
            {
                if($request->qty[$key] > 0)
                {
                $qty = $request->qty[$key];
                $pprice = $request->pprice[$key];
                $retail = $request->retail[$key];
                $percentage = $request->percentage[$key];
                $amount = $pprice * $qty;
                $total += $amount;

                purchase_details::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'productID'     => $id,
                        'retail'        => $retail,
                        'percentage'    => $percentage,
                        'pprice'        => $pprice,
                        'qty'           => $qty,
                        'amount'        => $amount,
                        'date'          => $request->date,
                        'refID'         => $ref,
                    ]
                );

                }
            }

            $purchase->update(
                [
                    'total' => $total,
                ]
            );

            if($request->status == 'paid')
            {
                purchase_payments::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'accountID'     => $request->accountID,
                        'date'          => $request->date,
                        'amount'        => $total,
                        'notes'         => "Full Paid",
                        'refID'         => $ref,
                    ]
                );

                createTransaction($request->accountID, $request->date, 0, $total, "Payment of Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
                createTransaction($request->vendorID, $request->date, $total, $total, "Payment of Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
            }
            elseif($request->status == 'advanced')
            {
                $balance = getAccountBalance($request->vendorID);
                if($total > $balance)
                {
                    createTransaction($request->vendorID, $request->date, 0, $total, "Pending Amount of Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
                    DB::commit();
                    return back()->with('success', "Purchase Created: Balance was not enough moved to unpaid / pending");
                }
                purchase_payments::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'accountID'     => $request->accountID,
                        'date'          => $request->date,
                        'amount'        => $total,
                        'notes'         => "Full Paid",
                        'refID'         => $ref,
                    ]
                );

                createTransaction($request->vendorID, $request->date, 0, $total, "Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
            }
            else
            {
                createTransaction($request->vendorID, $request->date, 0, $total, "Pending Amount of Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
            }
            DB::commit();
            return back()->with('success', "Purchase Created");

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
    public function show(purchase $purchase)
    {
        return view('purchase.view', compact('purchase'));
    }

    public function pdf($id)
    {
        $purchase = purchase::find($id);
        $pdf = Pdf::loadview('purchase.pdf', compact('purchase'));

        return $pdf->download("Purchase Vouchar No. $purchase->id.pdf");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(purchase $purchase)
    {
        $products = products::orderby('name', 'asc')->get();
        $warehouses = warehouses::all();
        $vendors = accounts::customerVendor()->get();
        $accounts = accounts::business()->get();
        $cats = categories::orderBy('name', 'asc')->get();

        return view('purchase.edit', compact('products', 'warehouses', 'vendors', 'accounts', 'purchase', 'cats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, purchase $purchase)
    {
        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            foreach($purchase->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            }
            foreach($purchase->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $purchase->refID)->delete();

            $purchase->update(
                [
                'vendorID'        => $request->vendorID,
                  'date'            => $request->date,
                  'notes'           => $request->notes,
                  'discount'        => $request->discount,
                  'dc'              => $request->dc,
                  'vendorName'      => $request->vendorName,
                  'payment_status'  => $request->status,
                  'inv'             => $request->inv,
                  ]
            );

            $ids = $request->id;
            $ref = $purchase->refID;

            $total = 0;
            foreach($ids as $key => $id)
            {
                if($request->qty[$key] > 0)
                {
                   $qty = $request->qty[$key];
                $pprice = $request->pprice[$key];
                $retail = $request->retail[$key];
                $percentage = $request->percentage[$key];
                $amount = $pprice * $qty;
                $total += $amount;

                purchase_details::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'productID'     => $id,
                        'pprice'        => $pprice,
                        'retail'        => $retail,
                        'percentage'    => $percentage,
                        'qty'           => $qty,
                        'amount'        => $amount,
                        'date'          => $request->date,
                        'refID'         => $ref,
                        'warehouseID'   => $request->warehouse[$key],
                    ]
                );
                createStock($id, $qty, 0, $request->date, "Purchased", $ref, $request->warehouse[$key]);

                }
            }
           
             $net = ($total + $request->dc) - $request->discount;
        
            $purchase->update(
                [

                    'total'       => $net,
                ]
            );

            if($request->status == 'paid')
            {
                purchase_payments::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'accountID'     => $request->accountID,
                        'date'          => $request->date,
                        'amount'        => $net,
                        'notes'         => "Full Paid",
                        'refID'         => $ref,
                    ]
                );
                createTransaction($request->accountID, $request->date, 0, $net, "Payment of Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
                createTransaction($request->vendorID, $request->date, $net, $net, "Payment of Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
            }
            elseif($request->status == 'advanced')
            {
                $balance = getAccountBalance($request->vendorID);
                if($net > $balance)
                {
                    createTransaction($request->vendorID, $request->date, 0, $net, "Pending Amount of Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
                    DB::commit();
                    return back()->with('success', "Purchase Created: Balance was not enough moved to unpaid / pending");
                }
                purchase_payments::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'accountID'     => $request->accountID,
                        'date'          => $request->date,
                        'amount'        => $net,
                        'notes'         => "Full Paid",
                        'refID'         => $ref,
                    ]
                );
                createTransaction($request->vendorID, $request->date, 0, $net, "Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
            }
            else
            {
                createTransaction($request->vendorID, $request->date, 0, $net, "Pending Amount of Purchase No. $purchase->id Notes: $request->notes", $ref, 'Purchase');
            }
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('purchase.index')->with('success', "Purchase Updated");
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->with('error', $e->getMessage());
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
            $purchase = purchase::find($id);
            foreach($purchase->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            }
            foreach($purchase->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $purchase->refID)->delete();
            $purchase->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('purchase.index')->with('success', "Purchase Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('purchase.index')->with('error', $e->getMessage());
        }
    }

    public function getSignleProduct($id)
    {
        $product = products::find($id);
        return $product;
    }
}

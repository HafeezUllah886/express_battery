<?php

namespace App\Http\Controllers;

use App\Models\scrapePurchase;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\sales;
use App\Models\scrapStock;
use App\Models\stock;
use App\Models\transactions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScrapePurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scrapePurchases = scrapePurchase::orderBy('id', 'desc')->get();
        $accounts = accounts::business()->get();
        return view('scrape.purchase.index', compact('scrapePurchases', 'accounts'));
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
    $refID = getRef();
    $request->merge(['refID' => $refID]);
    $purchase = scrapePurchase::create($request->except('accountID'));

    scrapStock::create(
        [
            'date' => $request->date,
            'cr' => $request->weight,
            'refID' => $refID,
            'notes' => $request->notes,
        ]
    );

    if($request->type == 'Payment') {
        createTransaction($request->accountID, $request->date, 0, $request->amount, "Payment of Scrap Purchase",$refID, 'Scrap Purchase');
    }
    
    return redirect()->route('scrap_purchase.index')->with('success', 'Scrap Purchase Created');
}
    
    /**
     * Display the specified resource.
     */
    public function show(scrapePurchase $scrapePurchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(scrapePurchase $scrapePurchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, scrapePurchase $scrapePurchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ref)
    {
        try
        {
            DB::beginTransaction();
        scrapePurchase::where('refID', $ref)->delete();
        scrapStock::where('refID', $ref)->delete();
        transactions::where('refID', $ref)->delete();
        $sales = sales::where('refID', $ref)->first();
        if($sales)
        {
           foreach($sales->details as $detail)
           {
            $detail->delete();
           }
           foreach($sales->payments as $payment)
           {
            $payment->delete();
           }
            $sales->delete();
        }
        stock::where('refID', $ref)->delete();
        DB::commit();
        session()->forget('confirmed_password');
        return redirect()->route('scrap_purchase.index')->with('success', 'Scrap Purchase Deleted');
        }
        catch(Exception $e)
        {
            DB::rollBack();
             session()->forget('confirmed_password');
            return redirect()->route('scrap_purchase.index')->with('error', 'Scrap Purchase Not Deleted');
        }
    }
}

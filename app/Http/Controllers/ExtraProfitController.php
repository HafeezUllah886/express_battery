<?php

namespace App\Http\Controllers;

use App\Models\extraProfit;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExtraProfitController extends Controller
{
     public function index()
    {
        $extra_profits = extraProfit::orderBy('id', 'desc')->get();
        $accounts = accounts::Business()->get();

        return view('Finance.extra_profit.index', compact('extra_profits', 'accounts'));
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
        try
        {
            DB::beginTransaction();
            $ref = getRef();
            extraProfit::create(
                [
                    'accountID' => $request->accountID,
                    'from' => $request->from,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'notes' => $request->notes,
                    'refID' => $ref,
                ]
            );

            createTransaction($request->accountID, $request->date, $request->amount, 0, "Extra Profit from " . $request->from . "<br>" . $request->notes, $ref, "Extra Profit");

            DB::commit();
            return back()->with('success', 'Extra Profit Saved');
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $receiving = paymentReceiving::find($id);
        return view('Finance.receiving.receipt', compact('receiving'));
    }

    public function pdf($id)
    {
        $receiving = paymentReceiving::find($id);
        $pdf = Pdf::loadview('Finance.receiving.pdf', compact('receiving'));
        $pdf->set_paper('letter', 'landscape');
        return $pdf->download("Receiving - $receiving->refID.pdf");
    }

    public function edit(paymentReceiving $paymentReceiving)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, paymentReceiving $paymentReceiving)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($refID)
    {
        try
        {
            DB::beginTransaction();
            extraProfit::where('refID', $refID)->delete();
            transactions::where('refID', $refID)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return back()->with('success', 'Extra Profit Deleted');
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return back()->with('error', $e->getMessage());
        }
    }
}

@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-9">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                            <a href="javascript:window.print()" class="btn btn-success ml-4"><i
                                    class="ri-printer-line mr-4"></i> Print</a>
                        </div>
                        <div class="card-header border-bottom-dashed p-4">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h1>{{ projectNameAuth() }}</h1>
                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">
                                    <h3>Profit / Loss Report</h3>
                                </div>
                            </div>
                        </div>
                        <!--end card-header-->
                    </div><!--end col-->
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">From</p>
                                    <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($from)) }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">To</p>
                                    <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($to)) }}</h5>
                                </div>
                                <!--end col-->
                                <!--end col-->
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                    <h5 class="fs-14 mb-0"><span id="total-amount">{{ date('d M Y') }}</span></h5>
                                    {{-- <h5 class="fs-14 mb-0"><span id="total-amount">{{ \Carbon\Carbon::now()->format('h:i A') }}</span></h5> --}}
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end card-body-->
                    </div><!--end col-->
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center table-nowrap align-middle mb-0">
                                    <thead>
                                        <tr class="table-active">
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col">Bill #</th>
                                            <th scope="col">Date</th>
                                            <th scope="col" class="text-end">Retail</th>
                                            <th scope="col" class="text-end">Purchase %</th>
                                            <th scope="col" class="text-end">Sale %</th>
                                            <th scope="col" class="text-end">Sold Qty</th>
                                            <th scope="col" class="text-end">Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalProfit = 0;
                                        @endphp
                                        @foreach ($products as $key => $product)
                                            @if ($product->sales->count() > 0)
                                                <tr class="table-active">
                                                    <td class="text-start p-1" colspan="8">{{ $product->name }}</td>
                                                </tr>
                                                @foreach ($product->sales as $key => $sale)
                                                    @php
                                                        $totalProfit += $sale->profit * $sale->qty;
                                                    @endphp
                                                    <tr>
                                                        <td class="p-1">{{ $key + 1 }}</td>
                                                        <td class="p-1">{{ $sale->salesID }}</td>
                                                        <td class="p-1">{{ date('d M Y', strtotime($sale->date)) }}</td>
                                                        <td class="text-end p-1">{{ number_format($sale->retail, 2) }}</td>
                                                        <td class="text-end p-1">
                                                            {{ number_format($sale->purchase_percentage, 2) }}</td>
                                                        <td class="text-end p-1">
                                                            {{ number_format($sale->sale_percentage, 2) }}
                                                        </td>
                                                        <td class="text-end p-1">{{ number_format($sale->qty, 2) }}</td>
                                                        <td class="text-end p-1">
                                                            {{ number_format($sale->profit * $sale->qty, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="7" class="text-end p-1">Total</th>
                                            <th class="text-end p-1">{{ number_format($totalProfit, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="7" class="text-end p-1">Expense</th>
                                            <th class="text-end p-1">{{ number_format($expenses, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="7" class="text-end p-1">Extra Profit</th>
                                            <th class="text-end p-1">{{ number_format($extraProfit, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="7" class="text-end p-1">Net Profit</th>
                                            <th class="text-end p-1">
                                                {{ number_format($totalProfit - $expenses + $extraProfit, 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table><!--end table-->
                            </div>

                        </div>
                        <!--end card-body-->
                    </div><!--end col-->
                </div><!--end row-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection

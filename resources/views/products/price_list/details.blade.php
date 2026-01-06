@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
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
                                    <h3>Price List</h3>
                                </div>
                            </div>
                        </div>
                        <!--end card-header-->
                    </div><!--end col-->

                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center table-nowrap align-middle mb-0">
                                    <thead>
                                        <tr class="table-active">
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col" class="text-start">Product</th>
                                            <th scope="col">Plates</th>
                                            <th scope="col" class="text-end">Retail</th>
                                            @for ($i = $start; $i <= $end; $i++)
                                                <th scope="col" class="text-end">{{ $i }}%</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($products as $key => $product)
                                            <tr>
                                                <td class="p-1">{{ $key + 1 }}</td>
                                                <td class="p-1 text-start">{{ $product->name }}</td>
                                                <td class="p-1">{{ $product->plates }}</td>
                                                <td class="text-end p-1">{{ number_format($product->price, 0) }}</td>
                                                @for ($i = $start; $i <= $end; $i++)
                                                    @php
                                                        $extra_tax_value =
                                                            ($product->price * $product->extra_tax) / 100;
                                                        if ($i > 18) {
                                                            $percentagewithouttax = $i - 18;
                                                            $value =
                                                                $product->price -
                                                                ($product->price * $percentagewithouttax) / 100 +
                                                                $extra_tax_value;
                                                        } elseif ($i < 18) {
                                                            $difference = 18 - $i;
                                                            $percentagewithouttax = $i + $difference;
                                                            $value =
                                                                $product->price +
                                                                ($product->price * $difference) / 100 +
                                                                $extra_tax_value;
                                                        } else {
                                                            $value = $product->price + $extra_tax_value;
                                                        }
                                                    @endphp
                                                    <td class="text-end p-1">
                                                        {{ number_format($value, 0) }}</td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    </tbody>

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

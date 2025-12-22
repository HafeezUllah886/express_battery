@extends('layout.popups')
@section('content')
    <script>
        var existingProducts = [];

        @foreach ($sale->details as $product)
            @php
                $productID = $product->productID;
            @endphp
            existingProducts.push({{ $productID }});
        @endforeach
    </script>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <h3> Edit Sale </h3>
                            </div>
                            <div class="col-6 d-flex flex-row-reverse">
                                <button onclick="window.close()" class="btn btn-danger">Close</button>
                                <button type="button" class="btn btn-primary mr-2" data-bs-toggle="modal"
                                    data-bs-target="#scrap_purchase">Create Scrap Purchase</button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('sale.update', $sale->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div id="scrap_purchase" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel"
                            aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="myModalLabel">Create Scrap Purchase</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        </button>
                                    </div>

                                    @csrf
                                    @php
                                        if ($sale->scrap_amount > 0) {
                                            $scrap = App\Models\scrapePurchase::where('refID', $sale->refID)->first();
                                            $weight = $scrap->weight;
                                            $rate = $scrap->rate;
                                            $amount = $scrap->amount;
                                        } else {
                                            $weight = 0;
                                            $rate = 0;
                                            $amount = 0;
                                        }
                                    @endphp
                                    <div class="modal-body">
                                        <div class="form-group mt-2">
                                            <label for="weight">Weight</label>
                                            <div class="input-group">
                                                <input type="number" step="any" value="{{ $weight }}"
                                                    oninput="updateAmount()" name="weight" id="weight"
                                                    class="form-control">
                                                <span class="input-group-text">Kg</span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-2">
                                            <label for="rate">Rate</label>
                                            <input type="number" step="any" value="{{ $rate }}"
                                                oninput="updateAmount()" name="rate" id="rate" class="form-control">
                                        </div>
                                        <div class="form-group mt-2">
                                            <label for="amount">Amount</label>
                                            <input type="number" step="any" value="{{ $amount }}"
                                                name="scrap_amount" readonly id="amount" class="form-control">
                                        </div>

                                    </div>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="product">Product</label>
                                    <select name="product" class="selectize" id="product">
                                        <option value=""></option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">

                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="20%">Product</th>
                                        <th class="text-center">Warehouse</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Retail</th>
                                        <th class="text-center">Percentage</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Amount</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="products_list">
                                        @foreach ($sale->details as $product)
                                            @php
                                                $id = $product->product->id;
                                                $stock = $product->stock + $product->qty;
                                            @endphp
                                            <tr id="row_{{ $id }}">
                                                <td class="no-padding">{{ $product->product->name }}</td>
                                                <td class="no-padding"><select name="warehouse[]"
                                                        class="form-control text-center no-padding"
                                                        id="warehouse_{{ $id }}">
                                                        @foreach ($warehouses as $warehouse)
                                                            <option value="{{ $warehouse->id }}"
                                                                @selected($warehouse->id == $product->warehouseID)>{{ $warehouse->name }}
                                                            </option>
                                                        @endforeach
                                                    </select></td>

                                                <td class="no-padding">
                                                    <div class="input-group"><span class="input-group-text no-padding"
                                                            id="stockValue_{{ $id }}">{{ $stock }}</span><input
                                                            type="number" max="{{ $stock }}" name="qty[]"
                                                            oninput="updateChanges({{ $id }})" min="0"
                                                            required step="any" value="{{ $product->qty }}"
                                                            class="form-control text-center no-padding"
                                                            id="qty_{{ $id }}"></div>
                                                </td>
                                                <td class="no-padding"><input type="number" name="retail[]" required
                                                        step="any" value="{{ $product->retail }}" min="0"
                                                        class="form-control text-center no-padding"
                                                        oninput="updateChanges({{ $id }})"
                                                        id="retail_{{ $id }}"></td>
                                                <td class="no-padding"><input type="number" name="percentage[]" required
                                                        step="any" value="{{ $product->percentage }}" min="0"
                                                        class="form-control text-center no-padding"
                                                        oninput="updateChanges({{ $id }})"
                                                        id="percentage_{{ $id }}"></td>
                                                <td class="no-padding"><input type="number" name="price[]" required
                                                        step="any" value="{{ $product->price }}" min="0"
                                                        class="form-control text-center no-padding"
                                                        oninput="updateChanges({{ $id }})"
                                                        id="price_{{ $id }}"></td>
                                                <td class="no-padding"><input type="number" name="amount[]"
                                                        min="0.1" readonly required step="any"
                                                        value="{{ $product->amount }}"
                                                        class="form-control text-center no-padding"
                                                        id="amount_{{ $id }}"></td>
                                                <td class="no-padding"> <span class="btn btn-sm btn-danger"
                                                        onclick="deleteRow({{ $id }})">X</span> </td>
                                                <input type="hidden" name="id[]" value="{{ $id }}">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6" class="text-end">Total</th>
                                            <th class="text-end" id="totalAmount">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="scrap_amount">Scrap Amount</label>
                                    <input type="number" name="scrap_amount" oninput="updateTotal()" id="scrap_amount"
                                        value="{{ $sale->scrap_amount }}" step="any" class="form-control no_zero">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="discount">Discount</label>
                                    <input type="number" name="discount" oninput="updateTotal()" id="discount"
                                        value="{{ $sale->discount }}" step="any" class="form-control no_zero">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="dc">Delivery Charges</label>
                                    <input type="number" name="dc" id="dc" oninput="updateTotal()"
                                        min="0" value="{{ $sale->dc }}" step="any"
                                        class="form-control no_zero">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="net">Net Amount</label>
                                    <input type="number" name="net" id="net" step="any" readonly
                                        value="{{ $sale->total }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" value="{{ $sale->date }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="customer">Customer</label>
                                    <select name="customerID" id="customerID" class="selectize1">
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" @selected($customer->id == $sale->customerID)>
                                                {{ $customer->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group customerName mt-2">
                                    <label for="customerName">Name</label>
                                    <input type="text" name="customerName" value="{{ $sale->customerName }}"
                                        id="customerName" class="form-control">
                                </div>
                            </div>

                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="account">Account</label>
                                    <select name="accountID" id="account" class="selectize1">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="status">Payment Status</label>
                                    <select name="status" id="status1" class="selectize1">
                                        <option value="advanced" @selected($sale->payment_status == 'advanced')>Paid in Advance</option>
                                        <option value="paid" @selected($sale->payment_status == 'paid')>Paid</option>
                                        <option value="pending" @selected($sale->payment_status == 'pending')>Pending</option>
                                        <option value="partial" @selected($sale->payment_status == 'partial')>Partial Payment</option>
                                    </select>
                                </div>
                                <div class="form-group d-none paid mt-2">
                                    <label for="paid">Paid Amount</label>
                                    <input type="number" name="paid" id="paid" value="{{ $sale->payment }}"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="5">{{ $sale->notes }}</textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Update Sale</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!--end row-->

        </div>
        <!--end card-->
    </div>
    <!--end col-->
    </div>
    <!--end row-->
@endsection

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
    <style>
        .no-padding {
            padding: 5px 5px !important;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>
        $(".selectize1").selectize();
        $(".selectize").selectize({
            onChange: function(value) {
                if (!value.length) return;
                if (value != null) {
                    getSingleProduct(value);
                    this.clear();
                    this.focus();
                }

            },
        });
        var warehouses = @json($warehouses);

        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('sales/getproduct/') }}/" + id,
                method: "GET",
                success: function(product) {
                    let found = $.grep(existingProducts, function(element) {
                        return element === product.id;
                    });
                    if (found.length > 0) {

                    } else {
                        var id = product.id;
                        var html = '<tr id="row_' + id + '">';
                        html += '<td class="no-padding">' + product.name + '</td>';
                        html +=
                            '<td class="no-padding"><select name="warehouse[]" class="form-control text-center no-padding" id="warehouse_' +
                            id + '">';
                        warehouses.forEach(function(warehouse) {
                            html += '<option value="' + warehouse.id + '" >' + warehouse.name +
                                '</option>';
                        });
                        html += '</select></td>';
                        html +=
                            '<td class="no-padding"><div class="input-group"><span class="input-group-text no-padding" id="stockValue_' +
                            id + '">' + product.stock + '</span><input type="number" max="' + product.stock +
                            '" name="qty[]" oninput="updateChanges(' + id +
                            ')" min="0" required step="any" value="1" class="form-control text-center no-padding" id="qty_' +
                            id + '"></div></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="retail[]" oninput="updateChanges(' +
                            id + ')" required step="any" value="' + product.price +
                            '" min="1" class="form-control text-center" id="retail_' + id + '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="percentage[]" oninput="updateChanges(' +
                            id +
                            ')" required step="any" value="0" min="1" class="form-control text-center" id="percentage_' +
                            id + '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="price[]" oninput="updateChanges(' +
                            id + ')" required step="any" value="' + product.price +
                            '" min="1" class="form-control text-center" id="price_' + id + '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="amount[]" readonly step="any" value="0.00" min="0" class="form-control text-center" id="amount_' +
                            id + '"></td>';
                        html += '<td> <span class="btn btn-sm btn-danger" onclick="deleteRow(' + id +
                            ')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '<input type="hidden" id="stock_' + id + '" value="' + product.stock + '">';
                        html += '</tr>';
                        $("#products_list").prepend(html);
                        updateChanges(id);
                        existingProducts.push(id);
                    }
                }
            });
        }

        function updateChanges(id) {
            var retail = $('#retail_' + id).val();
            var percentage = $('#percentage_' + id).val();
            var stock = $('#stock_' + id).val();
            var value = retail - (retail * percentage / 100);
            $("#qty_" + id).attr("max", stock);
            var qty = $('#qty_' + id).val();

            var amount = value * qty;

            $("#amount_" + id).val(amount);
            $("#price_" + id).val(value);

            updateTotal();
        }

        updateTotal();

        function updateTotal() {
            var totalAmount = 0;
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                totalAmount += parseFloat(inputValue);
            });
            $("#totalAmount").html(totalAmount.toFixed(2));

            var discount = parseFloat($("#discount").val());
            var dc = parseFloat($("#dc").val());
            var scrap_amount = parseFloat($("#scrap_amount").val());

            var net = (totalAmount + dc) - discount - scrap_amount;

            $("#net").val(net);
        }

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_' + id).remove();
            updateTotal();
        }

        function checkAccount() {
            var id = $("#customerID").find(":selected").val();
            if (id == 2) {
                $(".customerName").removeClass("d-none");
                $('#status1 option').each(function() {
                    var optionValue = $(this).val();
                    if (optionValue === 'advanced' || optionValue === 'pending' || optionValue === 'partial') {
                        $(this).prop('disabled', true);
                    }
                    if (optionValue === 'paid') {
                        $(this).prop('selected', true);
                    }
                });
            } else {
                $(".customerName").addClass("d-none");
                $('#status1 option').each(function() {
                    var optionValue = $(this).val();
                    if (optionValue === 'advanced' || optionValue === 'pending' || optionValue === 'partial') {
                        $(this).prop('disabled', false);
                    }
                });
            }
        }

        $("#customerID").on("change", function() {
            checkAccount();
        });

        checkAccount();

        function updateAmount() {
            const weight = parseFloat(document.getElementById('weight').value) || 0;
            const rate = parseFloat(document.getElementById('rate').value) || 0;
            const amount = weight * rate;
            document.getElementById('amount').value = amount.toFixed(2);
            document.getElementById('scrap_amount').value = amount.toFixed(2);
            updateTotal();

        }

        updateAmount();
    </script>
@endsection

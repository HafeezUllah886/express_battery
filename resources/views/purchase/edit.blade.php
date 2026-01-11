@extends('layout.popups')
@section('content')
    <script>
        var existingProducts = [];

        @foreach ($purchase->details as $product)
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
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h3> Edit Purchase </h3>
                                </div>
                                <div class="col-6 d-flex flex-row-reverse">
                                    <button onclick="window.close()" class="btn btn-danger">Close</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">

                    <div class="row">
                        <div class="row">
                            <div class="col-9">
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
                            <div class="col-3">
                                <div class="form-group">
                                    <form method="get" id="code_form">
                                        <label for="code">Code</label>
                                        <input type="text" class="form-control" id="code">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <form action="{{ route('purchase.update', $purchase->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <th width="30%">Item</th>
                                                <th class="text-center">Retail</th>
                                                <th class="text-center">Percentage</th>
                                                <th class="text-center">Price</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Amount</th>
                                                <th></th>
                                            </thead>
                                            <tbody id="products_list">
                                                @foreach ($purchase->details as $product)
                                                    @php
                                                        $id = $product->product->id;
                                                    @endphp
                                                    <tr id="row_{{ $id }}">
                                                        <td class="no-padding">{{ $product->product->name }}</td>
                                                        <td class="no-padding"><input type="number" readonly
                                                                name="retail[]" oninput="updateChanges({{ $id }})"
                                                                required step="any" value="{{ $product->retail }}"
                                                                min="1" class="form-control text-center no-padding"
                                                                id="retail_{{ $id }}"></td>
                                                        <td class="no-padding"><input type="number" name="percentage[]"
                                                                required step="any" value="{{ $product->percentage }}"
                                                                oninput="updateChanges({{ $id }})" min="0"
                                                                min="0" class="form-control text-center no-padding"
                                                                id="percentage_{{ $id }}"></td>
                                                        <td class="no-padding"><input type="number" name="pprice[]"
                                                                required step="any" readonly
                                                                value="{{ $product->pprice }}" min="0"
                                                                class="form-control text-center no-padding"
                                                                id="pprice_{{ $id }}"></td>
                                                        <td class="no-padding"><input type="number" name="qty[]"
                                                                oninput="updateChanges({{ $id }})" min="0"
                                                                required step="any" value="{{ $product->qty }}"
                                                                class="form-control text-center no-padding"
                                                                id="qty_{{ $id }}"></td>
                                                        <td class="no-padding"><input type="number" name="amount[]"
                                                                min="0.1" readonly required step="any"
                                                                value="{{ $product->amount }}" readonly
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
                                                    <th colspan="4" class="text-end">Total</th>
                                                    <th class="text-center" id="totalQty">0.00</th>
                                                    <th class="text-end" id="totalAmount">0.00</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <label for="comp">Purchase Inv No.</label>
                                                        <input type="text" name="inv" id="inv"
                                                            value="{{ $purchase->inv }}" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <label for="date">Date</label>
                                                        <input type="date" name="date" id="date"
                                                            value="{{ $purchase->date }}" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <label for="vendor">Vendor</label>
                                                        <select name="vendorID" id="vendorID" class="selectize1">
                                                            @foreach ($vendors as $vendor)
                                                                <option value="{{ $vendor->id }}"
                                                                    @selected($vendor->id == $purchase->vendorID)>{{ $vendor->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group vendorName">
                                                        <label for="vendorName">Name</label>
                                                        <input type="text" name="vendorName"
                                                            value="{{ $purchase->vendorName }}" id="vendorName"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <label for="account">Account</label>
                                                        <select name="accountID" id="account" class="selectize1">
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->id }}">{{ $account->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <label for="status">Payment Status</label>
                                                        <select name="status" id="status1" class="form-control">
                                                            <option value="advanced" @selected($purchase->payment_status == 'advanced')>Paid in
                                                                Advance</option>
                                                            <option value="paid" @selected($purchase->payment_status == 'paid')>Paid
                                                            </option>
                                                            <option value="pending" @selected($purchase->payment_status == 'pending')>Pending
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <label for="attachment">Attachment</label>
                                                        <input type="file" class="form-control" name="file">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <label for="warehouse">Warehouse</label>
                                                        <select name="warehouseID" id="warehouseID"required
                                                            class="form-control">
                                                            <option value="">Select Warehouse</option>
                                                            @foreach ($warehouses as $warehouse)
                                                                <option value="{{ $warehouse->id }}"
                                                                    @selected($warehouse->id == $purchase->warehouseID)>{{ $warehouse->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <div class="form-group">
                                                        <label for="notes">Notes</label>
                                                        <textarea name="notes" id="notes" class="form-control" cols="30" rows="5">{{ $purchase->notes }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <button type="submit" class="btn btn-primary w-100">Update
                                                        Purchase</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </form>
                    </div>

                </div>

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

        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('purchases/getproduct/') }}/" + id,
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
                            '<td class="no-padding"><input type="number" readonly name="retail[]" oninput="updateChanges(' +
                            id + ')" step="any" value="' + product.price +
                            '" min="1" class="form-control text-center no-padding" id="retail_' + id +
                            '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="percentage[]" oninput="updateChanges(' +
                            id +
                            ')" step="any" value="0" min="0" class="form-control text-center no-padding" id="percentage_' +
                            id +
                            '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" readonly name="pprice[]" step="any" value="' +
                            product.price +
                            '" min="0" class="form-control text-center no-padding" id="pprice_' +
                            id + '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="qty[]" oninput="updateChanges(' +
                            id +
                            ')" min="0" step="any" value="0" class="form-control text-center no-padding" id="qty_' +
                            id + '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="amount[]" min="0.1" readonly required step="any" value="1" class="form-control text-center no-padding" id="amount_' +
                            id + '"></td>';
                        html +=
                            '<td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow(' +
                            id + ')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '</tr>';
                        $("#products_list").prepend(html);
                        existingProducts.push(id);
                        updateChanges(id);
                    }
                }
            });
        }

        function updateChanges(id) {
            var qty = parseFloat($('#qty_' + id).val());
            var retail = parseFloat($('#retail_' + id).val());
            var percentage = parseFloat($('#percentage_' + id).val());
            if (percentage > 18) {
                var percentagewithouttax = percentage - 18;
                var value = retail - (retail * percentagewithouttax / 100);
            } else if (percentage < 18) {
                var difference = 18 - percentage;
                var percentagewithouttax = percentage + difference;
                var value = retail + (retail * difference / 100);
            } else {
                var percentagewithouttax = percentage;
                var value = retail;
            }

            var amount = qty * value;
            $("#amount_" + id).val(amount.toFixed(2));
            $("#pprice_" + id).val(value.toFixed(2));
            updateTotal();
        }
        updateTotal();

        function updateTotal() {
            var total = 0;
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                total += parseFloat(inputValue);
            });

            $("#totalAmount").html(total.toFixed(2));

            var net = total;

            $("#net").val(net.toFixed(2));
            var count = $("[id^='row_']").length;
            var numQty = 0;
            $("input[id^='qty_']").each(function() {
                var value = parseFloat($(this).val());
                var unit = $("")
                if (!isNaN(value)) {
                    numQty += value;
                }
            });
            $("#totalQty").html(count + "(" + numQty + ")");
        }

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_' + id).remove();
            updateTotal();
        }

        function checkAccount() {
            var id = $("#vendorID").find(":selected").val();
            if (id == 3) {
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


        $("#vendorID").on("change", function() {
            checkAccount();
        });
        checkAccount();

        function generateCode() {

            $.ajax({
                url: "{{ url('product/generateCode') }}",
                method: "GET",
                success: function(code) {

                    $("#code1").val(code);
                }
            });

        }

        $(document).ready(function() {
            $('#productForm').submit(function(e) {
                e.preventDefault(); // Prevent default form submission

                $.ajax({
                    url: '{{ url('/productAjax') }}', // Your GET URL
                    method: 'GET',
                    data: $(this).serialize(), // Serialize the form data
                    success: function(response) {
                        $("#new").modal('hide');
                        if (response.response == "Exists") {
                            Toastify({
                                text: "Product Already Exists",
                                className: "info",
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "linear-gradient(to right, #FF5733, #E70000)",
                                }
                            }).showToast();
                        } else {
                            getSingleProduct(response.response);
                        }

                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                        // Handle errors
                    }
                });
            });
        });

        $("#code_form").on("submit", function(e) {
            e.preventDefault();
            var code = $("#code").val();
            $("#code").val('');
            $.ajax({
                url: "{{ url('product/searchByCode/') }}/" + code,
                method: "GET",
                success: function(response) {
                    if (response == "Not Found") {
                        Toastify({
                            text: "Product Not Found",
                            className: "info",
                            close: true,
                            gravity: "top", // `top` or `bottom`
                            position: "center", // `left`, `center` or `right`
                            stopOnFocus: true, // Prevents dismissing of toast on hover
                            style: {
                                background: "linear-gradient(to right, #FF5733, #E70000)",
                            }
                        }).showToast();
                    } else {
                        getSingleProduct(response);
                    }
                }
            });
        });
    </script>
@endsection

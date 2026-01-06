@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h3> Create Quotation </h3>
                                </div>
                                <div class="col-6 d-flex flex-row-reverse">
                                    <button onclick="window.close()" class="btn btn-danger">Close</button>
                                    <button type="button" class="btn btn-primary" style="margin-right:10px;"
                                        data-bs-toggle="modal" data-bs-target="#new">Add Product</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('quotation.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="product">Product</label>
                                    <select name="product" class="selectize" id="product">
                                        <option value=""></option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}
                                                ({{ $product->avail_qty }})
                                                | {{ date('d-m-Y', strtotime($product->date)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">

                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="20%">Product</th>
                                        <th class="text-center">Retail</th>
                                        <th class="text-center">Sale Percentage</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Amount</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="products_list"></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total</th>
                                            <th class="text-end" id="totalQty">0.00</th>
                                            <th></th>
                                            <th class="text-end" id="totalAmount">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-2 mt-2">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-2 mt-2">
                                <div class="form-group">
                                    <label for="valid">Valid Till</label>
                                    <input type="date" name="valid" id="valid" required class="form-control">
                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="customer">Customer Name</label>
                                    <input type="text" name="customer" id="customer" required class="form-control">
                                </div>
                            </div>
                            <div class="col-5 mt-2">
                                <div class="form-group">
                                    <label for="address">Customer Address</label>
                                    <input type="text" name="address" id="address" required class="form-control">
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Create Quotation</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form id="productForm">
                    <div class="modal-body">
                        <div class="form-group mt-2">
                            <label for="name">Name</label>
                            <input type="text" name="name" required id="name" class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="catID">Category</label>
                            <select name="catID" id="catID" class="form-control">
                                @foreach ($cats as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="pprice">Purchase Price</label>
                            <input type="number" step="any" required name="pprice" value="0" min="0"
                                id="pprice" class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="price">Sale Price</label>
                            <input type="number" step="any" required name="price" value="0" min="0"
                                id="price" class="form-control">
                        </div>
                        {{--  <div class="form-group mt-2">
                                <label for="discount">Discount</label>
                                <input type="number" step="any" name="discount" required value="0" min="0" id="discount" class="form-control">
                            </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

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
        var existingProducts = [];

        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('sales/getproduct/') }}/" + id,
                method: "GET",
                success: function(product) {
                    let found = $.grep(existingProducts, function(element) {
                        return element === product.id;
                    });
                    if (found.length > 0) {} else {
                        var id = product.id;
                        var html = '<tr id="row_' + id + '">';
                        html += '<td class="no-padding">' + product.name + '</td>';

                        html +=
                            '<td class="no-padding"><input type="number" name="retail[]" readonly oninput="updateChanges(' +
                            id + ')" step="any" value="' + product.retail +
                            '" min="1" class="form-control text-center no-padding" id="retail_' + id +
                            '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="sale_percentage[]" oninput="updateChanges(' +
                            id +
                            ')" step="any" value="' + product.sale_percentage +
                            '" class="form-control text-center no-padding" id="sale_percentage_' +
                            id +
                            '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="qty[]" oninput="updateChanges(' +
                            id +
                            ')" min="0" required step="any" value="1" class="form-control text-center no-padding" id="qty_' +
                            id + '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="price[]" oninput="updateChanges(' +
                            id +
                            ')" step="any" value="0" min="1" class="form-control text-center no-padding" id="price_' +
                            id + '"></td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="amount[]" readonly step="any" value="0.00" min="0" class="form-control text-center no-padding" id="amount_' +
                            id + '"></td>';
                        html +=
                            '<td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow(' +
                            id + ')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '<input type="hidden" name="productID[]" value="' + product.productID + '">';
                        html += '<input type="hidden" name="purchase_percentage[]" id="purchase_percentage_' +
                            id + '" value="' + product
                            .percentage + '">';
                        html += '<input type="hidden" name="extra_tax[]" id="extra_tax_' + id + '" value="' +
                            product.extra_tax +
                            '">';
                        html += '<input type="hidden" name="pprice[]" id="pprice_' + id + '" value="' + product
                            .pprice +
                            '">';
                        html += '</tr>';
                        $("#products_list").prepend(html);
                        updateChanges(id);
                        existingProducts.push(id);
                    }
                }
            });
        }

        function updateChanges(id) {
            var retail = parseFloat($('#retail_' + id).val());
            var percentage = parseFloat($('#sale_percentage_' + id).val());
            var extra_tax = parseFloat($('#extra_tax_' + id).val());
            var extra_tax_value = parseFloat(retail * extra_tax / 100);

            if (percentage > 18) {
                var percentagewithouttax = parseFloat(percentage - 18);
                var value = retail - (retail * percentagewithouttax / 100) + extra_tax_value;
            } else if (percentage < 18) {
                var difference = parseFloat(18 - percentage);
                var percentagewithouttax = parseFloat(percentage + difference);
                var value = parseFloat(retail + (retail * difference / 100)) + extra_tax_value;
            } else {
                var percentagewithouttax = parseFloat(percentage);
                var value = parseFloat(retail + extra_tax_value);
            }


            var qty = $('#qty_' + id).val();
            $('#price_' + id).val(value);

            var amount = value * qty;

            $("#amount_" + id).val(amount.toFixed(0));

            updateTotal();
        }

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

            var net = (totalAmount + dc) - discount;

            $("#net").val(net);
        }

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_' + id).remove();
            updateTotal();
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
    </script>
    @foreach ($products as $product)
        @if ($product->isDefault == 'Yes')
            <script>
                getSingleProduct({{ $product->id }});
            </script>
        @endif
    @endforeach
@endsection

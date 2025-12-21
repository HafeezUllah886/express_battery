<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
<head>
    <meta charset="utf-8" />
    <title>Business Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Business Management System" name="description" />
    <meta content="Hafeez Ullah" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/toastify/toastify.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/pos.css')}}">

</head>

<body>

    <div class="row " style="height: 100vh; margin:0; overflow:hidden; background:rgb(194, 194, 194);">
        <div class="col-6 h-100 d-flex flex-column" style="background:#fff;">

            <div class="row flex-grow-1" style=" overflow-y:auto;">
                <div class="col-12">
                    <form method="post" id="productsForm">
                        <table class="table productsList">
                            <thead>
                                <th width="30%">Product</th>
                                <th width="20%" class="text-center">Batch</th>
                                <th width="10%">Price</th>
                                <th width="20%" class="text-center">Qty</th>
                                <th>Amount</th>
                                <th></th>
                            </thead>
                            <tbody id="productsList">

                            </tbody>
                        </table>
                </div>
            </div>

            <div class="row" style="height:80px;border-top:1px solid gray;">
                <div class="col-12">
                    <table style="width: 100%">
                        <tbody>
                            <tr>
                                <td>Items</td>
                                <td width="20%" class="text-center text-dark"><span id="rowQty">0</span>(<span
                                        id="numQty">0</span>)</td>

                                <td>Discount</td>
                                <td width="20%"><input type="number" class="pos-input" step="any"
                                        oninput="updateAmounts()" value="0.00" name="billDiscount" id="discount"></td>
                                <td>Total</td>
                                <td width="20%"><input type="number" class="pos-input" style="background:#97ffbf;"
                                        readonly="" step="any" value="0.00" name="total" id="total"></td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div class="d-grid">
                                        <button class="btn btn-info btn-lg btn-flat btn-block" type="submit"
                                            id="continueBtn">Continue (F2)</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-6 h-100 d-flex flex-column" style="background:#fff;">
            <div class="row pt-1 g-1" style="height: 100px;">
                <div class="col-3">
                    <button class="btn btn-info btn-sm btn-flat btn-block w-100" onclick="fullscreen()" id="fullScreen">Full Screen (F11)</button>
                </div>
                <div class="col-3">
                    <a class="btn btn-primary btn-sm btn-flat btn-block w-100" href="{{ url('/pos/printlast') }}">Print Last Bill</a>
                </div>
                <div class="col-3">
                    <a class="btn btn-success btn-sm btn-flat btn-block w-100" onclick="allProducts()">All Products (F6)</a>
                </div>
                <div class="col-3">
                    <a class="btn btn-dark btn-sm btn-flat btn-block w-100" onclick="window.close()">Exit POS</a>
                </div>
                <div class="col-6">
                    <select name="product" class="selectize" placeholder="Search Product (F8)">
                        @foreach ($products as $product)
                            <option value=""></option>
                            <option value="{{ $product->id }}">{{ $product->name }} | {{$product->price}} | {{$product->stock}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <select class="selectize1" placeholder="Select Category (F9)">
                        @foreach ($categories as $cat)
                            <option value=""></option>
                            <option value="{{ $cat->id }}">{{ $cat->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <form method="get" id="code_form">
                    <input type="text" id="code" placeholder="Scan Barcode (F10)" class="form-control">
                    </form>
                </div>

            </div>
            <div class="row flex-grow-1" style="overflow-y:auto;">
                <div class="col-12">
                    <div class="row" id="sideBar">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width:100%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Sale Details</h5>
                </div>
                <div class="modal-body">
                    <form method="post" id="detailsForm">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="billAmount">Total Bill</label>
                                    <input type="number" readonly class="form-control" id="billAmount"
                                        name="billAmount">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row g-1">
                                    <div class="col-3"><button type="button" onclick="addToReceived(5)"
                                            class="btn btn-success btn-sm w-100">5</button></div>
                                    <div class="col-3"><button type="button" onclick="addToReceived(10)"
                                            class="btn btn-success btn-sm w-100">10</button></div>
                                    <div class="col-3"><button type="button" onclick="addToReceived(20)"
                                            class="btn btn-success btn-sm w-100">20</button></div>
                                    <div class="col-3"><button type="button" onclick="addToReceived(50)"
                                            class="btn btn-success btn-sm w-100">50</button></div>
                                    <div class="col-3"><button type="button" onclick="addToReceived(100)"
                                            class="btn btn-success btn-sm w-100">100</button></div>
                                    <div class="col-3"><button type="button" onclick="addToReceived(500)"
                                            class="btn btn-success btn-sm w-100">500</button></div>
                                    <div class="col-3"><button type="button" onclick="addToReceived(1000)"
                                            class="btn btn-success btn-sm w-100">1000</button></div>
                                    <div class="col-3"><button type="button" onclick="addToReceived(5000)"
                                            class="btn btn-success btn-sm w-100">5000</button></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="received">Received Amount</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="received"
                                            oninput="changeReceived()" placeholder="Enter Received Amount"
                                            aria-label="Enter Received Amount" aria-describedby="basic-addon2">
                                        <span class="input-group-text btn-warning" id="basic-addon2">0.00</span>
                                    </div>
                                </div>

                            </div>
                            <div class="col-3 g-1">
                                <div class="form-group">
                                    <label for="account">Account</label>
                                    <select name="account" id="account" class="form-control">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" value="{{ date("Y-m-d") }}" name="date"
                                        id="date" class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="customer">Customer</label>
                                    <select name="customer" id="customer" class="form-control">
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success w-100">Save (F2)</button>
                        </div>
                        {{-- <div class="col-6">
                        <button type="submit" class="btn btn-success w-100">Save & Print</button>
                    </div> --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    {{--     <script src="{{ asset('assets/js/plugins.js') }}"></script> --}}

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/libs/toastify/toastify.min.js') }}"></script>

    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script src="{{asset('assets/js/pos.js')}}"></script>
    @if (Session::get('success'))
    <script>
       Toastify({
        text: "{{Session::get('success')}}",
        className: "info",
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "center", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "linear-gradient(to right, #01CB3E, #96c93d)",
        }
        }).showToast();
    </script>
@endif
@if (Session::get('error'))
    <script>
         Toastify({
        text: "{{Session::get('error')}}",
        className: "info",
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "center", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "linear-gradient(to right, #FF5733, #E70000)",
        }
        }).showToast();
    </script>
    @endif
    @yield('page-js')
    <script>
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
         $(".selectize1").selectize({
            onChange: function(value) {
                if (!value.length) return;
                if (value != null) {
                    findByCategory(value);
                }
                else
                {
                    allProducts();
                }

            },
        });
        allProducts();

        function allProducts() {
            spinner();
            $.ajax({
                url: "{{ url('/pos/allproducts') }}",
                method: "GET",
                success: function(data) {
                    sidebar(data.products);
                }
            });
        }

        function findByCategory(id) {
            spinner();
            $.ajax({
                url: "{{ url('/pos/bycategory/') }}/" + id,
                method: "GET",
                success: function(data) {
                    sidebar(data.products);
                }
            });
        }

        function sidebar(data) {
            var sidebarHTML = "";
            var image = "";
            data.forEach(function(s) {
                /* image = "{{ asset('') }}" + s.pic;
                if (s.pic == null) {
                    image = "{{ asset('assets/images/no_image.jpg') }}";
                } */
                sidebarHTML += '<div class="col-3 mt-1 g-1">';
                sidebarHTML += '<div class="card border-success" onclick="getSingleProduct(' + s.id + ')">';
                /* sidebarHTML += '<img src="' + image + '" class="card-img-top" style="width:100%;height:130px;">'; */
                sidebarHTML += '<div class="card-body">';
                sidebarHTML += '<h6 class="card-title">' + s.name + '</h5>';
                sidebarHTML += '<p class="card-subtitle text-muted" style="font-size:15px;">'+ s.price + " | "+ s.stock +'</p>';
                sidebarHTML += '</div>';
                sidebarHTML += '</div>';
                sidebarHTML += '</div>';

            });
            if(sidebarHTML == "")
            {
                sidebarHTML = "<h2 class='text-center' style='margin-top:200px;'>No Product Found</h2>";
            }
            $("#sideBar").html(sidebarHTML);
        }

        function spinner() {
            var sideBarHTML = "";
            sideBarHTML +=
                '<div class="d-flex justify-content-center" style="margin-top:200px;"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            $("#sideBar").html(sideBarHTML);
        }
        var existingProducts = [];

        function getSingleProduct(id) {
            var productsListHTML = '';
            $.ajax({
                url: "{{ url('/pos/getSingleProduct/') }}/" + id,
                method: "GET",
                success: function(data) {
                    if (!existingProducts.includes(data.product.id)) {
                        if (data.product.balance !== 0) {
                            productsListHTML += '<tr id="row_' + data.product.id + '">';
                            productsListHTML += '<td><p>' + data.product.name + '</p></td>';
                            productsListHTML += '<td class="no-padding">';
                                productsListHTML += '<select class="form-control text-center no-padding form-control-sm" name="batch[]" id="batch_'+id+'">';
                                data.product.batches.forEach(function (b){
                                    if(b.balance > 0)
                                    {
                                        productsListHTML += '<option value="'+b.batch+'" data-stock="'+b.balance+'">'+b.batch+'</option>';
                                    }
                                });
                                productsListHTML += '</select>';
                            productsListHTML += '</td>';
                            productsListHTML += '<td><input type="number" name="price[]" step="any" id="price_' + data.product.id +'" class="form-control form-control-sm bg-white text-dark" style="background: transparent;outline: none;border: none;text-align: center;padding:0;" readonly value="' +data.product.price + '"></td>';
                            productsListHTML += '<td class="text-center">';
                            productsListHTML += '<div class="input-group">';
                            productsListHTML += '<span class="input-group-text btn btn-danger btn-sm" onclick="decreaseQty(' +data.product.id + ')">-</span>';
                            productsListHTML += '<input type="number" name="qty[]" max="' + data.product.stock +'" required oninput="updateQty(' + data.product.id + ')" id="qty_' + data.product.id + '" class="form-control form-control-sm text-center" value="1">';
                            productsListHTML += '<span class="input-group-text btn btn-success btn-sm" onclick="increaseQty(' +data.product.id + ')">+</span>';
                            productsListHTML += '</div>';
                            productsListHTML += '</td>';
                            productsListHTML += '<td><input type="number" name="amount[]" step="any" id="amount_' + data.product.id +'" class="form-control form-control-sm bg-white text-dark" style="background: transparent;outline: none;border: none;text-align: center;padding:0;" readonly value="' +data.product.price + '"></td>';
                            productsListHTML += '<td><span class="btn btn-danger btn-sm" onclick="deleteRow(' +data.product.id + ')">X</span></td>';
                            productsListHTML += '<input type="hidden" value="' + data.product.id +'" name="id[]">';
                            productsListHTML += '<input type="hidden" value="' + data.stock + '" id="max_' + data.product.id + '">';
                            productsListHTML += '</tr>';
                            $("#productsList").prepend(productsListHTML);
                                existingProducts.push(data.product.id);
                            updateAmounts();
                        } else {
                            alert("Stock Not Available");
                        }
                    } else {
                        /* var existingQty = $("#qty_" + id).val();
                        existingQty++;
                        $("#qty_" + id).val(existingQty);
                        updateQty(id); */
                        

                    }
                }
            });
        }


        function updateQty(id) {
            $("input[id^='qty_']").each(function() {
                var $input = $(this);
                var currentValue = parseInt($input.val());
                var maxAttributeValue = parseInt($input.attr("max"));
                var max = parseInt(maxAttributeValue);
                if (currentValue > max) {
                    alert(max + " Available in stock");
                    $input.val(max);
                }
                if (currentValue < 1) {
                    $input.val(1);
                }
            });
            var qty = $("#qty_" + id).val();
            var amount = qty * ($("#price_" + id).val());
            $("#amount_" + id).val(amount.toFixed(2));
            updateAmounts();
        }


        function updateDiscount(id) {
            var discount = $("#discount_" + id).val();
            var amount = ($("#price_" + id).val() - discount) * $("#qty_" + id).val();
            $("#amount_" + id).val(amount.toFixed(2));
            updateAmounts();
        }

        function increaseQty(id) {
            var existingQty = $("#qty_" + id).val();
            existingQty++;
            $("#qty_" + id).val(existingQty);
            updateQty(id);
        }

        function decreaseQty(id) {
            var existingQty = $("#qty_" + id).val();
            existingQty--;
            $("#qty_" + id).val(existingQty);
            updateQty(id);
        }

        function deleteRow(id) {
            $("#row_" + id).remove();
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            updateAmounts();
        }

        function updateAmounts() {
            var subTotal = 0;
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                subTotal += parseFloat(inputValue);
            });
            var discount = $("#discount").val();
            var gTotal = (parseFloat(subTotal) - parseFloat((discount == '') ? 0 : discount));
            $("#total").val(gTotal.toFixed(2));
            var count = $("[id^='row_']").length;
            $("#rowQty").html(count);
            var numQty = 0;
            // Select input fields whose id starts with "qty_"
            $("input[id^='qty_']").each(function() {
                var value = parseFloat($(this).val());
                var unit = $("")
                if (!isNaN(value)) {
                    numQty += value ;
                }
            });
            $("#numQty").html(numQty);
        }
        /////////////////////////// Submit Products Form /////////////////////////
        $("#productsForm").submit(function(e) {
            e.preventDefault();
            if (existingProducts.length > 0) {
                $("#billAmount").val($("#total").val());
                $("#received").focus();
                $("#detailsModal").modal("show");
            } else {
                alert("Please add at least one product");
            }

        });

        function changeReceived() {
            var received = $("#received").val();
            var billAmount = $("#billAmount").val();
            $("#basic-addon2").text(received - billAmount);
        }

        function addToReceived(amount) {
            var existingAmount = $("#received").val();
            var newAmount = Number(amount) + Number(existingAmount);

            $("#received").val(newAmount);
            changeReceived();
        }
        var isSubmitting = false;
        ////////////////// Save ////////////////////////
        $("#detailsForm").submit(function(e) {
            e.preventDefault();
            if (isSubmitting) {
                console.log("Waiting for response");
                return;
            }
            isSubmitting = true;
            var data1 = $("#productsForm").serialize();
            var data2 = $("#detailsForm").serialize();
            var combinedData = data1 + '&' + data2;
            $.ajax({
                url: "{{ url('/pos/store') }}",
                method: "GET",
                data: combinedData,
                success: function(response) {
                    console.log(response);
                   window.open("{{ url('/pos/print/') }}/"+response, "_self") 
                }
            });
        });
    </script>
</body>

</html>

@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Create Claim</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('claim_amount.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="customerID">Customer</label>
                                    <select name="customerID" id="customerID" class="form-control">
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="productID">Product</label>
                                    <select name="productID" id="productID" class="selectize">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="qty">Qty</label>
                                    <input type="number" name="qty" value="1" id="qty" class="form-control">
                                </div>
                            </div>
                            <div class="col-4 mt-2">
                                <div class="form-group">
                                    <label for="claimType">Claim Type</label>
                                    <select name="claimType" id="claimType" onchange="claimTypeChange(this.value)"
                                        class="form-control">
                                        <option value="Item">Item</option>
                                        <option value="Amount">Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2 mt-2 customer_claim_amount">
                                <div class="form-group">
                                    <label for="amount">Issuing Amount</label>
                                    <input type="number" name="amount" value="0" id="amount" class="form-control">
                                </div>
                            </div>
                            <div class="col-2 mt-2 customer_claim_amount">
                                <div class="form-group">
                                    <label for="isPaid">Is Paid</label>
                                    <select name="isPaid" id="isPaid" class="form-control">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4 mt-2 customer_claim_amount">
                                <div class="form-group">
                                    <label for="paidFrom">Paid From</label>
                                    <select name="paidFrom" id="paidFrom" class="form-control">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-2 mt-2 customer_claim_item">
                                <label for="productID">Claim Product</label>
                                <select name="claim_product_id" id="productID" class="selectize">
                                    @foreach ($claim_products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}
                                            ({{ $product->avail_qty }})
                                            | {{ $product->percentage }}%</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2 mt-2 customer_claim_item">
                                <div class="form-group">
                                    <label for="claimQty">Claim Qty</label>
                                    <input type="number" value="1" name="claimQty" id="claimQty"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-2 mt-2 customer_claim_item">
                                <div class="form-group">
                                    <label for="extraAmount">Extra Amount</label>
                                    <input type="number" value="0" name="extraAmount" id="extraAmount"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-2 mt-2 customer_claim_item">
                                <div class="form-group">
                                    <label for="receivedIn">Received In</label>
                                    <select name="receivedIn" id="receivedIn" class="form-control">
                                        <option value="0">Unpaid</option>
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
                                    <label for="stock">Stock</label>
                                    <select name="stock" id="stock" onchange="stockChange(this.value)"
                                        class="form-control">
                                        <option value="Stock-In">Stock-In</option>
                                        <option value="Scrap">Scrap</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4 scrap">
                                <div class="form-group">
                                    <label for="weight">Weight</label>
                                    <div class="input-group">
                                        <input type="number" name="weight" id="weight" class="form-control">
                                        <span class="input-group-text">Kg</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 scrap">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="scrap_notes" id="notes" cols="30" rows="1" class="form-control">Received in Claim</textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="vendorID">Vendor</label>
                                    <select name="vendorID" id="vendorID" class="form-control">
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" id="date"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" cols="30" rows="1" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>


                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-secondary w-100">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Default Modals -->


@endsection

@section('page-css')
    <link href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css" />
@endsection


@section('page-js')
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".selectize").selectize();
            $(".customer_claim_amount").hide();
            $(".scrap").hide();
        });

        function claimTypeChange(type) {

            if (type == "Item") {
                $(".customer_claim_amount").hide();
                $(".customer_claim_item").show();
            } else {
                $(".customer_claim_amount").show();
                $(".customer_claim_item").hide();
            }
        }

        function stockChange(stock) {
            if (stock == "Stock-In") {
                $(".scrap").hide();
            } else {
                $(".scrap").show();
            }
        }
    </script>
@endsection

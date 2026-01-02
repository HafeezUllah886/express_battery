@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Claim Receiving</h3>
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
                    <form action="{{ route('claim_amount.receivingStore', $claim->id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="vendor">Vendor</label>
                                    <input type="text" name="vendor" value="{{ $claim->vendor->title }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="claimType">Claim Type</label>
                                    <select name="claimType" id="claimType" onchange="claimTypeChange(this.value)"
                                        class="form-control">
                                        <option value="Amount">Amount</option>
                                        <option value="Item" disabled>Item</option>

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
                            <div class="col-4 mt-2 vendor_claim_item">
                                <label for="productID">Claim Product</label>
                                <select name="claim_product_id" id="productID" class="selectize">
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 mt-2 vendor_claim_item">
                                <div class="form-group">
                                    <label for="claimQty">Claim Qty</label>
                                    <input type="number" value="1" name="claimQty" id="claimQty"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-4 mt-2 vendor_claim_amount">
                                <div class="form-group">
                                    <label for="claimAmount">Claim Amount</label>
                                    <input type="number" value="0" name="claimAmount" id="claimAmount"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-4 mt-2 vendor_claim_amount">
                                <div class="form-group">
                                    <label for="isPaid">Is Paid</label>
                                    <select name="isPaid" id="isPaid" class="form-control">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4 mt-2 vendor_claim_amount">
                                <div class="form-group">
                                    <label for="receivedIn">Received In</label>
                                    <select name="receivedIn" id="receivedIn" class="form-control">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" cols="30" rows="1" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>


                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-secondary w-100">Receive Claim</button>
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
            $(".vendor_claim_item").hide();

        });

        function claimTypeChange(type) {

            if (type == "Item") {
                $(".vendor_claim_amount").hide();
                $(".vendor_claim_item").show();
            } else {
                $(".vendor_claim_amount").show();
                $(".vendor_claim_item").hide();
            }
        }
    </script>
@endsection

@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Scrap Purchases</h3>
                    <div>
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create
                            New</button>
                    </div>

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

                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Date</th>
                            <th>Weight</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($scrapePurchases as $key => $purchase)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ date('d M Y', strtotime($purchase->date)) }}</td>
                                    <td>{{ $purchase->weight }}</td>
                                    <td>{{ $purchase->rate }}</td>
                                    <td>{{ number_format($purchase->amount) }}</td>
                                    <td>{{ $purchase->type }}</td>
                                    <td>{{ $purchase->notes }}</td>
                                    <td>
                                        <a href="{{ route('scrap_purchase.delete', $purchase->refID) }}"
                                            class="btn btn-danger">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Default Modals -->

    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Scrap Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('scrap_purchase.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mt-2">
                            <label for="weight">Weight</label>
                            <div class="input-group">
                                <input type="number" step="any" oninput="updateAmount()"  required name="weight" id="weight"
                                    class="form-control">
                                <span class="input-group-text">Kg</span>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label for="rate">Rate</label>
                            <input type="number" step="any" oninput="updateAmount()"  required name="rate" id="rate"
                                class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="amount">Amount</label>
                            <input type="number" step="any" name="amount"  readonly id="amount"
                                class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="type">Type</label>
                            <select name="type" id="type" required class="selectize">
                                <option value="Bill Adjustment">Bill Adjustment</option>
                                <option value="Payment">Payment</option>
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="account">Account</label>
                            <select name="accountID" id="account" required class="selectize">
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="date">Date</label>
                            <input type="date" name="date" required id="date" value="{{ date('Y-m-d') }}"
                                class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" cols="30" class="form-control" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
<!--datatable responsive css-->
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection

@section('page-js')
<script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>

    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>
        $(".selectize").selectize({
    diacritics: true,
    onType: function (query) {
        query = query.normalize('NFC'); // Normalize the query to ensure consistent search
    }
        });

        function updateAmount() {
            const weight = parseFloat(document.getElementById('weight').value) || 0;
            const rate = parseFloat(document.getElementById('rate').value) || 0;
            const amount = weight * rate;
            document.getElementById('amount').value = amount.toFixed(2);
        }
    </script>
@endsection

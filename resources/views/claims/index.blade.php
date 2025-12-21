@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Claims</h3>
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
                            <th>Ref #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Vendor</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($claims as $key => $claim)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $claim->refID }}</td>
                                    <td>{{ date('d M Y', strtotime($claim->date)) }}</td>
                                    <td>{{ $claim->customer->title }}</td>
                                    <td>{{ $claim->vendor->title }}</td>
                                    <td>{{ $claim->product->name }}</td>
                                    <td>{{ $claim->qty }}</td>
                                    <td>{{ $claim->notes }}</td>
                                    <td>{{ $claim->status }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if ($claim->status == "Reported by Customer")
                                                <li>
                                                    <button class="dropdown-item" onclick="newWindow('{{route('claim.status', ['Reported to Vendor', $claim->refID])}}')"
                                                        onclick=""><i
                                                            class="ri-check-line align-bottom me-2 text-muted"></i>
                                                            Reported to Vendor
                                                    </button>
                                                </li>
                                                @endif
                                                @if ($claim->status == "Reported to Vendor" || $claim->status == "Reported by Customer")
                                                <li>
                                                    <button class="dropdown-item" onclick="newWindow('{{route('claim.status', ['Received from Vendor', $claim->refID])}}')"
                                                        onclick=""><i
                                                            class="ri-check-line align-bottom me-2 text-muted"></i>
                                                            Received from Vendor
                                                    </button>
                                                </li>
                                                @endif
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{route('claim.delete', $claim->refID)}}">
                                                        <i class="ri-delete-bin-2-fill align-bottom me-2 text-danger"></i>
                                                        Delete
                                                    </a>
                                                </li> 
                                            </ul>
                                        </div>
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
                    <h5 class="modal-title" id="myModalLabel">Create Claim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('claims.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mt-2">
                            <label for="customer">Customer</label>
                            <select name="customerID" id="customer" required class="selectize">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="vendor">Vendor</label>
                            <select name="vendorID" id="vendor" required class="selectize">
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->title }}

                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="product">Product</label>
                            <select name="productID" id="product" required class="selectize">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}

                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="qty">Qty</label>
                            <input type="number" value="1" name="qty" required id="qty"
                                class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="warehouse">Warehouse</label>
                            <select name="warehouseID" id="warehouse" required class="selectize">
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}

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
                            <textarea name="notes" required id="notes" cols="30" class="form-control" rows="5"></textarea>
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
    </script>
@endsection

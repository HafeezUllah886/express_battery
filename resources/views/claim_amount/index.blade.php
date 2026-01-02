@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Claims</h3>
                    <div>
                        <a href="{{route('claim_amount.create')}}" type="button" class="btn btn-primary ">Create
                            New</a>
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
                                                @if ($claim->status == "Pending")
                                                <li>
                                                    <button class="dropdown-item" onclick="newWindow('{{route('claim_amount.receiving', $claim->id)}}')"
                                                        onclick=""><i
                                                            class="ri-check-line align-bottom me-2 text-muted"></i>
                                                            Received from Vendor
                                                    </button>
                                                </li>
                                                @endif
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{route('claim_amount.delete', $claim->id)}}">
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

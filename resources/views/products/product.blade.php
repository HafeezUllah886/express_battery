@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Products</h3>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create
                        New</button>
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
                            <th>Code</th>
                            <th>Name</th>
                            <th>Vendor</th>
                            <th>Plates</th>
                            <th>Category</th>
                            <th>Weight</th>
                            <th>Retail Price</th>
                            <th>Sale Percentage</th>
                            <th>Extra Tax</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($items as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->vendor }}</td>
                                    <td>{{ $item->plates }}</td>
                                    <td>{{ $item->category->name }}</td>
                                    <td>{{ number_format($item->weight, 2) }} Kg</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>{{ number_format($item->sale_percentage, 2) }}</td>
                                    <td>{{ number_format($item->extra_tax, 2) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info " data-bs-toggle="modal"
                                            data-bs-target="#edit_{{ $item->id }}">Edit</button>
                                        <a href="{{ url('product/printbarcode/') }}/{{ $item->id }}"
                                            class="btn btn-primary">Print Barcode</a>
                                    </td>
                                </tr>
                                <div id="edit_{{ $item->id }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Edit - Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"> </button>
                                            </div>
                                            <form action="{{ route('product.update', $item->id) }}" method="post">
                                                @csrf
                                                @method('patch')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="code">Code</label>
                                                        <input type="text" name="code" required
                                                            value="{{ $item->code }}" id="code"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="name">Name</label>
                                                        <input type="text" name="name" required
                                                            value="{{ $item->name }}" id="name"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="vendor">Vendor</label>
                                                        <select name="vendor" id="vendor" class="form-control">
                                                            @foreach ($vendors as $vendor)
                                                                <option value="{{ $vendor->title }}"
                                                                    @selected($vendor->title == $item->vendor)>{{ $vendor->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="plates">Plates</label>
                                                        <input type="number" name="plates" value="{{ $item->plates }}"
                                                            id="plates" class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="catID">Category</label>
                                                        <select name="catID" id="catID" class="form-control">
                                                            @foreach ($cats as $cat)
                                                                <option value="{{ $cat->id }}"
                                                                    @selected($cat->id == $item->catID)>{{ $cat->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="weight">Weight (Kgs)</label>
                                                        <input type="number" step="any" name="weight" required
                                                            value="{{ $item->weight }}" min="0" id="weight"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="price">Retail Price</label>
                                                        <input type="number" step="any" name="price" required
                                                            value="{{ $item->price }}" min="0" id="price"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="sale_percentage">Sale Percentage</label>
                                                        <input type="number" step="any" name="sale_percentage" required
                                                            value="{{ $item->sale_percentage }}" min="0"
                                                            id="sale_percentage" class="form-control">
                                                    </div>

                                                    <div class="form-group mt-2">
                                                        <label for="extra_tax">Extra Tax</label>
                                                        <input type="number" step="any" name="extra_tax" required
                                                            value="{{ $item->extra_tax }}" min="0" id="extra_tax"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </form>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $items->links() }}
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
                    <h5 class="modal-title" id="myModalLabel">Create New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('product.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="code">Code</label>
                            <div class="input-group mb-3">
                                <input type="text" name="code" required id="code1" class="form-control">
                                <button class="input-group-text btn-info" type="button" onclick="generateCode()"
                                    id="basic-addon2">Generate</button>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label for="name">Name</label>
                            <input type="text" name="name" required id="name" class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="vendor">Vendor</label>
                            <select name="vendor" id="vendor" class="form-control">
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->title }}" @selected($vendor->title)>
                                        {{ $vendor->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="plates">Plates</label>
                            <input type="number" name="plates" id="plates" class="form-control">
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
                            <label for="weight">Weight (Kgs)</label>
                            <input type="number" step="any" name="weight" required value="" min="0"
                                id="weight" class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="price">Retail Price</label>
                            <input type="number" step="any" name="price" required value="" min="0"
                                id="price" class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="sale_percentage">Sale Percentage</label>
                            <input type="number" step="any" name="sale_percentage" required value=""
                                min="0" id="sale_percentage" class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="extra_tax">Extra Tax</label>
                            <input type="number" step="any" name="extra_tax" required value=""
                                min="0" id="extra_tax" class="form-control">
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

    <script>
        function generateCode() {

            $.ajax({
                url: "{{ url('product/generateCode') }}",
                method: "GET",
                success: function(code) {

                    $("#code1").val(code);
                }
            });

        }
    </script>
@endsection

@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Price List</h3>
                </div>
                <div class="card-body">

                    <div class="form-group mt-2">
                        <label for="from">Vendor</label>
                        <select name="vendor" id="vendor" class="form-control">
                            <option value="All">All</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="from">Percentage (Start)</label>
                        <input type="number" name="from" id="from" value="0" class="form-control">
                    </div>
                    <div class="form-group mt-2">
                        <label for="to">Percentage (End)</label>
                        <input type="number" name="to" id="to" value="20" class="form-control">
                    </div>
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" id="viewBtn">View List</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script>
        $("#viewBtn").on("click", function() {
            var vendor = $("#vendor").find(":selected").val();
            var from = $("#from").val();
            var to = $("#to").val();
            console.log(vendor, from, to);
            var url =
                "{{ route('product.price_list.details', ['vendor' => ':vendor', 'from' => ':from', 'to' => ':to']) }}"
                .replace(':vendor', vendor)
                .replace(':from', from)
                .replace(':to', to);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection

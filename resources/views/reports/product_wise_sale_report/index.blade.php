@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Product Wise Sale Report</h3>
                </div>
                <form action="{{ route('productWiseSaleReportData') }}" method="get">
                <div class="card-body">
                    <div class="form-group mt-2">
                        <label for="from">From</label>
                        <input type="date" name="from" id="from" value="{{firstDayOfMonth()}}" class="form-control">
                    </div>
                    <div class="form-group mt-2">
                        <label for="to">To</label>
                                <input type="date" name="to" id="to" value="{{lastDayOfMonth()}}" class="form-control">
                    </div>
                    <div class="form-group mt-2">
                        <label for="product">Product</label>
                        <select name="products[]" id="product" class="selectize" multiple>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="category">Category</label>
                        <select name="categories[]" id="category" class="selectize" multiple>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <button type="submit" class="btn btn-success w-100" id="viewBtn">View Report</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>


@endsection
@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection
@section('page-js')
<script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>

$(".selectize").selectize({
            plugins: ['remove_button'],
            maxItems: null,
            create: false,
            placeholder: 'Select...'
        });

       /*  $("#viewBtn").on("click", function (){
            var from = $("#from").val();
            var to = $("#to").val();
            var products = $("#product").val() || [];
            var productsStr = products.join(',');
            var categories = $("#category").val() || [];
            var categoriesStr = categories.join(',');
            if(productsStr == "")
            {
               productsStr = "0";
            }
            if(categoriesStr == "")
            {
               categoriesStr = "0";
            }
            var url = "{{ route('productWiseSaleReportData', ['from' => ':from', 'to' => ':to', 'products' => ':products', 'categories' => ':categories']) }}"
        .replace(':from', from)
        .replace(':to', to)
        .replace(':products', productsStr)
        .replace(':categories', categoriesStr);
            window.open(url, "_blank", "width=1000,height=800");
        }); */
    </script>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>POS Barecode</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body{
           /*  background: rgb(199, 199, 199); */
            font-size: 15px;
            font-family: "Helvetica";
            width: 80mm;
            height: 28mm;
            margin: 0;
            padding: 0;
        }
        .main{
            width: 80mm;
            height: 28mm;
            background: #fff;
            overflow: hidden;
            margin: 0px auto;
            padding-left: 3px;
            box-sizing: border-box;
        }
        .logo{
            width: 100%;
            overflow: hidden;
            height: 130px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logo img{
            width:80%;
        }
        .header p{
            margin: 2px 0px;
        }
        .content{
            overflow: hidden;
            width: 100%;
        }
        .content table{
            width: 100%;
            border-collapse: collapse;
        }
        .bg-dark{
            background: black;
            color:#ffff;
        }
        .text-left{
            text-align: left !important;
        }
        .text-right{
            text-align: right !important;
        }
        .text-center{
            text-align: center !important;
        }
        .area-title{

            font-size: 18px;
        }
        tr.bottom-border {
            border-bottom: 1px solid #ccc; /* Add a 1px solid border at the bottom of rows with the "my-class" class */
        }
        .uppercase{
            text-transform: uppercase;
        }
        .truncate {
            width: 38mm;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        }
        @media print {
  body * {
    visibility: hidden;
  }
   .main {
    visibility: visible !important;
  }
   .main * {
    visibility: visible !important;
  }
  .main {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    float: left;
  }

}
    </style>
</head>
<body>
    <div class="row main m-0 p-0 mt-1"  id="main">
        <div style="width:38mm;margin-right:4mm;margin-left:0mm;">
            <div>
                <table width="100%">
                    <tr>
                        <td colspan="2" class="">
                            <h5 class="text-center truncate" style="margin:0;">
                                {{ $product->name }}
                            </h5>
                        </td>
                    </tr>
                    <tr>
                        {{-- <td>
                            <h5 style="margin:0;">
                             {{ $product->color }} /
                             {{ $product->size }}
                            </h5>
                        </td> --}}
                        <td>
                            <h3 style="margin:0;" class="text-center">
                                Rs. {{ number_format($product->price) }}
                            </h3>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center">
                                {!! DNS1D::getBarcodeSVG($product->code, 'C128', 1, 50) !!}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="width:38mm;">
            <div >
                <table width="100%">
                    <tr>
                        <td colspan="2">
                            <h5 class="text-center truncate" style="margin:0;">
                                {{ $product->name }}
                            </h5>
                        </td>
                    </tr>
                    <tr>
                        {{-- <td>
                            <h5 style="margin:0;">
                             {{ $product->color }} /
                             {{ $product->size }}
                            </h5>
                        </td> --}}
                        <td>
                            <h3 style="margin:0;" class="text-center">
                                Rs. {{ number_format($product->price) }}
                            </h3>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center">
                                {!! DNS1D::getBarcodeSVG($product->code, 'C128', 1, 50) !!}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<script src="{{ asset('src/plugins/src/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
{{-- <script>
    window.print();
        setTimeout(function() {
        window.location.href = "{{ url('/product')}}";
    }, 5000);
</script> --}}

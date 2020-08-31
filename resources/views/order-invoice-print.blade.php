<!DOCTYPE html>
<html lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <title>Toppers Pakistan Invoice</title>
    <style>
        @media print {
            @page {
                margin: 0 auto; /* imprtant to logo margin */
                sheet-size: 300px 200mm; /* imprtant to set paper size */
            }
            html {
                direction: ltr;
            }
            html,body{
                margin:0;
                padding:0
            }
            #printContainer {
                width: 400px;
                margin: auto;
                /*padding: 10px;*/
                /*border: 2px dotted #000;*/
                text-align: justify;
            }

           .text-center{text-align: center;}
        }
    </style>
</head>
<script>
    function closePrintView() {
        document.location.href = '/invoice/{{$order->id}}';
    }    
</script>
<body onload="window.print();setTimeout('closePrintView()', 300);">
    <h1 id="logo" class="text-center"><img width="150" height="150" src="/images/ToppersPakistanLogo.png" alt='Logo'></h1>
    <div id='printContainer'>
        <h1 class="text-center">Toppers Pakistan</h1>
        <h3 class="text-center">{{$order->branch->name}}</h3>
        <h2 class="text-center">Order # {{$order->id}}</h2>
        @if($order->delivery)
        <h2 class="text-center">Delivery</h2>
        @else
        <h2 class="text-center">Dine In</h2>
        @endif
        <h4 class="text-center">Order Date: {{$order->created_at}}</h4>
        <h4 class="text-center">Delivery Date: {{$order->created_at}}</h4>
        <h2 class="text-center">Invoice to {{$order->customer->name}}</h2>
        <h3 class="text-center">{{$order->address->house}}, {{$order->address->street}}, {{$order->address->area}}, {{$order->address->city}} </h3>
        <h3 class="text-center">{{$order->address->mobile}}</h3>
        <table style="width:100%">
            <tr class="thead-light">
                <th><h3>Item</h3></th>
                <th><h3>Quantity</h3></th>
                <th><h3>Price</h3></th>
            </tr>
            @for ($i = 0; $i < count($orderItems); $i++)
            <tr>
                <td><h3>{{$orderItems[$i]->product->name}}</h3></td>
                <td><h3>{{$orderItems[$i]->quantity}} {{$orderItems[$i]->product->unit->name}}</h3></td>
                <td><h3>Rs. {{$orderItems[$i]->product->unit_price * $orderItems[$i]->quantity}}</h3></td>
            </tr>
            @endfor
        </table>
        @if($order->delivery)
        <div class="row">
            <div class="col-sm">
                <h3>Sub Total</h3>
            </div>
            <div class="col-sm">
                <h3>Rs. {{$order->total_price  - 50}}</h3>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm">
                <h3>Delivery Charges</h3>
            </div>
            <div class="col-sm">
                <h3>Rs. 50</h3>
            </div>
        </div>
        <hr>
        @else
        <div class="row">
            <div class="col-sm">
                <h3>Sub Total</h3>
            </div>
            <div class="col-sm">
                <h3>Rs. {{$order->total_price}}</h3>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm">
                <h3>Delivery Charges</h3>
            </div>
            <div class="col-sm">
                <h3>Rs. 0.0</h3>
            </div>
        </div>
        <hr>
        @endif
        <div class="row">
            <div class="col-sm">
                <h3>Total</h3>
            </div>
            <div class="col-sm">
                <h3>Rs. {{$order->total_price}}</h3>
            </div>
        </div>
    </div>
</body>
</html> 
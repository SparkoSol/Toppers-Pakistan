@auth

@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-center" id="content">
    <div style="width:70%; padding:50px;">
        <div class="page-header row">
            <div class="col-md-12" align="center">
                <h1>Toppers Pakistan</h1>
                <h3>Address</h3>
            </div>
        </div>
        <script type="text/javascript">
        $(document).ready(function(){
        window.print();
        setTimeout("closePrintView()", 300);
    });
    function closePrintView() {
        document.location.href = '/home';
    }    </script>
        <div class="page-header row">
            <div class="col-md-6">
                <h1>Order # {{$order->id}}</h1>
                <h2>Delivery</h2>
                <h3>Order Date: {{$order->created_at}}</h3>
                <h3>Delivery Date: {{$order->created_at}}</h3>
            </div>
            <div class="col-md-6" align="right">
                <h1>Invoice to</h1>
                <h1>{{$order->customer->name}}</h1>
                <h3>{{$order->address->house}}, {{$order->address->street}}, {{$order->address->area}}, {{$order->address->city}} </h3>
                <h3>{{$order->address->mobile}}</h3>
            </div>
        </div>
        <div class="table-responsive">
            <table style="width:100%" class="table">
                <tr class="thead-light">
                    <th colspan="2">Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
                @for ($i = 0; $i < count($orderItems); $i++)
                    <tr>
                        <td colspan="2">{{$orderItems[$i]->product->name}}</td>
                        <td>{{$orderItems[$i]->quantity}}</td>
                        <td>Rs. {{$orderItems[$i]->product->unit_price * $orderItems[$i]->quantity}}</td>
                    </tr>
                @endfor
            </table>
        </div>
        <div class="row d-flex justify-content-end" id="bill" style="margin-right:20px">
            <div align="left">
                <h1>Sub Total</h1>
                <hr>
                <h1>Delivery Charges</h1>
                <hr>
                <h1>Total</h1>
            </div>
            <div align="right">
                <h1>Rs. {{$order->total_price  - 80}}</h1>
                <hr>
                <h1>Rs. 80</h1>
                <hr>
                <h1>Rs. {{$order->total_price}}</h1>
            </div>
        </div>
    </div>
</div>

<div style="height:100px;width:1500px;" id="space">
    <h1 style="color:white;opacity:0">
      Hello
    </h1>
</div>

@endsection

@else
<script>window.location = "/login";</script>
@endauth
@auth

@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-center">
    <div style="width:70%;padding:50px;" id="content">
        <div class="d-flex justify-content-center" style="padding:10px;">
            <img width="150" height="150" src="/images/ApnaPos.png" alt="">
        </div>
        <div class="page-header row">
            <div class="col-md-9"align="center" id="title">
                <h1>Apna Pos</h1>
                <h3>{{$order->branch->name}}</h3>
            </div>
            <div class="col-md-3">
                <button onclick="window.location.href = '/invoice-print/{{$order->id}}' " class="btn btn-primary">Print</button>
            <Button class="btn btn-primary" onclick="window.location.href = '/order-complete/{{$order->id}}' ">Complete</Button>
                <script>
                function myFunction() {
                  var backup = document.body.innerHTML;
                  var title = document.getElementById('title').innerHTML;
                  var info = document.getElementById('info').innerHTML;
                  var items = document.getElementById('items').innerHTML;
                  var bill = document.getElementById('bill').innerHTML;
                  var content = document.getElementById('content').innerHTML;
                  var space = document.getElementById('space').innerHTML;
                  document.body.innerHTML = content;
                  window.print();
                  document.body.innerHTML = backup;
                }
                </script>
            </div>
        </div>

        <div class="page-header row" id="info">
            <div class="col-md-6">
                <h1>Order # {{$order->id}}</h1>
                @if($order->delivery)
                <h2>Delivery</h2>
                @else
                <h2>Dine In</h2>
                @endif
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
        <div class="table-responsive" id="items">
            <table style="width:100%" class="table">
                <tr class="thead-light">
                    <th colspan="2">Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
                @for ($i = 0; $i < count($orderItems); $i++)
                    <tr>
                        <td colspan="2">{{$orderItems[$i]->product->name}}</td>
                        <td>{{$orderItems[$i]->quantity}} {{$orderItems[$i]->product->unit->name}}</td>
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
                @if($order->delivery)
                <h1>Rs. {{$order->total_price  - 50}}</h1>
                <hr>
                <h1>Rs. 50</h1>
                <hr>
                @else
                <h1>Rs. {{$order->total_price}}</h1>
                <hr>
                <h1>Rs. 0.0</h1>
                <hr>
                @endif
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

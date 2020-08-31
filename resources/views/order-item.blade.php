@auth 

@extends('layouts.app')

@section('content')

<div style="padding:50px;">
    <div class="page-header">
        <h1>Order Items</h1>      
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
            @for ($i = 0; $i < count($orderItems); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$orderItems[$i]->product->name}}</th>
                    <td>{{$orderItems[$i]->quantity}} {{$orderItems[$i]->product->unit->name}}</td>
                    <td>Rs. {{$orderItems[$i]->product->unit_price}}</td>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection

@else 
<script>window.location = "/login";</script>
@endauth 
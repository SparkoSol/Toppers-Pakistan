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
            </tr>
            @for ($i = 0; $i < count($orderItems); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$orderItems[$i]->product->name}}</th>
                    <th>{{$orderItems[$i]->quantity}}</th>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection

@else 
<script>window.location = "/login";</script>
@endauth 
@auth 

@extends('layouts.app')

@section('content')

<div style="padding:50px;">
    <div class="page-header">
        <h1>Completed Orders</h1>      
      </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Customer Name</th>
                <th>Instruction</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Order Items</th>
            </tr>
            @for ($i = 0; $i < count($completedOrders); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$completedOrders[$i]->customer->name}}</th>
                    <th>{{$completedOrders[$i]->instructions}}</th>
                    <th>{{$completedOrders[$i]->total_price}}</th>
                    <th>{{$completedOrders[$i]->status}}</th>
                    <th class="crud"><a href='/view-order-item/{{$completedOrders[$i]->id}}' class="btn btn-success">View</a></th>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection

@else 
<script>window.location = "/home";</script>
@endauth 
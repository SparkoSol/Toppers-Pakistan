@auth 

@extends('layouts.app')

@section('content')
<script type="text/javascript">
    setTimeout(function(){
        location.reload();
    },100000);
 </script>
<div style="padding:50px;">
    <div class="page-header">
        <h1>Pending Orders</h1>      
      </div>
    <div style="padding:20px;">
        <a href="/order-completed" class="btn btn-primary">Completed Orders</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Customer Name</th>
                <th>Instruction</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Mode</th>
                <th>Order Items</th>
                <th>Action</th>
            </tr>
            @for ($i = 0; $i < count($pendingOrders); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$pendingOrders[$i]->customer->name}}</th>
                    <th>{{$pendingOrders[$i]->instructions}}</th>
                    <th>{{$pendingOrders[$i]->total_price}}</th>
                    <th>{{$pendingOrders[$i]->status}}</th>
                    @if($pendingOrders[$i]->delivery)
                    <th>Delivery</th>
                    @else
                    <th>Dine In</th>
                    @endif
                    <th class="crud"><a href='/view-order-item/{{$pendingOrders[$i]->id}}' class="btn btn-success">View</a></th>
                    <th class="crud">
                        <a href='/invoice/{{$pendingOrders[$i]->id}}' class="btn btn-success">Invoice</a> 
                        @if(@Auth::user()->type == "Main Admin")
                            <a href='/reject-order/{{$pendingOrders[$i]->id}}' class="btn btn-danger">Reject</a>
                        @endif
                    </th>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection

@else 
<script>window.location = "/login";</script>
@endauth 
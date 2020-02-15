@auth 

@extends('layouts.app')
@if(Auth::user()->type == "Main Admin")
@section('content')

<div style="padding:50px;">
    <div class="page-header">
        <h1>Customers</h1>      
      </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Addresses</th>
                <th>Orders</th>
            </tr>
            @for ($i = 0; $i < count($customers); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$customers[$i]->name}}</th>
                    <th>{{$customers[$i]->email}}</th>
                    <th>{{$customers[$i]->phone}}</th>
                <th class="crud"><a href='/customer-address/{{$customers[$i]->id}}' class="btn btn-success">View</a></th>
                    <th class="crud"><a href='/customer-order/{{$customers[$i]->id}}' class="btn btn-success">View</a></th>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection
@else
<script>window.location = "/home";</script>
@endif
@else 
<script>window.location = "/login";</script>
@endauth 
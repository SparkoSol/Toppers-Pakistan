@auth 

@extends('layouts.app')

@section('content')
<?php  $_SESSION['total'] = 0; ?>    

<div style="padding-left:50px;padding-right:50px">
    <div class="page-header">
        <h1>Selected Product(s)</h1>      
    </div>
    <div style="padding:20px;">
        <a href="/add-product-order" class="btn btn-primary">Add Product(s)</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
            @if (isset($_SESSION['items']))
            @for ($i = 0; $i < count($_SESSION['items']); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$_SESSION['items'][$i]->product->name}}</th>
                    <th>{{$_SESSION['items'][$i]->quantity}}</th>
                    <th>Rs. {{ $_SESSION['items'][$i]->quantity * $_SESSION['items'][$i]->product->unit_price}}</th>
                </tr>
            @endfor
            @endif

        </table>
    </div>
</div>

<div style="padding:50px;">
<div class="table-responsive">
    <table style="width:50%" align="center" class="table">
        @if (isset($_SESSION['items']))
            @for ($i = 0; $i < count($_SESSION['items']); $i++)
            <?php $_SESSION['total'] = $_SESSION['total'] + ($_SESSION['items'][$i]->quantity * $_SESSION['items'][$i]->product->unit_price) ?> 
            @endfor
        @endif
        <tr>
            <th>Sub Total</th>
        <td>Rs. {{$_SESSION['total']}}</td>
        </tr>
        <tr>
            <th>Delivery Charges</th>
            <td>Rs. 0.0</td>
        </tr>
        <tr>
            <th>Tax</th>
            <td>Rs. 0.0</td>
        </tr>
        <tr>
            <th>Total</th>
            <td>Rs. {{$_SESSION['total']}}</td>
        </tr>
    </table>
</div>
</div>
<div class="container">
    <form method="POST" action="{{ url('/store-order-admin') }}">
        @csrf
        <div class="form-group row mb-1">
            <div class="col-md-12 offset-md-5">
                <button type="submit" class="btn btn-primary">
                    {{ __('Punch Order') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
@else 
<script>window.location = "/login";</script>
@endauth 
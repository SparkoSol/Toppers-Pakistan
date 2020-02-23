@auth 

@extends('layouts.app')
@if(Auth::user()->type == "Sub Admin")

@section('content')
<?php  $_SESSION['total'] = 0; ?>
<?php  $_SESSION['customer'] = $customer; ?>
<?php  $_SESSION['address'] = $address; ?>

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
<form method="POST" action="{{ url('/store-order-admin') }}">
    @csrf
<div style="padding:30px;" class="d-flex justify-content-end">   
    <div class="table-responsive col-md-7">
        <table style="width:50%" align="center" class="table">
            <tr>
                <th>Customer Name</th>
                <td>{{$customer->name}}</td>
            </tr>
            <tr>
                <th>Customer Email</th>
                <td>{{$customer->email}}</td>
            </tr>
            <tr>
                <th>Customer Address</th>
                <td>{{$address->description}} ,{{ $address->house}},{{ $address->street}}, {{$address->area}}</td>
            </tr>
            <tr>
                <th>Customer City</th>
                <td>{{$address->city}}</td>
            </tr>
            <tr>
                <th>Customer Phone</th>
                <td>{{$customer->phone}}</td>
            </tr>
        </table>
    </div>
    <div class="table-responsive col-md-5">
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
                <script>
                    $(document).ready(function() {
                    $('#option1').change(function() {
                        if(this.checked) {
                            document.getElementById('delivery').innerHTML = 'Rs. 50.0';
                            document.getElementById('total').innerHTML = parseInt(document.getElementById('total').innerText) + 50 ; 
                        }else{
                            document.getElementById('delivery').innerHTML = 'Rs. 0.0';
                            document.getElementById('total').innerHTML = parseInt(document.getElementById('total').innerText) - 50 ; 
                        }       
                        });
                    });
                </script>
                <th><input type="checkbox" name="option1" id="option1"> Delivery Charges</th>
                <td id="delivery">Rs. 0.0</td>
            </tr>
            <tr>
                <th>Tax</th>
                <td>Rs. 0.0</td>
            </tr>
            <tr>
                <th>Total</th>
                <td id="total">{{$_SESSION['total'] }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="container d-flex justify-content-center">
        <div class="form-group row mb-1">
            <div class="col-md-12 offset-md-5">
                <button type="submit" class="btn btn-primary">
                    {{ __('Punch Order') }}
                </button>
            </div>
        </div>
</div>

</form>
@endsection
@else
<script>window.location = "/home";</script>
@endif
@else 
<script>window.location = "/login";</script>
@endauth 
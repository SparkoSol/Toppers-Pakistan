@auth 

@extends('layouts.app')
@if(Auth::user()->type == "Sub Admin")
@section('content')
<div style="padding:50px;">
    <div class="page-header">
        <h1>Products</h1>      
      </div>
    <div class="table-responsive">
        <form  action="{{ url('/add-product-order-list') }}" method="POST">
            @csrf

        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Product Name</th>
                <th>Restaurant Name</th>
                <th>Category Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Serving</th>
                <th>Quantity to aAdd</th>
                <th>Add</th>
            </tr>
            @for ($i = 0; $i < count($products); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$products[$i]->name}}</th>
                    <th>{{$products[$i]->restaurant->name}}</th>
                    <th>{{$products[$i]->category->name}}</th>
                    <th>{{$products[$i]->quantity . " " . $products[$i]->unit->name}}</th>
                    <th>{{"Rs. " .$products[$i]->unit_price}}</th>
                    <th>{{$products[$i]->serving. " Person"}}</th>
                    <th><input class="form-control col-md-3" type="number" id="quantity" name="quantity[{{$products[$i]->id}}]" step="1" value="1" min="1" > </th>
                    <th><input type="checkbox" class="form-control col-md-3" name="check_list[]" value="{{$products[$i]->id}}"></th>
                </tr>
            @endfor
            <button type="submit" class="btn btn-primary">
                {{ __('Add') }}
            </button>

        </table>
    </form> 
    </div>
</div>

@endsection
@else
<script>window.location = "/home";</script>
@endif
@else 
<script>window.location = "/login";</script>
@endauth 
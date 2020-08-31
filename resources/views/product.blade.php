@auth 

@extends('layouts.app')
@if(Auth::user()->type == "Main Admin")

@section('content')
<div style="padding:50px;">
    <div class="page-header">
        <h1>Products</h1>      
      </div>
    <div style="padding:20px;">
        <a href="{{url('/add-product')}}" class="btn btn-primary">Add New Restaurant Product</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Product Name</th>
                <th>Restaurant Name</th>
                <th>Sub Category Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Actions</th>
            </tr>
            @for ($i = 0; $i < count($products); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$products[$i]->name}}</th>
                    <th>{{$products[$i]->restaurant->name}}</th>
                    <th>{{$products[$i]->subCategory->name}}</th>
                    <th>{{$products[$i]->quantity . " " . $products[$i]->unit->name}}</th>
                    <th>{{"Rs. " .$products[$i]->unit_price}}</th>
                    <th class="crud"><a href='/edit-product/{{$products[$i]->id}}' class="btn btn-primary">Edit</a>  <a href='/delete-product/{{$products[$i]->id}}' class="btn btn-danger">Delete</a> </th>
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
@auth 

@extends('layouts.app')

@section('content')

<div style="padding:50px;">
    <div style="padding:20px;">
        <a href="#" class="btn btn-primary" disabled>Restaurant Branches</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Branch Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
            </tr>
            @for ($i = 0; $i < count($restaurantBranches); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$restaurantBranches[$i]->name}}</th>
                    <th>{{$restaurantBranches[$i]->email}}</th>
                    <th>{{$restaurantBranches[$i]->address}}</th>
                    <th>{{$restaurantBranches[$i]->phone}}</th>
                </tr>
            @endfor
        </table>
    </div>

    <div style="padding:20px;">
        <a href="#" class="btn btn-primary" disabled>Restaurant Products</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Product Name</th>
                <th>Quanity</th>
                <th>Serving</th>
                <th>Unit Price</th>
            </tr>
            @for ($i = 0; $i < count($restaurantProducts); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$restaurantProducts[$i]->name}}</th>
                    <th>{{$restaurantProducts[$i]->quantity . " " . $restaurantProducts[$i]->unit->name}}</th>
                    <th>{{$restaurantProducts[$i]->serving . " Person"}}</th>
                    <th>{{"Rs. " . $restaurantProducts[$i]->unit_price}}</th>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection

@else 
<script>window.location = "/home";</script>
@endauth 
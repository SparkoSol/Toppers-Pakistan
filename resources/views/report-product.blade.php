@auth 

@extends('layouts.app')

@section('content')
<?php $totalRevenue = 0;  ?>
<div class="row" style="padding:30px;">
    <div>
        <nav class="navbar bg-dark navbar-dark">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="/report">Daily Report</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Monthly Report</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Yearly Report</a>
              </li> 
              <li class="nav-item">
                <a class="nav-link active" href="/report-productWise">Product Wise Report</a>
              </li>
            </ul>
          </nav>
    </div>
    <div class="col-md-10" >
        <div class="page-header">
            <h1>Product Wise Sales Report</h1>      
          </div>
        <div class="table-responsive">
            <table style="width:100%" class="table">
                <tr>
                    <th>Sr#</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Sale</th>
                </tr>
                @for ($i = 0; $i < count($products); $i++)
                    <?php $total = 0 ?>
                    <tr>
                        <td>{{$i+1}}</td>
                        <td>{{$products[$i]->name}}</td>
                        @for ($j = 0; $j < count($orders); $j++)
                                @for ($k = 0; $k < count($orders[$j]->orderItems); $k++)
                                    @if($products[$i]->id == $orders[$j]->orderItems[$k]->product_id)    
                                        <?php $total = $total +  $orders[$j]->orderItems[$k]->quantity ?>
                                    @endif
                                @endfor
                        @endfor
                        <td>{{$total}}</td>
                        <td>Rs. {{$total * $products[$i]->unit_price}}</td>
                        <?php $totalRevenue = $totalRevenue +   ($total * $products[$i]->unit_price) ?>
                        {{-- <th>{{$products[$i]->instructions}}</th>
                        <th>{{$products[$i]->total_price}}</th>
                        <th>{{$products[$i]->status}}</th>
                        <th class="crud"><a href='/view-order-item/{{$pendingOrders[$i]->id}}' class="btn btn-success">View</a></th>
                        <th class="crud"><a href='/order-complete/{{$pendingOrders[$i]->id}}' class="btn btn-success">Complete</a></th> --}}
                    </tr>
                @endfor
                <tr>
                    <th colspan="3">Total Revenue</th>
                    <th>Rs. {{$totalRevenue}}</th>
                </tr>
            </table>
        </div>
    </div>

    </div>
</div>

@endsection

@else 
<script>window.location = "/login";</script>
@endauth 
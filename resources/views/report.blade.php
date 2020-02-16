@auth 

@extends('layouts.app')

@section('content')
<?php $totalRevenue = 0;  ?>
<div class="row" style="padding:30px;">
    <div>
        <nav class="navbar bg-dark navbar-dark">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link active" href="/report" >Daily Report</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Monthly Report</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Yearly Report</a>
              </li> 
              <li class="nav-item">
                <a class="nav-link " href="/report-productWise">Product Wise Report</a>
              </li>
            </ul>
          </nav>
    </div>
    <div class="col-md-10" >
        <div class="page-header">
            <h1>Daily Report</h1>      
          </div>
        <div class="table-responsive">
            <table style="width:100%" class="table">
                <tr>
                    <th >Sr#</th>
                    <th>Order Id</th>
                    <th>Order Items</th>
                    <th>Total Sale</th>
                </tr>
                @for ($i = 0; $i < count($orders); $i++)
                    <tr>
                        <td>{{$i+1}}</td>
                        <td>{{$orders[$i]->id}}</td>
                            <td >
                                @for ($j = 0; $j < count($orders[$i]->orderItems); $j++)
                                    {{$orders[$i]->orderItems[$j]->product->name}} ({{$orders[$i]->orderItems[$j]->quantity}}) </br> 
                                @endfor                            
                            </td>
                        <td>Rs. {{$orders[$i]->total_price}}</td>
                        <?php $totalRevenue = $totalRevenue +   $orders[$i]->total_price  ?>
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
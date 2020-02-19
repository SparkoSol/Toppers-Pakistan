@auth 

@extends('layouts.app')

@section('content')
<?php $totalRevenue = 0;  ?>
<div class="row" style="padding:30px;">
    <div>
        <nav class="navbar bg-dark navbar-dark">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="/report" >Daily Report</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/report-productWise">Daily Product Wise Report</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/report-monthly">Monthly Report</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/report-monthly-product">Monthly Product Wise Report</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/report-yearly">Yearly Report</a>
              </li> 
              <li class="nav-item">
                <a class="nav-link" href="/report-yearly-product">Yearly Product Wise Report</a>
              </li> 
              <li class="nav-item">
                <a class="nav-link" href="/report-custom">Custom Report</a>
              </li> 
              <li class="nav-item">
                <a class="nav-link active" href="/report-custom-product">Custom Product Wise Report</a>
              </li> 
             </ul>
          </nav>
    </div>
    <div class="col-md-10" >
        <div class="page-header" id="headerText">
            <h1>{{$title}} { {{date("d-F-Y",strtotime($timeFrom))}}  to  {{date("d-F-Y",strtotime($timeTo))}}  }</h1>            
          </div>
          <div class="page-header">
            <button onclick="myFunction()" class="btn btn-primary">Print this report</button>

<script>
function myFunction() {
  var backup = document.body.innerHTML;
  var content1 = document.getElementById('headerText').innerHTML;
  var content2 = document.getElementById('report').innerHTML;
  var space = document.getElementById('space').innerHTML;
  document.body.innerHTML = space + content1 + space + space + content2;
  window.print();
  document.body.innerHTML = backup;
}
</script>
          </div>
          <div class='container float-left'>
            <form method="POST" action="{{ url('/report-custom-product-specific') }}" class="row">
              @csrf
              <input  type="date" class="form-control col-md-3" style="margin:20px" id="dateFrom" name="dateFrom">
              <input  type="date" class="form-control col-md-3" style="margin:20px" id="dateTo" name="dateTo">
              <button type="submit" class="btn btn-primary" style="margin:20px">
                {{ __('Generate Sale') }}
              </button>
            </form>
        </div>
        <div class="table-responsive" id="report">
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
<div style="height:100px;width:1500px;" id="space">
  <h1 style="color:white;opacity:0">
    Hello
  </h1>
</div>
@endsection

@else 
<script>window.location = "/login";</script>
@endauth 
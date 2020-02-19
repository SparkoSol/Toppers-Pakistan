@auth 
@extends('layouts.app')
@section('content')
<?php $totalRevenue = 0;  ?>
<script type="text/javascript">
    $(document).ready(function(){
      $('.row'  ).show();
      window.print();
      });
  </script>
<div class="row" style="padding:30px;">
    <div class="col-md-10" >
          <div class="page-header" id="headerText">
            <h1>{{$title}} { {{date("d-F-Y",strtotime($time))}} }</h1>      
          </div>
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
                <?php if($orders[$i]->customer_id == '1' ) {
                  $text = "Punched From Admin Panel";
                  $color = '#0bbd00';
              }else{
                  $text = "App Order";
                  $color = '#3490dc';
              }  ?>
              <tr style="color:{{$color}};">
                  <td>{{$i+1}}</td>
                  
                  <td>{{$orders[$i]->id}} <br> ( {{$orders[$i]->branch->name}} ) ( {{$text}} )</td>
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
<script>
  $( function() {
    $( "#datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
  } );
  </script>
@endsection

@else 
<script>window.location = "/login";</script>
@endauth 
@auth 

@extends('layouts.app')

@section('content')

<div style="padding:50px;">
    <div class="page-header">
        <h1>Addresses</h1>      
      </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>House</th>
                <th>Street</th>
                <th>Area</th>
                <th>City</th>
                <th>Phone</th>
                <th>Description</th>
            </tr>
            @for ($i = 0; $i < count($addresses); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$addresses[$i]->house}}</th>
                    <th>{{$addresses[$i]->street}}</th>
                    <th>{{$addresses[$i]->area}}</th>
                    <th>{{$addresses[$i]->city}}</th>
                    <th>{{$addresses[$i]->mobile}}</th>
                    <th>{{$addresses[$i]->description}}</th>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection

@else 
<script>window.location = "/home";</script>
@endauth 
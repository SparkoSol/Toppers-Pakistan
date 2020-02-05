@auth 

@extends('layouts.app')

@section('content')
<div style="padding:50px;">
    <div style="padding:20px;">
        <a href="{{url('/add-unit')}}" class="btn btn-primary">Add New Unit</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Unit Name</th>
                <th>Actions</th>
            </tr>
            @for($i = 0; $i < count($units); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$units[$i]->name}}</th>
                    <th class="crud"><a href='/edit-unit/{{$units[$i]->id}}' class="btn btn-primary">Edit</a>  <a href='/delete-unit/{{$units[$i]->id}}' class="btn btn-danger">Delete</a> </th>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection

@else 
<script>window.location = "/home";</script>
@endauth 

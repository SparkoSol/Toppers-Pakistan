@auth 

@extends('layouts.app')
@if(Auth::user()->type == "Main Admin")

@section('content')
<div style="padding:50px;">
    <div class="page-header">
        <h1>Restaurant Branches</h1>      
      </div>
    <div style="padding:20px;">
        <a href="{{url('/add-branch')}}" class="btn btn-primary">Add New Restaurant Branch</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Restaurant Branch Name</th>
                <th>Restaurant Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
            @for ($i = 0; $i < count($branches); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$branches[$i]->name}}</th>
                    <th>{{$branches[$i]->restaurant->name}}</th>
                    <th>{{$branches[$i]->email}}</th>
                    <th>{{$branches[$i]->phone}}</th>
                    <th>{{$branches[$i]->address}}</th>
                    <th class="crud"><a href='/edit-branch/{{$branches[$i]->id}}' class="btn btn-primary">Edit</a>  <a href='/delete-branch/{{$branches[$i]->id}}' class="btn btn-danger">Delete</a> </th>
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
@auth 

@extends('layouts.app')

@section('content')

<div style="padding:50px;">
    <div style="padding:20px;">
        <a href="{{url('/add-restaurant')}}" class="btn btn-primary">Add New Restaurant</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Restaurant Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
            @for ($i = 0; $i < count($restaurants); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$restaurants[$i]->name}}</th>
                    <th>{{$restaurants[$i]->email}}</th>
                    <th>{{$restaurants[$i]->address}}</th>
                    <th>{{$restaurants[$i]->phone}}</th>
                    <th class="crud"><a href='/view-restaurant/{{$restaurants[$i]->id}}' class="btn btn-success">View</a>  <a href='/edit-restaurant/{{$restaurants[$i]->id}}' class="btn btn-primary">Edit</a>  <a href='/delete-restaurant/{{$restaurants[$i]->id}}' class="btn btn-danger">Delete</a> </th>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection

@else 
<script>window.location = "/home";</script>
@endauth 
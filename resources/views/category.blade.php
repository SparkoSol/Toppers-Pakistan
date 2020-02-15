@auth 

@extends('layouts.app')
@if(Auth::user()->type == "Main Admin")

@section('content')
<div style="padding:50px;">
    <div class="page-header">
        <h1>Categories</h1>      
      </div>
    <div style="padding:20px;">
        <a href="{{url('/add-category')}}" class="btn btn-primary">Add New Category</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Category Name</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            @for($i = 0; $i < count($categories); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$categories[$i]->name}}</th>
                    <th>{{$categories[$i]->image}}</th>
                    <th class="crud"><a href='/edit-category/{{$categories[$i]->id}}' class="btn btn-primary">Edit</a>  <a href='/delete-category/{{$categories[$i]->id}}' class="btn btn-danger">Delete</a> </th>
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
@auth 

@extends('layouts.app')
@if(Auth::user()->type == "Main Admin")

@section('content')
<div style="padding:50px;">
    <div class="page-header">
        <h1>Sub Categories</h1>      
      </div>
    <div style="padding:20px;">
        <a href="{{url('/add-sub-category')}}" class="btn btn-primary">Add New Sub Category</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Sub Category Name</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
            @for ($i = 0; $i < count($subCategories); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$subCategories[$i]->name}}</th>
                    <th>{{$subCategories[$i]->category->name}}</th>
                    <th class="crud"><a href='/edit-sub-category/{{$subCategories[$i]->id}}' class="btn btn-primary">Edit</a>  <a href='/delete-sub-category/{{$subCategories[$i]->id}}' class="btn btn-danger">Delete</a> </th>
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
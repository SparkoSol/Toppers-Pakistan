@auth 

@extends('layouts.app')

@section('content')
<div style="padding:50px;">
    <div class="page-header">
        <h1>Advertisement Images</h1>      
      </div>
    <div style="padding:20px;">
        <a href="{{url('/add-carosel')}}" class="btn btn-primary">Add New Image</a>
    </div>
    <div class="table-responsive">
        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            @for($i = 0; $i < count($carosels); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$carosels[$i]->image}}</th>
                    <th class="crud"><a href='/edit-carosel/{{$carosels[$i]->id}}' class="btn btn-primary">Edit</a>  <a href='/delete-carosel/{{$carosels[$i]->id}}' class="btn btn-danger">Delete</a> </th>
                </tr>
            @endfor
        </table>
    </div>
</div>

@endsection

@else 
<script>window.location = "/home";</script>
@endauth 

@auth 

@extends('layouts.app')

@section('content')
<div style="padding:50px;">
    <div class="page-header">
        <h1>Categories</h1>      
    </div>

    <div class="col-md-12" style="background-color:red;">
        <div class="col-md-6" style="background-color:green">

        </div>
    </div>
</div>

@endsection
@else 
<script>window.location = "/login";</script>
@endauth 
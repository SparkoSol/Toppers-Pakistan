@auth 

@extends('layouts.app')
@if(Auth::user()->type == "Sub Admin")
@section('content')
<div style="padding:50px;">

<script>
    $(document).ready(
        function() {
            $("#category").change(function(){
                var categoryId = $("#category").val();
                subCategory(categoryId); 
            });
        }
    );

    function subCategory(id){
        $('#subCategory').empty();
        $('#subCategory').append("<option>Loading...</option>");
        $.ajax({
                type: "GET",
                url: "http://toppers-pakistan.toppers-mart.com/api/category/"+id+"/subCategories",
                dataType: "json",
                success: function (result, status, xhr) {
                                        $('#subCategory').empty();
                    $('#subCategory').append("<option>Select Sub Category</option>");
                    $.each(result,function(i,item){
                        $('#subCategory').append("<option value="+result[i].id+">"+result[i].name+"</option>");
                    });
                },
                error: function (xhr, status, error) {
                    alert("Status=> " + status + " ,Error=> " + error + " ,Xhr=> " + xhr.status + " ,Xhr status text=>" + xhr.statusText)
                }
            });
    }
</script>



 <div style="padding:50px;">
    <div class="page-header">
        <h1>Products</h1>      
    </div>

    <div class='container float-left'>
        <form method="POST" action="{{ url('/filter-products') }}" class="row">
          @csrf
            <div class="form-group row col-md-3" style="margin:10px">
                <select class="form-control" id="category" name="category">
                    <option value="" disabled selected>Category</option>
                    @for ($i = 0; $i < count($categories); $i++)
                        <option value="{{$categories[$i]->id}}">{{$categories[$i]->name}}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group row col-md-3" style="margin:10px">
                <select class="form-control" id="subCategory" name="subCategory" >
                    <option value="" disabled selected>Sub Category</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="margin:10px">
                {{ __('Filter') }}
            </button>
        </form>
    </div>
    
    <div class="table-responsive">
        <form  action="{{ url('/add-product-order-list') }}" method="POST">
            @csrf

        <table style="width:100%" class="table">
            <tr>
                <th>Sr#</th>
                <th>Product Name</th>
                <th>Restaurant Name</th>
                <th>Sub Category Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Quantity to Add</th>
                <th>Add</th>
            </tr>
            @for ($i = 0; $i < count($products); $i++)
                <tr>
                    <th>{{$i+1}}</th>
                    <th>{{$products[$i]->name}}</th>
                    <th>{{$products[$i]->restaurant->name}}</th>
                    <th>{{$products[$i]->subCategory->name}}</th>
                    <th>{{$products[$i]->quantity . " " . $products[$i]->unit->name}}</th>
                    <th>{{"Rs. " .$products[$i]->unit_price}}</th>
                    <th><input class="form-control col-md-3" type="number" id="quantity" name="quantity[{{$products[$i]->id}}]" step="1" value="1" min="1" > </th>
                    <th><input type="checkbox" class="form-control col-md-3" name="check_list[]" value="{{$products[$i]->id}}"></th>
                </tr>
            @endfor
            <button type="submit" class="btn btn-primary">
                {{ __('Add') }}
            </button>

        </table>
    </form> 
    </div>
</div>

@endsection
@else
<script>window.location = "/home";</script>
@endif
@else 
<script>window.location = "/login";</script>
@endauth 
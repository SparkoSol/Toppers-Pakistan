@auth 


@extends('layouts.app')
@if(Auth::user()->type == "Main Admin")


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Update Product') }}</div>

                <div class="card-body">
                    <form method="POST" action='/update-product/{{$product->id}}' enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Product Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" value="{{$product->name}}" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="restaurant" class="col-md-4 col-form-label text-md-right">Restaurant </label>
                            <div class="col-md-6">
                                <select class="form-control" id="restaurant" name="restaurant">
                                    @for ($i = 0; $i < count($restaurants); $i++)
                                        @if($restaurants[$i]->id == $product->restaurant_id)
                                            <option selected="selected" value="{{$restaurants[$i]->id}}">{{$restaurants[$i]->name}}</option>
                                        @else
                                            <option value="{{$restaurants[$i]->id}}">{{$restaurants[$i]->name}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="category" class="col-md-4 col-form-label text-md-right">Sub Category </label>
                            <div class="col-md-6">
                                <select class="form-control" id="subCategory" name="subCategory">
                                    @for ($i = 0; $i < count($subCategories); $i++)
                                        @if($subCategories[$i]->id == $product->subCategory_id)
                                        <option value="{{$subCategories[$i]->id}}" selected="selected">{{$subCategories[$i]->name}}</option>
                                        @else
                                        <option value="{{$subCategories[$i]->id}}">{{$subCategories[$i]->name}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="quantity" class="col-md-4 col-form-label text-md-right">{{ __('Product Quantity') }}</label>

                            <div class="col-md-3">
                                <input id="quantity" value="{{$product->quantity}}" type="text" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity') }}" required autocomplete="quantity" autofocus>

                                @error('quantity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="unit" name="unit">
                                    @for ($i = 0; $i < count($units); $i++)
                                        @if($units[$i]->id == $product->unit_id)
                                            <option selected="selected" value="{{$units[$i]->id}}">{{$units[$i]->name}}</option>
                                        @else
                                            <option value="{{$units[$i]->id}}">{{$units[$i]->name}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="price" class="col-md-4 col-form-label text-md-right">{{ __('Product Unit Price') }}</label>

                            <div class="col-md-6">
                                <input id="price" value="{{$product->unit_price}}" type="text" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}" required autocomplete="price" autofocus>

                                @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="serving" class="col-md-4 col-form-label text-md-right">{{ __('Product Serving') }}</label>

                            <div class="col-md-6">
                                <input id="serving" value="{{$product->serving}}" type="text" class="form-control @error('serving') is-invalid @enderror" name="serving" value="{{ old('serving') }}" required autocomplete="serving" autofocus>

                                @error('serving')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="image" class="col-md-4 col-form-label text-md-right">Insert Image</label>
                            <div class="col-md-6">
                                <input type="file"  class="form-control form-control-file" id="image" name="image">
                            </div>
                        </div>

                        <div class="form-group">
                            <img class="img-thumbnail" src="{{ asset('images/products/' . $product->image) }}" />
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@else
<script>window.location = "/home";</script>
@endif

@else 
<script>window.location = "/login";</script>
@endauth 

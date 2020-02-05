@auth 


@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add Product') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ url('/store-product') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Product Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

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
                                        <option value="{{$restaurants[$i]->id}}">{{$restaurants[$i]->name}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="category" class="col-md-4 col-form-label text-md-right">Category </label>
                            <div class="col-md-6">
                                <select class="form-control" id="category" name="category">
                                    @for ($i = 0; $i < count($categories); $i++)
                                        <option value="{{$categories[$i]->id}}">{{$categories[$i]->name}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="quantity" class="col-md-4 col-form-label text-md-right">{{ __('Product Quantity') }}</label>

                            <div class="col-md-3">
                                <input id="quantity" type="text" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity') }}" required autocomplete="quantity" autofocus>

                                @error('quantity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="unit" name="unit">
                                    @for ($i = 0; $i < count($units); $i++)
                                        <option value="{{$units[$i]->id}}">{{$units[$i]->name}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="price" class="col-md-4 col-form-label text-md-right">{{ __('Product Unit Price') }}</label>

                            <div class="col-md-6">
                                <input id="price" type="text" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}" required autocomplete="price" autofocus>

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
                                <input id="serving" type="text" class="form-control @error('serving') is-invalid @enderror" name="serving" value="{{ old('serving') }}" required autocomplete="serving" autofocus>

                                @error('serving')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Add') }}
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
@endauth 

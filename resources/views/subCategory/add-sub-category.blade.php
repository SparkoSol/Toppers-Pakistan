@auth 

@extends('layouts.app')
@if(Auth::user()->type == "Main Admin")

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add Sub Category') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ url('/store-sub-category') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Sub Category Name') }}</label>

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
                            <label for="category" class="col-md-4 col-form-label text-md-right">Category</label>
                            <div class="col-md-6">
                                <select class="form-control" id="category" name="category">
                                    @for ($i = 0; $i < count($categories); $i++)
                                        <option value="{{$categories[$i]->id}}">{{$categories[$i]->name}}</option>
                                    @endfor
                                </select>
                            </div>
                        
                        </div>

                        <div class="form-group row">
                            <label for="image" class="col-md-4 col-form-label text-md-right">Insert Image</label>
                            <div class="col-md-6">
                                <input type="file" class="form-control form-control-file" id="image" name="image">
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
@endif

@else 
<script>window.location = "/login";</script>
@endauth 

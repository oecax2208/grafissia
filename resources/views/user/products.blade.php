@extends('layouts.user')
@section('title', 'Products')
@section('content')
    <div class="container products">
        <div class="row">
             @foreach($products as $key => $product)
                <div class="col-xs-18 col-sm-6 col-md-3">
                    <div class="thumbnail">
                    @if($product->photo)
                        <a href="{{ $product->photo->getUrl() }}" target="_blank" style="display: inline-block">
                    
                            <img src="{{ $product->photo->getUrl('thumb') }}" width="500" height="300">
                        </a>
                     @endif
                        
                        <div class="caption">
                            <h4> {{ $product->name ?? '' }}</h4>
                            <p>{{ strtolower($product->description) }}</p>
                            <p><strong>Price: </strong>{{ $product->price ?? '' }}</p>
                            <p class="btn-holder"><a href="{{ url('add-to-cart/'.$product->id) }}" class="btn btn-warning btn-block text-center" role="button">Add to cart</a> </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div><!-- End row -->
    </div>
@endsection
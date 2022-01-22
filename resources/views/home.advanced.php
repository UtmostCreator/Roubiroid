@extends('layouts/auth')
@include('includes/large-text');
@foreach($products as $i => $product)
<div class="
        @if($i % 2 === 0)
            bg-grey
        @endif
            ">
    <div class="container">
        <h2>ID: {{ $product->id }}; {{ $product->name }}</h2>
        <p>{{ $product->description }}</p>
        <a href="{{ $product->route }}">Order</a>
    </div>
</div>
@endforeach
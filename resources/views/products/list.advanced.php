<?php
//\Modules\DD::dd($this);
/* @var numeric $test */
?>
@extends('layouts/products')
@include('products/product-detail', ['name' => 'test'])
<h1>All Products</h1>
<a href="#">Show All Products</a>
{{--@if($test == 123)--}}
@if($test == 321)
    <a href="#">IF true statement</a>
@else
<a href="#">ELSE statement</a>
@endif
<textarea name="" id="" cols="30" rows="10">
    {{$scary}}
</textarea>
<p>{!!$scary!!}</p>
<!--<textarea name="" id="" cols="30" rows="10">-->
<!--    {!!$scary!!}-->
<!--</textarea>-->
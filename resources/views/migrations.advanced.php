@extends('layouts/auth2')
<h1>List of migrations</h1>
<ol>
@foreach($migrations as $m)
    <li>Migration Name: <span class="badge bg-secondary">{{ $m->name }}</span></li>
@endforeach
</ol>
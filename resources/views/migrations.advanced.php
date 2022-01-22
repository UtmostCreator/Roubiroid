@extends('layouts/auth2')
<h1>Список виконаних міграцій</h1>
<ol>
@foreach($migrations as $m)
    <li>Назва міграції: <span class="badge bg-secondary">{{ $m->name }}</span></li>
@endforeach
</ol>
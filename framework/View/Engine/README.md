# Engines

## BaseEngine
- replaces `{variable}` with real php array `$view->data`

## PhpEngine
### Allows you to use custom existing macros
- `$this->escape('TESTS');`
- `$this->includes('path/to/file');`

## AdvancedEngine
### Allows you to use the following constructions
- **Flow Controls**:
  - `@if`
  - `@else`
  - `@elseif` - PENDING
  - `@endif`
  - `@foreach`
  - `@endforeach`
- **Output controls**:
  - `{{ $escapedVariable }}`
  - `{!! $unescapedVariable !!}`
  - Templating:
    - `@extends('path/to/file')`
    - `@include('path/to/file')`
    - create UseMacros trait to allow use of marcos - PENDING
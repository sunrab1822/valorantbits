@extends('layouts.app')

@section('content')
<div class="container">
    <open-crate crate="{{$crate}}" is-authenticated="{{ Auth::check() }}"></open-crate>
</div>
@endsection

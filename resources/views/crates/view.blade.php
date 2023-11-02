@extends('layouts.app')

@section('content')
<div class="container">

    <open-crate :crate="`{{$crate}}`"></open-crate>
</div>
@endsection

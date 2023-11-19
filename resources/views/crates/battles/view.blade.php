@extends('layouts.app')

@section('content')
<div class="container">
    <open-crate-battle crate_battle="{{$battle}}" is-authenticated="{{ Auth::check() }}"></open-crate-battle>
</div>
@endsection

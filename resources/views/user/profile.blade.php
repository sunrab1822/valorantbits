@extends('layouts.app')

@section('content')
    <div class="container">
        <profile user="{{ $user }}"></profile>
    </div>
@endsection

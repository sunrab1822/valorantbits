@extends('layouts.app')

@section('content')
<div class="container">
    <profile username="{{ $user->username  }}" profileImage="{{ $user->profileImage }}"></profile>
</div>
@endsection

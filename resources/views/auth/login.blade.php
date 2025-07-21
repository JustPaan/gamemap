@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Log in to GameMap</h2>
    
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Log In</button>

        @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
        @endif
    </form>
    
    <div class="small-text">
        Don't have an account? <a href="{{ route('register') }}">Sign up</a>
    </div>
</div>
@endsection
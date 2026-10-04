@extends('layouts.admin')
@section('title', 'Masuk')
@section('content')
<section class="login-card">
    <p class="overline">CUSTOMER AREA</p><h1>Kelola hari istimewa.</h1><p>Masuk untuk memeriksa dan menampilkan ucapan tamu.</p>
    <form method="POST" action="{{ route('login.store') }}" class="admin-form">
        @csrf
        <label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
        @error('email')<small class="field-error">{{ $message }}</small>@enderror
        <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>
        @error('password')<small class="field-error">{{ $message }}</small>@enderror
        <label class="checkbox"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        <button type="submit" class="primary-button">Masuk ke Dashboard</button>
    </form>
</section>
@endsection

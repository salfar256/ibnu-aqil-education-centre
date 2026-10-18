@extends('layouts.auth')
@section('title','Pemulihan akun')
@section('content')<h2>Pulihkan akses Anda</h2><p>Kami akan mengirim tautan pengaturan kata sandi ke email akun aktif.</p><form method="post" action="{{ route('password.email') }}" class="auth-form">@csrf<label for="email">Email terdaftar</label><input id="email" name="email" type="email" required value="{{ old('email') }}" autocomplete="email"><button class="button button-primary" type="submit">Kirim petunjuk pemulihan</button></form><a class="back-link" href="{{ route('login') }}">← Kembali ke halaman masuk</a>@endsection

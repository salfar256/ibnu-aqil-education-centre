@extends('layouts.workspace')
@section('title', $title)
@section('content')
<div class="page-heading"><div><p class="eyebrow">WEBSITE LEMBAGA</p><h1>{{ $title }}</h1><p class="lead">Hubungkan pengunjung dengan informasi resmi yang sudah dikonfirmasi.</p></div></div>
<div class="alert alert-info">Kosongkan informasi yang belum dikonfirmasi. Tautan harus menggunakan HTTPS. Informasi yang disimpan dapat ditampilkan langsung pada website publik.</div>
<form class="panel edit-panel" method="post" action="{{ route('admin.resources.store', 'settings') }}">@csrf<div class="form-grid">
    @foreach($fields as $key => [$caption, $inputType]) @include('admin.field', ['name'=>$key, 'label'=>$caption, 'type'=>$inputType, 'value'=>$values[$key] ?? '', 'wide'=>$key === 'address', 'max'=>$key === 'address' ? 1000 : 500, 'help'=>$key === 'whatsapp_url' ? 'Contoh format: https://wa.me/6281234567890. Gunakan nomor resmi lembaga.' : ($key === 'registration_url' ? 'Tautan ke sistem pendaftaran yang sudah ada.' : '')]) @endforeach
    @include('admin.field', ['name'=>'verified', 'label'=>'Saya sudah memastikan kontak dan tautan di atas merupakan informasi resmi terkini', 'type'=>'checkbox', 'wide'=>true])
</div><div class="form-actions"><button class="button button-primary" type="submit">Simpan informasi resmi</button></div></form>
@endsection

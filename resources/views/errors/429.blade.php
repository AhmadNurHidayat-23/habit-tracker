@extends('layouts.app')
@section('content')
<section class="hero">
<p class="eyebrow">HABIT TRACKER / 429</p>
<h1>Terlalu banyak permintaan.</h1>
<p class="subtitle">Tunggu sebentar sebelum mencoba kembali.</p>
<a href="{{ route('habits.index') }}">← Kembali ke halaman utama</a>
</section>
@endsection

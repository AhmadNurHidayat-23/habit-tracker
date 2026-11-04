@extends('layouts.app')
@section('content')
<section class="hero">
<p class="eyebrow">HABIT TRACKER / 500</p>
<h1>Terjadi kendala.</h1>
<p class="subtitle">Silakan coba kembali nanti.</p>
<a href="{{ route('habits.index') }}">← Kembali ke halaman utama</a>
</section>
@endsection

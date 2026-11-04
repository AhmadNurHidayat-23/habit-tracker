@extends('layouts.app')
@section('content')
<section class="hero">
<p class="eyebrow">HABIT TRACKER / 404</p>
<h1>Kebiasaan tidak ditemukan.</h1>
<p class="subtitle">Kebiasaan ini mungkin sudah dihapus.</p>
<a href="{{ route('habits.index') }}">← Kembali ke halaman utama</a>
</section>
@endsection

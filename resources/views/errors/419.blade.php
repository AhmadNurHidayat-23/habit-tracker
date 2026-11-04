@extends('layouts.app')
@section('content')
<section class="hero">
<p class="eyebrow">HABIT TRACKER / 419</p>
<h1>Sesi telah berakhir.</h1>
<p class="subtitle">Muat ulang halaman sebelum melanjutkan.</p>
<a href="{{ route('habits.index') }}">← Kembali ke halaman utama</a>
</section>
@endsection

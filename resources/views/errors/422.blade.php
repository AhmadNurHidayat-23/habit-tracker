@extends('layouts.app')
@section('content')
<section class="hero"><p class="eyebrow">HABIT TRACKER / 422</p><h1>Bulan tidak valid.</h1><p class="subtitle">Gunakan navigasi kalender untuk memilih bulan yang tersedia.</p><a href="{{ route('habits.index') }}">← Kembali ke halaman utama</a></section>
@endsection

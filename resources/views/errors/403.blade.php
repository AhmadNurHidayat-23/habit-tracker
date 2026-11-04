@extends('layouts.app')
@section('content')
<section class="hero">
<p class="eyebrow">HABIT TRACKER / 403</p>
<h1>Hanya untuk penggunaan lokal.</h1>
<p class="subtitle">Pengelolaan kebiasaan hanya dapat diakses dari komputer ini dengan host localhost atau 127.0.0.1.</p>
<a href="{{ route('habits.index') }}">← Kembali ke halaman utama</a>
</section>
@endsection

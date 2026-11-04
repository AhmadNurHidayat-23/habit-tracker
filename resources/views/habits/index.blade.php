@extends('layouts.app')
@section('content')
<section class="hero">
<p class="eyebrow">01 / KEBIASAAN, SATU PER SATU</p>
<h1>Bangun Kebiasaan Baik.</h1>
<div class="hero-bottom">
<p>Mulai dari langkah kecil, tumbuh setiap hari.</p>
<time datetime="{{ $today->toDateString() }}">{{ $today->locale('id')->translatedFormat('l, d F Y') }}</time>
</div>
</section>
<section class="metrics" aria-label="Progres harian">
<div>
<span>Kebiasaan aktif</span>
<strong>{{ str_pad($habits->count(),2,'0',STR_PAD_LEFT) }}</strong>
</div>
<div>
<span>Selesai hari ini</span>
<strong>{{ str_pad($done,2,'0',STR_PAD_LEFT) }}<small> / {{ $habits->count() }}</small>
</strong>
</div>
<div>
<span>Progres hari ini</span>
<strong>{{ $habits->count() ? (int)floor($done/$habits->count()*100) : 0 }}<small>%</small>
</strong>
<progress value="{{ $done }}" max="{{ max(1,$habits->count()) }}" aria-label="Progres kebiasaan hari ini">
</progress>
</div>
</section>
<div class="home-grid">
<section>
<div class="section-heading">
<div>
<p class="eyebrow">RUTINITAS HARI INI</p>
<h2>Rawat yang berarti.</h2>
</div>
<span class="small-label">{{ $done }} dari {{ $habits->count() }} selesai</span>
</div>
<div class="habits-list">
@forelse($habits as $habit)@php($summary=$summaries[$habit->id])<article @class(['habit-row','completed'=>$summary['done']])>@include('partials.completion',['done'=>$summary['done']])<div class="habit-text">
<h3>
<a href="{{ route('habits.show',$habit) }}">{{ $habit->name }}</a>
</h3>@if($habit->description)<p>{{ $habit->description }}</p>@endif<div class="habit-tags">
<span>{{ $summary['streak'] }} hari berturut-turut</span>
<span>{{ $summary['done'] ? 'Selesai hari ini' : 'Belum selesai hari ini' }}</span>
</div>
</div>
<a class="row-link" href="{{ route('habits.show',$habit) }}" aria-label="Lihat detail {{ $habit->name }}">↗</a>
</article>
@empty<div class="empty">
<span aria-hidden="true">↗</span>
<h3>Setiap kebiasaan dimulai dari satu langkah.</h3>
<p>Tambahkan kebiasaan pertama Anda. Belum ada aktivitas yang tercatat.</p>
<a href="{{ route('habits.create') }}">Tambah kebiasaan pertama →</a>
</div>@endforelse</div>
<div class="editorial-note">
<span>CATATAN KECIL</span>
<p>Belum selesai hari ini? Streak tetap terjaga jika kemarin selesai. Jika satu hari terlewat, streak kembali ke nol; riwayat Anda tetap tersimpan.</p>
</div>
</section>
<aside class="plant-panel">
<div class="section-heading">
<p class="eyebrow">TUMBUH BERSAMA ANDA</p>
<span>0{{ $stage['index']+1 }} / 05</span>
</div>
<img class="plant-visual" src="{{ asset('images/plants/'.$stage['image'].'.svg') }}" alt="Tahap tanaman: {{ $stage['name'] }}" width="320" height="370">
<div class="plant-caption">
<h2>{{ $stage['name'] }}</h2>
<p>Streak terpanjang saat ini: <strong>{{ $best }} hari.</strong>
</p>
</div>@include('partials.stages')<p class="plant-note">Tanaman mengikuti streak aktif, bukan total penyelesaian. Konsistensi memberi ruang untuk tumbuh.</p>
</aside>
</div>
@endsection

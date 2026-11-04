@extends('layouts.app')
@section('title',$habit->name.' — Habit Tracker')
@section('content')
<section class="hero detail-heading">
<p class="eyebrow">02 / JEJAK KONSISTENSI</p>
<h1>{{ $habit->name }}</h1>@if($habit->description)<p class="subtitle">{{ $habit->description }}</p>@endif<div class="detail-actions">
<a href="{{ route('habits.edit',$habit) }}">Edit kebiasaan ↗</a>
<a href="{{ route('habits.index') }}">← Semua kebiasaan</a>
</div>
</section>
<div class="detail-grid">
<aside class="plant-panel">
<p class="eyebrow">TAHAP PERTUMBUHAN / 0{{ $summary['stage']['index']+1 }}</p>
<img class="plant-visual" src="{{ asset('images/plants/'.$summary['stage']['image'].'.svg') }}" alt="Tahap tanaman: {{ $summary['stage']['name'] }}" width="320" height="370">
<h2>{{ $summary['stage']['name'] }}</h2>
<div class="detail-metrics">
<div>
<strong>{{ $summary['streak'] }}</strong>
<span>Hari berturut-turut</span>
</div>
<div>
<strong>{{ $summary['total'] }}</strong>
<span>Total hari berhasil</span>
</div>
</div>
<div class="today-action">@include('partials.completion',['done'=>$summary['done']])<div>
<strong>{{ $summary['done'] ? 'Selesai hari ini' : 'Belum selesai hari ini' }}</strong>
<span>{{ $today->locale('id')->translatedFormat('d F Y') }} · WIB</span>
</div>
</div>@include('partials.stages',['stage'=>$summary['stage']])</aside>
<div>
<section class="calendar-section">
<div class="section-heading">
<div>
<p class="eyebrow">KALENDER AKTIVITAS</p>
<h2>{{ $calendar['label'] }}</h2>
</div>
<div class="month-navigation">
<a href="{{ route('habits.show',[$habit,'month'=>$calendar['previous']]) }}" aria-label="Bulan sebelumnya">←</a>@if($calendar['next'])<a href="{{ route('habits.show',[$habit,'month'=>$calendar['next']]) }}" aria-label="Bulan berikutnya">→</a>@else<span aria-hidden="true">→</span>@endif</div>
</div>
<table class="calendar" aria-label="Kalender aktivitas {{ $calendar['label'] }}">
<thead>
<tr>@foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $day)<th scope="col">{{ $day }}</th>@endforeach</tr>
</thead>
<tbody>@foreach($calendar['rows'] as $week)<tr>@foreach($week as $cell)<td>@if($cell)<span class="day {{ $cell['state'] }}" aria-label="{{ $cell['label'] }}" title="{{ $cell['label'] }}">{{ $cell['day'] }}@if($cell['state']==='done')<small aria-hidden="true">✓</small>@endif</span>@endif</td>@endforeach</tr>@endforeach</tbody>
</table>
<div class="legend">
<span>
<i class="legend-done">
</i>Selesai</span>
<span>
<i class="legend-today">
</i>Hari ini</span>
<span>
<i>
</i>Tidak selesai / belum tersedia</span>
</div>
</section>
<section class="weekly-section">
<div class="section-heading">
<div>
<p class="eyebrow">RITME MINGGUAN</p>
<h2>Empat minggu terakhir.</h2>
</div>
</div>@foreach($summary['weeks'] as $week)<div class="week-row">
<div>
<strong>{{ $week['label'] }}</strong>
<span>{{ $week['start'] }} — {{ $week['end'] }}</span>
</div>
<progress value="{{ $week['done'] }}" max="{{ max(1,$week['possible']) }}" aria-label="Progres {{ $week['label'] }}">
</progress>
<span>{{ $week['possible'] ? $week['done'].' / '.$week['possible'].' hari' : 'Belum tersedia' }}</span>
</div>@endforeach<p class="hint weekly-hint">Jumlah hari dihitung sejak kebiasaan dibuat sampai hari ini. Tanggal mendatang tidak dihitung.</p>
</section>
<section class="delete-section">
<h2>Hapus kebiasaan</h2>
<p>Penghapusan juga menghapus seluruh riwayat kebiasaan ini.</p>
<form action="{{ route('habits.destroy',$habit) }}" method="POST" data-confirm-delete>@csrf @method('DELETE')<button class="text-button">Hapus kebiasaan →</button>
</form>
</section>
</div>
</div>
@endsection

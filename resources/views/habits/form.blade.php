@extends('layouts.app')
@section('content')
<section class="hero">
<p class="eyebrow">01 / {{ $habit->exists ? 'RAWAT RUTINITAS' : 'LANGKAH PERTAMA' }}</p>
<h1>{{ $habit->exists ? 'Perbarui kebiasaan.' : 'Mulai kebiasaan baik.' }}</h1>
<p class="subtitle">Buat sederhana, agar mudah dilakukan setiap hari.</p>
</section>
<div class="form-grid">
<form method="POST" action="{{ $habit->exists ? route('habits.update',$habit) : route('habits.store') }}">@csrf @if($habit->exists) @method('PUT') @endif<label for="name">Nama kebiasaan</label>
<input name="name" id="name" value="{{ old('name',$habit->name) }}" maxlength="100" required placeholder="Contoh: Membaca selama 15 menit">
<label for="description">Deskripsi <span>(opsional)</span>
</label>
<textarea name="description" id="description" rows="5" maxlength="500" placeholder="Tulis pengingat singkat untuk diri sendiri.">{{ old('description',$habit->description) }}</textarea>
<p class="hint">Maksimal 500 karakter. Satu kebiasaan kecil sudah cukup untuk memulai.</p>
<div class="actions">
<button class="button">Simpan Kebiasaan <span>↗</span>
</button>
<a href="{{ $habit->exists ? route('habits.show',$habit) : route('habits.index') }}">Batal</a>
</div>
</form>
<aside class="side-note">
<p class="eyebrow">SEDIKIT, TETAPI RUTIN.</p>
<h2>Yang sederhana lebih mudah bertahan.</h2>
<p>Tentukan kebiasaan yang jelas dan realistis. Tandai selesai setelah Anda benar-benar melakukannya.</p>
<img src="{{ asset('images/plants/sprout.svg') }}" class="form-plant" width="320" height="370" alt="Ilustrasi tunas tanaman">
</aside>
</div>
@endsection

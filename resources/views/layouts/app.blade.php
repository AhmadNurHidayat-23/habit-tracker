<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>@yield('title','Habit Tracker — Bangun Kebiasaan Baik')</title>
<meta name="description" content="Catat kebiasaan harian dan tumbuhkan konsistensi, satu langkah setiap hari.">@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body>
<div class="shell">
<header>
<a class="brand" href="{{ route('habits.index') }}">
<span class="brand-mark" aria-hidden="true">↗</span> Habit Tracker<span class="edition"> / 01</span>
</a>
<nav aria-label="Navigasi utama">
<a href="{{ route('habits.index') }}" @class(['selected'=>request()->routeIs('habits.index')])>Kebiasaan saya</a>
</nav>
<a class="button" href="{{ route('habits.create') }}">Tambah Kebiasaan <span>+</span>
</a>
</header>
<main>
@if(session('status'))<p class="notice" role="status">{{ session('status') }}</p>@endif
@if($errors->any())<div class="notice" role="alert">
<strong>Periksa kembali data Anda.</strong>
<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>@endif
@yield('content')</main>
<footer>
<span>Habit Tracker / Tumbuh dengan konsisten</span>
<span>Langkah kecil. Setiap hari.</span>
<span>Demo lokal · Asia/Jakarta</span>
</footer>
</div>
<dialog id="confirmation" aria-labelledby="confirm-title">
<h2 id="confirm-title">Hapus kebiasaan?</h2>
<p>Nama, deskripsi, dan seluruh riwayat kebiasaan ini akan dihapus permanen.</p>
<div class="dialog-actions">
<button class="button outline" id="confirm-cancel">Batal</button>
<button class="button" id="confirm-accept">Hapus kebiasaan</button>
</div>
</dialog>
</body>
</html>

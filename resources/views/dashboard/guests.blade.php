@extends('layouts.admin')
@section('title', 'Link Tamu '.$invitation->title)
@section('content')
<div class="page-heading"><a href="{{ route('dashboard') }}" class="back-link">← Semua undangan</a><p class="overline">{{ strtoupper($invitation->title) }}</p><h1>Generator Nama Tamu</h1><p>Masukkan satu nama per baris. Setiap link memiliki token unik dan maksimal tiga kali pengiriman ucapan.</p></div>
<section class="generator-card">
    <form method="POST" action="{{ route('dashboard.guests.store', $invitation) }}" class="admin-form">@csrf<label for="names">Daftar nama tamu</label><textarea id="names" name="names" rows="7" placeholder="Bapak Budi & Keluarga&#10;Cenara Dio & Partner" required>{{ old('names') }}</textarea>@error('names')<small class="field-error">{{ $message }}</small>@enderror<button type="submit" class="primary-button">Buat Link Tamu</button></form>
</section>
<div class="guest-list-heading"><h2>Link yang sudah dibuat</h2><span>{{ $guests->total() }} tamu</span></div>
<div class="guest-link-list">
    @forelse($guests as $guest)
        @php($guestUrl = route('invitation.show').'?guest='.$guest->token.'&to='.rawurlencode($guest->name))
        <article class="guest-link-card"><div><h3>{{ $guest->name }}</h3><p>{{ $guestUrl }}</p><span>{{ $guest->submission_count }} dari {{ $guest->max_submissions }} pengiriman digunakan</span></div><button type="button" class="secondary-button copy-guest-link" data-link="{{ $guestUrl }}">Salin Link</button></article>
    @empty
        <div class="empty-state">Belum ada link tamu. Masukkan daftar nama di atas untuk memulai.</div>
    @endforelse
</div>
<div class="pagination">{{ $guests->links() }}</div>
<script>
document.querySelectorAll('.copy-guest-link').forEach(button => button.addEventListener('click', async () => {
    try { await navigator.clipboard.writeText(button.dataset.link); button.textContent = 'Tersalin'; setTimeout(() => button.textContent = 'Salin Link', 1800); }
    catch { window.prompt('Salin link berikut:', button.dataset.link); }
}));
</script>
@endsection

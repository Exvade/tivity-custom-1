@extends('layouts.admin')
@section('title', 'Ucapan '.$invitation->title)
@section('content')
<div class="page-heading split">
    <div><a href="{{ route('dashboard') }}" class="back-link">← Semua undangan</a><p class="overline">{{ strtoupper($invitation->title) }}</p><h1>Moderasi Ucapan</h1><p>Hanya ucapan berstatus “Ditampilkan” yang terlihat oleh tamu.</p></div>
    <form method="POST" action="{{ route('dashboard.invitations.settings', $invitation) }}" class="mode-form">@csrf @method('PATCH')<label for="moderation_mode">Mode moderasi</label><select id="moderation_mode" name="moderation_mode" onchange="this.form.submit()"><option value="manual" @selected($invitation->moderation_mode === 'manual')>Semua harus disetujui</option><option value="hybrid" @selected($invitation->moderation_mode === 'hybrid')>Otomatis jika aman</option></select></form>
</div>
<nav class="filter-tabs">
    <a @class(['active' => ! $status]) href="{{ route('dashboard.wishes', $invitation) }}">Semua</a>
    @foreach(['pending' => 'Menunggu', 'approved' => 'Ditampilkan', 'rejected' => 'Ditolak', 'hidden' => 'Disembunyikan'] as $key => $label)<a @class(['active' => $status === $key]) href="{{ route('dashboard.wishes', [$invitation, 'status' => $key]) }}">{{ $label }}</a>@endforeach
</nav>
<div class="wish-admin-list">
    @forelse($wishes as $wish)
        <article class="wish-admin-card">
            <div class="wish-admin-top"><div><h2>{{ $wish->guest_name }}</h2><span class="badge {{ $wish->attendance === 'yes' ? 'positive' : '' }}">{{ $wish->attendance === 'yes' ? 'Hadir · '.$wish->guest_count.' orang' : 'Berhalangan' }}</span></div><span class="status-badge {{ $wish->status }}">{{ ['pending'=>'Menunggu','approved'=>'Ditampilkan','rejected'=>'Ditolak','hidden'=>'Disembunyikan'][$wish->status] }}</span></div>
            <blockquote>{{ $wish->message }}</blockquote>
            @if($wish->moderation_reason && $wish->moderation_reason !== 'seed:sample')<p class="flag-reason">⚑ {{ $wish->moderation_reason }}</p>@endif
            <p class="meta">Dikirim {{ $wish->created_at->diffForHumans() }}</p>
            <div class="moderation-actions">@foreach(['approved'=>'Tampilkan','rejected'=>'Tolak','hidden'=>'Sembunyikan'] as $value => $label)<form method="POST" action="{{ route('dashboard.wishes.update', [$invitation, $wish]) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $value }}"><button type="submit" class="{{ $value === 'approved' ? 'approve-button' : 'secondary-button' }}" @disabled($wish->status === $value)>{{ $label }}</button></form>@endforeach</div>
        </article>
    @empty
        <div class="empty-state">Tidak ada ucapan dalam kategori ini.</div>
    @endforelse
</div>
<div class="pagination">{{ $wishes->links() }}</div>
@endsection

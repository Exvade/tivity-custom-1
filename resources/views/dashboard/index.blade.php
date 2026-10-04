@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="page-heading"><div><p class="overline">DASHBOARD</p><h1>Undangan Anda</h1><p>Kelola ucapan dan moderasi dari satu tempat.</p></div></div>
<div class="invitation-list">
    @forelse($invitations as $invitation)
        <article class="invitation-card">
            <div><span class="status-dot"></span> {{ $invitation->is_published ? 'Tayang' : 'Draft' }}</div>
            <h2>{{ $invitation->title }}</h2><p>{{ url('/') }}</p>
            <div class="stats"><div><strong>{{ $invitation->pending_wishes_count }}</strong><span>Menunggu</span></div><div><strong>{{ $invitation->approved_wishes_count }}</strong><span>Ditampilkan</span></div><div><strong>{{ $invitation->wishes_count }}</strong><span>Total</span></div></div>
            <div class="card-actions"><a class="secondary-button" href="{{ route('invitation.show') }}" target="_blank">Lihat Undangan</a><a class="secondary-button" href="{{ route('dashboard.guests', $invitation) }}">Link Tamu</a><a class="primary-button" href="{{ route('dashboard.wishes', $invitation) }}">Kelola Ucapan</a></div>
        </article>
    @empty
        <div class="empty-state">Belum ada undangan yang terhubung ke akun ini.</div>
    @endforelse
</div>
@endsection

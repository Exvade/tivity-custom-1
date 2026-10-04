<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardGuestController extends Controller
{
    public function index(Invitation $invitation): View
    {
        $this->authorizeInvitation($invitation);
        $guests = $invitation->guests()->latest()->paginate(30);

        return view('dashboard.guests', compact('invitation', 'guests'));
    }

    public function store(Request $request, Invitation $invitation): RedirectResponse
    {
        $this->authorizeInvitation($invitation);
        $data = $request->validate(['names' => ['required', 'string', 'max:10000']]);
        $names = collect(preg_split('/\R/u', $data['names']))
            ->map(fn ($name) => trim($name))
            ->filter(fn ($name) => mb_strlen($name) >= 2 && mb_strlen($name) <= 70)
            ->unique(fn ($name) => mb_strtolower($name))
            ->take(100);

        if ($names->isEmpty()) {
            return back()->withErrors(['names' => 'Masukkan minimal satu nama tamu yang valid.']);
        }

        $created = 0;
        DB::transaction(function () use ($invitation, $names, &$created) {
            foreach ($names as $name) {
                $guest = $invitation->guests()->firstOrCreate(
                    ['name' => $name],
                    ['token' => Str::random(64), 'max_submissions' => 3]
                );
                if ($guest->wasRecentlyCreated) {
                    $created++;
                }
            }
        });

        return back()->with('success', $created.' link tamu baru berhasil dibuat.');
    }

    private function authorizeInvitation(Invitation $invitation): void
    {
        abort_unless($invitation->user_id === auth()->id(), 403);
    }
}

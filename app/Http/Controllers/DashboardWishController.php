<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Wish;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardWishController extends Controller
{
    public function index(Request $request, Invitation $invitation): View
    {
        $this->authorizeInvitation($invitation);
        $status = $request->string('status')->value();

        $wishes = $invitation->wishes()
            ->when(in_array($status, ['pending', 'approved', 'rejected', 'hidden'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.wishes', compact('invitation', 'wishes', 'status'));
    }

    public function update(Request $request, Invitation $invitation, Wish $wish): RedirectResponse
    {
        $this->authorizeInvitation($invitation);
        abort_unless($wish->invitation_id === $invitation->id, 404);
        $data = $request->validate(['status' => ['required', Rule::in(['approved', 'rejected', 'hidden'])]]);

        $wish->update([
            'status' => $data['status'],
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        return back()->with('success', 'Status ucapan berhasil diperbarui.');
    }

    public function settings(Request $request, Invitation $invitation): RedirectResponse
    {
        $this->authorizeInvitation($invitation);
        $data = $request->validate(['moderation_mode' => ['required', Rule::in(['manual', 'hybrid'])]]);
        $invitation->update($data);

        return back()->with('success', 'Mode moderasi berhasil diperbarui.');
    }

    private function authorizeInvitation(Invitation $invitation): void
    {
        abort_unless($invitation->user_id === auth()->id(), 403);
    }
}

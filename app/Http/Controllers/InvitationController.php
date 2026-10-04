<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(): View
    {
        $invitation = Invitation::where('slug', config('invitation.default_slug'))->firstOrFail();

        abort_unless($invitation->is_published || auth()->id() === $invitation->user_id, 404);

        $content = $invitation->content;
        $content['rsvpEndpoint'] = route('wishes.store');
        $content['csrfToken'] = csrf_token();
        $content['guestToken'] = request('guest');
        $content['guestName'] = $invitation->guests()
            ->where('token', request('guest'))
            ->value('name');
        $content['wishes'] = $invitation->wishes()
            ->where('status', 'approved')
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($wish) => [
                'name' => $wish->guest_name,
                'attendance' => $wish->attendance,
                'message' => $wish->message,
                'createdAt' => $wish->created_at->toISOString(),
                'sample' => $wish->moderation_reason === 'seed:sample',
            ])
            ->values();

        return view('invitation', [
            'invitation' => $invitation,
            'frontendData' => $content,
        ]);
    }
}

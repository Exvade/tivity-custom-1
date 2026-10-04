<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Services\WishModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WishController extends Controller
{
    public function store(Request $request, Invitation $invitation, WishModerationService $moderator): JsonResponse
    {
        abort_unless($invitation->is_published, 404);

        if ($request->filled('website')) {
            return response()->json(['message' => 'Terima kasih.'], 202);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:70'],
            'attendance' => ['required', Rule::in(['yes', 'no'])],
            'guests' => ['required', 'integer', 'between:0,4'],
            'message' => ['required', 'string', 'min:3', 'max:600'],
            'guest_token' => ['nullable', 'string', 'size:64'],
            'website' => ['nullable', 'max:0'],
        ]);

        $guest = null;
        if (! empty($data['guest_token'])) {
            $guest = $invitation->guests()->where('token', $data['guest_token'])->first();
            if (! $guest) {
                return response()->json(['message' => 'Tautan tamu tidak valid.'], 422);
            }
            if ($guest->submission_count >= $guest->max_submissions) {
                return response()->json(['message' => 'Batas pengiriman ucapan untuk tautan ini sudah tercapai.'], 429);
            }
            $data['name'] = $guest->name;
        }

        $inspection = $moderator->inspect($data['message']);
        $status = $invitation->moderation_mode === 'hybrid' && ! $inspection['flagged']
            ? 'approved'
            : 'pending';

        $wish = DB::transaction(function () use ($request, $invitation, $guest, $data, $inspection, $status) {
            $wish = $invitation->wishes()->create([
                'guest_id' => $guest?->id,
                'guest_name' => trim($data['name']),
                'attendance' => $data['attendance'],
                'guest_count' => $data['attendance'] === 'no' ? 0 : $data['guests'],
                'message' => trim($data['message']),
                'status' => $status,
                'moderation_reason' => $inspection['reason'],
                'ip_hash' => hash_hmac('sha256', (string) $request->ip(), config('app.key')),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
            ]);

            if ($guest) {
                $guest->increment('submission_count');
                $guest->update(['last_submitted_at' => now()]);
            }

            return $wish;
        });

        return response()->json([
            'message' => $wish->status === 'approved'
                ? 'Terima kasih! Ucapan Anda sudah ditampilkan.'
                : 'Terima kasih! Ucapan Anda menunggu persetujuan mempelai.',
            'status' => $wish->status,
            'wish' => $wish->status === 'approved' ? [
                'name' => $wish->guest_name,
                'attendance' => $wish->attendance,
                'message' => $wish->message,
                'createdAt' => $wish->created_at->toISOString(),
            ] : null,
        ], 201);
    }
}

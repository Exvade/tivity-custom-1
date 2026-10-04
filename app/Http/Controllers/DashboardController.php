<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $invitations = auth()->user()->invitations()
            ->withCount([
                'wishes',
                'wishes as pending_wishes_count' => fn ($query) => $query->where('status', 'pending'),
                'wishes as approved_wishes_count' => fn ($query) => $query->where('status', 'approved'),
            ])
            ->get();

        return view('dashboard.index', compact('invitations'));
    }
}

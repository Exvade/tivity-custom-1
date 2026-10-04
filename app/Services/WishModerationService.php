<?php

namespace App\Services;

use Illuminate\Support\Str;

class WishModerationService
{
    public function inspect(string $message): array
    {
        $normalized = Str::of($message)
            ->lower()
            ->replace(['4', '3', '1', '0', '5', '7'], ['a', 'e', 'i', 'o', 's', 't'])
            ->replaceMatches('/[^\pL\pN\s]/u', ' ')
            ->replaceMatches('/(.)\1{2,}/u', '$1$1')
            ->squish()
            ->value();

        foreach (config('moderation.flagged_terms', []) as $term) {
            if (preg_match('/(?<!\pL)'.preg_quote($term, '/').'(?!\pL)/iu', $normalized)) {
                return ['flagged' => true, 'reason' => 'Kata atau frasa perlu diperiksa.'];
            }
        }

        foreach (config('moderation.blocked_patterns', []) as $pattern) {
            if (preg_match($pattern, $message)) {
                return ['flagged' => true, 'reason' => 'Pola tautan atau spam terdeteksi.'];
            }
        }

        return ['flagged' => false, 'reason' => null];
    }
}

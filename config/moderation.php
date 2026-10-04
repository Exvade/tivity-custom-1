<?php

return [
    // Kata di sini hanya menandai ucapan untuk diperiksa; tidak langsung menghapusnya.
    'flagged_terms' => [
        'anjing', 'bangsat', 'bajingan', 'goblok', 'tolol', 'kontol', 'memek',
        'ngentot', 'jancok', 'porno', 'bokep', 'slot gacor', 'judol',
    ],
    'blocked_patterns' => [
        '/https?:\/\//iu',
        '/(?:wa\.me|t\.me|bit\.ly)\//iu',
        '/(.)\1{7,}/u',
    ],
];

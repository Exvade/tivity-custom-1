<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (! $email || ! $password) {
            throw new \RuntimeException('ADMIN_EMAIL dan ADMIN_PASSWORD wajib diisi sebelum menjalankan seeder.');
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => config('admin.name'), 'password' => Hash::make($password)]
        );

        $invitation = Invitation::updateOrCreate(
            ['slug' => 'alya-dan-salman'],
            [
                'user_id' => $user->id,
                'title' => 'Levi & Dio',
                'moderation_mode' => 'manual',
                'is_published' => true,
                'content' => [
                    'bride' => ['name' => 'Levi', 'fullName' => 'Levi Anindya Putri', 'parents' => 'Bapak Aditya Pratama & Ibu Ratna Sari', 'order' => 'Putri pertama dari', 'instagram' => ''],
                    'groom' => ['name' => 'Dio', 'fullName' => 'Dio Ahmad Pratama', 'parents' => 'Bapak Ahmad Wijaya & Ibu Dewi Lestari', 'order' => 'Putra pertama dari', 'instagram' => ''],
                    'date' => '2026-12-12T08:00:00+07:00',
                    'dateLabel' => 'Sabtu, 12 Desember 2026',
                    'venue' => 'The Grand Ballroom',
                    'address' => 'Hotel Majapahit, Jl. Tunjungan No. 65, Surabaya',
                    'city' => 'Surabaya, Indonesia',
                    'mapUrl' => 'https://www.google.com/maps/search/?api=1&query=Hotel+Majapahit+Surabaya',
                    'events' => [
                        ['name' => 'Akad Nikah', 'time' => '08.00 – 10.00 WIB', 'start' => '20261212T010000Z', 'end' => '20261212T030000Z', 'icon' => 'gem'],
                        ['name' => 'Resepsi', 'time' => '11.00 – 14.00 WIB', 'start' => '20261212T040000Z', 'end' => '20261212T070000Z', 'icon' => 'wine'],
                    ],
                    'streamingUrl' => '',
                    'accounts' => [
                        ['bank' => 'BCA', 'number' => '0000000000', 'holder' => 'Levi Anindya Putri'],
                        ['bank' => 'mandiri', 'number' => '0000000000000', 'holder' => 'Dio Ahmad Pratama'],
                    ],
                    'giftAddress' => 'Levi & Dio — Jl. Melati No. 12, Surabaya, Jawa Timur 60261',
                    'demo' => true,
                    'families' => ['Keluarga Bapak Aditya Pratama & Ibu Ratna Sari', 'Keluarga Bapak Ahmad Wijaya & Ibu Dewi Lestari'],
                    'vendors' => ['MAJAPAHIT · VENUE', 'ARUNA · PHOTOGRAPHY', 'KALA · WEDDING ORGANIZER'],
                ],
            ]
        );

        if ($invitation->wishes()->count() === 0) {
            $invitation->wishes()->createMany([
                ['guest_name' => 'Nadia & Rizky', 'attendance' => 'yes', 'guest_count' => 2, 'message' => 'Barakallahu lakuma! Semoga menjadi keluarga yang penuh cinta, keberkahan, dan kebahagiaan.', 'status' => 'approved', 'moderation_reason' => 'seed:sample'],
                ['guest_name' => 'Putri Amelia', 'attendance' => 'yes', 'guest_count' => 1, 'message' => 'Akhirnya sampai di bab paling indah. Bahagia selalu untuk kalian berdua.', 'status' => 'approved', 'moderation_reason' => 'seed:sample'],
                ['guest_name' => 'Andi Saputra', 'attendance' => 'no', 'guest_count' => 0, 'message' => 'Selamat menempuh hidup baru! Meski belum bisa hadir, doa terbaik selalu menyertai.', 'status' => 'approved', 'moderation_reason' => 'seed:sample'],
            ]);
        }
    }
}

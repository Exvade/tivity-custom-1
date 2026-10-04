<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_the_default_invitation(): void
    {
        $this->get('/')->assertRedirect('/alya-dan-salman');
    }

    public function test_public_invitation_only_displays_approved_wishes(): void
    {
        $invitation = $this->invitation();
        $invitation->wishes()->createMany([
            ['guest_name' => 'Ucapan Aman', 'attendance' => 'yes', 'guest_count' => 2, 'message' => 'Selamat berbahagia', 'status' => 'approved'],
            ['guest_name' => 'Ucapan Pending', 'attendance' => 'yes', 'guest_count' => 1, 'message' => 'Belum boleh terlihat', 'status' => 'pending'],
        ]);

        $response = $this->get('/'.$invitation->slug);

        $response->assertOk()->assertSee('Ucapan Aman')->assertDontSee('Ucapan Pending');
    }

    public function test_manual_moderation_stores_a_new_wish_as_pending(): void
    {
        $invitation = $this->invitation(['moderation_mode' => 'manual']);

        $response = $this->postJson(route('wishes.store', $invitation), [
            'name' => 'Bapak Budi',
            'attendance' => 'yes',
            'guests' => 2,
            'message' => 'Semoga menjadi keluarga yang bahagia.',
        ]);

        $response->assertCreated()->assertJsonPath('status', 'pending');
        $this->assertDatabaseHas('wishes', ['guest_name' => 'Bapak Budi', 'status' => 'pending']);
    }

    public function test_hybrid_moderation_approves_clean_text_and_holds_flagged_text(): void
    {
        $invitation = $this->invitation(['moderation_mode' => 'hybrid']);

        $this->postJson(route('wishes.store', $invitation), [
            'name' => 'Tamu Baik', 'attendance' => 'yes', 'guests' => 1,
            'message' => 'Selamat menempuh hidup baru.',
        ])->assertCreated()->assertJsonPath('status', 'approved');

        $this->postJson(route('wishes.store', $invitation), [
            'name' => 'Tamu Bermasalah', 'attendance' => 'no', 'guests' => 0,
            'message' => 'Pesan goblok yang harus ditahan.',
        ])->assertCreated()->assertJsonPath('status', 'pending');

        $this->assertDatabaseHas('wishes', ['guest_name' => 'Tamu Bermasalah', 'status' => 'pending']);
    }

    public function test_customer_can_approve_their_own_pending_wish(): void
    {
        $owner = User::factory()->create();
        $invitation = $this->invitation(['user_id' => $owner->id]);
        $wish = $invitation->wishes()->create([
            'guest_name' => 'Tamu', 'attendance' => 'yes', 'guest_count' => 1,
            'message' => 'Mohon disetujui', 'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->patch(route('dashboard.wishes.update', [$invitation, $wish]), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertDatabaseHas('wishes', ['id' => $wish->id, 'status' => 'approved', 'moderated_by' => $owner->id]);
    }

    public function test_customer_cannot_moderate_another_customers_invitation(): void
    {
        $invitation = $this->invitation();
        $wish = $invitation->wishes()->create([
            'guest_name' => 'Tamu', 'attendance' => 'yes', 'guest_count' => 1,
            'message' => 'Pesan', 'status' => 'pending',
        ]);

        $this->actingAs(User::factory()->create())
            ->patch(route('dashboard.wishes.update', [$invitation, $wish]), ['status' => 'approved'])
            ->assertForbidden();
    }

    public function test_customer_can_generate_unique_guest_links(): void
    {
        $owner = User::factory()->create();
        $invitation = $this->invitation(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->post(route('dashboard.guests.store', $invitation), [
                'names' => "Bapak Budi & Keluarga\nIbu Rani\nBapak Budi & Keluarga",
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('guests', 2);
        $this->assertDatabaseHas('guests', ['invitation_id' => $invitation->id, 'name' => 'Ibu Rani']);
    }

    public function test_guest_token_prevents_name_spoofing(): void
    {
        $invitation = $this->invitation();
        $guest = $invitation->guests()->create([
            'name' => 'Nama Dari Token',
            'token' => str_repeat('a', 64),
            'max_submissions' => 3,
        ]);

        $this->postJson(route('wishes.store', $invitation), [
            'name' => 'Nama Palsu',
            'attendance' => 'yes',
            'guests' => 1,
            'message' => 'Selamat berbahagia.',
            'guest_token' => $guest->token,
        ])->assertCreated();

        $this->assertDatabaseHas('wishes', ['guest_name' => 'Nama Dari Token']);
        $this->assertDatabaseMissing('wishes', ['guest_name' => 'Nama Palsu']);
    }

    private function invitation(array $attributes = []): Invitation
    {
        return Invitation::create(array_merge([
            'user_id' => User::factory()->create()->id,
            'slug' => 'alya-dan-salman',
            'title' => 'Alya & Salman',
            'content' => $this->content(),
            'moderation_mode' => 'manual',
            'is_published' => true,
        ], $attributes));
    }

    private function content(): array
    {
        return [
            'bride' => ['name' => 'Alya', 'fullName' => 'Alya Putri', 'parents' => 'Bapak & Ibu Alya', 'order' => 'Putri dari'],
            'groom' => ['name' => 'Salman', 'fullName' => 'Salman Putra', 'parents' => 'Bapak & Ibu Salman', 'order' => 'Putra dari'],
            'date' => '2026-12-12T08:00:00+07:00', 'dateLabel' => '12 Desember 2026',
            'venue' => 'Gedung', 'address' => 'Surabaya', 'city' => 'Surabaya', 'mapUrl' => '#',
            'events' => [
                ['name' => 'Akad', 'time' => '08.00 WIB', 'start' => '20261212T010000Z', 'end' => '20261212T020000Z', 'icon' => 'gem'],
                ['name' => 'Resepsi', 'time' => '11.00 WIB', 'start' => '20261212T040000Z', 'end' => '20261212T060000Z', 'icon' => 'wine'],
            ],
            'streamingUrl' => '', 'accounts' => [], 'giftAddress' => 'Alamat', 'demo' => false,
            'families' => [], 'vendors' => [],
        ];
    }
}

<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Registration;
use App\Models\ScannerLink;
use App\Models\User;
use App\Notifications\RegistrationConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EventRolesAndTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendees_cannot_access_admin_or_organizer_dashboards(): void
    {
        $attendee = User::factory()->create(['role' => 'attendee']);

        $this->actingAs($attendee)->get('/admin')->assertForbidden();
        $this->actingAs($attendee)->get('/organizer')->assertForbidden();
        $this->actingAs($attendee)->get('/dashboard')->assertOk();
    }

    public function test_organizers_can_only_manage_and_view_registrations_for_their_events(): void
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $otherOrganizer = User::factory()->create(['role' => 'organizer']);
        $attendee = User::factory()->create(['role' => 'attendee']);
        $ownedEvent = $this->createEvent($organizer);
        $otherEvent = $this->createEvent($otherOrganizer, 'Other event');
        $registration = Registration::create([
            'event_id' => $otherEvent->id,
            'user_id' => $attendee->id,
            'barcode' => 'EVT-OTHER123456',
            'status' => 'registered',
        ]);

        $this->actingAs($organizer)
            ->get(route('organizer.dashboard'))
            ->assertOk()
            ->assertSee($ownedEvent->title)
            ->assertDontSee($otherEvent->title);

        $this->actingAs($organizer)
            ->get(route('dashboard'))
            ->assertRedirect(route('organizer.dashboard'));

        $this->actingAs($organizer)->get(route('admin.dashboard'))->assertForbidden();

        $this->actingAs($organizer)
            ->get(route('organizer.events.edit', $ownedEvent))
            ->assertOk()
            ->assertSee('method="POST"', false)
            ->assertSee('name="_method" value="PUT"', false);

        $this->actingAs($organizer)
            ->get(route('organizer.events.edit', $otherEvent))
            ->assertForbidden();

        $this->actingAs($organizer)
            ->put(route('organizer.events.update', $otherEvent), $this->eventData())
            ->assertForbidden();

        $this->actingAs($organizer)
            ->get(route('registrations.show', $registration))
            ->assertForbidden();

        $this->actingAs($organizer)
            ->put(route('organizer.events.update', $ownedEvent), $this->eventData(['title' => 'Updated event']))
            ->assertRedirect(route('organizer.dashboard'));

        $this->assertDatabaseHas('events', [
            'id' => $ownedEvent->id,
            'organizer_id' => $organizer->id,
            'title' => 'Updated event',
        ]);
    }

    public function test_scanner_link_requires_the_assigned_organizer_to_be_signed_in(): void
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $otherOrganizer = User::factory()->create(['role' => 'organizer']);
        $attendee = User::factory()->create(['role' => 'attendee']);
        $event = $this->createEvent($organizer);
        $registration = Registration::create([
            'event_id' => $event->id,
            'user_id' => $attendee->id,
            'barcode' => 'EVT-SCAN1234567',
            'status' => 'registered',
        ]);
        $scannerLink = ScannerLink::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'hash' => 'unique-test-scanner-hash',
            'is_active' => true,
        ]);

        $this->get(route('scanner.scan', $scannerLink->hash))->assertRedirect(route('login'));
        $this->actingAs($attendee)->get(route('scanner.scan', $scannerLink->hash))->assertForbidden();
        $this->actingAs($otherOrganizer)->get(route('scanner.scan', $scannerLink->hash))->assertForbidden();
        $this->actingAs($organizer)->get(route('scanner.scan', $scannerLink->hash))->assertOk();
        $this->actingAs($organizer)
            ->postJson(route('scanner.scan.post', $scannerLink->hash), ['barcode' => $registration->barcode])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($organizer)
            ->postJson(route('scanner.scan.post', $scannerLink->hash), ['barcode' => $registration->barcode])
            ->assertStatus(409)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => 'checked_in',
        ]);
    }

    public function test_registration_confirmation_includes_a_scannable_qr_image(): void
    {
        Notification::fake();

        $attendee = User::factory()->create(['role' => 'attendee']);
        $event = $this->createEvent(User::factory()->create(['role' => 'organizer']));

        $this->actingAs($attendee)
            ->post(route('registrations.store', $event))
            ->assertRedirect();

        $registration = Registration::firstOrFail();
        $this->get(route('registrations.show', $registration))
            ->assertOk()
            ->assertSee('data:image/png;base64,', false);

        Notification::assertSentTo(
            $attendee,
            RegistrationConfirmation::class,
            function (RegistrationConfirmation $notification) use ($attendee): bool {
                $mail = $notification->toMail($attendee);
                $attachment = $mail->rawAttachments[0] ?? null;

                return $attachment !== null
                    && $attachment['options']['mime'] === 'image/png'
                    && str_starts_with($attachment['data'], "\x89PNG\r\n\x1a\n");
            }
        );
    }

    public function test_admin_can_assign_an_event_to_an_organizer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organizer = User::factory()->create(['role' => 'organizer']);
        $attendee = User::factory()->create(['role' => 'attendee']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee($organizer->name)
            ->assertSee('Make Organizer');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($admin)
            ->post(route('admin.users.make-organizer', $attendee))
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $attendee->id,
            'role' => 'organizer',
        ]);

        $this->actingAs($admin)
            ->post(route('events.store'), $this->eventData([
                'organizer_id' => $organizer->id,
                'status' => 'draft',
            ]))
            ->assertRedirect(route('events.index'));

        $this->assertDatabaseHas('events', [
            'title' => 'Test event',
            'organizer_id' => $organizer->id,
        ]);
    }

    public function test_admin_scanner_links_are_bound_to_the_assigned_organizer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organizer = User::factory()->create(['role' => 'organizer']);
        $otherOrganizer = User::factory()->create(['role' => 'organizer']);
        $event = $this->createEvent($organizer);

        $this->actingAs($admin)
            ->from(route('admin.event.scanners', $event))
            ->post(route('admin.scanners.generate', $event), [
                'organizer_id' => $otherOrganizer->id,
                'expires_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('admin.event.scanners', $event));

        $scannerLink = ScannerLink::firstOrFail();
        $this->assertSame($organizer->id, $scannerLink->organizer_id);

        $this->actingAs($organizer)->get($scannerLink->getScannerUrl())->assertOk();
        $this->actingAs($otherOrganizer)->get($scannerLink->getScannerUrl())->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.users.remove-organizer', $organizer))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $organizer->id,
            'role' => 'organizer',
        ]);
    }

    private function createEvent(User $organizer, string $title = 'Test event'): Event
    {
        return Event::create($this->eventData([
            'title' => $title,
            'organizer_id' => $organizer->id,
        ]));
    }

    private function eventData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Test event',
            'description' => 'Event details',
            'start_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(10)->addHours(4)->format('Y-m-d H:i:s'),
            'location' => 'Jakarta',
            'city' => 'Jakarta',
            'category' => 'music',
            'status' => 'published',
            'max_attendees' => 100,
            'is_featured' => false,
        ], $overrides);
    }
}

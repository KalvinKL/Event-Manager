<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $organizerEmail = config('app.demo_organizer_email');
        $organizer = User::where('email', $organizerEmail)->first();

        if (! $organizer) {
            $organizerPassword = config('app.demo_organizer_password');

            if (! $organizerPassword && $organizerEmail !== 'organizer@example.com') {
                throw new \RuntimeException('Set DEMO_ORGANIZER_PASSWORD to create the configured organizer account.');
            }

            $organizer = User::factory()->create([
                'name' => 'Organizer User',
                'email' => $organizerEmail,
                'password' => Hash::make($organizerPassword ?: 'password'),
                'role' => 'organizer',
            ]);
        }

        if (! $organizer->isOrganizer()) {
            throw new \RuntimeException('The configured demo event owner must have the organizer role.');
        }

        User::firstOrCreate([
            'email' => 'attendee@example.com',
        ], [
            'name' => 'Test Attendee',
            'password' => Hash::make('password'),
            'role' => 'attendee',
        ]);

        $this->call(FestivalSeeder::class);
    }
}

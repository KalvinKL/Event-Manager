<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FestivalSeeder extends Seeder
{
    public function run(): void
    {
        $festivals = [
            [
                'title' => 'Jakarta International Java Jazz Festival 2027',
                'description' => "Indonesia's biggest jazz festival returns for its 19th edition with world-class artists across multiple stages. Experience 3 days of incredible jazz, fusion, soul, and contemporary music featuring both international legends and rising local talents.\n\nHighlights:\n- 50+ performances across 10 stages\n- International jazz legends and emerging artists\n- Workshops and masterclasses with musicians\n- Food village with diverse culinary options\n- Jazz market with vinyl, merch, and instruments",
                'start_date' => Carbon::parse('2027-03-07 18:00'),
                'end_date' => Carbon::parse('2027-03-09 23:59'),
                'location' => 'JIExpo Kemayoran',
                'city' => 'Jakarta',
                'category' => 'music',
                'status' => 'published',
                'max_attendees' => 50000,
                'is_featured' => true,
            ],
            [
                'title' => 'Djakarta Warehouse Project 2027',
                'description' => "Asia's largest electronic dance music festival returns with the world's top DJs, mind-blowing production, and unforgettable moments across multiple stages. DWP 2027 brings you the biggest names in EDM, techno, house, and bass music.\n\nWhat to expect:\n- 100+ DJs across 6 stages\n- State-of-the-art sound and lighting\n- Immersive art installations\n- VIP areas with premium amenities\n- After-parties at top Jakarta venues",
                'start_date' => Carbon::parse('2027-12-12 16:00'),
                'end_date' => Carbon::parse('2027-12-13 06:00'),
                'location' => 'JIExpo Kemayoran',
                'city' => 'Jakarta',
                'category' => 'electronic',
                'status' => 'published',
                'max_attendees' => 80000,
                'is_featured' => true,
            ],
            [
                'title' => 'Bali Beach Festival 2027',
                'description' => "Sun, sand, and beats at Bali's premier beach festival. International and local artists, sunset parties, and the ultimate tropical festival experience. Dance barefoot on the sand as the sun sets over the Indian Ocean.\n\nFeatures:\n- Beachfront main stage with ocean views\n- Sunset chill sessions\n- Pool parties at partner resorts\n- Wellness area with yoga at sunrise\n- Night market with local crafts",
                'start_date' => Carbon::parse('2027-07-15 14:00'),
                'end_date' => Carbon::parse('2027-07-17 23:59'),
                'location' => 'Kuta Beach',
                'city' => 'Bali',
                'category' => 'electronic',
                'status' => 'published',
                'max_attendees' => 25000,
                'is_featured' => true,
            ],
            [
                'title' => 'Rock in Java 2027',
                'description' => "Indonesia's biggest rock festival returns with legendary rock acts and the best emerging bands. Three days of pure rock energy - from classic rock to modern metal, alternative to indie.\n\nLineup includes:\n- International headliners\n- Best Indonesian rock bands\n- Emerging stage for new talent\n- Rock market with merch and vinyl\n- Meet & greet sessions",
                'start_date' => Carbon::parse('2027-09-20 15:00'),
                'end_date' => Carbon::parse('2027-09-22 23:59'),
                'location' => 'Gelora Bung Karno Sports Complex',
                'city' => 'Jakarta',
                'category' => 'rock',
                'status' => 'published',
                'max_attendees' => 40000,
                'is_featured' => false,
            ],
            [
                'title' => 'Ubud Village Jazz Festival 2027',
                'description' => "An intimate jazz experience in the cultural heart of Bali. Set among rice terraces and traditional villages, this festival combines world-class jazz with Balinese culture and spirituality.\n\nUnique features:\n- Outdoor stages in rice field settings\n- Gamelan-jazz fusion performances\n- Cultural workshops (dance, offering making)\n- Organic farm-to-table dining\n- Meditation and sound healing sessions",
                'start_date' => Carbon::parse('2027-08-10 16:00'),
                'end_date' => Carbon::parse('2027-08-12 22:00'),
                'location' => 'Ubud Palace Grounds',
                'city' => 'Bali',
                'category' => 'music',
                'status' => 'published',
                'max_attendees' => 5000,
                'is_featured' => false,
            ],
            [
                'title' => 'Jakarta Food Festival 2027',
                'description' => "The ultimate culinary celebration in the capital. From street food legends to Michelin-starred chefs, experience Indonesia's diverse flavors in one place.\n\nFood zones:\n- Street Food Alley (warung favorites)\n- Fine Dining Pavilion\n- Coffee & Tea Garden\n- Dessert Wonderland\n- Cooking demos by celebrity chefs\n- Food photography workshops",
                'start_date' => Carbon::parse('2027-05-10 11:00'),
                'end_date' => Carbon::parse('2027-05-12 22:00'),
                'location' => 'Senayan Park',
                'city' => 'Jakarta',
                'category' => 'food',
                'status' => 'published',
                'max_attendees' => 30000,
                'is_featured' => false,
            ],
            [
                'title' => 'Bali Spirit Festival 2027',
                'description' => "A transformative celebration of yoga, dance, and music in the heart of Bali. Join thousands of seekers from around the world for 7 days of workshops, ceremonies, and community.\n\nProgram includes:\n- 200+ yoga classes (all styles)\n- Ecstatic dance & movement\n- Live music & kirtan\n- Healing & wellness workshops\n- Sacred ceremonies\n- Kids & family program",
                'start_date' => Carbon::parse('2027-04-15 08:00'),
                'end_date' => Carbon::parse('2027-04-21 20:00'),
                'location' => 'The Yoga Barn & Ubud Area',
                'city' => 'Bali',
                'category' => 'wellness',
                'status' => 'published',
                'max_attendees' => 3000,
                'is_featured' => false,
            ],
            [
                'title' => 'Solo International Batik Festival 2027',
                'description' => "Celebrate Indonesia's UNESCO heritage with the world's largest batik festival. Fashion shows, exhibitions, workshops, and cultural performances in the city of batik.\n\nHighlights:\n- Batik fashion shows by top designers\n- 500+ artisan booths\n- Natural dyeing workshops\n- Batik museum exhibitions\n- Royal palace cultural tours\n- International symposium on batik",
                'start_date' => Carbon::parse('2027-10-15 10:00'),
                'end_date' => Carbon::parse('2027-10-19 21:00'),
                'location' => 'Solo City Hall & Kampung Batik',
                'city' => 'Solo',
                'category' => 'cultural',
                'status' => 'published',
                'max_attendees' => 20000,
                'is_featured' => false,
            ],
            [
                'title' => 'Jakarta Comedy Festival 2027',
                'description' => "Indonesia's biggest comedy festival featuring stand-up, improv, sketch comedy, and comedic theater. Laugh with the best local and international comedians.\n\nShows include:\n- Headliner stand-up specials\n- Improv comedy battles\n- Open mic nights\n- Comedy workshops\n- Podcast recordings live\n- Late-night comedy lounge",
                'start_date' => Carbon::parse('2027-06-20 19:00'),
                'end_date' => Carbon::parse('2027-06-22 23:59'),
                'location' => 'Taman Ismail Marzuki',
                'city' => 'Jakarta',
                'category' => 'comedy',
                'status' => 'published',
                'max_attendees' => 5000,
                'is_featured' => false,
            ],
            [
                'title' => 'Bandung Creative Festival 2027',
                'description' => "A multidisciplinary arts festival celebrating creativity in the Paris of Java. Visual arts, design, music, fashion, and technology converge in Bandung's vibrant creative scene.\n\nPrograms:\n- Art installations across the city\n- Design talks & workshops\n- Indie music showcases\n- Fashion shows by local designers\n- Creative market & bazaar\n- Digital art & VR experiences",
                'start_date' => Carbon::parse('2027-11-08 10:00'),
                'end_date' => Carbon::parse('2027-11-10 22:00'),
                'location' => 'Gedung Sate & Bandung Creative Hub',
                'city' => 'Bandung',
                'category' => 'cultural',
                'status' => 'published',
                'max_attendees' => 15000,
                'is_featured' => false,
            ],
        ];

        $organizer = User::where('email', config('app.demo_organizer_email'))->first();

        if (! $organizer || ! $organizer->isOrganizer()) {
            throw new \RuntimeException('The configured demo event owner must exist and have the organizer role.');
        }

        foreach ($festivals as $festival) {
            $festival['organizer_id'] = $organizer->id;
            $legacyTitle = str_replace(' 2027', ' 2025', $festival['title']);
            $event = Event::whereIn('title', [$festival['title'], $legacyTitle])->first();

            if ($event) {
                $event->update($festival);
            } else {
                Event::create($festival);
            }
        }

        $this->command->info('Prepared '.count($festivals).' festival events.');
    }
}

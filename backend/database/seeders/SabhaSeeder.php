<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SabhaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Admin User
         \App\Models\User::firstOrCreate(
             ['email' => 'admin@sabha.com'],
             [
                 'name' => 'Admin User',
                 'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                 'role' => 'admin'
             ]
         );

         // Regular User
         \App\Models\User::firstOrCreate(
             ['email' => 'john@example.com'],
             [
                 'name' => 'John Doe',
                 'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                 'role' => 'user'
             ]
         );

         // Businesses
         \App\Models\Business::firstOrCreate(
             ['name' => 'TechWave Solutions'],
             [
                 'category' => 'Software Development',
                 'description' => 'Leading innovation in cloud architecture and enterprise software.',
                 'website' => 'https://techwave.io',
                 'status' => 'approved',
                 'is_verified' => true
             ]
         );
         \App\Models\Business::firstOrCreate(
             ['name' => 'DesignFlow'],
             [
                 'category' => 'Creative Agency',
                 'description' => 'Premium UI/UX design and brand strategy for modern startups.',
                 'website' => 'https://designflow.com',
                 'status' => 'approved',
                 'is_verified' => true
             ]
         );
         \App\Models\Business::firstOrCreate(
             ['name' => 'ScaleUp Capital'],
             [
                 'category' => 'Venture Capital',
                 'description' => 'Empowering entrepreneurs with seed funding and strategic growth.',
                 'website' => 'https://scaleup.vc',
                 'status' => 'pending',
                 'is_verified' => false
             ]
         );

         // Events
         \App\Models\Event::firstOrCreate(
             ['title' => 'Sabha Networking Mixer'],
             [
                 'description' => 'Connect with 50+ industry leaders in an informal rooftop setting.',
                 'date' => now()->addDays(7),
                 'location' => 'Grand Hyatt, Ahmedabad',
                 'type' => 'Mixer',
                 'image' => 'https://images.unsplash.com/photo-1540575861501-7ad0582373f3',
                 'price_normal' => '₹2,499',
                 'price_verified' => '₹1,499'
             ]
         );
         \App\Models\Event::firstOrCreate(
             ['title' => 'Scaling 101 Workshop'],
             [
                 'description' => 'A deep dive into operational efficiency and scaling your team.',
                 'date' => now()->addDays(14),
                 'location' => 'Business Park, Mumbai',
                 'type' => 'Workshop',
                 'image' => 'https://images.unsplash.com/photo-1511578314322-379afb476865',
                 'price_normal' => '₹999',
                 'price_verified' => 'Free'
             ]
         );
         \App\Models\Event::firstOrCreate(
             ['title' => 'Tech Founders Summit'],
             [
                 'description' => 'The ultimate gathering of Indian tech founders discussing the future.',
                 'date' => now()->addDays(30),
                 'location' => 'JW Marriott, Bangalore',
                 'type' => 'Summit',
                 'image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678',
                 'price_normal' => '₹5,499',
                 'price_verified' => '₹3,499'
             ]
         );

         // Stats
         \App\Models\Statistic::updateOrCreate(
             ['label' => 'Active Professionals'],
             ['value' => '500+']
         );
         \App\Models\Statistic::updateOrCreate(
             ['label' => 'Strategic Events'],
             ['value' => '120+']
         );
         \App\Models\Statistic::updateOrCreate(
             ['label' => 'Success Stories'],
             ['value' => '2500+']
         );
         \App\Models\Statistic::updateOrCreate(
             ['label' => 'Business Exchanged'],
             ['value' => '₹10Cr+']
         );
         \App\Models\Statistic::updateOrCreate(
             ['label' => 'Monthly Mixers'],
             ['value' => '50+']
         );
         \App\Models\Statistic::updateOrCreate(
             ['label' => 'Cities Covered'],
             ['value' => '12+']
         );

         // Settings
         \App\Models\Setting::updateOrCreate(
             ['key' => 'contact_email'],
             ['value' => 'hello@sabha.global']
         );
         \App\Models\Setting::updateOrCreate(
             ['key' => 'response_time'],
             ['value' => 'Within 1 Business Day']
         );
         \App\Models\Setting::updateOrCreate(
             ['key' => 'coordinators'],
             ['value' => json_encode([
                 [
                     'city' => 'Mumbai Coordinator',
                     'contact' => 'Ravi Sharma',
                     'phone' => '+91 98200 12345',
                     'email' => 'mumbai@sabha.global'
                 ],
                 [
                     'city' => 'Pune Coordinator',
                     'contact' => 'Pooja Verma',
                     'phone' => '+91 96110 54321',
                     'email' => 'pune@sabha.global'
                 ],
                 [
                     'city' => 'Ahmedabad Coordinator',
                     'contact' => 'Dev Patel',
                     'phone' => '+91 94260 98765',
                     'email' => 'ahmedabad@sabha.global'
                 ]
             ])]
         );

         \App\Models\Setting::updateOrCreate(
             ['key' => 'trustees'],
             ['value' => json_encode([
                 [
                     'name' => 'Ravi Sharma',
                     'role' => 'President & Trustee',
                     'company' => 'Founder, Vertex Solutions',
                     'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150&auto=format&fit=crop'
                 ],
                 [
                     'name' => 'Pooja Verma',
                     'role' => 'Vice President',
                     'company' => 'Chief Architect & CEO, Prime Builders',
                     'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=150&auto=format&fit=crop'
                 ],
                 [
                     'name' => 'Amit Shah',
                     'role' => 'Treasurer & Growth Lead',
                     'company' => 'Director of Operations, Global Logistics',
                     'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=150&auto=format&fit=crop'
                 ],
                 [
                     'name' => 'Sara Khan',
                     'role' => 'General Secretary',
                     'company' => 'Senior Partner, Summit Consulting',
                     'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=150&auto=format&fit=crop'
                 ]
             ])]
         );

         // Seed gallery images and videos
         $evt1 = \App\Models\Event::where('title', 'Sabha Networking Mixer')->first();
         $evt2 = \App\Models\Event::where('title', 'Scaling 101 Workshop')->first();

         if ($evt1) {
             \App\Models\GalleryImage::firstOrCreate(
                 ['image_path' => 'https://images.unsplash.com/photo-1540575861501-7ad0582373f3?auto=format&fit=crop&q=80&w=1200'],
                 ['event_id' => $evt1->id, 'caption' => 'Connecting local businesses at our rooftop mixer.']
             );
             \App\Models\GalleryImage::firstOrCreate(
                 ['image_path' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&q=80&w=1200'],
                 ['event_id' => $evt1->id, 'caption' => 'Dynamic team discussions and strategic alliance planning.']
             );
         }

         if ($evt2) {
             \App\Models\GalleryImage::firstOrCreate(
                 ['image_path' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=1200'],
                 ['event_id' => $evt2->id, 'caption' => 'Scaling 101 hands-on workshop breakout session.']
             );
         }

         // Seed common images
         \App\Models\GalleryImage::firstOrCreate(
             ['image_path' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&q=80&w=1200'],
             ['event_id' => null, 'caption' => 'Founders roundtable dinner discussion.']
         );

         // Seed a sample video (using mixkit stock video URL)
         \App\Models\GalleryImage::firstOrCreate(
             ['image_path' => 'https://assets.mixkit.co/videos/preview/mixkit-business-people-meeting-in-a-modern-office-41585-large.mp4'],
             ['event_id' => null, 'caption' => 'Sabha strategic leadership and innovation video briefing.']
         );

         // Seed hero images
         \App\Models\HeroImage::firstOrCreate(
             ['image_path' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=2000&auto=format&fit=crop'],
             ['title' => 'SABHA Business Network', 'caption' => 'A trusted community of Sathwara entrepreneurs.']
         );
         \App\Models\HeroImage::firstOrCreate(
             ['image_path' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=2000&auto=format&fit=crop'],
             ['title' => 'Sabha Networking Mixer', 'caption' => 'Rooftop mixers with 50+ industry leaders.']
         );
         \App\Models\HeroImage::firstOrCreate(
             ['image_path' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?q=80&w=2000&auto=format&fit=crop'],
             ['title' => 'Summit Discussions', 'caption' => 'Roundtable sessions with top founders.']
         );
     }
}


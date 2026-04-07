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
         \App\Models\User::create([
             'name' => 'Admin User',
             'email' => 'admin@sabha.com',
             'password' => \Illuminate\Support\Facades\Hash::make('password123'),
             'role' => 'admin'
         ]);

         // Regular User
         \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'user'
        ]);

         // Businesses
         \App\Models\Business::create([
            'name' => 'TechWave Solutions',
            'category' => 'Software Development',
            'description' => 'Leading innovation in cloud architecture and enterprise software.',
            'website' => 'https://techwave.io',
            'status' => 'approved',
            'is_verified' => true
        ]);
        \App\Models\Business::create([
            'name' => 'DesignFlow',
            'category' => 'Creative Agency',
            'description' => 'Premium UI/UX design and brand strategy for modern startups.',
            'website' => 'https://designflow.com',
            'status' => 'approved',
            'is_verified' => true
        ]);
        \App\Models\Business::create([
            'name' => 'ScaleUp Capital',
            'category' => 'Venture Capital',
            'description' => 'Empowering entrepreneurs with seed funding and strategic growth.',
            'website' => 'https://scaleup.vc',
            'status' => 'pending',
            'is_verified' => false
        ]);

        // Events
        \App\Models\Event::create([
            'title' => 'Sabha Networking Mixer',
            'description' => 'Connect with 50+ industry leaders in an informal rooftop setting.',
            'date' => now()->addDays(7),
            'location' => 'Grand Hyatt, Ahmedabad',
            'type' => 'Mixer',
            'image' => 'https://images.unsplash.com/photo-1540575861501-7ad0582373f3'
        ]);
        \App\Models\Event::create([
            'title' => 'Scaling 101 Workshop',
            'description' => 'A deep dive into operational efficiency and scaling your team.',
            'date' => now()->addDays(14),
            'location' => 'Business Park, Mumbai',
            'type' => 'Workshop',
            'image' => 'https://images.unsplash.com/photo-1511578314322-379afb476865'
        ]);
        \App\Models\Event::create([
            'title' => 'Tech Founders Summit',
            'description' => 'The ultimate gathering of Indian tech founders discussing the future.',
            'date' => now()->addDays(30),
            'location' => 'JW Marriott, Bangalore',
            'type' => 'Summit',
            'image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678'
        ]);

        // Stats
        \App\Models\Statistic::create(['label' => 'Active Professionals', 'value' => '500+']);
        \App\Models\Statistic::create(['label' => 'Strategic Events', 'value' => '120+']);
        \App\Models\Statistic::create(['label' => 'Success Stories', 'value' => '2500+']);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MembershipFormSeeder extends Seeder
{
    /**
     * Seed users and businesses from membership form responses table.
     */
    public function run(): void
    {
        $responses = DB::table('membership_form_responses')->get();
        $hashedPassword = Hash::make('password123');

        foreach ($responses as $row) {
            $email = $row->email ?: "member_{$row->id}@sabha.com";
            $name = $row->full_name ?: "Member {$row->id}";
            $businessName = $row->business_name ?: "Business {$row->id}";

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'phone' => $row->mobile,
                    'city' => $row->city,
                    'designation' => $row->designation,
                    'company' => $businessName,
                    'bio' => $row->business_introduction,
                    'avatar' => $row->profile_picture_url,
                    'password' => $hashedPassword,
                    'role' => 'business_owner',
                ]
            );

            Business::updateOrCreate(
                ['name' => $businessName],
                [
                    'user_id' => $user->id,
                    'category' => $row->business_category ?: 'General',
                    'location' => $row->city ? $row->city . ', Gujarat' : 'Gujarat',
                    'description' => $row->business_introduction,
                    'logo' => $row->business_logo_url,
                    'phone' => $row->mobile,
                    'email' => $email,
                    'status' => 'approved',
                    'is_verified' => true,
                    'payment_screenshot' => $row->payment_attachment_url,
                ]
            );
        }
    }
}

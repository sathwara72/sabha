<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

$hashedPassword = Hash::make('SABHA@123');

// Truncate business_categories, and reset businesses relationships
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('businesses')->update(['business_category_id' => null]);
BusinessCategory::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$rows = DB::table('membership_form')->get();
$emailCounts = [];

function cleanPhone($phone) {
    if (empty($phone)) {
        return "";
    }
    $phoneStr = trim((string)$phone);
    if (str_ends_with($phoneStr, '.0')) {
        $phoneStr = substr($phoneStr, 0, -2);
    }
    return preg_replace('/[^0-9]/', '', $phoneStr);
}

function getStandardCategory($raw, $name = '', $bio = '') {
    $search = strtoupper(trim($raw . ' ' . $name . ' ' . $bio));
    $rawUpper = strtoupper(trim($string = (string)$raw));

    if (
        str_contains($rawUpper, 'IT') ||
        str_contains($rawUpper, 'SOFTWARE') ||
        str_contains($rawUpper, 'DIGITAL MARKETING') ||
        str_contains($rawUpper, 'CYBER')
    ) {
        return 'IT & Software';
    }

    if (
        str_contains($rawUpper, 'FINANCE') ||
        str_contains($rawUpper, 'FINANCIAL') ||
        str_contains($rawUpper, 'WEALTH') ||
        str_contains($rawUpper, 'INSURANCE') ||
        str_contains($rawUpper, 'TAX') ||
        str_contains($rawUpper, 'ACCOUNT') ||
        str_contains($rawUpper, 'AUDIT') ||
        str_contains($rawUpper, 'CHARTERED') ||
        str_contains($search, 'WEALTH') ||
        str_contains($search, 'INSURANCE')
    ) {
        return 'Finance & Accounting';
    }

    if (
        str_contains($rawUpper, 'REAL ESTATE') ||
        str_contains($rawUpper, 'DEVELOPER') ||
        str_contains($rawUpper, 'BUILDING CONTRACTOR') ||
        str_contains($rawUpper, 'CIVIL WORK') ||
        str_contains($rawUpper, 'INTERIOR DESIGNER')
    ) {
        return 'Construction & Real Estate';
    }

    if (
        str_contains($rawUpper, 'BUILDING MATERIAL') ||
        str_contains($rawUpper, 'CONSTRUCTION MATERIAL') ||
        str_contains($rawUpper, 'CONSTRUCTION CHEMICAL') ||
        str_contains($rawUpper, 'HARDWARE') ||
        str_contains($rawUpper, 'PAINTS') ||
        str_contains($rawUpper, 'GLASS') ||
        str_contains($rawUpper, 'MARBLE') ||
        str_contains($rawUpper, 'ELECTRICAL')
    ) {
        return 'Building Materials & Hardware';
    }

    if (
        str_contains($rawUpper, 'PHARMA') ||
        str_contains($rawUpper, 'HEALTH') ||
        str_contains($rawUpper, 'MEDICINE') ||
        str_contains($rawUpper, 'DENTAL') ||
        str_contains($rawUpper, 'HOSPITAL') ||
        str_contains($rawUpper, 'SONOGRAPHY') ||
        str_contains($rawUpper, 'PERSONAL CARE')
    ) {
        return 'Healthcare & Pharma';
    }

    if (
        str_contains($rawUpper, 'SOLAR') ||
        str_contains($rawUpper, 'RENEW') ||
        str_contains($search, 'SOLAR')
    ) {
        return 'Renewable Energy';
    }

    if (
        str_contains($rawUpper, 'MANUFACTURING') ||
        str_contains($rawUpper, 'MENUFECTURING') ||
        str_contains($rawUpper, 'ENGINEERING') ||
        str_contains($rawUpper, 'PACKAGING') ||
        str_contains($rawUpper, 'AIR MOTOR') ||
        str_contains($rawUpper, 'TOOLS') ||
        str_contains($rawUpper, 'EQUIPMENT') ||
        str_contains($rawUpper, 'LIFT') ||
        str_contains($rawUpper, 'UTENSILES') ||
        str_contains($rawUpper, 'AEROSPACE')
    ) {
        return 'Manufacturing & Engineering';
    }

    if (
        str_contains($rawUpper, 'AUTOMOBILE') ||
        str_contains($rawUpper, 'CAR') ||
        str_contains($rawUpper, 'AIR CONDITION') ||
        str_contains($rawUpper, 'AC SERVICE')
    ) {
        return 'Automobile & AC Services';
    }

    if (
        str_contains($rawUpper, 'TRAVEL') ||
        str_contains($rawUpper, 'EVENT')
    ) {
        return 'Travel & Events';
    }

    if (
        str_contains($rawUpper, 'GARMENT') ||
        str_contains($rawUpper, 'CLOTH') ||
        str_contains($rawUpper, 'KITCHEN, TOYS') ||
        str_contains($rawUpper, 'WHOLESALE') ||
        str_contains($rawUpper, 'RETAIL')
    ) {
        return 'Garment, Retail & Trade';
    }

    if (str_contains($rawUpper, 'PRINT')) {
        return 'Printing & Packaging';
    }

    if (
        str_contains($rawUpper, 'IMPORT EXPORT') ||
        str_contains($rawUpper, 'LOGISTICS') ||
        str_contains($rawUpper, 'SUPPLY CHAIN')
    ) {
        return 'Import, Export & Logistics';
    }

    if (str_contains($rawUpper, 'COMMUNITY') || str_contains($rawUpper, 'ENTREPRENEURS')) {
        return 'Community & Networking';
    }

    if (str_contains($rawUpper, 'LEGAL') || str_contains($rawUpper, 'ADVOCATE')) {
        return 'Legal & Advisory';
    }

    if (str_contains($rawUpper, 'PG') || str_contains($rawUpper, 'HOME')) {
        return 'Hospitality & Accommodation';
    }

    if (str_contains($rawUpper, 'CONSTRUCTION')) {
        return 'Construction & Real Estate';
    }

    return 'General Services';
}

$count = 0;
foreach ($rows as $idx => $row) {
    $fullName = trim($row->Full_name_with_surname ?? '');
    if (empty($fullName)) {
        continue;
    }

    $designation = trim($row->Designation_in_your_business ?? '');
    $businessName = trim($row->Business_Name ?? '');
    if (empty($businessName)) {
        $businessName = "Business of " . $fullName;
    }

    $rawCategory = trim($row->Business_Category_i_e_IT_Construction_Travel_Automobile_etc ?? 'General');
    $categoryName = getStandardCategory($rawCategory, $businessName, $row->Brief_Business_Introduction ?? '');
    $category = BusinessCategory::firstOrCreate(
        ['name' => $categoryName],
        ['sort_order' => 0, 'is_active' => true]
    );

    $mobile = cleanPhone($row->Mobile);
    $email = trim($row->Email ?? '');
    if (!empty($email)) {
        $email = strtolower($email);
    }

    if (empty($email) || !str_contains($email, '@')) {
        $slug = preg_replace('/[^a-z0-9]/', '', strtolower($fullName));
        $email = $slug . "_" . $idx . "@sabha.com";
    }

    if (isset($emailCounts[$email])) {
        $emailCounts[$email]++;
        $parts = explode('@', $email);
        $email = $parts[0] . "_" . $emailCounts[$email] . "@" . $parts[1];
    } else {
        $emailCounts[$email] = 0;
    }

    $city = trim($row->City ?? '');
    if (empty($city)) {
        $city = "Ahmedabad";
    }

    // Create or update user
    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $fullName,
            'phone' => $mobile,
            'city' => $city,
            'designation' => $designation,
            'company' => $businessName,
            'bio' => trim($row->Brief_Business_Introduction ?? ''),
            'avatar' => trim($row->Profile_picture ?? ''),
            'password' => $hashedPassword,
            'email_verified_at' => now(),
            'role' => 'business_owner',
        ]
    );

    // Create or update business
    Business::updateOrCreate(
        ['name' => $businessName],
        [
            'user_id' => $user->id,
            'business_category_id' => $category->id,
            'category' => $categoryName,
            'location' => $city ? $city . ', Gujarat' : 'Gujarat',
            'description' => trim($row->Brief_Business_Introduction ?? ''),
            'logo' => trim($row->Business_Logo ?? ''),
            'phone' => $mobile,
            'email' => $email,
            'status' => 'approved',
            'is_verified' => true,
            'payment_screenshot' => trim($row->Attachment_of_Membership_Payment_Rs_1000 ?? ''),
        ]
    );
    $count++;
}

echo "Successfully synchronized $count members to users, businesses, and categories tables.\n";

<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Seeder;

/**
 * Starter set of Gujarat cities and their well-known areas/localities.
 * Admin-manageable from here on via Admin > Locations — this seeder just
 * gives the list a real starting point instead of an empty table.
 */
class CityAreaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Ahmedabad' => [
                'Navrangpura', 'Vastrapur', 'Satellite', 'Bodakdev', 'Thaltej', 'Prahladnagar',
                'SG Highway', 'Maninagar', 'Naranpura', 'Paldi', 'Ellisbridge', 'CG Road',
                'Ashram Road', 'Vastral', 'Nikol', 'Naroda', 'Odhav', 'Bapunagar', 'Ghatlodia',
                'Chandkheda', 'Motera', 'Sabarmati', 'Ranip', 'New Ranip', 'Gota', 'South Bopal',
                'Bopal', 'Science City', 'Shilaj', 'Sarkhej', 'Vejalpur', 'Jodhpur', 'Isanpur',
                'Vatva', 'Amraiwadi', 'Rakhial', 'Kankaria', 'Khokhra', 'Kalupur', 'Shahibaug',
                'Memnagar', 'Usmanpura', 'Vasna', 'Juhapura', 'Nava Vadaj', 'Chandlodiya',
                'Jagatpur', 'Anandnagar', 'Nirnaynagar', 'Sardarnagar', 'Krishnanagar', 'Hansol',
                'Aslali', 'Vinzol',
            ],
            'Surat' => [
                'Adajan', 'Vesu', 'City Light', 'Athwa', 'Piplod', 'Ghod Dod Road', 'Varachha',
                'Katargam', 'Udhna', 'Pandesara', 'Rander', 'Dumas', 'Nanpura', 'Sarthana', 'Amroli',
            ],
            'Vadodara' => [
                'Alkapuri', 'Fatehgunj', 'Sayajigunj', 'Manjalpur', 'Gotri', 'Karelibaug',
                'Waghodia Road', 'Akota', 'Vasna Road', 'Old Padra Road', 'Nizampura', 'Subhanpura',
                'Harni', 'Tarsali',
            ],
            'Rajkot' => [
                'Kalawad Road', 'University Road', 'Raiya Road', 'Gondal Road', 'Bhaktinagar',
                'Mavdi', '150 Feet Ring Road', 'Yagnik Road',
            ],
            'Gandhinagar' => [
                'Sector 1', 'Sector 7', 'Sector 11', 'Sector 16', 'Sector 21', 'Sector 26',
                'Infocity', 'Kudasan', 'Raysan', 'Pethapur', 'Adalaj', 'Koba',
            ],
            'Bhavnagar' => ['Kaliyabid', 'Waghawadi Road', 'Ghogha Circle', 'Sardarnagar'],
            'Jamnagar' => ['Bedi Bandar Road', 'Patel Colony', 'Digjam Circle', 'Ranjit Sagar Road'],
            'Junagadh' => ['Girnar Taluka', 'Zanzarda Road', 'Moti Baug'],
            'Anand' => ['Vidyanagar', 'Karamsad', 'Grid', 'V V Nagar'],
            'Mehsana' => ['Radhanpur Road', 'Highway Road', 'Modhera Road'],
            'Morbi' => ['Ravapar Road', 'Sanala Road'],
            'Bharuch' => ['Zadeshwar', 'Link Road', 'Kasak'],
            'Nadiad' => ['College Road', 'Santram Road'],
            'Bhuj' => ['Station Road', 'Mundra Road'],
            'Navsari' => ['Lunsikui', 'Eru Char Rasta'],
            'Valsad' => ['Tithal Road', 'Halar'],
            'Porbandar' => ['Chowpatty', 'MG Road'],
            'Patan' => ['Sidhpur Road'],
            'Gandhidham' => ['Sector 1', 'Sector 4', 'Sector 8', 'Sector 12'],
            'Palanpur' => ['Ambaji Road'],
            'Himatnagar' => ['Modasa Road'],
            'Godhra' => ['Lunawada Road'],
            'Botad' => ['Bhavnagar Road'],
            'Surendranagar' => ['Wadhwan'],
            'Veraval' => ['Somnath Road'],
            'Vapi' => ['GIDC', 'Chala'],
            'Ankleshwar' => ['GIDC'],
            'Dahod' => ['Godhra Road'],
        ];

        $order = 0;
        foreach ($data as $cityName => $areaNames) {
            $city = City::updateOrCreate(
                ['name' => $cityName],
                ['sort_order' => $order++, 'is_active' => true]
            );

            foreach ($areaNames as $areaName) {
                Area::updateOrCreate(['city_id' => $city->id, 'name' => $areaName]);
            }
        }
    }
}

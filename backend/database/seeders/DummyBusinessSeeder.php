<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyBusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessesData = [
            [
                'user' => [
                    'name' => 'Rajesh Sathwara',
                    'email' => 'rajesh@apexwebsolutions.com',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'phone' => '+91 98765 43210',
                    'city' => 'Ahmedabad',
                    'designation' => 'Founder & CEO',
                    'company' => 'Apex Web Solutions',
                    'bio' => 'Rajesh is a seasoned software engineer and entrepreneur with 15+ years of experience building scalable software architectures for enterprise businesses.',
                    'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150&auto=format&fit=crop',
                ],
                'business' => [
                    'name' => 'Apex Web Solutions',
                    'logo' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=150&auto=format&fit=crop',
                    'cover_image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=800&auto=format&fit=crop',
                    'category' => 'Software Development',
                    'tagline' => 'Building the future of web and mobile software',
                    'location' => 'Ahmedabad, Gujarat',
                    'description' => "Apex Web Solutions is a premier software development firm specializing in cloud-native applications, enterprise software integrations, and cutting-edge mobile solutions. We work with global brands to digitize operations and scale infrastructure.\n\nOur team is committed to delivering clean code, responsive layouts, and robust security across all web platforms. From dynamic startups to established enterprises, we provide custom tech strategies tailored to your exact business needs.",
                    'website' => 'https://apexwebsolutions.com',
                    'phone' => '+91 98765 43210',
                    'email' => 'info@apexwebsolutions.com',
                    'linkedin' => 'https://linkedin.com/company/apex-web-solutions',
                    'instagram' => 'https://instagram.com/apexwebsolutions',
                    'youtube' => 'https://youtube.com/c/apexwebsolutions',
                    'twitter' => 'https://twitter.com/apexweb_sol',
                    'whatsapp' => 'https://wa.me/919876543210',
                    'hours' => '9:00 AM - 6:30 PM (Mon - Sat)',
                    'founded' => '2017',
                    'team_size' => '45+ Professionals',
                    'projects' => '150+ Projects Delivered',
                    'services' => [
                        'Custom Software Development',
                        'Cloud Architecture',
                        'Mobile App Development',
                        'UI/UX Design'
                    ],
                    'status' => 'approved',
                    'is_verified' => true,
                ]
            ],
            [
                'user' => [
                    'name' => 'Manish Sathwara',
                    'email' => 'manish@buildcraftinfra.com',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'phone' => '+91 99887 76655',
                    'city' => 'Vadodara',
                    'designation' => 'Managing Director',
                    'company' => 'BuildCraft Infrastructures',
                    'bio' => 'Manish is a civil engineer with over two decades of expertise in heavy infrastructure and high-rise commercial structures.',
                    'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=150&auto=format&fit=crop',
                ],
                'business' => [
                    'name' => 'BuildCraft Infrastructures',
                    'logo' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=150&auto=format&fit=crop',
                    'cover_image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?q=80&w=800&auto=format&fit=crop',
                    'category' => 'Construction',
                    'tagline' => 'Constructing sustainable and modern architectures',
                    'location' => 'Vadodara, Gujarat',
                    'description' => "BuildCraft Infrastructures is a leading construction and civil engineering enterprise. We specialize in green buildings, commercial complexes, and modern residential townships. Our mission is to combine sustainability with state-of-the-art technology.\n\nWe utilize eco-friendly materials and energy-efficient building methods to ensure each structure is durable, functional, and visually impressive. Let us lay the solid foundation for your next dream development.",
                    'website' => 'https://buildcraftinfra.com',
                    'phone' => '+91 99887 76655',
                    'email' => 'contact@buildcraftinfra.com',
                    'linkedin' => 'https://linkedin.com/company/buildcraft-infra',
                    'instagram' => 'https://instagram.com/buildcraftinfra',
                    'youtube' => 'https://youtube.com/c/buildcraftinfra',
                    'twitter' => 'https://twitter.com/buildcraftinfra',
                    'whatsapp' => 'https://wa.me/919988776655',
                    'hours' => '8:00 AM - 7:00 PM (Mon - Sat)',
                    'founded' => '2012',
                    'team_size' => '120+ Employees',
                    'projects' => '40+ Landmark Projects',
                    'services' => [
                        'Commercial Construction',
                        'Residential Townships',
                        'Structural Designing',
                        'Interior Turnkey Solutions'
                    ],
                    'status' => 'approved',
                    'is_verified' => true,
                ]
            ],
            [
                'user' => [
                    'name' => 'Kiran Sathwara',
                    'email' => 'kiran@elitewealthplanners.in',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'phone' => '+91 98250 11223',
                    'city' => 'Surat',
                    'designation' => 'Chief Financial Advisor',
                    'company' => 'Elite Wealth Planners',
                    'bio' => 'Kiran holds a CA degree and is passionate about financial literacy. He has helped over 1000+ individuals plan their retirement and investments.',
                    'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=150&auto=format&fit=crop',
                ],
                'business' => [
                    'name' => 'Elite Wealth Planners',
                    'logo' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=150&auto=format&fit=crop',
                    'cover_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=800&auto=format&fit=crop',
                    'category' => 'Financial Services',
                    'tagline' => 'Your trusted partner in wealth creation and security',
                    'location' => 'Surat, Gujarat',
                    'description' => "Elite Wealth Planners offers comprehensive financial consulting, portfolio management, tax advisory, and retirement planning services. We help families and corporate firms build wealth securely and optimize their financial strategies.\n\nOur certified advisors utilize data-backed investment products and risk management strategies to help you navigate volatile markets and achieve long-term financial freedom.",
                    'website' => 'https://elitewealthplanners.in',
                    'phone' => '+91 98250 11223',
                    'email' => 'advisor@elitewealthplanners.in',
                    'linkedin' => 'https://linkedin.com/company/elite-wealth-planners',
                    'instagram' => 'https://instagram.com/elitewealthplanners',
                    'youtube' => 'https://youtube.com/c/elitewealthplanners',
                    'twitter' => 'https://twitter.com/elitewealth',
                    'whatsapp' => 'https://wa.me/919825011223',
                    'hours' => '10:00 AM - 6:00 PM (Mon - Fri)',
                    'founded' => '2015',
                    'team_size' => '25+ Wealth Managers',
                    'projects' => '1200+ Clients Managed',
                    'services' => [
                        'Portfolio Management',
                        'Tax Optimization',
                        'Retirement Planning',
                        'Mutual Fund Distribution'
                    ],
                    'status' => 'approved',
                    'is_verified' => true,
                ]
            ],
            [
                'user' => [
                    'name' => 'Vikram Sathwara',
                    'email' => 'vikram@greengridrenewables.com',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'phone' => '+91 98799 55443',
                    'city' => 'Rajkot',
                    'designation' => 'Technical Director',
                    'company' => 'GreenGrid Renewables',
                    'bio' => 'Vikram is an electrical engineer specializing in renewable energy microgrids and smart metering systems.',
                    'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=150&auto=format&fit=crop',
                ],
                'business' => [
                    'name' => 'GreenGrid Renewables',
                    'logo' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=150&auto=format&fit=crop',
                    'cover_image' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?q=80&w=800&auto=format&fit=crop',
                    'category' => 'Renewables',
                    'tagline' => 'Powering a cleaner tomorrow with solar energy',
                    'location' => 'Rajkot, Gujarat',
                    'description' => "GreenGrid Renewables is at the forefront of solar energy installations and smart grid management. We provide commercial, residential, and agricultural solar setups, reducing carbon footprint and electric bills simultaneously.\n\nFrom engineering procurement to construction and system commissioning, we offer end-to-end solar solutions that yield maximum energy harvest and quick return on investment.",
                    'website' => 'https://greengridrenewables.com',
                    'phone' => '+91 98799 55443',
                    'email' => 'sales@greengridrenewables.com',
                    'linkedin' => 'https://linkedin.com/company/greengrid-renewables',
                    'instagram' => 'https://instagram.com/greengrid_solar',
                    'youtube' => 'https://youtube.com/c/greengridsolar',
                    'twitter' => 'https://twitter.com/greengrid_solar',
                    'whatsapp' => 'https://wa.me/919879955443',
                    'hours' => '9:00 AM - 6:00 PM (Mon - Sat)',
                    'founded' => '2019',
                    'team_size' => '35+ Field Engineers',
                    'projects' => '350+ Solar Rooftops Installed',
                    'services' => [
                        'Industrial Solar Setup',
                        'Residential Solar Rooftop',
                        'Net Metering Consultancy',
                        'Solar Maintenance & Cleaning'
                    ],
                    'status' => 'approved',
                    'is_verified' => true,
                ]
            ],
            [
                'user' => [
                    'name' => 'Pooja Sathwara',
                    'email' => 'pooja@novacreativestudios.com',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'phone' => '+91 95588 33221',
                    'city' => 'Mumbai',
                    'designation' => 'Creative Director',
                    'company' => 'Nova Creative Studios',
                    'bio' => 'Pooja is an art director and visual designer. She has worked with premium fashion, tech, and FMCG brands to craft their identities.',
                    'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=150&auto=format&fit=crop',
                ],
                'business' => [
                    'name' => 'Nova Creative Studios',
                    'logo' => 'https://images.unsplash.com/photo-1572021335469-31706a17aaef?q=80&w=150&auto=format&fit=crop',
                    'cover_image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?q=80&w=800&auto=format&fit=crop',
                    'category' => 'Creative Agency',
                    'tagline' => 'Elevating brands through dynamic visual design',
                    'location' => 'Mumbai, Maharashtra',
                    'description' => "Nova Creative Studios is a full-service creative agency. We work on brand strategy, commercial video production, high-end photography, graphic design, and experiential marketing. We bring brands to life with stunning visual stories.\n\nOur collaborative team of designers, writers, and directors combine art with consumer insights to produce impactful multimedia campaigns that drive engagement and build brand loyalty.",
                    'website' => 'https://novacreativestudios.com',
                    'phone' => '+91 95588 33221',
                    'email' => 'hello@novacreativestudios.com',
                    'linkedin' => 'https://linkedin.com/company/nova-creative-studios',
                    'instagram' => 'https://instagram.com/novacreativestudios',
                    'youtube' => 'https://youtube.com/c/novacreativestudios',
                    'twitter' => 'https://twitter.com/nova_studios',
                    'whatsapp' => 'https://wa.me/919558833221',
                    'hours' => '10:00 AM - 7:00 PM (Mon - Sat)',
                    'founded' => '2021',
                    'team_size' => '18+ Creatives',
                    'projects' => '85+ Successful Campaigns',
                    'services' => [
                        'Brand Identity Design',
                        'Corporate Video Shoots',
                        'Social Media Content Creation',
                        'Packaging & Product Design'
                    ],
                    'status' => 'approved',
                    'is_verified' => true,
                ]
            ],
        ];

        foreach ($businessesData as $data) {
            // Create or update the user
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                $data['user']
            );

            // Set user_id for the business
            $businessDetails = $data['business'];
            $businessDetails['user_id'] = $user->id;

            // Create or update the business
            Business::updateOrCreate(
                ['name' => $businessDetails['name']],
                $businessDetails
            );
        }
    }
}

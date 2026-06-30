// Temporary Dummy Data implementation for showcase
// Original fetch implementations are replaced with mock delays

function delay(ms: number) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

export async function fetchBusinesses() {
  await delay(500);
  return [
    {
      id: 99,
      name: "User Demo Business",
      category: "Software Development",
      description: "This is a demo business associated with the logged-in user.",
      location: "Ahmedabad, Gujarat",
      website: "https://userdemobusiness.com",
      is_verified: true,
      rating: "5.0",
      logo: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop",
      tagline: "Innovative solutions for custom software systems",
      founded: "2020",
      team_size: "10-25 members",
      projects: "30+ projects completed",
      services: "Web Development, Mobile App Development, UI/UX Design",
      hours: "9:00 AM - 6:00 PM (Mon - Fri)",
      phone: "+91 98765 43210",
      email: "contact@userdemobusiness.com",
      whatsapp: "+919876543210"
    },
    {
      id: 1,
      name: "Sathwara Infrastructure",
      category: "Construction",
      description: "Premier construction & civil engineering firm delivering state-of-the-art commercial hubs, industrial spaces, and residential complexes across Gujarat.",
      location: "Ahmedabad",
      website: "https://sathwarainfra.com",
      is_verified: true,
      rating: "4.9",
      logo: "https://images.unsplash.com/photo-1541888081622-1bc5ffafafc5?w=150&h=150&fit=crop",
      tagline: "Building High-Quality Vetted Infrastructures",
      founded: "2012",
      team_size: "150+ workers",
      projects: "65+ commercial projects",
      services: "Commercial Construction, Turnkey Building, Architecture Design, Soil Testing",
      hours: "9:00 AM - 6:00 PM (Mon - Sat)",
      phone: "+91 79 4001 9283",
      email: "info@sathwarainfra.com",
      linkedin: "https://linkedin.com/company/sathwara-infrastructure",
      whatsapp: "+917940019283"
    },
    {
      id: 2,
      name: "Ambika Agro Industries",
      category: "Supply Chain",
      description: "Leading processors and supply chain exporters of high-quality seeds, agricultural grains, and organic fertilizers based out of North Gujarat.",
      location: "Mehsana",
      website: "https://ambikaagro.in",
      is_verified: true,
      rating: "4.7",
      logo: "https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?w=150&h=150&fit=crop",
      tagline: "Nurturing Agriculture with Quality Supply Chain",
      founded: "2015",
      team_size: "45+ specialists",
      projects: "300+ distributor networks",
      services: "Crop Processing, Supply Chain Distribution, Cold Storage Solutions, Exporting",
      hours: "8:00 AM - 7:00 PM (Mon - Sat)",
      phone: "+91 2762 253401",
      email: "contact@ambikaagro.in",
      linkedin: "https://linkedin.com/company/ambika-agro-industries",
      whatsapp: "+912762253401"
    },
    {
      id: 3,
      name: "Karni Digital Solutions",
      category: "Software Development",
      description: "Enterprise software agency specialized in designing custom ERP dashboards, high-scale web platforms, and mobile apps for manufacturing companies.",
      location: "Gandhinagar",
      website: "https://karnidigital.com",
      is_verified: true,
      rating: "4.8",
      logo: "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=150&h=150&fit=crop",
      tagline: "Accelerating Manufacturing with Smart ERPs",
      founded: "2019",
      team_size: "35+ developers",
      projects: "110+ systems deployed",
      services: "ERP Development, React Native Apps, Cloud Architecture, Industrial Automation",
      hours: "10:00 AM - 7:00 PM (Mon - Fri)",
      phone: "+91 79 5012 8832",
      email: "hello@karnidigital.com",
      linkedin: "https://linkedin.com/company/karni-digital",
      whatsapp: "+917950128832"
    },
    {
      id: 4,
      name: "Viram Financial Services",
      category: "Financial Services",
      description: "Comprehensive financial planning, tax consultation, wealth management, and capital funding for small-to-medium enterprises.",
      location: "Rajkot",
      website: "https://viramfinance.com",
      is_verified: false,
      rating: "4.5",
      logo: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=150&h=150&fit=crop",
      tagline: "Empowering Local Businesses with Safe Capital",
      founded: "2021",
      team_size: "12 advisors",
      projects: "500+ clients advised",
      services: "Business Auditing, GST Filing, SME Loans, Mutual Fund Portfolios",
      hours: "9:30 AM - 6:30 PM (Mon - Fri)",
      phone: "+91 281 245 8899",
      email: "support@viramfinance.com",
      linkedin: "https://linkedin.com/company/viram-financial",
      whatsapp: "+912812458899"
    },
    {
      id: 5,
      name: "Navdurga Solar & Power",
      category: "Renewables",
      description: "Turnkey EPC installers of commercial rooftop solar systems, solar agricultural pumps, and net-metering services.",
      location: "Vadodara",
      website: "https://navdurgasolar.com",
      is_verified: true,
      rating: "4.9",
      logo: "https://images.unsplash.com/photo-1509391366360-2e959784a276?w=150&h=150&fit=crop",
      tagline: "Powering Industries with Green Energy",
      founded: "2016",
      team_size: "50+ technicians",
      projects: "3MW+ solar capacity installed",
      services: "Rooftop Solar Installation, Agricultural Pumps, Net Metering, Battery Storage",
      hours: "9:00 AM - 6:00 PM (Mon - Sat)",
      phone: "+91 265 233 4455",
      email: "sales@navdurgasolar.com",
      whatsapp: "+912652334455"
    },
    {
      id: 6,
      name: "Maruti Logistics",
      category: "Supply Chain",
      description: "Third-party logistics provider offering high-speed container trucking, temperature-controlled fleet, and cargo handling services.",
      location: "Surat",
      logo: "https://images.unsplash.com/photo-1586528116311-ad8ed7c663e0?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.6",
      tagline: "On-Time Secure Logistics Solutions",
      founded: "2010",
      team_size: "120+ drivers & staff",
      projects: "5000+ successful consignments",
      services: "FCL Trucking, Cold Chain Transit, Custom Clearance, Warehouse Logistics",
      hours: "24/7 Dispatch",
      phone: "+91 261 445 6600",
      email: "ops@marutilogistics.in",
      whatsapp: "+912614456600"
    },
    {
      id: 7,
      name: "Royal Web Services",
      category: "Software Development",
      description: "Dedicated web developers crafting high-conversion landing pages, WordPress websites, and custom web portals for businesses.",
      location: "Pune",
      logo: "https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=150&h=150&fit=crop",
      is_verified: false,
      rating: "4.4",
      tagline: "Stunning Web Experiences That Convert",
      founded: "2020",
      team_size: "8 developers",
      projects: "150+ sites built",
      services: "WordPress Sites, Landing Page Optimization, Static Portals, Shopify Stores",
      hours: "9:00 AM - 6:00 PM (Mon - Fri)",
      phone: "+91 20 6688 1234",
      email: "hello@royalweb.in",
      whatsapp: "+912066881234"
    },
    {
      id: 8,
      name: "Shree Balaji Builders",
      category: "Construction",
      description: "Renowned developers of smart residential complexes, luxury row houses, and community halls focusing on affordable premium styling.",
      location: "Jamnagar",
      logo: "https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.7",
      tagline: "Making Modern Luxury Affordable",
      founded: "2014",
      team_size: "60+ site supervisors",
      projects: "12 residential projects",
      services: "Residential Development, RCC Structures, Interior Fitouts, Project Estimation",
      hours: "9:00 AM - 7:00 PM (Mon - Sat)",
      phone: "+91 288 255 1928",
      email: "build@shreebalajigroup.com",
      whatsapp: "+912882551928"
    },
    {
      id: 9,
      name: "Siddhnath Advertising",
      category: "Digital Marketing",
      description: "Creative agency focusing on local SEO, localized social media marketing campaigns, and regional Google ads optimization.",
      location: "Bhavnagar",
      logo: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=150&h=150&fit=crop",
      is_verified: false,
      rating: "4.5",
      tagline: "Targeted Regional Customer Acquisition",
      founded: "2018",
      team_size: "15 marketers",
      projects: "60+ brands managed",
      services: "Google Maps SEO, Social Media Campaigns, Local Lead Generation, Branding Design",
      hours: "10:00 AM - 6:30 PM (Mon - Fri)",
      phone: "+91 278 244 9911",
      email: "growth@siddhnathads.com",
      whatsapp: "+912782449911"
    },
    {
      id: 10,
      name: "Radhe Venture Capital",
      category: "Venture Capital",
      description: "Early-stage fund backing community-led start-ups, AgriTech solutions, and local manufacturing facilities with growth capital.",
      location: "Mumbai",
      logo: "https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.8",
      tagline: "Investing in Community Founders & Local Growth",
      founded: "2017",
      team_size: "10 partners & analysts",
      projects: "25+ active startups",
      services: "Seed Capital Funding, Mentorship Programs, Series A Syndication, Mergers & Acquisitions",
      hours: "10:00 AM - 6:00 PM (Mon - Fri)",
      phone: "+91 22 8899 3010",
      email: "deals@radhevc.com",
      whatsapp: "+912288993010"
    },
    {
      id: 11,
      name: "Chamunda Creative Studio",
      category: "Creative Agency",
      description: "High-end product photography studio, catalog designers, and branding experts helping eCommerce sellers present premium layouts.",
      location: "Bangalore",
      logo: "https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.6",
      tagline: "Stunning Visual Assets for Modern Brands",
      founded: "2020",
      team_size: "14 creative heads",
      projects: "300+ eCommerce catalogs",
      services: "Product Photography, Logo & Brand Guidelines, Video Ads Production, Packaging Design",
      hours: "9:30 AM - 6:30 PM (Mon - Fri)",
      phone: "+91 80 4488 2211",
      email: "creatives@chamundastudio.in",
      whatsapp: "+918044882211"
    },
    {
      id: 12,
      name: "Gajanan Enterprise",
      category: "Supply Chain",
      description: "Wholesale suppliers and distributors of electrical equipment, industrial switches, and power cabling systems.",
      location: "Ahmedabad",
      logo: "https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=150&h=150&fit=crop",
      is_verified: false,
      rating: "4.3",
      tagline: "Reliable Wholesale Electrical Supplies",
      founded: "2013",
      team_size: "20 sales executives",
      projects: "150+ wholesale clients",
      services: "Industrial Cabling, Distribution Boards, Wholesale Sourcing, Site Deliveries",
      hours: "9:00 AM - 7:00 PM (Mon - Sat)",
      phone: "+91 79 2200 4545",
      email: "sales@gajananent.com",
      whatsapp: "+917922004545"
    },
    {
      id: 13,
      name: "Valkeshwar Tech Systems",
      category: "Software Development",
      description: "Custom software engineering and cyber-security consultants helping logistics firms build robust backend platforms.",
      location: "Delhi",
      logo: "https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.8",
      tagline: "Robust APIs & Bulletproof Security Services",
      founded: "2018",
      team_size: "40+ engineers",
      projects: "80+ server networks secured",
      services: "Logistics SaaS Development, Penetration Testing, API Integrations, Server Scaling",
      hours: "9:00 AM - 6:00 PM (Mon - Fri)",
      phone: "+91 11 5500 2938",
      email: "secops@valkeshwartech.com",
      whatsapp: "+911155002938"
    },
    {
      id: 14,
      name: "Avadh Investment Group",
      category: "Financial Services",
      description: "Personalized portfolio management, mutual funds advising, stock trading strategies, and insurance brokerage services.",
      location: "Rajkot",
      logo: "https://images.unsplash.com/photo-1559526324-c1f275fbfa32?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.7",
      tagline: "Your Partner in Vetted Wealth Growth",
      founded: "2011",
      team_size: "18 consultants",
      projects: "1200+ active portfolios",
      services: "Portfolio Advisory, Mutual Funds, Life Insurance Brokerage, Tax Planning",
      hours: "9:00 AM - 5:30 PM (Mon - Fri)",
      phone: "+91 281 230 4567",
      email: "advisor@avadhinvest.com",
      whatsapp: "+912812304567"
    },
    {
      id: 15,
      name: "Shiv Shakti Renewables",
      category: "Renewables",
      description: "Dedicated developers of high-capacity wind turbine systems and solar hybrid parks supplying green energy grids.",
      location: "Mehsana",
      logo: "https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.9",
      tagline: "Large-Scale Wind & Solar Grid Engineering",
      founded: "2013",
      team_size: "80+ operators",
      projects: "15MW+ energy output",
      services: "Wind Turbine Commissioning, Grid Synchronization, Solar Hybrid Parks, Site Audits",
      hours: "9:00 AM - 6:00 PM (Mon - Sat)",
      phone: "+91 2762 284920",
      email: "grids@shivshaktirenew.in",
      whatsapp: "+912762284920"
    },
    {
      id: 16,
      name: "Narmada Cements",
      category: "Construction",
      description: "Bulk cement distributors and aggregate concrete suppliers offering high-durability mixtures for bridges and skyscrapers.",
      location: "Vadodara",
      logo: "https://images.unsplash.com/photo-1590069261209-f8e9b8642343?w=150&h=150&fit=crop",
      is_verified: false,
      rating: "4.4",
      tagline: "High-Strength Concrete Mixtures for Mega Projects",
      founded: "2009",
      team_size: "35 delivery crews",
      projects: "400+ construction sites served",
      services: "Bulk OPC Supply, Ready Mix Concrete, Eco-aggregate Concrete, High-Stress Testing",
      hours: "7:00 AM - 6:00 PM (Mon - Sat)",
      phone: "+91 265 244 5599",
      email: "supply@narmadacement.com",
      whatsapp: "+912652445599"
    },
    {
      id: 17,
      name: "Tulsi Agri Exports",
      category: "Supply Chain",
      description: "Vetted export house shipping Indian spices, fresh farm vegetables, and Basmati rice to GCC countries and Europe.",
      location: "Anand",
      logo: "https://images.unsplash.com/photo-1530595467537-0b5996c41f2d?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.8",
      tagline: "Connecting Local Farms with Global Markets",
      founded: "2016",
      team_size: "30 international trade experts",
      projects: "450+ containers exported",
      services: "GCC Export Clearance, Fresh Veg Cold Logistics, Spices Processing, Quality Checks",
      hours: "9:00 AM - 6:00 PM (Mon - Fri)",
      phone: "+91 2692 233910",
      email: "trade@tulsiagro.co.in",
      whatsapp: "+912692233910"
    },
    {
      id: 18,
      name: "Jay Ambe Marketing",
      category: "Digital Marketing",
      description: "Performance marketing agents running YouTube video ads campaigns, influencer tie-ups, and regional media promotions.",
      location: "Surat",
      logo: "https://images.unsplash.com/photo-1533750516457-a7f992034fec?w=150&h=150&fit=crop",
      is_verified: false,
      rating: "4.2",
      tagline: "Dominating Social Media Feeds for SME Brands",
      founded: "2021",
      team_size: "10 media buyers",
      projects: "45 active brand campaigns",
      services: "YouTube Ads Campaigns, Influencer Outreach, Media Planning, Video Reels Editing",
      hours: "10:00 AM - 7:00 PM (Mon - Fri)",
      phone: "+91 261 558 9200",
      email: "ads@jayambemarketing.in",
      whatsapp: "+912615589200"
    },
    {
      id: 19,
      name: "Dwarkadhish Software",
      category: "Software Development",
      description: "Specialized Node.js, Go, and React web agency developing low-latency e-commerce architectures and vector-based AI search engines.",
      location: "Jamnagar",
      logo: "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.9",
      tagline: "Next-Gen Architectures with Blazing Speed",
      founded: "2022",
      team_size: "12 engineers",
      projects: "25 platforms built",
      services: "API Engineering, AI Vector Databases, eCommerce Custom Stacks, React UI Kits",
      hours: "10:00 AM - 6:00 PM (Mon - Fri)",
      phone: "+91 288 266 4422",
      email: "dev@dwarka.software",
      whatsapp: "+912882664422"
    },
    {
      id: 20,
      name: "Somnath Security Services",
      category: "Supply Chain",
      description: "Leading security solutions company providing industrial security guards, corporate office monitoring systems, and cash-in-transit convoys.",
      location: "Veraval",
      logo: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=150&h=150&fit=crop",
      is_verified: false,
      rating: "4.5",
      tagline: "Vetted Security for Industrial Safeguards",
      founded: "2014",
      team_size: "350+ guards & personnel",
      projects: "90+ factories secured",
      services: "Industrial Guarding, Cash-in-Transit, CCTV Control Rooms, Corporate Risk Assessment",
      hours: "24/7 Operations",
      phone: "+91 2876 221199",
      email: "ops@somnathsecurity.com",
      whatsapp: "+912876221199"
    },
    {
      id: 21,
      name: "Khodiyar Engineering",
      category: "Construction",
      description: "Structural engineering specialists designing high-load steel structural sheds, pre-engineered buildings (PEB), and industrial roofing.",
      location: "Bhavnagar",
      logo: "https://images.unsplash.com/photo-1581094288338-2314dddb7ecc?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.7",
      tagline: "Robust Pre-Engineered Industrial Steel Sheds",
      founded: "2010",
      team_size: "45 structural engineers",
      projects: "150+ factories erected",
      services: "PEB Shed Design, Steel Fabrications, Industrial Roofing, Load Stress Auditing",
      hours: "9:00 AM - 6:30 PM (Mon - Sat)",
      phone: "+91 278 251 4090",
      email: "steel@khodiyareng.com",
      whatsapp: "+912782514090"
    },
    {
      id: 22,
      name: "Devkrupa Tech & Digital",
      category: "Digital Marketing",
      description: "All-in-one digital marketing agency helping retail shops establish their local online presence through Google business profiles, email newsletters, and WhatsApp marketing lists.",
      location: "Gandhinagar",
      logo: "https://images.unsplash.com/photo-1557804506-669a67965ba0?w=150&h=150&fit=crop",
      is_verified: false,
      rating: "4.3",
      tagline: "Bridging Offline Shops with Online Customers",
      founded: "2020",
      team_size: "7 marketing assistants",
      projects: "85+ retail shops digitized",
      services: "Google Business Profiles, WhatsApp Broadcast Lists, Loyalty Campaigns, Newsletter Copywriting",
      hours: "10:00 AM - 6:00 PM (Mon - Fri)",
      phone: "+91 79 6632 8111",
      email: "hello@devkrupa.digital",
      whatsapp: "+917966328111"
    },
    {
      id: 23,
      name: "Krishna Finvest",
      category: "Financial Services",
      description: "Corporate loan consultants, debt syndication experts, and treasury managers facilitating low-cost capital for manufacturing units.",
      location: "Ahmedabad",
      logo: "https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.8",
      tagline: "Simplifying Debt Syndication & SME Loans",
      founded: "2015",
      team_size: "14 finance professionals",
      projects: "₹200Cr+ loans syndicated",
      services: "Debt Syndication, Machinery Loans, Working Capital Funding, Project Reports",
      hours: "9:30 AM - 6:00 PM (Mon - Fri)",
      phone: "+91 79 2658 8900",
      email: "loans@krishnafinvest.in",
      whatsapp: "+917926588900"
    },
    {
      id: 24,
      name: "Uma Wind Farms",
      category: "Renewables",
      description: "Maintenance and optimization contractors for heavy-industry wind turbines and solar-grid infrastructure.",
      location: "Bhuj",
      logo: "https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.9",
      tagline: "Keeping Renewable Grids Working Seamlessly",
      founded: "2012",
      team_size: "55 technicians",
      projects: "120+ wind turbines serviced",
      services: "Turbine Blade Servicing, Gearbox Lubrication, Solar Grid Cleaning, Efficiency Audits",
      hours: "8:00 AM - 6:00 PM (Mon - Sat)",
      phone: "+91 2832 254411",
      email: "ops@umawindfarms.com",
      whatsapp: "+912832254411"
    },
    {
      id: 25,
      name: "Mahadev Consulting",
      category: "Venture Capital",
      description: "Wealth advisors, investment angels, and incubators helping early stage tech entrepreneurs refine business models and secure institutional venture funding.",
      location: "Mumbai",
      logo: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=150&h=150&fit=crop",
      is_verified: true,
      rating: "4.8",
      tagline: "Incubating Community Startups for Global Scale",
      founded: "2019",
      team_size: "12 investment managers",
      projects: "₹50Cr+ startup capital funded",
      services: "Angel Seed Investments, Pitch Deck Reviews, Product-Market Fit Advisory, VC Networking",
      hours: "10:00 AM - 6:00 PM (Mon - Fri)",
      phone: "+91 22 6602 1200",
      email: "pitches@mahadev.vc",
      whatsapp: "+912266021200"
    }
  ];
}

export async function fetchEvents() {
  await delay(500);
  return [
    {
      id: 1,
      title: "Sabha Grand Business Summit 2026",
      description: "Unlock key networking opportunities, interact with angel investors, and listen to community business leaders outlining strategic roadmaps for 2026.",
      date: new Date(Date.now() + 86400000 * 3).toISOString(), // 3 days later -> upcoming
      location: "Ahmedabad Exhibition Center, Gujarat",
      type: "Conference",
      price_normal: "₹999",
      price_verified: "₹499",
      image: "https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=800&auto=format&fit=crop"
    },
    {
      id: 2,
      title: "Harmony Networking Mixer",
      description: "An informal, high-energy networking meet designed for local business owners, service providers, and startup founders to exchange referrals.",
      date: new Date().toISOString(), // Today -> current
      location: "DoubleTree by Hilton, Pune",
      type: "Mixer",
      price_normal: "₹999",
      price_verified: "₹499",
      image: "https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=800&auto=format&fit=crop"
    },
    {
      id: 3,
      title: "Digital Growth & Local SEO Workshop",
      description: "A hands-on, practical session with digital marketing specialists training store owners to capture local markets using online business profiles.",
      date: new Date(Date.now() - 86400000).toISOString(), // Yesterday -> past
      location: "Inder Residency, Rajkot",
      type: "Workshop",
      price_normal: "Free",
      price_verified: "Free",
      image: "https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=800&auto=format&fit=crop"
    },
    {
      id: 4,
      title: "AgriTech & Supply Chain Symposium",
      description: "Panel discussions on cold storage integrations, automated sorting technologies, and direct-to-market channels for community farmers.",
      date: new Date(Date.now() + 86400000 * 7).toISOString(), // 7 days later -> upcoming
      location: "Sardar Patel Hall, Mehsana",
      type: "Conference",
      price_normal: "₹999",
      price_verified: "₹499",
      image: "https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?q=80&w=800&auto=format&fit=crop"
    },
    {
      id: 5,
      title: "SME Capital Funding & Debt Seminar",
      description: "Bank leaders and debt syndication experts explaining rules, documentation, and government subsidies for machinery loans and cash credits.",
      date: new Date(Date.now() + 86400000 * 12).toISOString(), // 12 days later -> upcoming
      location: "Courtyard by Marriott, Surat",
      type: "Seminar",
      price_normal: "₹999",
      price_verified: "₹499",
      image: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=800&auto=format&fit=crop"
    },
    {
      id: 6,
      title: "Renewable Energy & Solar Expo",
      description: "Exhibitions showcasing solar agricultural pumps, industrial rooftop installations, and wind turbine components with government net-metering experts.",
      date: new Date(Date.now() - 86400000 * 4).toISOString(), // 4 days ago -> past
      location: "Grand Bhagwati, Vadodara",
      type: "Exhibition",
      price_normal: "Free",
      price_verified: "Free",
      image: "https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=800&auto=format&fit=crop"
    },
    {
      id: 7,
      title: "Global Export-Import Summit",
      description: "Detailed seminar on custom regulations, shipping logistics, and cross-border trade for export-oriented SMEs.",
      date: new Date(Date.now() + 86400000 * 20).toISOString(),
      location: "Hotel Hyatt, Ahmedabad",
      type: "Conference",
      price_normal: "₹999",
      price_verified: "₹499",
      image: "https://images.unsplash.com/photo-1530595467537-0b5996c41f2d?q=80&w=800&auto=format&fit=crop"
    },
    {
      id: 8,
      title: "Cyber-Security for Retail",
      description: "Essential training for business owners on managing payment gateway security and customer data privacy compliance.",
      date: new Date(Date.now() + 86400000 * 25).toISOString(),
      location: "Virtual Webinar",
      type: "Workshop",
      price_normal: "₹999",
      price_verified: "₹499",
      image: "https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=800&auto=format&fit=crop"
    }
  ];
}

export async function fetchStatistics() {
  await delay(300);
  return [
    { id: 1, label: "Active Members", value: "5000+" },
    { id: 2, label: "Businesses Registered", value: "1200+" },
    { id: 3, label: "Events Hosted", value: "150+" }
  ];
}

export async function fetchGallery() {
  await delay(300);
  return [
    // ── Event 1: Sabha Grand Business Summit 2026 ───────────────────────────
    { id: 1,  image_path: "https://images.unsplash.com/photo-1540575461501-7ad0582373f3?q=80&w=1200&auto=format&fit=crop", caption: "Grand Summit – Opening Ceremony", event_id: 1, created_at: new Date().toISOString() },
    { id: 2,  image_path: "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1200&auto=format&fit=crop", caption: "Keynote Address on Community Growth", event_id: 1, created_at: new Date().toISOString() },
    { id: 3,  image_path: "https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=1200&auto=format&fit=crop", caption: "Panel Discussion – Future of SMEs", event_id: 1, created_at: new Date().toISOString() },
    { id: 4,  image_path: "https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1200&auto=format&fit=crop", caption: "Awards Ceremony & Recognition", event_id: 1, created_at: new Date().toISOString() },
    { id: 5,  image_path: "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=1200&auto=format&fit=crop", caption: "Attendee Networking Lounge", event_id: 1, created_at: new Date().toISOString() },
    { id: 6,  image_path: "https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=1200&auto=format&fit=crop", caption: "Investor Meet – Ahmedabad Hall A", event_id: 1, created_at: new Date().toISOString() },
    { id: 7,  image_path: "https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=1200&auto=format&fit=crop", caption: "Post-Summit Group Photo", event_id: 1, created_at: new Date().toISOString() },

    // ── Event 2: Harmony Networking Mixer ───────────────────────────────────
    { id: 8,  image_path: "https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1200&auto=format&fit=crop", caption: "Mixer – Speed Networking Rounds", event_id: 2, created_at: new Date().toISOString() },
    { id: 9,  image_path: "https://images.unsplash.com/photo-1557804506-669a67965ba0?q=80&w=1200&auto=format&fit=crop", caption: "Community Members Connecting", event_id: 2, created_at: new Date().toISOString() },
    { id: 10, image_path: "https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=1200&auto=format&fit=crop", caption: "Welcome Address – Pune Chapter", event_id: 2, created_at: new Date().toISOString() },
    { id: 11, image_path: "https://images.unsplash.com/photo-1527529482837-4698179dc6ce?q=80&w=1200&auto=format&fit=crop", caption: "Dinner & Gala – Mixer Evening", event_id: 2, created_at: new Date().toISOString() },
    { id: 12, image_path: "https://images.unsplash.com/photo-1530099486328-e021101a494a?q=80&w=1200&auto=format&fit=crop", caption: "Business Card Exchange Zone", event_id: 2, created_at: new Date().toISOString() },

    // ── Event 3: Digital Growth & Local SEO Workshop ─────────────────────────
    { id: 13, image_path: "https://images.unsplash.com/photo-1528605248644-14dd04022da1?q=80&w=1200&auto=format&fit=crop", caption: "SEO Workshop – Hands-on Training", event_id: 3, created_at: new Date().toISOString() },
    { id: 14, image_path: "https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=1200&auto=format&fit=crop", caption: "Google Maps Optimization Session", event_id: 3, created_at: new Date().toISOString() },
    { id: 15, image_path: "https://images.unsplash.com/photo-1542744173-8e7e53415bb0?q=80&w=1200&auto=format&fit=crop", caption: "Rajkot Workshop – Presentation", event_id: 3, created_at: new Date().toISOString() },
    { id: 16, image_path: "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1200&auto=format&fit=crop", caption: "Q&A With Digital Marketing Experts", event_id: 3, created_at: new Date().toISOString() },

    // ── Event 4: AgriTech & Supply Chain Symposium ───────────────────────────
    { id: 17, image_path: "https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?q=80&w=1200&auto=format&fit=crop", caption: "AgriTech Panel – Mehsana Hall", event_id: 4, created_at: new Date().toISOString() },
    { id: 18, image_path: "https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?q=80&w=1200&auto=format&fit=crop", caption: "Cold Storage Technology Demo", event_id: 4, created_at: new Date().toISOString() },
    { id: 19, image_path: "https://images.unsplash.com/photo-1470115636492-6d2b56f9146d?q=80&w=1200&auto=format&fit=crop", caption: "Supply Chain Exhibition Floor", event_id: 4, created_at: new Date().toISOString() },
    { id: 20, image_path: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=1200&auto=format&fit=crop", caption: "Farmer & Exporter Roundtable", event_id: 4, created_at: new Date().toISOString() },
    { id: 21, image_path: "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=1200&auto=format&fit=crop", caption: "Award of Excellence – AgriTech", event_id: 4, created_at: new Date().toISOString() },

    // ── Event 5: SME Capital Funding & Debt Seminar ──────────────────────────
    { id: 22, image_path: "https://images.unsplash.com/photo-1505373877841-8d25f7d46678?q=80&w=1200&auto=format&fit=crop", caption: "SME Funding Seminar – Surat", event_id: 5, created_at: new Date().toISOString() },
    { id: 23, image_path: "https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=1200&auto=format&fit=crop", caption: "Bank Representatives Briefing", event_id: 5, created_at: new Date().toISOString() },
    { id: 24, image_path: "https://images.unsplash.com/photo-1473186505569-9c61870c11f9?q=80&w=1200&auto=format&fit=crop", caption: "Debt Syndication Workshop", event_id: 5, created_at: new Date().toISOString() },

    // ── Common Gallery (no event_id – miscellaneous) ─────────────────────────
    { id: 25, image_path: "https://images.unsplash.com/photo-1464746133101-a2c3f88e0dd9?q=80&w=1200&auto=format&fit=crop", caption: "SABHA Members – Morning Session", event_id: null, created_at: new Date().toISOString() },
    { id: 26, image_path: "https://images.unsplash.com/photo-1521737852567-6949f3f9f2b5?q=80&w=1200&auto=format&fit=crop", caption: "Office Inauguration – Ahmedabad HQ", event_id: null, created_at: new Date().toISOString() },
    { id: 27, image_path: "https://images.unsplash.com/photo-1556484687-30636164638b?q=80&w=1200&auto=format&fit=crop", caption: "Committee Review & Planning", event_id: null, created_at: new Date().toISOString() },
    { id: 28, image_path: "https://images.unsplash.com/photo-1517048676732-d65bc937f952?q=80&w=1200&auto=format&fit=crop", caption: "Leaders Brainstorming Session", event_id: null, created_at: new Date().toISOString() },
    { id: 29, image_path: "https://images.unsplash.com/photo-1508780709619-79562169bc64?q=80&w=1200&auto=format&fit=crop", caption: "Community Members Volunteer Meet", event_id: null, created_at: new Date().toISOString() },
    { id: 30, image_path: "https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=1200&auto=format&fit=crop", caption: "Youth Entrepreneurs Cohort", event_id: null, created_at: new Date().toISOString() },
    { id: 31, image_path: "https://images.unsplash.com/photo-1560523160-754a9e25c68f?q=80&w=1200&auto=format&fit=crop", caption: "Regional Chapter Launch – Rajkot", event_id: null, created_at: new Date().toISOString() },
    { id: 32, image_path: "https://images.unsplash.com/photo-1609921212029-bb5a28e60960?q=80&w=1200&auto=format&fit=crop", caption: "Award Ceremony – Best Business 2025", event_id: null, created_at: new Date().toISOString() },
    { id: 33, image_path: "https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?q=80&w=1200&auto=format&fit=crop", caption: "SABHA Annual Gala Night", event_id: null, created_at: new Date().toISOString() },
    { id: 34, image_path: "https://images.unsplash.com/photo-1588196749597-9ff075ee6b5b?q=80&w=1200&auto=format&fit=crop", caption: "Member Spotlight Shoot – Mumbai", event_id: null, created_at: new Date().toISOString() },
    { id: 35, image_path: "https://images.unsplash.com/photo-1600880292089-90a7e086ee0c?q=80&w=1200&auto=format&fit=crop", caption: "Community Charity Drive 2025", event_id: null, created_at: new Date().toISOString() },
    { id: 36, image_path: "https://images.unsplash.com/photo-1541178735493-479c1a27ed24?q=80&w=1200&auto=format&fit=crop", caption: "Founders' Dinner – Vadodara", event_id: null, created_at: new Date().toISOString() }
  ];
}

export async function submitBusiness(formData: FormData) {
  await delay(1000);
  return { success: true, message: "Business submitted successfully" };
}

export async function forgotPassword(email: string) {
  await delay(1000);
  return { success: true, message: "Password reset link sent to your email" };
}

export async function registerSendOtp(data: any) {
  await delay(1000);
  return { success: true, message: "OTP sent successfully" };
}

export async function registerConfirm(email: string, otp: string) {
  await delay(1000);
  if (otp === "1234") {
      return { success: true, token: "dummy-token", user: { id: 1, email } };
  }
  return { success: true, token: "dummy-token-default", user: { id: 1, email } };
}

export async function updateProfile(data: FormData) {
  await delay(1000);
  return {
    success: true,
    message: "Profile updated successfully",
    user: {
      name: (data.get("name") as string) || "Demo User",
      email: (data.get("email") as string) || "user@example.com",
      role: "user",
      phone: (data.get("phone") as string) || "",
      city: (data.get("city") as string) || "",
      designation: (data.get("designation") as string) || "",
      company: (data.get("company") as string) || "",
      bio: (data.get("bio") as string) || "",
      avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop"
    }
  };
}

export async function getUserBusiness() {
  await delay(500);
  return {
    id: 99,
    name: "User Demo Business",
    status: "approved",
    category: "Software Development",
    website: "https://userdemobusiness.com",
    description: "This is a demo business associated with the logged-in user.",
    tagline: "Innovative solutions for custom software systems",
    location: "Ahmedabad, Gujarat",
    hours: "9:00 AM - 6:00 PM (Mon - Fri)",
    founded: "2020",
    team_size: "10-25 members",
    projects: "30+ projects completed",
    phone: "+91 98765 43210",
    email: "contact@userdemobusiness.com",
    linkedin: "https://linkedin.com/company/userdemobusiness",
    instagram: "",
    youtube: "",
    twitter: "",
    whatsapp: "+919876543210",
    services: ["Web Development", "Mobile App Development", "UI/UX Design"]
  };
}

export async function fetchAllBusinessesAdmin() {
  const list = await fetchBusinesses();
  return list.map(b => ({ ...b, status: "approved" }));
}

export async function approveBusiness(id: number) {
  await delay(500);
  return { success: true };
}

export async function rejectBusiness(id: number, rejectionReason: string) {
  await delay(500);
  return { success: true };
}

export async function createEventAdmin(eventData: any) {
  await delay(500);
  return { success: true };
}

export async function fetchUsersAdmin() {
  await delay(500);
  return [
    { id: 1, email: "admin@sabha.com", role: "admin", created_at: new Date().toISOString(), name: "Admin" },
    { id: 2, email: "user@example.com", role: "user", created_at: new Date().toISOString(), name: "Demo User" }
  ];
}

export async function uploadGalleryImage(formData: FormData) {
  await delay(500);
  return { success: true, image_url: "https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=800&auto=format&fit=crop" };
}

export async function updateStatistic(id: number, statData: { value: string; label: string }) {
  await delay(500);
  return { success: true };
}

export async function fetchSettings() {
  await delay(300);
  return {
    platformName: "Sabha",
    contactEmail: "hello@sabha.global",
    contact_email: "hello@sabha.global",
    response_time: "Within 1 Business Day",
    coordinators: [
      { city: "Mumbai Coordinator", contact: "Ravi Sharma", phone: "+91 98200 12345", email: "mumbai@sabha.global" },
      { city: "Pune Coordinator", contact: "Pooja Verma", phone: "+91 96110 54321", email: "pune@sabha.global" },
      { city: "Ahmedabad Coordinator", contact: "Dev Patel", phone: "+91 94260 98765", email: "ahmedabad@sabha.global" }
    ],
    theme: "light",
  };
}

export async function updateSettingsAdmin(settingsData: Record<string, any>) {
  await delay(500);
  return { success: true };
}

export async function fetchReviews(businessId: number) {
  await delay(500);
  return [
    { id: 1, content: "Great service and very professional.", rating: 5, user: { name: "John Doe" }, created_at: new Date().toISOString() },
    { id: 2, content: "Highly recommend their products.", rating: 4, user: { name: "Jane Smith" }, created_at: new Date().toISOString() }
  ];
}

export async function submitReview(businessId: number, reviewData: { content: string; rating: number }) {
  await delay(500);
  return {
    success: true,
    review: {
      id: Date.now(),
      content: reviewData.content,
      rating: reviewData.rating,
      user: { name: "Demo User" },
      created_at: new Date().toISOString()
    }
  };
}

export async function reserveEventSeat(eventId: number, formData: FormData) {
  await delay(1000);
  return { success: true, message: "Seat reserved successfully" };
}

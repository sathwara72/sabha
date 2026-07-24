// Temporary Dummy Data implementation for showcase
// Original fetch implementations are replaced with mock delays
import { API_BASE_URL, API_ORIGIN } from "@/lib/config";

function delay(ms: number) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

const originalFetch = globalThis.fetch;
const fetch = async (input: RequestInfo | URL, init?: RequestInit): Promise<Response> => {
  const response = await originalFetch(input, init);
  if (response.status === 401) {
    if (typeof window !== "undefined") {
      localStorage.removeItem("sabha_token");
      localStorage.removeItem("sabha_user");
      localStorage.removeItem("sabha_auth");
      window.location.href = "/";
    }
  }
  return response;
};

export async function fetchBusinesses(params?: { page?: number; limit?: number; search?: string; category?: string }) {
  let url = `${API_BASE_URL}/businesses`;
  if (params) {
    const queryParts: string[] = [];
    if (params.page !== undefined) queryParts.push(`page=${params.page}`);
    if (params.limit !== undefined) queryParts.push(`limit=${params.limit}`);
    if (params.search !== undefined) queryParts.push(`search=${encodeURIComponent(params.search)}`);
    if (params.category !== undefined) queryParts.push(`category=${encodeURIComponent(params.category)}`);
    if (queryParts.length > 0) {
      url += `?${queryParts.join("&")}`;
    }
  }

  const response = await fetch(url, {
    method: "GET",
    headers: {
      "Accept": "application/json"
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch businesses");
  }

  const result = await response.json();

  const resolveUrl = (path: string | null) => {
    if (!path) return "";
    if (/^https?:\/\//i.test(path)) return path;
    return `${API_ORIGIN}${path.startsWith("/") ? "" : "/"}${path}`;
  };

  const mapBusiness = (b: any) => ({
    ...b,
    logo: resolveUrl(b.logo),
    cover_image: resolveUrl(b.cover_image)
  });

  if (Array.isArray(result)) {
    return result.map(mapBusiness);
  } else if (result && Array.isArray(result.data)) {
    return {
      ...result,
      data: result.data.map(mapBusiness)
    };
  }

  return result;
}

export async function dummyFetchBusinesses() {
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
  const response = await fetch(`${API_BASE_URL}/events`, {
    method: "GET",
    headers: {
      "Accept": "application/json"
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch events");
  }

  const events = await response.json();
  return events.map((event: any) => {
    const formatPrice = (price: string) => {
      if (!price) return "";
      const trimmed = price.trim();
      if (trimmed.toLowerCase() === "free") return "Free";
      if (trimmed.startsWith("₹") || trimmed.startsWith("$")) return trimmed;
      return `₹${trimmed}`;
    };
    return {
      ...event,
      price_normal: formatPrice(event.price_normal),
      price_verified: formatPrice(event.price_verified),
      image: event.image || "https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=800&auto=format&fit=crop"
    };
  });
}

export async function fetchStatistics() {
  const response = await fetch(`${API_BASE_URL}/statistics`, {
    method: "GET",
    headers: {
      "Accept": "application/json"
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch statistics");
  }

  return await response.json();
}

export async function fetchGallery() {
  const response = await fetch(`${API_BASE_URL}/gallery`, {
    method: "GET",
    headers: {
      "Accept": "application/json"
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch gallery");
  }

  return await response.json();
}

export async function fetchHeroImages() {
  const response = await fetch(`${API_BASE_URL}/hero-images`, {
    method: "GET",
    headers: {
      "Accept": "application/json"
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch hero images");
  }

  return await response.json();
}

export async function uploadHeroImage(formData: FormData) {
  const token = localStorage.getItem("sabha_token");
  const response = await fetch(`${API_BASE_URL}/admin/hero-images`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: formData
  });

  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.message || "Failed to upload hero image");
  }

  return await response.json();
}

export async function deleteHeroImage(id: number | string) {
  const token = localStorage.getItem("sabha_token");
  const response = await fetch(`${API_BASE_URL}/admin/hero-images/${id}`, {
    method: "DELETE",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  if (!response.ok) {
    throw new Error("Failed to delete hero image");
  }

  return await response.json();
}

export async function deleteGalleryImage(id: number | string) {
  const token = localStorage.getItem("sabha_token");
  const response = await fetch(`${API_BASE_URL}/admin/gallery/${id}`, {
    method: "DELETE",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  if (!response.ok) {
    throw new Error("Failed to delete gallery image");
  }

  return await response.json();
}

export async function submitBusiness(formData: FormData) {
  const token = localStorage.getItem("sabha_token");
  
  const response = await fetch(`${API_BASE_URL}/businesses`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: formData
  });

  const resData = await response.json();

  if (!response.ok) {
    throw new Error(resData.message || "Failed to submit business details");
  }

  return { success: true, message: resData.message || "Business submitted successfully" };
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
  const token = localStorage.getItem("sabha_token");
  
  const response = await fetch(`${API_BASE_URL}/user/profile`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: data
  });

  const resData = await response.json();

  if (!response.ok) {
    throw new Error(resData.message || "Failed to update profile");
  }

  return {
    success: true,
    message: resData.message || "Profile updated successfully",
    user: resData.user
  };
}

export async function getUserBusiness() {
  const token = localStorage.getItem("sabha_token");
  if (!token) return null;

  const response = await fetch(`${API_BASE_URL}/user/business`, {
    method: "GET",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  if (response.status === 404 || response.status === 401) {
    return null;
  }

  if (!response.ok) {
    throw new Error("Failed to load business details");
  }

  return await response.json();
}

export async function fetchAllBusinessesAdmin() {
  const token = localStorage.getItem("sabha_token");
  
  const response = await fetch(`${API_BASE_URL}/admin/businesses`, {
    method: "GET",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch businesses for admin");
  }

  return await response.json();
}

export async function approveBusiness(id: number) {
  const token = localStorage.getItem("sabha_token");
  
  const response = await fetch(`${API_BASE_URL}/admin/businesses/${id}/approve`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  const resData = await response.json();

  if (!response.ok) {
    throw new Error(resData.message || "Failed to approve business");
  }

  return { success: true };
}

export async function rejectBusiness(id: number, rejectionReason: string) {
  const token = localStorage.getItem("sabha_token");
  
  const response = await fetch(`${API_BASE_URL}/admin/businesses/${id}/reject`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Content-Type": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: JSON.stringify({ rejection_reason: rejectionReason })
  });

  const resData = await response.json();

  if (!response.ok) {
    throw new Error(resData.message || "Failed to reject business");
  }

  return { success: true };
}

export async function createEventAdmin(eventData: any) {
  const token = localStorage.getItem("sabha_token");

  const isFormData = eventData instanceof FormData;

  const headers: Record<string, string> = {
    "Accept": "application/json",
    "Authorization": `Bearer ${token}`
  };

  if (!isFormData) {
    headers["Content-Type"] = "application/json";
  }

  const response = await fetch(`${API_BASE_URL}/admin/events`, {
    method: "POST",
    headers,
    body: isFormData ? eventData : JSON.stringify(eventData)
  });

  const resData = await response.json();

  if (!response.ok) {
    throw new Error(resData.message || "Failed to create event");
  }

  return { success: true };
}

export async function updateEventAdmin(id: number | string, eventData: any) {
  const token = localStorage.getItem("sabha_token");

  const isFormData = eventData instanceof FormData;

  const headers: Record<string, string> = {
    "Accept": "application/json",
    "Authorization": `Bearer ${token}`
  };

  if (!isFormData) {
    headers["Content-Type"] = "application/json";
  }

  const response = await fetch(`${API_BASE_URL}/admin/events/${id}`, {
    method: "POST",
    headers,
    body: isFormData ? eventData : JSON.stringify(eventData)
  });

  const resData = await response.json();

  if (!response.ok) {
    throw new Error(resData.message || "Failed to update event");
  }

  return { success: true };
}

export async function fetchUsersAdmin() {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/admin/users`, {
    method: "GET",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch users");
  }

  return await response.json();
}

export async function uploadGalleryImage(formData: FormData) {
  const token = localStorage.getItem("sabha_token");
  
  try {
    const response = await fetch(`${API_BASE_URL}/gallery/upload`, {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "Authorization": `Bearer ${token}`
      },
      body: formData
    });

    let resData: any = {};
    try {
      resData = await response.json();
    } catch {
      if (response.status === 413) {
        throw new Error("File size is too large for the server. Try uploading a smaller ZIP file (under 10MB) or individual photos.");
      }
      throw new Error(`Server error (${response.status}). Please check PHP file upload limits.`);
    }

    if (!response.ok) {
      throw new Error(resData.message || "Failed to upload gallery media");
    }

    return { 
      success: true, 
      image_url: resData.gallery_image?.image_path 
    };
  } catch (err: any) {
    if (err.name === "TypeError" && (err.message === "Failed to fetch" || err.message?.includes("fetch"))) {
      throw new Error("Failed to upload: The ZIP file size exceeds live server upload limit (Nginx/PHP limit) or network request timed out.");
    }
    throw err;
  }
}

export async function updateStatistic(id: number, statData: { value: string; label: string }) {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/admin/statistics/${id}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: JSON.stringify(statData)
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || "Failed to update statistic");
  }

  return data;
}

export async function fetchSettings() {
  const response = await fetch(`${API_BASE_URL}/settings`, {
    method: "GET",
    headers: {
      "Accept": "application/json"
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch settings");
  }

  return await response.json();
}

export async function updateSettingsAdmin(settingsData: Record<string, any>) {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/admin/settings`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: JSON.stringify({ settings: settingsData })
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || "Failed to update settings");
  }

  return data;
}

export async function fetchReviews(businessId: number) {
  const response = await fetch(`${API_BASE_URL}/businesses/${businessId}/reviews`, {
    method: "GET",
    headers: {
      "Accept": "application/json"
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch reviews");
  }

  return await response.json();
}

export async function submitReview(businessId: number, reviewData: { content: string; rating: number }) {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/businesses/${businessId}/reviews`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: JSON.stringify(reviewData)
  });

  const resData = await response.json();

  if (!response.ok) {
    throw new Error(resData.message || "Failed to submit review");
  }

  return resData;
}

export async function getUserRegistrations() {
  const token = localStorage.getItem("sabha_token");
  if (!token) return [];

  const response = await fetch(`${API_BASE_URL}/user/registrations`, {
    method: "GET",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  if (!response.ok) {
    throw new Error("Failed to load registrations");
  }

  return await response.json();
}

export async function reserveEventSeat(eventId: number, formData: FormData) {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/events/${eventId}/reserve`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: formData
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || "Failed to reserve seat");
  }

  return data;
}

export async function getAllEventRegistrations() {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/admin/registrations`, {
    method: "GET",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  if (!response.ok) {
    throw new Error("Failed to fetch event registrations");
  }

  return await response.json();
}

export async function approveEventRegistration(id: number) {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/admin/registrations/${id}/approve`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || "Failed to approve registration");
  }

  return data;
}

export async function rejectEventRegistration(id: number, reason: string) {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/admin/registrations/${id}/reject`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: JSON.stringify({ rejection_reason: reason })
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || "Failed to reject registration");
  }

  return data;
}

export async function toggleAttendance(id: number) {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/admin/registrations/${id}/toggle-attendance`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    }
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || "Failed to update attendance status");
  }

  return data;
}

export async function checkInTicket(ticketNumber: string) {
  const token = localStorage.getItem("sabha_token");
  if (!token) throw new Error("Authentication required");

  const response = await fetch(`${API_BASE_URL}/admin/registrations/check-in`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: JSON.stringify({ ticket_number: ticketNumber })
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || "Failed to mark attendance");
  }

  return data;
}

export async function submitContactInquiry(inquiryData: {
  name: string;
  email: string;
  subject?: string;
  message: string;
  category?: string;
}) {
  const response = await fetch(`${API_BASE_URL}/contact`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json"
    },
    body: JSON.stringify(inquiryData)
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || "Failed to submit inquiry");
  }

  return data;
}

export async function submitBusinessInquiry(businessId: number, inquiryData: {
  name: string;
  email: string;
  subject: string;
  message: string;
}) {
  const response = await fetch(`${API_BASE_URL}/businesses/${businessId}/inquiry`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json"
    },
    body: JSON.stringify(inquiryData)
  });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || "Failed to submit inquiry");
  }

  return data;
}

// ─── Business Categories ─────────────────────────────────────────────────────

export async function fetchCategories(): Promise<string[]> {
  const response = await fetch(`${API_BASE_URL}/categories`, {
    method: "GET",
    headers: { "Accept": "application/json" }
  });
  const data = await response.json();
  if (!response.ok) throw new Error(data.message || "Failed to fetch categories");
  return data;
}

export async function fetchAdminCategories(): Promise<{ id: number; name: string; sort_order: number; is_active: boolean }[]> {
  const token = localStorage.getItem("sabha_token");
  const response = await fetch(`${API_BASE_URL}/admin/categories`, {
    method: "GET",
    headers: {
      "Accept": "application/json",
      ...(token && { Authorization: `Bearer ${token}` })
    }
  });
  const data = await response.json();
  if (!response.ok) throw new Error(data.message || "Failed to fetch admin categories");
  return data;
}

export async function storeCategory(name: string): Promise<any> {
  const token = localStorage.getItem("sabha_token");
  const response = await fetch(`${API_BASE_URL}/admin/categories`, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Content-Type": "application/json",
      ...(token && { Authorization: `Bearer ${token}` })
    },
    body: JSON.stringify({ name })
  });
  const data = await response.json();
  if (!response.ok) throw new Error(data.message || "Failed to add category");
  return data;
}

export async function deleteCategory(id: number): Promise<any> {
  const token = localStorage.getItem("sabha_token");
  const response = await fetch(`${API_BASE_URL}/admin/categories/${id}`, {
    method: "DELETE",
    headers: {
      "Accept": "application/json",
      ...(token && { Authorization: `Bearer ${token}` })
    }
  });
  const data = await response.json();
  if (!response.ok) throw new Error(data.message || "Failed to delete category");
  return data;
}

"use client";

import { useParams, useRouter } from "next/navigation";
import {
  ArrowLeft, MapPin, Globe, Mail, Clock,
  ShieldCheck, Star, Briefcase, Zap,
  MessageCircle, ArrowUpRight, Download,
  Phone, User, MessageSquare, Award, CheckCircle2, Share2,
} from "lucide-react";
import { InstagramIcon, YoutubeIcon, TwitterIcon, LinkedinIcon, WhatsappIcon } from "@/components/SocialIcons";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import { useState, useEffect, useMemo } from "react";
import { fetchBusinesses, fetchReviews, submitReview, submitBusinessInquiry } from "@/lib/api";
import { assetUrl } from "@/lib/config";
import { useAuth } from "@/lib/auth";
import { getCoverImage } from "@/lib/categoryCover";
import { useLanguage } from "@/lib/language";

interface ServiceItem {
  title: string;
  desc: string;
}

interface Review {
  reviewer: string;
  role: string;
  content: string;
  rating: number;
}

interface Member {
  name: string;
  role: string;
  avatar: string;
}

interface BusinessDetail {
  id: number;
  name: string;
  category: string;
  location: string;
  rating: number;
  reviews: number;
  verified: boolean;
  tagline: string;
  about: string;
  founded: string;
  teamSize: string;
  industry: string;
  projects: string;
  services: ServiceItem[];
  hours: string;
  website: string;
  phone: string;
  email: string;
  linkedin: string;
  bannerImage: string;
  reviewsList: Review[];
  member?: Member | null;
  logo?: string;
  instagram?: string;
  youtube?: string;
  twitter?: string;
  whatsapp?: string;
}

const detailedBusinesses: BusinessDetail[] = [
  {
    id: 1,
    name: "Vertex Solutions",
    category: "Software Development",
    location: "Mumbai",
    rating: 4.8,
    reviews: 24,
    verified: true,
    tagline: "Enterprise Cloud Architecture & Digital Transformations",
    about: "Vertex Solutions is a premier technology partner specializing in cloud engineering, system integrations, and bespoke SaaS platforms. We help startups and enterprises scale their infrastructure for high throughput, security, and global availability. Our certified team leverages the best engineering practices to accelerate product development cycles.",
    founded: "2018",
    teamSize: "45+ engineers",
    industry: "Information Technology",
    projects: "120+ projects",
    services: [
      { title: "Cloud Migration", desc: "Seamless migration of database clusters and application workloads to AWS and GCP with zero downtime." },
      { title: "Custom ERP Solutions", desc: "Centralized resource management systems built using Laravel and React for optimized business operations." },
      { title: "AI Stack Integration", desc: "Automating workflows and customer service pipelines using OpenAI LLMs and advanced vector databases." },
      { title: "SaaS Product Design", desc: "Multi-tenant dashboard platforms with strict tenant isolation and secure Stripe payment processing." }
    ],
    hours: "9:00 AM - 6:00 PM (Mon - Fri)",
    website: "https://vertex.solutions",
    phone: "+91 22 5550 1928",
    email: "hello@vertex.solutions",
    linkedin: "/company/vertex-solutions",
    bannerImage: "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1200&auto=format&fit=crop",
    reviewsList: [
      { reviewer: "Ravi Sharma", role: "Founder, TechWave", content: "Vertex Solutions helped us migrate our legacy database to AWS RDS. The transition was seamless, and our query latencies decreased by 40%. Highly recommended!", rating: 5 },
      { reviewer: "Pooja Verma", role: "CEO, DesignFlow", content: "Working with the Vertex team on our internal ERP was a great experience. They delivered on time and communicated effectively throughout.", rating: 4 }
    ],
    member: {
      name: "Ravi Sharma",
      role: "Founder & CEO, Vertex Solutions",
      avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150&auto=format&fit=crop"
    }
  },
  {
    id: 2,
    name: "Global Logistics",
    category: "Supply Chain",
    location: "Delhi",
    rating: 4.5,
    reviews: 18,
    verified: true,
    tagline: "Real-Time Tracking & Automated Global Distribution Hubs",
    about: "Global Logistics provides end-to-end logistics, cargo routing, and supply chain management services. Utilizing automated warehousing and AI-driven route optimization, we ensure your products reach global markets faster, safer, and with complete visibility at every stage of transit.",
    founded: "2015",
    teamSize: "180+ staff",
    industry: "Logistics & Transport",
    projects: "15,000+ tons shipped",
    services: [
      { title: "Cargo Forwarding", desc: "Multi-modal air, sea, and land cargo transport with complete customs clearance handling." },
      { title: "Smart Warehousing", desc: "IoT-enabled warehouse facilities offering temperature controls and automated stock tracking." },
      { title: "Last-Mile B2B Delivery", desc: "Express delivery services for corporate supply chains with real-time fleet coordinates." },
      { title: "Supply Chain Audits", desc: "Analytical evaluations to streamline raw material procurement and minimize overheads." }
    ],
    hours: "8:00 AM - 7:00 PM (Mon - Sat)",
    website: "https://globallogistics.com",
    phone: "+91 11 4488 9200",
    email: "ops@globallogistics.com",
    linkedin: "/company/global-logistics-group",
    bannerImage: "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1200&auto=format&fit=crop",
    reviewsList: [
      { reviewer: "Amit Shah", role: "Director, BuildCo", content: "Reliable, secure, and always on time. Their custom tracking portal makes monitoring material shipments incredibly straightforward.", rating: 5 },
      { reviewer: "Karan Mehta", role: "Founder, Zenith", content: "Excellent warehouse security and helpful customer support when dealing with tricky import customs clearances.", rating: 4 }
    ],
    member: {
      name: "Amit Shah",
      role: "Director of Operations",
      avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=150&auto=format&fit=crop"
    }
  },
  {
    id: 3,
    name: "Nexus Marketing",
    category: "Digital Marketing",
    location: "Bangalore",
    rating: 4.2,
    reviews: 12,
    verified: false,
    tagline: "Data-Driven Performance Marketing & Growth Strategies",
    about: "Nexus Marketing is a results-oriented digital agency focusing on search engine visibility, paid acquisition, and content-led growth. We turn traffic into revenue through systematic conversion rate optimization and creative brand campaigns.",
    founded: "2020",
    teamSize: "22 specialists",
    industry: "Marketing & Advertising",
    projects: "80+ brands scaled",
    services: [
      { title: "SEO Campaigning", desc: "Deep content audits and off-page backlinking to rank your primary services on Google Page 1." },
      { title: "Paid Acquisition", desc: "High-ROI Google, Meta, and LinkedIn ad management to reduce acquisition costs." },
      { title: "Creative Production", desc: "Designing converting landing pages, email copy, and motion graphics for social channels." },
      { title: "CRO & UX Auditing", desc: "Comprehensive user experience reviews to convert standard site visitors into buyers." }
    ],
    hours: "10:00 AM - 6:30 PM (Mon - Fri)",
    website: "https://nexusmarketing.io",
    phone: "+91 80 5002 8111",
    email: "growth@nexusmarketing.io",
    linkedin: "/company/nexus-marketing-global",
    bannerImage: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1200&auto=format&fit=crop",
    reviewsList: [
      { reviewer: "Sara Khan", role: "Partner, Summit", content: "Our lead generation increased by 60% within three months of hiring Nexus. They really understand paid performance advertising.", rating: 5 }
    ],
    member: {
      name: "Neha Gupta",
      role: "Managing Partner",
      avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=150&auto=format&fit=crop"
    }
  },
  {
    id: 4,
    name: "Prime Builders",
    category: "Construction",
    location: "Pune",
    rating: 4.9,
    reviews: 31,
    verified: true,
    tagline: "Sustainable Commercial Infrastructure & Eco-friendly Buildings",
    about: "Prime Builders is an award-winning civil construction and engineering firm. We design and construct green-certified commercial office spaces, warehouse hubs, and retail malls utilizing eco-friendly materials and renewable energy integration.",
    founded: "2010",
    teamSize: "95+ builders",
    industry: "Real Estate & Construction",
    projects: "45+ commercial hubs built",
    services: [
      { title: "Architectural Planning", desc: "Biophilic and structural blueprints prioritizing green energy and natural ventilation." },
      { title: "Commercial EPC", desc: "End-to-end engineering, procurement, and construction services for high-capacity hubs." },
      { title: "Smart Retrofitting", desc: "Modifying existing commercial buildings to install solar arrays and smart HVAC systems." },
      { title: "Zoning Consulting", desc: "Securing environmental clearance, building code compliance, and structural approvals." }
    ],
    hours: "10:00 AM - 6:00 PM (Mon - Sat)",
    website: "https://primebuilders.in",
    phone: "+91 20 6699 3010",
    email: "build@primebuilders.in",
    linkedin: "/company/primebuilders-co",
    bannerImage: "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?q=80&w=1200&auto=format&fit=crop",
    reviewsList: [
      { reviewer: "Neha Gupta", role: "Marketing Lead, Nexus", content: "They completed our corporate headquarters six weeks ahead of schedule. Professional engineering at its finest.", rating: 5 },
      { reviewer: "Dev Patel", role: "Engineer, CloudOps", content: "Impressive focus on sustainability and structural energy efficiency. Highly recommend them for commercial builds.", rating: 5 }
    ],
    member: {
      name: "Pooja Verma",
      role: "Chief Architect & CEO",
      avatar: "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=150&auto=format&fit=crop"
    }
  },
  {
    id: 5,
    name: "Zenith Finance",
    category: "Financial Services",
    location: "Mumbai",
    rating: 4.6,
    reviews: 15,
    verified: false,
    tagline: "Corporate Wealth Management & Strategic Advisory",
    about: "Zenith Finance provides financial consulting, tax planning, and investment management for private firms and high-net-worth individuals. We align financial structures with long-term scaling targets.",
    founded: "2012",
    teamSize: "18 advisors",
    industry: "Financial Services",
    projects: "₹500Cr+ assets managed",
    services: [
      { title: "Capital Raising", desc: "Advising on venture debt, angel equity rounds, and banking credit facility structures." },
      { title: "Corporate Tax Auditing", desc: "Optimizing structures to minimize tax liabilities while maintaining complete compliance." },
      { title: "Asset Management", desc: "Diversified investments across equities, fixed-income yields, and real assets." },
      { title: "Mergers & Valuations", desc: "Evaluating acquisition targets, handling valuations, and strategic mergers." }
    ],
    hours: "9:00 AM - 6:00 PM (Mon - Fri)",
    website: "https://zenithfinance.com",
    phone: "+91 22 6609 5019",
    email: "advisory@zenithfinance.com",
    linkedin: "/company/zenith-financial-advisory",
    bannerImage: "https://images.unsplash.com/photo-1559526324-4b87b5e36e44?q=80&w=1200&auto=format&fit=crop",
    reviewsList: [
      { reviewer: "Ravi Sharma", role: "CEO, TechWave", content: "Zenith structured our Series A round. Excellent attention to details and deep banking connections.", rating: 5 }
    ],
    member: {
      name: "Karan Mehta",
      role: "Managing Director",
      avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=150&auto=format&fit=crop"
    }
  },
  {
    id: 6,
    name: "Eco Energy",
    category: "Renewables",
    location: "Hyderabad",
    rating: 4.7,
    reviews: 20,
    verified: true,
    tagline: "Commercial Solar Power & Smart Grid Solutions",
    about: "Eco Energy designs, installs, and maintains industrial solar installations and energy storage grids. We help businesses slash their utility bills and achieve carbon-neutral operations through clean energy.",
    founded: "2017",
    teamSize: "35+ technical staff",
    industry: "Energy & Utilities",
    projects: "25MW+ installed capacity",
    services: [
      { title: "Solar Installation", desc: "Rooftop and ground-mounted solar panels for industrial plants and offices." },
      { title: "Power Storage Systems", desc: "High-capacity lithium battery storage setups for off-grid operations." },
      { title: "Energy Audits", desc: "Comprehensive evaluation of energy leakage and power efficiency recommendations." },
      { title: "Carbon Offsetting", desc: "Certified carbon credit investments and ESG reporting frameworks." }
    ],
    hours: "9:00 AM - 5:30 PM (Mon - Fri)",
    website: "https://ecoenergy.co",
    phone: "+91 40 4455 1289",
    email: "solar@ecoenergy.co",
    linkedin: "/company/eco-energy-india",
    bannerImage: "https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=1200&auto=format&fit=crop",
    reviewsList: [
      { reviewer: "Amit Shah", role: "Director, BuildCo", content: "They retrofitted our warehouses with solar panels. Our electricity costs dropped by 45% in the first quarter itself.", rating: 5 }
    ],
    member: {
      name: "Dev Patel",
      role: "Head of Engineering",
      avatar: "https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=150&auto=format&fit=crop"
    }
  },
  {
    id: 7,
    name: "Summit Consulting",
    category: "Business Strategy",
    location: "Mumbai",
    rating: 4.4,
    reviews: 10,
    verified: true,
    tagline: "Operational Scaling, Leadership Development & Restructuring",
    about: "Summit Consulting is a management advisory firm dedicated to helping mid-market enterprises resolve bottleneck operations, restructure organizational frameworks, and execute high-growth strategies.",
    founded: "2014",
    teamSize: "15 principal consultants",
    industry: "Management Consulting",
    projects: "120+ corporate audits",
    services: [
      { title: "Workflow Analysis", desc: "Time-motion analysis and workflow evaluations to cut waste and improve efficiency." },
      { title: "Executive Mentorship", desc: "Mentorship for corporate leaders and strategic succession planning." },
      { title: "Market Entry Strategy", desc: "Structured strategic planning models detailing market entry and product expansion." },
      { title: "Corporate Alignment", desc: "Optimizing departments and reporting lines for improved organizational agility." }
    ],
    hours: "9:00 AM - 6:00 PM (Mon - Fri)",
    website: "https://summitconsulting.in",
    phone: "+91 22 5590 1900",
    email: "partner@summitconsulting.in",
    linkedin: "/company/summit-strategy-group",
    bannerImage: "https://images.unsplash.com/photo-1454165833762-02ad50e8958?q=80&w=1200&auto=format&fit=crop",
    reviewsList: [
      { reviewer: "Karan Mehta", role: "CEO, Zenith", content: "Summit helped us reorganize our operations during a merger. Highly objective and deeply analytical.", rating: 4 }
    ],
    member: {
      name: "Sara Khan",
      role: "Senior Partner",
      avatar: "https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=150&auto=format&fit=crop"
    }
  },
  {
    id: 8,
    name: "Alpha Tech Lab",
    category: "Software Development",
    location: "Chennai",
    rating: 4.9,
    reviews: 45,
    verified: true,
    tagline: "Bespoke SaaS Platforms & IoT Hardware Integrations",
    about: "Alpha Tech Lab is a software R&D laboratory building complex web systems, connected IoT devices, and deep-learning platforms. We combine agile engineering with bleeding-edge stack development.",
    founded: "2019",
    teamSize: "30+ programmers",
    industry: "Information Technology",
    projects: "60+ products built",
    services: [
      { title: "SaaS Engineering", desc: "Multi-tenant web applications with secure payment processing and detailed analytics dashboards." },
      { title: "IoT Firmware Development", desc: "Embedded hardware programming for smart sensors and industrial automation." },
      { title: "Penetration Auditing", desc: "Penetration testing, source code auditing, and encryption compliance protocols." },
      { title: "DevOps Pipelines", desc: "Automating builds, tests, and cloud deployments with Kubernetes and Terraform." }
    ],
    hours: "10:00 AM - 7:00 PM (Mon - Fri)",
    website: "https://alphatechlab.com",
    phone: "+91 44 4450 1928",
    email: "contact@alphatechlab.com",
    linkedin: "/company/alpha-tech-lab-r-d",
    bannerImage: "https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=1200&auto=format&fit=crop",
    reviewsList: [
      { reviewer: "Ravi Sharma", role: "Founder, TechWave", content: "The Alpha Tech Lab team is unmatched in their technical capability. They helped us develop a custom sensor firmware in record time.", rating: 5 },
      { reviewer: "Dev Patel", role: "Engineer, CloudOps", content: "Clean code, excellent automated testing pipeline, and deep domain knowledge in container orchestration.", rating: 5 }
    ],
    member: {
      name: "Aisha Rao",
      role: "Chief R&D Officer",
      avatar: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=150&auto=format&fit=crop"
    }
  }
];

export default function BusinessDetailsPage() {
  const { id } = useParams();
  const router = useRouter();
  const { t } = useLanguage();
  const { user, isAuthenticated, openLogin } = useAuth();

  const [business, setBusiness] = useState<BusinessDetail | null>(null);
  const [loading, setLoading] = useState(true);

  // Form states
  const [formSubmitted, setFormSubmitted] = useState(false);
  const [formSubmitting, setFormSubmitting] = useState(false);
  const [formError, setFormError] = useState("");
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [subject, setSubject] = useState("");
  const [msg, setMsg] = useState("");

  // Review states
  const [reviewName, setReviewName] = useState("");
  const [reviewRating, setReviewRating] = useState(5);
  const [reviewContent, setReviewContent] = useState("");
  const [dbReviews, setDbReviews] = useState<any[]>([]);
  const [reviewSubmitted, setReviewSubmitted] = useState(false);
  const [reviewSubmitting, setReviewSubmitting] = useState(false);
  const [reviewError, setReviewError] = useState("");

  const allReviews = useMemo(() => {
    return [...dbReviews, ...(business?.reviewsList || [])];
  }, [dbReviews, business?.reviewsList]);

  const hasUserReviewed = useMemo(() => {
    if (!isAuthenticated || !user) return false;
    return allReviews.some(
      (rev: any) =>
        (rev.reviewer?.toLowerCase() === user.name?.toLowerCase()) ||
        (rev.user?.name?.toLowerCase() === user.name?.toLowerCase())
    );
  }, [allReviews, user, isAuthenticated]);

  useEffect(() => {
    async function loadBusiness() {
      try {
        setLoading(true);
        const [list, reviewsData] = await Promise.all([
          fetchBusinesses(),
          fetchReviews(Number(id)).catch(() => [])
        ]);
        setDbReviews(reviewsData || []);

        const matched = list.find((b: any) => b.id.toString() === id) as any;
        if (matched) {
          // Map backend services (since services is stored as a JSON array in Laravel)
          let coreServices: ServiceItem[] = [];
          let servicesData: any = [];
          if (Array.isArray(matched.services)) {
            servicesData = matched.services;
          } else if (typeof matched.services === "string" && matched.services) {
            try {
              servicesData = JSON.parse(matched.services);
            } catch (e) {
              servicesData = matched.services.split(",").map((s: string) => s.trim());
            }
          }

          if (Array.isArray(servicesData)) {
            coreServices = servicesData.map((s: any) => {
              if (s && typeof s === "object") {
                return {
                  title: s.title || "",
                  desc: s.desc || (s.title ? `Vetted capability in ${s.title}` : "")
                };
              } else {
                const titleStr = String(s || "");
                return {
                  title: titleStr,
                  desc: `Vetted capability in ${titleStr}`
                };
              }
            }).filter((s: any) => s.title);
          }

          // Fallback to detailed mock data if it aligns, for extra services/banners/reviews fallback
          const mockDetail = detailedBusinesses.find(
            b => b.name.toLowerCase() === matched.name.toLowerCase() || b.id.toString() === id
          );

          const logoUrl = assetUrl(matched.logo);
          const coverUrl = assetUrl(matched.cover_image);

          // Populate the business details dynamically
          setBusiness({
            id: matched.id,
            name: matched.name,
            category: matched.category,
            location: matched.location || "",
            rating: mockDetail ? mockDetail.rating : 5.0,
            reviews: mockDetail ? mockDetail.reviews : 0,
            verified: matched.is_verified,
            tagline: matched.tagline || "",
            about: matched.description || "",
            founded: matched.founded || "",
            teamSize: matched.team_size || "",
            industry: matched.category,
            projects: matched.projects || "",
            services: coreServices,
            hours: matched.hours || "",
            website: matched.website || "",
            phone: matched.phone || "",
            email: matched.email || "",
            linkedin: matched.linkedin || "",
            instagram: matched.instagram || "",
            youtube: matched.youtube || "",
            twitter: matched.twitter || "",
            whatsapp: matched.whatsapp || "",
            bannerImage: getCoverImage(coverUrl, matched.category),
            logo: logoUrl || "",
            reviewsList: mockDetail ? mockDetail.reviewsList : [],
            member: matched.user ? {
              name: matched.user.name,
              role: `${matched.user.designation || 'SABHA Member'}, ${matched.user.company || 'Member Company'}`,
              avatar: matched.user.avatar
                ? (matched.user.avatar.startsWith("http") ? matched.user.avatar : assetUrl(matched.user.avatar))
                : "https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=150&auto=format&fit=crop"
            } : null
          });
        } else {
          setBusiness(null);
        }
      } catch (e) {
        console.error("Failed to load business details:", e);
      } finally {
        setLoading(false);
      }
    }
    if (id) {
      loadBusiness();
    }
  }, [id]);

  useEffect(() => {
    if (user?.name) {
      setReviewName(user.name);
    } else {
      setReviewName("");
    }
  }, [user]);

  const handleSendMessage = async (e: React.FormEvent) => {
    e.preventDefault();
    setFormSubmitting(true);
    setFormError("");
    setFormSubmitted(false);

    try {
      await submitBusinessInquiry(Number(id), {
        name,
        email,
        subject,
        message: msg
      });
      setFormSubmitted(true);
      setName("");
      setEmail("");
      setSubject("");
      setMsg("");
      setTimeout(() => setFormSubmitted(false), 5000);
    } catch (err: any) {
      setFormError(err.message || "Failed to send inquiry. Please try again later.");
    } finally {
      setFormSubmitting(false);
    }
  };

  const handleAddReview = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!isAuthenticated) {
      openLogin();
      return;
    }
    if (!reviewContent.trim()) {
      setReviewError("Recommendation text is required.");
      return;
    }
    if (reviewContent.trim().length < 5) {
      setReviewError("Recommendation text must be at least 5 characters long.");
      return;
    }
    setReviewSubmitting(true);
    setReviewError("");

    try {
      const res = await submitReview(Number(id), {
        rating: reviewRating,
        content: reviewContent
      });
      setDbReviews(prev => [res.review, ...prev]);
      setReviewSubmitted(true);
      setReviewContent("");
      setTimeout(() => setReviewSubmitted(false), 5000);
    } catch (err: any) {
      setReviewError(err.message || t("businessDetail.review_failed"));
    } finally {
      setReviewSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background font-outfit">
        <div className="text-center space-y-3">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="text-sm font-medium text-muted">{t("businessDetail.loading")}</p>
        </div>
      </div>
    );
  }

  if (!business) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center bg-background font-outfit text-center p-6">
        <h2 className="text-2xl font-bold text-foreground">{t("businessDetail.not_found_title")}</h2>
        <p className="mt-2 text-sm text-muted">{t("businessDetail.not_found_desc")}</p>
        <button
          onClick={() => router.push("/businesses")}
          className="mt-6 inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]"
        >
          {t("businessDetail.back_to_directory")}
        </button>
      </div>
    );
  }



  return (
    <div className="min-h-screen bg-background font-outfit">
      {/* Cover Banner with Profile Bar */}
      <section className="relative h-80 sm:h-96 lg:h-[28rem] w-full overflow-hidden bg-slate-900">
        <img
          src={business.bannerImage}
          alt={`${business.name} workspace`}
          className="h-full w-full object-cover"
        />
        {/* Shadow gradient at bottom */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />

        {/* Back button */}
        <div className="absolute top-6 left-6 z-10">
          <button
            onClick={() => router.back()}
            className="group inline-flex items-center gap-1.5 rounded-lg bg-black/30 backdrop-blur-md px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-black/50"
          >
            <ArrowLeft className="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
            {t("businessDetail.back_to_directory")}
          </button>
        </div>

        {/* Profile Bar at bottom of cover */}
        <div className="absolute bottom-0 left-0 right-0 z-10">
          <div className="mx-auto max-w-7xl px-6 pb-6">
            <div className="flex flex-col md:flex-row md:items-end gap-5">
              <div className="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl overflow-hidden bg-gradient-to-tr from-primary to-primary-dark text-white text-4xl sm:text-5xl font-extrabold flex items-center justify-center border-4 border-white/20 shadow-2xl shrink-0 select-none backdrop-blur-sm">
                {business.logo ? (
                  <img src={business.logo} alt={business.name} className="h-full w-full object-cover" />
                ) : (
                  business.name?.[0] ?? "?"
                )}
              </div>

              {/* Name & Details */}
              <div className="flex-1 space-y-1.5 md:pb-1">
                <div className="flex flex-wrap items-center gap-2.5">
                  {business.verified && (
                    <span className="inline-flex items-center gap-1 rounded-full bg-white/15 backdrop-blur-sm px-2.5 py-0.5 text-[10px] font-bold text-white border border-white/20">
                      <ShieldCheck className="h-3 w-3" /> {t("businessDetail.verified")}
                    </span>
                  )}
                  <span className="inline-flex items-center gap-1 text-[10px] font-bold text-amber-300 bg-amber-500/15 backdrop-blur-sm px-2.5 py-0.5 rounded-full border border-amber-400/20">
                    <Star className="h-3 w-3 fill-current" /> {business.rating} ({allReviews.length})
                  </span>
                </div>
                <h1 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight drop-shadow-lg">
                  {business.name}
                </h1>
                {business.tagline && (
                  <p className="text-sm font-semibold text-white/70">
                    {business.tagline}
                  </p>
                )}
                <div className="flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-white/60 font-medium">
                  {business.location && <span className="inline-flex items-center gap-1"><MapPin className="h-3 w-3" /> {business.location}</span>}
                  {business.category && <span className="inline-flex items-center gap-1"><Briefcase className="h-3 w-3" /> {business.category}</span>}
                  {business.hours && <span className="inline-flex items-center gap-1"><Clock className="h-3 w-3" /> {t("businessDetail.open")} {business.hours.split(" (")[0]}</span>}
                </div>
              </div>

              {/* Connection Actions */}
              <div className="flex gap-2 shrink-0 md:pb-1">
                {business.phone && (
                  <a
                    href={`tel:${business.phone}`}
                    className="group inline-flex items-center justify-center gap-1.5 rounded-xl bg-white px-5 py-3 text-xs font-bold text-slate-900 shadow-lg transition-all hover:bg-primary hover:text-white active:scale-[0.98]"
                  >
                    <Phone className="h-3.5 w-3.5" />
                    {t("businessDetail.connect_now")}
                  </a>
                )}
                {business.email && (
                  <a
                    href={`mailto:${business.email}`}
                    className="flex items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 px-3.5 py-3 text-white transition-colors hover:bg-white/25 shadow-sm"
                    aria-label="Email"
                    title="Send Email"
                  >
                    <Mail size={16} />
                  </a>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Main Grid Content */}
      <div className="mx-auto max-w-7xl px-6 py-6">
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          
          {/* Main Column */}
          <div className="space-y-6 lg:col-span-2">
            
            {/* About Section */}
            {business.about && (
              <section className="space-y-2">
                <div className="flex items-center gap-2">
                  <span className="h-4 w-1 rounded-full bg-primary" />
                  <h2 className="text-base font-bold text-foreground">{t("businessDetail.about_company")}</h2>
                </div>
                <p className="text-xs leading-relaxed text-muted">
                  {business.about}
                </p>
              </section>
            )}

            {/* Highlights/Key Stats Grid */}
            {(business.founded || business.teamSize || business.industry) && (
              <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 pt-2">
                {business.founded && (
                  <div className="glass-card p-3 text-center">
                    <span className="text-[9px] font-semibold uppercase tracking-wider text-muted">{t("businessDetail.founded")}</span>
                    <p className="mt-0.5 text-xs font-bold text-foreground">{business.founded}</p>
                  </div>
                )}
                {business.teamSize && (
                  <div className="glass-card p-3 text-center">
                    <span className="text-[9px] font-semibold uppercase tracking-wider text-muted">{t("businessDetail.team_size")}</span>
                    <p className="mt-0.5 text-xs font-bold text-foreground">{business.teamSize}</p>
                  </div>
                )}
                {business.industry && (
                  <div className="glass-card p-3 text-center">
                    <span className="text-[9px] font-semibold uppercase tracking-wider text-muted">{t("businessDetail.industry")}</span>
                    <p className="mt-0.5 text-xs font-bold text-foreground truncate">{business.industry.split(" & ")[0]}</p>
                  </div>
                )}
              </div>
            )}

            {/* Services Grid */}
            {business.services && business.services.length > 0 && (
              <section className="space-y-2.5">
                <div className="flex items-center gap-2">
                  <span className="h-4 w-1 rounded-full bg-primary" />
                  <h2 className="text-base font-bold text-foreground">{t("businessDetail.core_services")}</h2>
                </div>
                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                  {business.services.map((service, i) => (
                    <div key={i} className="glass-card flex items-start gap-2.5 p-3">
                      <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                        <Zap className="h-3.5 w-3.5" />
                      </div>
                      <div className="space-y-0.5">
                        <h3 className="text-xs font-bold text-foreground">{service.title}</h3>
                        {service.desc && <p className="text-[11px] text-muted leading-normal">{service.desc}</p>}
                      </div>
                    </div>
                  ))}
                </div>
              </section>
            )}

            {/* Reviews list & submit review */}
            <section className="space-y-4">
              <div className="flex items-center gap-2">
                <span className="h-4 w-1 rounded-full bg-primary" />
                <h2 className="text-base font-bold text-foreground">{t("businessDetail.member_recommendations")}</h2>
              </div>

              {/* Add review form */}
              <div className="glass-card p-4 space-y-3 bg-surface/30">
                <h3 className="text-xs font-bold text-foreground inline-flex items-center gap-1.5">
                  <MessageSquare className="h-4 w-4 text-primary" /> {t("businessDetail.recommend_business")}
                </h3>
                {hasUserReviewed ? (
                  <div className="rounded-xl border border-dashed border-emerald-200 bg-emerald-50/50 p-4 text-center text-xs font-semibold text-emerald-800 flex items-center justify-center gap-1.5">
                    <CheckCircle2 className="h-4 w-4 text-emerald-600" /> {t("businessDetail.already_reviewed")}
                  </div>
                ) : (
                  <form onSubmit={handleAddReview} className="space-y-3">
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                      <div className="space-y-1">
                        <label className="text-xs font-semibold text-muted">{t("businessDetail.your_name")}</label>
                        <input
                          type="text"
                          placeholder="John Doe"
                          value={reviewName}
                          onChange={(e) => setReviewName(e.target.value)}
                          disabled={isAuthenticated}
                          className="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary disabled:bg-slate-50 disabled:text-muted"
                        />
                      </div>
                      <div className="space-y-1">
                        <label className="text-xs font-semibold text-muted">{t("businessDetail.rating")}</label>
                        <select
                          value={reviewRating}
                          onChange={(e) => setReviewRating(Number(e.target.value))}
                          className="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary"
                        >
                          {[5, 4, 3, 2, 1].map((r) => (
                            <option key={r} value={r}>{r} {t("businessDetail.stars")}</option>
                          ))}
                        </select>
                      </div>
                    </div>
                    <div className="space-y-1">
                      <label className="text-xs font-semibold text-muted">{t("businessDetail.recommendation_text")}</label>
                      <textarea
                        required
                        rows={3}
                        placeholder={t("businessDetail.recommendation_placeholder")}
                        value={reviewContent}
                        onChange={(e) => setReviewContent(e.target.value)}
                        className="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary resize-none"
                      />
                    </div>
                    <div className="flex flex-col gap-2">
                      <div className="flex items-center gap-3">
                        <button
                          type="submit"
                          disabled={reviewSubmitting}
                          className="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                        >
                          {reviewSubmitting ? t("common.loading") : t("businessDetail.submit_review")}
                        </button>
                        <AnimatePresence>
                          {reviewSubmitted && (
                            <motion.span
                              initial={{ opacity: 0, x: -5 }}
                              animate={{ opacity: 1, x: 0 }}
                              exit={{ opacity: 0 }}
                              className="text-xs text-green-600 font-semibold inline-flex items-center gap-1"
                            >
                              <CheckCircle2 className="h-3.5 w-3.5" /> {t("businessDetail.review_submitted")}
                            </motion.span>
                          )}
                        </AnimatePresence>
                      </div>
                      <AnimatePresence>
                        {reviewError && (
                          <motion.p
                            initial={{ opacity: 0, y: -5 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0 }}
                            className="text-xs text-red-600 font-semibold"
                          >
                            {reviewError}
                          </motion.p>
                        )}
                      </AnimatePresence>
                    </div>
                  </form>
                )}
              </div>

              {/* Reviews items list */}
              <div className="space-y-3.5">
                {allReviews.map((rev: any, idx) => {
                  const reviewerName = rev.reviewer || rev.user?.name || t("businessDetail.anonymous_member");
                  const reviewerRole = rev.role || t("businessDetail.verified_member");
                  return (
                    <div key={idx} className="glass-card p-5 space-y-3">
                      <div className="flex justify-between items-start">
                        <div className="flex items-center gap-3">
                          <div className="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center font-bold text-xs text-primary shrink-0 select-none">
                            {reviewerName[0]}
                          </div>
                          <div>
                            <h4 className="text-sm font-bold text-foreground leading-none">{reviewerName}</h4>
                            <span className="text-[10px] text-muted leading-none mt-0.5 inline-block">{reviewerRole}</span>
                          </div>
                        </div>
                      <div className="flex gap-0.5 text-amber-500 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200/30">
                        {[...Array(5)].map((_, i) => (
                          <Star
                            key={i}
                            className={`h-3 w-3 ${i < rev.rating ? "fill-current" : "text-amber-200"}`}
                          />
                        ))}
                      </div>
                    </div>
                    <p className="text-xs text-muted leading-relaxed font-medium bg-surface/40 p-3 rounded-lg">
                      "{rev.content}"
                    </p>
                  </div>
                );
              })}
              </div>
            </section>
          </div>

          {/* Sidebar Column */}
          <div className="space-y-4">

            {/* Listed by Member Card */}
            {business.member && (
              <div className="glass-card p-4 space-y-3">
                <h3 className="border-b border-border pb-2.5 text-xs font-bold text-foreground">{t("businessDetail.listed_by")}</h3>
                <div className="flex items-center gap-4">
                  <img
                    src={business.member.avatar}
                    alt={business.member.name}
                    className="h-14 w-14 rounded-full object-cover border border-border shadow-sm shrink-0"
                  />
                  <div className="min-w-0 flex-1">
                    <h4 className="text-sm font-bold text-slate-900 truncate">{business.member.name}</h4>
                    <p className="text-xs text-primary font-semibold truncate mt-0.5">{business.member.role}</p>
                    <span className="inline-flex items-center gap-1 text-[10px] text-muted-foreground mt-1">
                      <ShieldCheck className="h-3 w-3 text-green-500 fill-green-50/50" /> {t("businessDetail.verified_member")}
                    </span>
                  </div>
                </div>
              </div>
            )}

            {/* Contact Details Card */}
            {/* Contact Details Card */}
            {(business.website || business.email || business.phone || business.linkedin || business.location) && (
              <div className="glass-card p-4">
                <h3 className="border-b border-border pb-2.5 text-xs font-bold text-foreground">{t("businessDetail.contact_channels")}</h3>
                <div className="mt-4 space-y-3">
                  {business.website && (
                    <div className="flex items-center gap-3">
                      <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                        <Globe size={18} />
                      </div>
                      <div className="min-w-0">
                        <p className="text-[10px] font-semibold text-muted leading-none">{t("businessDetail.website")}</p>
                        <Link
                          href={business.website}
                          target="_blank"
                          className="mt-1 text-xs font-semibold text-foreground transition-colors hover:text-primary truncate block"
                        >
                          {business.website.replace("https://", "").replace("http://", "")}
                        </Link>
                      </div>
                    </div>
                  )}

                  {business.email && (
                    <div className="flex items-center gap-3">
                      <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                        <Mail size={18} />
                      </div>
                      <div className="min-w-0">
                        <p className="text-[10px] font-semibold text-muted leading-none">{t("businessDetail.email_address")}</p>
                        <a
                          href={`mailto:${business.email}`}
                          className="mt-1 text-xs font-semibold text-foreground transition-colors hover:text-primary truncate block"
                        >
                          {business.email}
                        </a>
                      </div>
                    </div>
                  )}

                  {business.phone && (
                    <div className="flex items-center gap-3">
                      <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                        <Phone size={18} />
                      </div>
                      <div className="min-w-0">
                        <p className="text-[10px] font-semibold text-muted leading-none">{t("businessDetail.direct_phone")}</p>
                        <a
                          href={`tel:${business.phone}`}
                          className="mt-1 text-xs font-semibold text-foreground transition-colors hover:text-primary truncate block"
                        >
                          {business.phone}
                        </a>
                      </div>
                    </div>
                  )}

                  {business.linkedin && (
                    <div className="flex items-center gap-3">
                      <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                        <Share2 size={18} />
                      </div>
                      <div className="min-w-0">
                        <p className="text-[10px] font-semibold text-muted leading-none">{t("businessDetail.linkedin_url")}</p>
                        <span className="mt-1 text-xs font-semibold text-foreground block truncate">
                          {business.linkedin}
                        </span>
                      </div>
                    </div>
                  )}
                </div>

                {/* Social Media Icons */}
                {(business.instagram || business.youtube || business.twitter || business.linkedin || business.whatsapp) && (
                  <div className="mt-5 border-t border-border pt-4">
                    <p className="text-[10px] font-semibold text-muted mb-3 uppercase tracking-wider">{t("businessDetail.social_channels")}</p>
                    <div className="flex flex-wrap items-center gap-2.5">
                      {business.instagram && (
                        <a href={business.instagram} target="_blank" rel="noreferrer" className="h-10 w-10 rounded-xl bg-gradient-to-br from-pink-500 via-red-500 to-yellow-500 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Instagram">
                          <InstagramIcon size={18} />
                        </a>
                      )}
                      {business.youtube && (
                        <a href={business.youtube} target="_blank" rel="noreferrer" className="h-10 w-10 rounded-xl bg-red-600 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Youtube">
                          <YoutubeIcon size={18} />
                        </a>
                      )}
                      {business.twitter && (
                        <a href={business.twitter} target="_blank" rel="noreferrer" className="h-10 w-10 rounded-xl bg-black text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Twitter / X">
                          <TwitterIcon size={18} />
                        </a>
                      )}
                      {business.linkedin && (
                        <a href={business.linkedin} target="_blank" rel="noreferrer" className="h-10 w-10 rounded-xl bg-[#0A66C2] text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="LinkedIn">
                          <LinkedinIcon size={18} />
                        </a>
                      )}
                      {business.whatsapp && (
                        <a href={`https://wa.me/${business.whatsapp.replace(/[^0-9]/g, '')}`} target="_blank" rel="noreferrer" className="h-10 w-10 rounded-xl bg-[#25D366] text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="WhatsApp">
                          <WhatsappIcon size={18} />
                        </a>
                      )}
                    </div>
                  </div>
                )}

                {/* Coordinates Map Preview */}
                <div className="mt-6 border-t border-border pt-5">
                  <p className="text-[10px] font-semibold text-muted mb-2.5">{t("businessDetail.geographic_location")}</p>
                  <div className="h-28 w-full rounded-xl bg-slate-100 border border-border overflow-hidden relative flex items-center justify-center select-none">
                    {/* Subtle Grid dots */}
                    <div className="absolute inset-0 opacity-15" style={{ backgroundImage: "radial-gradient(circle, #000 1px, transparent 1px)", backgroundSize: "12px 12px" }} />
                    <div className="flex flex-col items-center z-10 text-center p-4">
                      <MapPin className="h-5 w-5 text-primary animate-bounce mb-1" />
                      <span className="text-[10px] font-bold text-foreground truncate max-w-full">{business.location}, India</span>
                      <span className="text-[8px] text-muted-foreground mt-0.5">{t("businessDetail.vetted_office")}</span>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* Direct Inquiry Message Form */}
            <div className="glass-card p-5 space-y-4">
              <div>
                <h3 className="text-sm font-semibold text-foreground">{t("businessDetail.send_inquiry_title")}</h3>
                <p className="text-[10px] text-muted-foreground mt-0.5">{t("businessDetail.response_time")}</p>
              </div>
              <form onSubmit={handleSendMessage} className="space-y-2.5">
                <input
                  type="text"
                  required
                  disabled={formSubmitting}
                  placeholder={t("businessDetail.ph_name")}
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  className="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary disabled:opacity-50"
                />
                <input
                  type="email"
                  required
                  disabled={formSubmitting}
                  placeholder={t("businessDetail.ph_email")}
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary disabled:opacity-50"
                />
                <input
                  type="text"
                  required
                  disabled={formSubmitting}
                  placeholder={t("businessDetail.ph_subject")}
                  value={subject}
                  onChange={(e) => setSubject(e.target.value)}
                  className="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary disabled:opacity-50"
                />
                <textarea
                  required
                  rows={3}
                  disabled={formSubmitting}
                  placeholder={t("businessDetail.ph_message")}
                  value={msg}
                  onChange={(e) => setMsg(e.target.value)}
                  className="w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary resize-none disabled:opacity-50"
                />
                <button
                  type="submit"
                  disabled={formSubmitting}
                  className="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition-opacity hover:opacity-90 active:scale-[0.98] disabled:opacity-50"
                >
                  {formSubmitting ? t("common.loading") : t("businessDetail.send_inquiry")}
                </button>
                <AnimatePresence>
                  {formSubmitted && (
                    <motion.div
                      initial={{ opacity: 0, y: 5 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0 }}
                      className="text-[10px] text-center text-green-600 font-semibold p-2 bg-green-50 rounded-lg border border-green-200/50"
                    >
                      {t("businessDetail.inquiry_sent")}
                    </motion.div>
                  )}
                  {formError && (
                    <motion.div
                      initial={{ opacity: 0, y: 5 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0 }}
                      className="text-[10px] text-center text-red-600 font-semibold p-2 bg-red-50 rounded-lg border border-red-200/50"
                    >
                      {formError}
                    </motion.div>
                  )}
                </AnimatePresence>
              </form>
            </div>

            {/* Vetted member Trust badge */}
            <div className="rounded-2xl border border-border bg-primary p-5 text-white space-y-3.5">
              <div className="flex items-center gap-2">
                <Award className="h-5 w-5 text-white" />
                <h4 className="text-sm font-bold">{t("businessDetail.vetted_member")}</h4>
              </div>
              <p className="text-xs leading-relaxed text-white/80">
                {t("businessDetail.vetted_desc")}
              </p>
              <div className="inline-flex items-center gap-1.5 rounded-lg bg-white/15 px-3 py-1.5">
                <ShieldCheck className="h-4 w-4 text-white" />
                <span className="text-[10px] font-bold">{t("businessDetail.verified_profile")}</span>
              </div>
            </div>

          </div>
        </div>
      </div>
      {/* Floating WhatsApp Button */}
      {(business.whatsapp || business.phone) && (
        <a
          href={`https://wa.me/${(business.whatsapp || business.phone).replace(/[^0-9]/g, '')}?text=Hi, I found your business on Sabha and would like to connect!`}
          target="_blank"
          rel="noreferrer"
          className="fixed bottom-6 right-6 z-50 flex items-center gap-2.5 rounded-full bg-[#25D366] pl-4 pr-5 py-3.5 text-white shadow-xl hover:shadow-2xl transition-all hover:scale-105 active:scale-95"
          title="Chat on WhatsApp"
        >
          {/* Pulse ring */}
          <span className="absolute inset-0 rounded-full bg-[#25D366] animate-ping opacity-20" />
          <WhatsappIcon size={22} />
          <span className="text-sm font-bold relative">WhatsApp</span>
        </a>
      )}
    </div>
  );
}

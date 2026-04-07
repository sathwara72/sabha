"use client";

import { useParams, useRouter } from "next/navigation";
import { 
  ArrowLeft, MapPin, Globe, Mail, Phone, Clock, 
  ShieldCheck, Star, Briefcase, Zap, 
  Share2, MessageCircle, ArrowUpRight 
} from "lucide-react";
import Link from "next/link";
import { motion } from "framer-motion";

const mockBusinesses = [
  { id: 1, name: "Vertex Solutions", category: "Software Development", location: "Mumbai", rating: 4.8, reviews: 24, verified: true, about: "Vertex Solutions is a leading software development firm specializing in cloud architecture and enterprise-scale digital transformations. We've helped over 50 startups scale to international markets.", services: ["Cloud Migration", "Custom ERP", "AI Integration", "Mobile Apps"], hours: "9:00 AM - 6:00 PM", website: "https://vertex.solutions" },
  { id: 2, name: "Global Logistics", category: "Supply Chain", location: "Delhi", rating: 4.5, reviews: 18, verified: true, about: "Global Logistics provides end-to-end supply chain solutions with a focus on real-time tracking and automated warehousing. Our network spans across 12 countries.", services: ["Last-mile Delivery", "Warehousing", "Customs Clearance", "Freight Forwarding"], hours: "8:00 AM - 7:00 PM", website: "https://globallogistics.com" },
  { id: 4, name: "Prime Builders", category: "Construction", location: "Pune", rating: 4.9, reviews: 31, verified: true, about: "Prime Builders is an award-winning construction and architectural firm focused on sustainable, green-certified commercial developments.", services: ["Sustainable Architecture", "Project Management", "Commercial EPC", "Interior Design"], hours: "10:00 AM - 6:00 PM", website: "https://primebuilders.in" },
];

export default function BusinessDetailsPage() {
  const { id } = useParams();
  const router = useRouter();
  const business = mockBusinesses.find(b => b.id.toString() === id) || mockBusinesses[0];

  return (
    <div className="min-h-screen bg-background font-outfit text-white pt-20">
      {/* Dynamic Header */}
      <section className="relative py-24 lg:py-32 overflow-hidden border-b border-white/5">
        <div className="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-accent/10 opacity-30" />
        <div className="absolute top-0 right-0 w-full h-full -z-10">
          <div className="absolute top-0 right-0 w-[600px] h-[600px] bg-primary/5 rounded-full blur-[140px] animate-pulse" />
          <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-accent/5 rounded-full blur-[140px] animate-pulse" />
        </div>
        
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <button 
            onClick={() => router.back()} 
            className="flex items-center gap-3 text-white/30 hover:text-primary mb-12 transition-all group font-black text-[10px] uppercase tracking-[0.3em]"
          >
            <ArrowLeft className="w-4 h-4 group-hover:-translate-x-2 transition-transform" />
            Back to Directory
          </button>
          
          <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-16">
            <div className="flex flex-col md:flex-row gap-12 items-start md:items-center">
              <div className="w-40 h-40 rounded-[3rem] glass border-white/10 flex items-center justify-center text-5xl font-black text-white shrink-0 relative overflow-hidden group shadow-2xl">
                <div className="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity" />
                <span className="relative z-10">{business.name[0]}</span>
              </div>
              <div className="space-y-8">
                <div className="flex flex-wrap items-center gap-6">
                  {business.verified && (
                    <div className="glass text-primary px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border border-primary/20 flex items-center gap-3">
                       <ShieldCheck className="w-4 h-4" />
                       Elite Partner
                    </div>
                  )}
                  <div className="flex items-center gap-2.5 text-accent font-black text-xs glass px-4 py-2 rounded-full border border-accent/20">
                    <Star size={16} className="fill-accent" />
                    {business.rating} <span className="text-white/30 font-bold uppercase tracking-widest ml-2 px-3 border-l border-white/10">({business.reviews} reviews)</span>
                  </div>
                </div>
                <h1 className="text-5xl md:text-8xl font-black text-white leading-none tracking-tighter uppercase">
                   {business.name.split(' ')[0]} <span className="text-gradient leading-none">{business.name.split(' ').slice(1).join(' ')}</span>
                </h1>
                <div className="flex flex-wrap gap-8 text-[11px] text-white/40 font-black uppercase tracking-[0.2em]">
                  <span className="flex items-center gap-3"><MapPin size={18} className="text-primary" /> {business.location}</span>
                  <span className="flex items-center gap-3"><Briefcase size={18} className="text-primary" /> {business.category}</span>
                  <span className="flex items-center gap-3"><Clock size={18} className="text-primary" /> Open: {business.hours}</span>
                </div>
              </div>
            </div>

            <div className="flex gap-6 w-full lg:w-fit">
              <button className="btn-premium flex-1 lg:flex-none px-12 py-6 text-sm flex items-center justify-center gap-4 group">
                Connect Now
                <ArrowUpRight className="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
              </button>
              <button className="p-6 rounded-3xl glass border-white/5 text-white/40 hover:text-primary hover:border-primary/40 transition-all flex items-center justify-center">
                <MessageCircle size={28} />
              </button>
            </div>
          </div>
        </div>
      </section>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-24">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-24">
          {/* Main Info */}
          <div className="lg:col-span-2 space-y-24">
            <section>
              <div className="flex items-center gap-4 mb-10">
                 <div className="w-2 h-12 bg-primary rounded-full shadow-[0_0_20px_rgba(var(--primary-rgb),0.5)]" />
                 <h2 className="text-4xl font-black tracking-tighter uppercase text-white">Strategic Profile</h2>
              </div>
              <p className="text-2xl text-white/40 leading-relaxed font-bold uppercase tracking-tight">
                "{business.about}"
              </p>
            </section>

            <section>
              <div className="flex items-center gap-4 mb-10">
                 <div className="w-2 h-12 bg-primary rounded-full shadow-[0_0_20px_rgba(var(--primary-rgb),0.5)]" />
                 <h2 className="text-4xl font-black tracking-tighter uppercase text-white">Gallery & Showcase</h2>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                {[1, 2].map((i) => (
                  <div key={i} className="aspect-video glass rounded-[3rem] overflow-hidden relative group border-white/5">
                    <div className="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center text-white font-black uppercase text-xs tracking-[0.3em] z-10 backdrop-blur-md">Showcase {i}</div>
                    <img 
                      src={`https://images.unsplash.com/photo-${i === 1 ? '1522071823991-b471e724092f' : '1552664730-d307ca884978'}?auto=format&fit=crop&q=80&w=800`} 
                      className="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 group-hover:scale-105"
                      alt="Work Detail"
                    />
                  </div>
                ))}
              </div>
            </section>

            <section>
              <div className="flex items-center gap-4 mb-10">
                 <div className="w-2 h-12 bg-primary rounded-full shadow-[0_0_20px_rgba(var(--primary-rgb),0.5)]" />
                 <h2 className="text-4xl font-black tracking-tighter uppercase text-white">Service Portfolio</h2>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {business.services.map(service => (
                  <div key={service} className="p-10 rounded-[3rem] glass border-white/5 flex items-center justify-between group hover:border-primary/40 transition-all">
                    <span className="font-black text-white/50 uppercase tracking-tight group-hover:text-white transition-colors">{service}</span>
                    <Zap className="w-6 h-6 text-primary opacity-0 group-hover:opacity-100 transition-all transform scale-0 group-hover:scale-100" />
                  </div>
                ))}
              </div>
            </section>
          </div>

          {/* Sidebar Info */}
          <div className="space-y-10">
            <div className="glass rounded-[4rem] p-12 border-white/5 relative overflow-hidden">
              <div className="absolute top-0 right-0 w-48 h-48 bg-primary/5 rounded-full blur-[80px]" />
              <h3 className="text-[10px] font-black mb-12 border-b border-white/5 pb-4 uppercase tracking-[0.4em] text-white/20 relative z-10">Professional Coordinates</h3>
              <div className="space-y-10 relative z-10">
                <div className="flex items-center gap-6 group">
                  <div className="w-14 h-14 rounded-[1.25rem] glass border-white/10 flex items-center justify-center shrink-0 group-hover:border-primary/50 transition-colors">
                    <Globe size={24} className="text-primary" />
                  </div>
                  <div>
                    <p className="text-[10px] text-white/20 font-black uppercase tracking-widest mb-1.5">Network Hub</p>
                    <Link href={business.website} target="_blank" className="text-base font-black text-white hover:text-primary transition-colors uppercase tracking-tight">{business.name.toLowerCase().replace(" ", "")}.com</Link>
                  </div>
                </div>
                <div className="flex items-center gap-6 group">
                  <div className="w-14 h-14 rounded-[1.25rem] glass border-white/10 flex items-center justify-center shrink-0 group-hover:border-primary/50 transition-colors">
                    <Share2 size={24} className="text-primary" />
                  </div>
                  <div>
                    <p className="text-[10px] text-white/20 font-black uppercase tracking-widest mb-1.5">LinkedIn Node</p>
                    <span className="text-base font-black text-white uppercase tracking-tight">/company/{business.name.toLowerCase().replace(" ", "-")}</span>
                  </div>
                </div>
                <div className="flex items-center gap-6 group">
                  <div className="w-14 h-14 rounded-[1.25rem] glass border-white/10 flex items-center justify-center shrink-0 group-hover:border-primary/50 transition-colors">
                    <Mail size={24} className="text-primary" />
                  </div>
                  <div>
                    <p className="text-[10px] text-white/20 font-black uppercase tracking-widest mb-1.5">Direct Signal</p>
                    <span className="text-base font-black text-white uppercase tracking-tight">hello@{business.name.toLowerCase().replace(" ", "")}.com</span>
                  </div>
                </div>
              </div>
              <button className="w-full mt-16 py-6 rounded-3xl border-2 border-primary/20 text-primary font-black text-[10px] uppercase tracking-[0.3em] hover:bg-primary hover:text-white hover:border-primary transition-all shadow-xl shadow-primary/5 relative z-10">
                Strategic Brochure
              </button>
            </div>

            <div className="bg-primary p-12 rounded-[4rem] shadow-2xl shadow-primary/20 text-white relative overflow-hidden group">
              <div className="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity blur-3xl" />
              <h3 className="text-3xl font-black mb-6 relative z-10 uppercase tracking-tighter leading-none">Sabha Verified</h3>
              <p className="text-white/70 text-lg leading-relaxed mb-10 font-bold uppercase tracking-tight relative z-10">
                "{business.name} is a high-trust catalyst in our ecosystem. They hold an elite status with 4.8+ community confidence."
              </p>
              <div className="flex items-center gap-4 glass border-white/20 p-5 rounded-3xl relative z-10 w-fit">
                <ShieldCheck className="w-6 h-6 text-white" />
                <span className="text-[11px] font-black uppercase tracking-widest">Community Vetted</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

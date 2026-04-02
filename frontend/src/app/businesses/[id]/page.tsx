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
    <div className="min-h-screen bg-slate-50">
      {/* Dynamic Header */}
      <div className="bg-white py-24 relative overflow-hidden border-b">
        <div className="absolute inset-0 bg-gradient-to-r from-primary/5 to-accent/5 opacity-50" />
        <div className="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3" />
        
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <button 
            onClick={() => router.back()} 
            className="flex items-center gap-2 text-foreground/40 hover:text-primary mb-12 transition-all group font-black text-[10px] uppercase tracking-widest"
          >
            <ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
            Back to Directory
          </button>
          
          <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-12">
            <div className="flex flex-col md:flex-row gap-10 items-start md:items-center">
              <div className="w-32 h-32 rounded-[2.5rem] bg-white border shadow-2xl shadow-foreground/10 flex items-center justify-center text-4xl font-black text-foreground shrink-0 relative overflow-hidden group">
                <div className="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity" />
                <span className="relative z-10 italic">{business.name[0]}</span>
              </div>
              <div className="space-y-6">
                <div className="flex flex-wrap items-center gap-4">
                  {business.verified && (
                    <div className="bg-primary/10 text-primary px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border border-primary/20 flex items-center gap-2">
                       <ShieldCheck className="w-3.5 h-3.5" />
                       Elite Partner
                    </div>
                  )}
                  <div className="flex items-center gap-1.5 text-amber-500 font-black text-xs bg-amber-500/5 px-3 py-1.5 rounded-full border border-amber-500/10">
                    <Star className="w-4 h-4 fill-current" />
                    {business.rating} <span className="text-foreground/30 font-medium italic">({business.reviews} reviews)</span>
                  </div>
                </div>
                <h1 className="text-4xl md:text-7xl font-black text-foreground italic tracking-tight uppercase">
                   {business.name.split(' ')[0]} <span className="text-gradient">{business.name.split(' ').slice(1).join(' ')}</span>
                </h1>
                <div className="flex flex-wrap gap-6 text-[10px] text-foreground/40 font-black uppercase tracking-widest">
                  <span className="flex items-center gap-2 italic"><MapPin className="w-4 h-4 text-primary" /> {business.location}</span>
                  <span className="flex items-center gap-2 italic"><Briefcase className="w-4 h-4 text-primary" /> {business.category}</span>
                  <span className="flex items-center gap-2 italic"><Clock className="w-4 h-4 text-primary" /> Open: {business.hours}</span>
                </div>
              </div>
            </div>

            <div className="flex gap-4 w-full lg:w-fit">
              <button className="flex-1 lg:flex-none px-12 py-5 rounded-2xl bg-primary text-white font-black text-sm uppercase tracking-widest hover:bg-primary/90 transition-all flex items-center justify-center gap-3 shadow-xl shadow-primary/20 active:scale-95">
                Connect Now
                <ArrowUpRight className="w-4 h-4" />
              </button>
              <button className="p-5 rounded-2xl border bg-white shadow-xl shadow-foreground/5 text-foreground/40 hover:text-primary hover:border-primary/50 transition-all active:scale-95">
                <MessageCircle className="w-6 h-6" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-20">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-16">
          {/* Main Info */}
          <div className="lg:col-span-2 space-y-16">
            <section>
              <div className="flex items-center gap-3 mb-8">
                 <div className="w-1.5 h-8 bg-primary rounded-full" />
                 <h2 className="text-2xl font-black italic tracking-tight uppercase">Strategic Profile</h2>
              </div>
              <p className="text-xl text-foreground/60 leading-relaxed italic font-medium">
                "{business.about}"
              </p>
            </section>

            <section>
              <div className="flex items-center gap-3 mb-8">
                 <div className="w-1.5 h-8 bg-primary rounded-full" />
                 <h2 className="text-2xl font-black italic tracking-tight uppercase">Gallery & Showcase</h2>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {[1, 2].map((i) => (
                  <div key={i} className="aspect-video bg-white rounded-3xl overflow-hidden relative group border shadow-xl shadow-foreground/5">
                    <div className="absolute inset-0 bg-primary/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-primary font-black uppercase text-xs tracking-widest z-10 backdrop-blur-sm">Showcase {i}</div>
                    <img 
                      src={`https://images.unsplash.com/photo-${i === 1 ? '1522071823991-b471e724092f' : '1552664730-d307ca884978'}?auto=format&fit=crop&q=80&w=800`} 
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                      alt="Work Detail"
                    />
                  </div>
                ))}
              </div>
            </section>

            <section>
              <div className="flex items-center gap-3 mb-8">
                 <div className="w-1.5 h-8 bg-primary rounded-full" />
                 <h2 className="text-2xl font-black italic tracking-tight uppercase">Service Portfolio</h2>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {business.services.map(service => (
                  <div key={service} className="p-8 rounded-[2rem] border bg-white flex items-center justify-between group hover:border-primary/50 transition-all shadow-xl shadow-foreground/5">
                    <span className="font-bold text-foreground/70 italic">{service}</span>
                    <Zap className="w-4 h-4 text-primary opacity-0 group-hover:opacity-100 transition-all" />
                  </div>
                ))}
              </div>
            </section>
          </div>

          {/* Sidebar Info */}
          <div className="space-y-8">
            <div className="bg-white rounded-[2.5rem] p-10 border shadow-xl shadow-foreground/5 relative overflow-hidden">
              <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl" />
              <h3 className="text-[10px] font-black mb-10 border-b pb-4 uppercase tracking-[0.2em] text-foreground/30 relative z-10">Professional Coordinates</h3>
              <div className="space-y-8 relative z-10">
                <div className="flex items-center gap-5">
                  <div className="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center shrink-0 border border-primary/10">
                    <Globe className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <p className="text-[10px] text-foreground/30 font-black uppercase tracking-widest mb-1">Network Hub</p>
                    <Link href={business.website} target="_blank" className="text-sm font-black hover:text-primary transition-colors italic">{business.name.toLowerCase().replace(" ", "")}.com</Link>
                  </div>
                </div>
                <div className="flex items-center gap-5">
                  <div className="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center shrink-0 border border-primary/10">
                    <Share2 className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <p className="text-[10px] text-foreground/30 font-black uppercase tracking-widest mb-1">LinkedIn Signal</p>
                    <span className="text-sm font-black italic">/company/{business.name.toLowerCase().replace(" ", "-")}</span>
                  </div>
                </div>
                <div className="flex items-center gap-5">
                  <div className="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center shrink-0 border border-primary/10">
                    <Mail className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <p className="text-[10px] text-foreground/30 font-black uppercase tracking-widest mb-1">Direct Signal</p>
                    <span className="text-sm font-black italic">hello@{business.name.toLowerCase().replace(" ", "")}.com</span>
                  </div>
                </div>
              </div>
              <button className="w-full mt-12 py-5 rounded-2xl border-2 border-primary text-primary font-black text-[10px] uppercase tracking-widest hover:bg-primary hover:text-white transition-all shadow-lg shadow-primary/5 relative z-10">
                Strategic Brochure
              </button>
            </div>

            <div className="bg-primary rounded-[2.5rem] p-10 border shadow-2xl shadow-primary/20 text-white relative overflow-hidden group">
              <div className="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity blur-3xl" />
              <h3 className="text-xl font-black mb-4 italic tracking-tight">Sabha Verified</h3>
              <p className="text-white/70 text-sm leading-relaxed mb-8 font-medium italic">
                "{business.name} is a high-trust catalyst in our ecosystem. They hold an elite status with 4.8+ community confidence."
              </p>
              <div className="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest bg-white/10 p-4 rounded-2xl border border-white/10 w-fit">
                <ShieldCheck className="w-4 h-4 text-white" />
                Community Vetted
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

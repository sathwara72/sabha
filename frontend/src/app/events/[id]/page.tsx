"use client";

import { useParams, useRouter } from "next/navigation";
import { 
  ArrowLeft, Calendar, MapPin, Clock, 
  Users, Zap, ShieldCheck, Star, 
  MessageSquare, Globe, ArrowUpRight, 
  CheckCircle2, Info
} from "lucide-react";
import Link from "next/link";
import { motion } from "framer-motion";

const mockEvents = [
  { id: 1, title: "Modern Business Networking Mixer", date: "Oct 12, 2026", time: "6:30 PM", type: "Physical", category: "Networking", description: "Connect with over 50 local entrepreneurs and service providers in a structured networking environment.", location: "The Grand Ballroom, Mumbai", price: "₹2,499", attendees: "120+", agenda: ["6:30 PM - Welcome Drinks", "7:00 PM - Speed Networking", "8:00 PM - Open Floor", "9:00 PM - Closing Ceremony"], speakers: [{ name: "Ananya Iyer", role: "Networking Expert", bio: "Renowned connector and community strategist." }] },
  { id: 2, title: "Next.js & Laravel: Scaling to Millions", date: "Oct 15, 2026", time: "2:00 PM", type: "Virtual", category: "Workshop", description: "Deep dive into full-stack architecture with industry experts. Learn how to scale your community platform.", location: "Zoom (Elite Access)", price: "Free", attendees: "500+", agenda: ["2:00 PM - System Design", "3:30 PM - Performance Tactics", "4:30 PM - Q&A Session"], speakers: [{ name: "Vikram Seth", role: "CTO, ScalingHub", bio: "Built and scaled 3 SaaS platforms to 1M+ MAU." }] },
];

export default function EventDetailsPage() {
  const { id } = useParams();
  const router = useRouter();
  const event = mockEvents.find(e => e.id.toString() === id) || mockEvents[0];

  return (
    <div className="min-h-screen bg-background font-outfit text-white pt-20">
      {/* Immersive Header */}
      <section className="relative py-24 lg:py-32 overflow-hidden border-b border-white/5">
        <div className="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-accent/10 opacity-30" />
        <div className="absolute top-0 left-0 w-full h-full -z-10">
          <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[140px] animate-pulse" />
          <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-accent/5 rounded-full blur-[140px] animate-pulse" />
        </div>
        
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <button 
            onClick={() => router.back()} 
            className="flex items-center gap-3 text-white/30 hover:text-primary mb-12 transition-all group font-black text-[10px] uppercase tracking-[0.3em]"
          >
            <ArrowLeft className="w-4 h-4 group-hover:-translate-x-2 transition-transform" />
            Back to All Events
          </button>
          
          <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-16">
            <div className="space-y-8 max-w-4xl">
              <div className="flex items-center gap-6">
                <div className="glass px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.25em] text-primary border-primary/20">
                  {event.category}
                </div>
                <div className="flex items-center gap-2 text-[10px] font-black text-white/40 uppercase tracking-widest">
                   <Users className="w-4 h-4 text-primary" /> {event.attendees} Registered
                </div>
              </div>
              <h1 className="text-5xl md:text-8xl font-black text-white leading-none tracking-tighter uppercase">
                {event.title.split(' ')[0]} <span className="text-gradient">"{event.title.split(' ').slice(1).join(' ')}"</span>
              </h1>
              <div className="flex flex-wrap gap-8 text-[11px] text-white/50 font-black uppercase tracking-widest">
                <span className="flex items-center gap-3"><Calendar className="w-5 h-5 text-primary" /> {event.date}</span>
                <span className="flex items-center gap-3"><Clock className="w-5 h-5 text-primary" /> {event.time}</span>
                <span className="flex items-center gap-3"><MapPin className="w-5 h-5 text-primary" /> {event.location}</span>
              </div>
            </div>

            <div className="glass p-12 rounded-[3.5rem] border-white/5 min-w-[360px] relative overflow-hidden group">
              <div className="absolute top-0 right-0 w-48 h-48 bg-primary/10 rounded-full blur-[60px] group-hover:bg-primary/20 transition-colors" />
              <p className="text-[10px] text-white/30 font-black uppercase tracking-[0.3em] mb-6 text-center">Strategic Access</p>
              <h3 className="text-6xl font-black text-center mb-10 text-white tracking-tighter">{event.price}</h3>
              <button className="btn-premium w-full py-6 text-sm flex items-center justify-center gap-3 group active:scale-95">
                Reserve Seat
                <ArrowUpRight size={20} className="group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
              </button>
            </div>
          </div>
        </div>
      </section>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-24">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-24">
          {/* Main Context */}
          <div className="lg:col-span-2 space-y-24">
            <section>
              <div className="flex items-center gap-4 mb-10">
                 <div className="w-2 h-12 bg-primary rounded-full shadow-[0_0_20px_rgba(var(--primary-rgb),0.5)]" />
                 <h2 className="text-4xl font-black tracking-tighter uppercase text-white">Event Brief</h2>
              </div>
              <p className="text-2xl text-white/40 leading-relaxed font-bold uppercase tracking-tight">
                "{event.description}"
              </p>
            </section>

            <section>
              <div className="flex items-center gap-4 mb-10">
                 <div className="w-2 h-12 bg-primary rounded-full shadow-[0_0_20px_rgba(var(--primary-rgb),0.5)]" />
                 <h2 className="text-4xl font-black tracking-tighter uppercase text-white">Scheduled Agenda</h2>
              </div>
              <div className="space-y-6">
                {event.agenda.map((item, i) => (
                  <div key={i} className="flex gap-8 items-center p-10 glass rounded-[3rem] border-white/5 hover:border-primary/40 transition-all group">
                    <div className="h-4 w-4 rounded-full bg-primary/20 flex items-center justify-center shrink-0 group-hover:bg-primary transition-colors">
                      <div className="h-1.5 w-1.5 rounded-full bg-primary group-hover:bg-white" />
                    </div>
                    <span className="text-xl font-black text-white/50 uppercase tracking-tight group-hover:text-white transition-colors">{item}</span>
                  </div>
                ))}
              </div>
            </section>

            <section>
              <div className="flex items-center gap-4 mb-10">
                 <div className="w-2 h-12 bg-primary rounded-full shadow-[0_0_20px_rgba(var(--primary-rgb),0.5)]" />
                 <h2 className="text-4xl font-black tracking-tighter uppercase text-white">Keynote Speakers</h2>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                {event.speakers.map(speaker => (
                  <div key={speaker.name} className="glass p-12 rounded-[3.5rem] border-white/5 flex flex-col items-center text-center group hover:bg-white/[0.02] transition-all relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-[40px] group-hover:bg-primary/10 transition-colors" />
                    <div className="w-28 h-28 rounded-[2rem] bg-background mb-8 overflow-hidden border-4 border-white/5 p-1 group-hover:border-primary transition-all relative z-10" />
                    <h4 className="text-3xl font-black mb-2 relative z-10 uppercase tracking-tighter text-white">{speaker.name}</h4>
                    <p className="text-primary text-[10px] font-black uppercase tracking-widest mb-8 relative z-10">"{speaker.role}"</p>
                    <p className="text-base text-white/30 tracking-wide font-bold relative z-10 leading-relaxed uppercase">"{speaker.bio}"</p>
                  </div>
                ))}
              </div>
            </section>
          </div>

          {/* Logistics & Registration */}
          <div className="space-y-10">
            <div className="glass rounded-[4rem] p-12 border-white/5 relative overflow-hidden">
              <div className="absolute top-0 right-0 w-48 h-48 bg-primary/5 rounded-full blur-[80px]" />
              <h3 className="text-[10px] font-black mb-12 border-b border-white/5 pb-4 uppercase tracking-[0.3em] text-white/20 relative z-10">Logistics Hub</h3>
              <div className="space-y-10 relative z-10">
                <div className="flex gap-6">
                   <div className="w-14 h-14 rounded-[1.25rem] glass border-white/10 flex items-center justify-center shrink-0">
                    <MapPin className="w-7 h-7 text-primary" />
                   </div>
                   <div>
                     <p className="text-[10px] font-black text-white/20 uppercase tracking-widest mb-1.5">Coordinates</p>
                     <p className="font-black text-lg tracking-tight text-white uppercase">{event.location}</p>
                   </div>
                </div>
                <div className="flex gap-6">
                   <div className="w-14 h-14 rounded-[1.25rem] glass border-white/10 flex items-center justify-center shrink-0">
                    <Zap className="w-7 h-7 text-primary" />
                   </div>
                   <div>
                     <p className="text-[10px] font-black text-white/20 uppercase tracking-widest mb-1.5">Signal Type</p>
                     <p className="font-black text-lg tracking-tight text-white uppercase">{event.type} Interactive</p>
                   </div>
                </div>
              </div>
              
              <div className="mt-16 space-y-6 relative z-10">
                <div className="flex items-center gap-4 text-[10px] font-black text-accent glass border-accent/20 p-5 rounded-[1.5rem] uppercase tracking-widest">
                  <CheckCircle2 className="w-5 h-5" />
                  Signal: Limited Capacity
                </div>
                <div className="flex items-center gap-3 text-white/20 text-[10px] font-black uppercase tracking-[0.2em] px-2">
                   <Info size={16} />
                   Register before Oct 05 for early access.
                </div>
              </div>
            </div>

            <div className="bg-primary p-12 rounded-[4rem] shadow-2xl shadow-primary/20 text-white relative overflow-hidden group">
              <div className="absolute inset-x-0 inset-y-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity blur-3xl" />
              <h4 className="text-3xl font-black mb-6 relative z-10 uppercase tracking-tighter">Sabha Guard</h4>
              <p className="text-white/70 text-lg leading-relaxed mb-12 relative z-10 font-bold uppercase tracking-tight">
                "All events are vetted for high-impact professional networking. Your breakthrough is our priority."
              </p>
              <div className="flex items-center gap-4 glass border-white/20 p-5 rounded-3xl relative z-10 w-fit">
                 <Star className="w-6 h-6 text-accent fill-accent" />
                 <span className="text-[11px] font-black uppercase tracking-widest text-white">Premium Experience</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

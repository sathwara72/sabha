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
    <div className="min-h-screen bg-slate-50">
      {/* Immersive Header */}
      <div className="bg-white py-24 relative overflow-hidden border-b">
        <div className="absolute inset-0 bg-gradient-to-r from-primary/5 to-accent/5 opacity-50" />
        <div className="absolute top-0 left-0 w-96 h-96 bg-primary/5 rounded-full blur-[120px] -translate-y-1/2 -translate-x-1/3" />
        
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <button 
            onClick={() => router.back()} 
            className="flex items-center gap-2 text-foreground/40 hover:text-primary mb-12 transition-all group font-black text-[10px] uppercase tracking-widest"
          >
            <ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
            Back to All Events
          </button>
          
          <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-12">
            <div className="space-y-6 max-w-3xl">
              <div className="flex items-center gap-4">
                <div className="bg-primary/10 text-primary px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] border border-primary/20 italic">
                  {event.category}
                </div>
                <div className="flex items-center gap-2 text-[10px] font-black text-foreground/30 uppercase tracking-widest">
                   <Users className="w-4 h-4 text-primary" /> {event.attendees} Registered
                </div>
              </div>
              <h1 className="text-4xl md:text-7xl font-black text-foreground italic tracking-tight uppercase">
                {event.title.split(' ')[0]} <span className="text-gradient">"{event.title.split(' ').slice(1).join(' ')}"</span>
              </h1>
              <div className="flex flex-wrap gap-6 text-[10px] text-foreground/40 font-black uppercase tracking-widest">
                <span className="flex items-center gap-2 italic"><Calendar className="w-4 h-4 text-primary" /> {event.date}</span>
                <span className="flex items-center gap-2 italic"><Clock className="w-4 h-4 text-primary" /> {event.time}</span>
                <span className="flex items-center gap-2 italic"><MapPin className="w-4 h-4 text-primary" /> {event.location}</span>
              </div>
            </div>

            <div className="bg-white p-10 rounded-[2.5rem] border shadow-2xl shadow-foreground/5 min-w-[320px] relative overflow-hidden group">
              <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors" />
              <p className="text-[10px] text-foreground/30 font-black uppercase tracking-[0.2em] mb-4 text-center relative z-10">Strategic Access</p>
              <h3 className="text-5xl font-black text-center mb-8 relative z-10 italic">{event.price}</h3>
              <button className="w-full py-5 rounded-2xl bg-primary text-white font-black text-xs uppercase tracking-widest hover:bg-primary/90 transition-all flex items-center justify-center gap-2 shadow-xl shadow-primary/20 group relative z-10 active:scale-95">
                Reserve Seat
                <ArrowUpRight className="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-20">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-16">
          {/* Main Context */}
          <div className="lg:col-span-2 space-y-16">
            <section>
              <div className="flex items-center gap-3 mb-8">
                 <div className="w-1.5 h-10 bg-primary rounded-full" />
                 <h2 className="text-3xl font-black italic tracking-tight uppercase">Event Brief</h2>
              </div>
              <p className="text-xl text-foreground/60 leading-relaxed font-medium italic">
                "{event.description}"
              </p>
            </section>

            <section>
              <div className="flex items-center gap-3 mb-8">
                 <div className="w-1.5 h-10 bg-primary rounded-full" />
                 <h2 className="text-3xl font-black italic tracking-tight uppercase">Scheduled Agenda</h2>
              </div>
              <div className="space-y-4">
                {event.agenda.map((item, i) => (
                  <div key={i} className="flex gap-6 items-center p-8 bg-white rounded-[2rem] border hover:border-primary/50 transition-all group shadow-xl shadow-foreground/5">
                    <div className="h-4 w-4 rounded-full bg-primary/10 flex items-center justify-center shrink-0 group-hover:bg-primary transition-colors">
                      <div className="h-1.5 w-1.5 rounded-full bg-primary group-hover:bg-white" />
                    </div>
                    <span className="text-lg font-bold text-foreground/70 italic">{item}</span>
                  </div>
                ))}
              </div>
            </section>

            <section>
              <div className="flex items-center gap-3 mb-8">
                 <div className="w-1.5 h-10 bg-primary rounded-full" />
                 <h2 className="text-3xl font-black italic tracking-tight uppercase">Keynote Speakers</h2>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                {event.speakers.map(speaker => (
                  <div key={speaker.name} className="p-10 rounded-[2.5rem] border bg-white flex flex-col items-center text-center group transition-all shadow-xl shadow-foreground/5 relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-colors" />
                    <div className="w-24 h-24 rounded-full bg-slate-50 mb-6 overflow-hidden border-4 border-primary/20 p-1 group-hover:border-primary transition-all shadow-inner relative z-10" />
                    <h4 className="text-2xl font-black mb-1 relative z-10 italic uppercase tracking-tight">{speaker.name}</h4>
                    <p className="text-primary text-[10px] font-black uppercase tracking-widest mb-6 relative z-10 italic">"{speaker.role}"</p>
                    <p className="text-sm text-foreground/50 italic leading-relaxed font-medium relative z-10">"{speaker.bio}"</p>
                  </div>
                ))}
              </div>
            </section>
          </div>

          {/* Logistics & Registration */}
          <div className="space-y-8">
            <div className="bg-white rounded-[2.5rem] p-10 border shadow-xl shadow-foreground/5 relative overflow-hidden">
              <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl" />
              <h3 className="text-[10px] font-black mb-10 border-b pb-4 uppercase tracking-[0.2em] text-foreground/30 relative z-10">Logistics Hub</h3>
              <div className="space-y-8 relative z-10">
                <div className="flex gap-5">
                   <div className="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center shrink-0 border border-primary/10">
                    <MapPin className="w-6 h-6 text-primary" />
                   </div>
                   <div>
                     <p className="text-[10px] font-black text-foreground/30 uppercase tracking-widest mb-1">Coordinates</p>
                     <p className="font-bold text-sm tracking-tight italic">{event.location}</p>
                   </div>
                </div>
                <div className="flex gap-5">
                   <div className="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center shrink-0 border border-primary/10">
                    <Zap className="w-6 h-6 text-primary" />
                   </div>
                   <div>
                     <p className="text-[10px] font-black text-foreground/30 uppercase tracking-widest mb-1">Signal Type</p>
                     <p className="font-bold text-sm tracking-tight italic capitalize">{event.type} Interactive</p>
                   </div>
                </div>
              </div>
              
              <div className="mt-12 space-y-4 relative z-10">
                <div className="flex items-center gap-3 text-[10px] font-black text-green-600 bg-green-500/5 p-4 rounded-2xl border border-green-500/20 uppercase tracking-widest">
                  <CheckCircle2 className="w-4 h-4" />
                  Signal: Limited Capacity
                </div>
                <div className="flex items-center gap-2 text-foreground/30 text-[10px] font-black uppercase tracking-widest italic px-2">
                   <Info className="w-3.5 h-3.5" />
                   Register before Oct 05 for early access.
                </div>
              </div>
            </div>

            <div className="bg-primary rounded-[2.5rem] p-10 border shadow-2xl shadow-primary/20 text-white relative overflow-hidden group">
              <div className="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity blur-3xl" />
              <h4 className="text-2xl font-black mb-4 relative z-10 italic uppercase tracking-tight">Sabha Guard</h4>
              <p className="text-white/70 text-sm leading-relaxed mb-10 relative z-10 font-medium italic">
                "All events are vetted for high-impact professional networking. Your breakthrough is our priority."
              </p>
              <div className="flex items-center gap-3 bg-white/10 p-4 rounded-2xl border border-white/10 relative z-10 w-fit">
                 <Star className="w-5 h-5 text-amber-300 fill-current" />
                 <span className="text-[10px] font-black uppercase tracking-widest text-white/90">Premium Experience</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

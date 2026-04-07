"use client";

import React, { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { 
  ArrowRight, Users, Calendar, ShieldCheck, Zap, Globe, 
  ChevronRight, Briefcase, Mail, Send, Code
} from "lucide-react";
import Link from "next/link";
import { fetchBusinesses, fetchEvents, fetchStatistics } from "@/lib/api";

interface Business {
  id: number;
  name: string;
  category: string;
  description: string;
  website: string;
  logo?: string;
}

interface Event {
  id: number;
  title: string;
  description: string;
  date: string;
  location: string;
  type: string;
  image?: string;
}

interface Stat {
  label: string;
  value: string;
}

export default function Home() {
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [events, setEvents] = useState<Event[]>([]);
  const [stats, setStats] = useState<Stat[]>([
    { label: "Active Professionals", value: "500+" },
    { label: "Strategic Events", value: "120+" },
    { label: "Success Stories", value: "2500+" },
  ]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadData() {
      try {
        const [bizData, eventData, statData] = await Promise.all([
          fetchBusinesses(),
          fetchEvents(),
          fetchStatistics()
        ]);
        setBusinesses(bizData);
        setEvents(eventData);
        if (statData && statData.length > 0) setStats(statData);
      } catch (error) {
        console.error("Error loading live data:", error);
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, []);

  return (
    <main className="relative isolate min-h-screen pt-20 overflow-hidden">
      {/* Premium Background Blobs */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
        <div className="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-primary/10 rounded-full blur-[120px] animate-pulse" />
        <div className="absolute bottom-[10%] right-[-10%] w-[600px] h-[600px] bg-accent/10 rounded-full blur-[150px] animate-pulse" />
      </div>

      {/* Hero Section */}
      <section className="relative py-24 lg:py-32 overflow-hidden">
        <div className="container mx-auto px-6 text-center">
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-full glass border-white/5 mb-8"
          >
            <span className="w-2 h-2 rounded-full bg-primary animate-ping" />
            <span className="text-[10px] font-black uppercase tracking-[0.2em] text-white/50">
              India's Premier Professional Collective
            </span>
          </motion.div>
          
          <motion.h1 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            className="text-6xl md:text-8xl font-black mb-8 leading-none tracking-tighter"
          >
            BRIDGE. BUILD.<br />
            <span className="text-gradient">BELONG.</span>
          </motion.h1>
          
          <motion.p 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.1 }}
            className="text-xl md:text-2xl text-white/50 max-w-2xl mx-auto mb-12 font-bold leading-relaxed"
          >
            SABHA is the destination for visionary entrepreneurs and service providers to connect, collaborate, and conquer.
          </motion.p>
          
          <motion.div 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2 }}
            className="flex flex-col sm:flex-row items-center justify-center gap-6"
          >
            <Link href="/register" className="btn-premium w-full sm:w-auto text-lg px-12 py-5">
              Start Your Journey <ArrowRight size={20} />
            </Link>
            <Link href="/events" className="glass px-12 py-5 rounded-full text-lg font-bold text-white/70 hover:text-white transition-all hover:border-white/20">
              Browse Events
            </Link>
          </motion.div>
        </div>
      </section>

      {/* Impact Stats */}
      <section className="py-20 bg-white/5 border-y border-white/5">
        <div className="container mx-auto px-6">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            {stats.map((stat, i) => (
              <motion.div 
                key={i}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1 }}
              >
                <h3 className="text-5xl md:text-7xl font-black text-white mb-2">{stat.value}</h3>
                <p className="text-sm font-black text-white/30 uppercase tracking-[0.2em]">{stat.label}</p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Features Bento Grid (Fetching from Businesses API) */}
      <section className="py-24 lg:py-40">
        <div className="container mx-auto px-6">
          <div className="max-w-2xl text-center mx-auto mb-20">
            <h2 className="text-sm font-black text-primary uppercase tracking-[0.3em] mb-4">Growth Ecosystem</h2>
            <p className="text-4xl md:text-6xl font-black text-white tracking-tighter uppercase">
              Leading Professional Partners.
            </p>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {businesses.length > 0 ? businesses.map((biz, i) => (
              <motion.div
                key={biz.id}
                initial={{ opacity: 0, scale: 0.95 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1 }}
                className="glass-card p-10 rounded-[2.5rem] group"
              >
                <div className="w-16 h-16 rounded-2xl flex items-center justify-center mb-8 bg-primary/10 text-primary">
                  <Briefcase size={32} />
                </div>
                <h3 className="text-2xl font-black text-white mb-2 uppercase tracking-tight">{biz.name}</h3>
                <p className="text-xs font-black text-primary uppercase tracking-widest mb-4">{biz.category}</p>
                <p className="text-lg text-white/40 font-bold leading-relaxed">
                  {biz.description}
                </p>
                <a href={biz.website} target="_blank" className="mt-6 inline-flex items-center gap-2 text-white/60 hover:text-white font-black text-xs uppercase tracking-widest transition-colors">
                  Visit Site <ChevronRight size={14} />
                </a>
              </motion.div>
            )) : (
              <div className="col-span-full py-20 text-center text-white/20 font-black uppercase tracking-widest">
                {loading ? "Discovering partners..." : "Loading premium network..."}
              </div>
            )}
          </div>
        </div>
      </section>

      {/* Live Sabha / Events Gallery (Fetching from Events API) */}
      <section className="py-24 relative overflow-hidden">
        <div className="container mx-auto px-6">
          <div className="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16 px-4">
            <div>
              <h2 className="text-sm font-black text-accent uppercase tracking-[0.3em] mb-4">LIVE SABHA</h2>
              <p className="text-4xl md:text-6xl font-black text-white tracking-tighter uppercase">COMMUNITY IN ACTION</p>
            </div>
            <Link href="/gallery" className="text-white font-black text-sm uppercase tracking-widest flex items-center gap-2 hover:text-accent transition-colors group">
              View Full Gallery <Zap size={16} className="group-hover:scale-125 transition-transform" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {events.slice(0, 3).map((event, i) => (
              <motion.div
                key={event.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1 }}
                className={cn(
                  "rounded-[3rem] overflow-hidden glass-card relative group border-none",
                  i === 0 ? "lg:row-span-2 aspect-[4/5] lg:aspect-auto" : "aspect-square"
                )}
              >
                <img 
                  src={event.image || "https://images.unsplash.com/photo-1540575861501-7ad0582373f3"} 
                  alt={event.title} 
                  className="w-full h-full object-cover grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105" 
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-90 group-hover:opacity-60 transition-opacity" />
                <div className="absolute bottom-10 left-10 right-10">
                  <h4 className="text-xl font-black text-white mb-1 uppercase tracking-tight">{event.title}</h4>
                  <p className="text-white/40 text-[10px] font-black tracking-[0.2em] uppercase mb-4">{new Date(event.date).toLocaleDateString()} • {event.location}</p>
                </div>
              </motion.div>
            ))}
            
            {events.length === 0 && !loading && (
              <div className="col-span-full py-20 text-center text-white/20 font-black uppercase tracking-widest border border-dashed border-white/5 rounded-[3rem]">
                No upcoming events scheduled.
              </div>
            )}

            <div className="glass shadow-2xl rounded-[3rem] p-12 flex flex-col justify-between border-white/5 h-full relative overflow-hidden group">
              <div className="absolute top-[-10%] right-[-10%] w-32 h-32 bg-primary/20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700" />
              <div className="space-y-8 relative z-10">
                <h3 className="text-3xl font-black text-white uppercase tracking-tight">Join the Next Event</h3>
                <p className="text-white/40 text-lg leading-relaxed font-bold">
                  The Sabha environment is electric. Meet your next business partners in an environment built for scaling.
                </p>
                <div className="flex items-center gap-4">
                  <div className="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center font-black text-white uppercase">RS</div>
                  <div>
                    <p className="text-white font-black uppercase text-sm tracking-widest">Ravi Sharma</p>
                    <p className="text-primary text-[10px] font-black uppercase tracking-[0.2em] mt-1">Founder, TechWave</p>
                  </div>
                </div>
              </div>
              <Link href="/events" className="btn-premium w-full mt-12 py-5 text-lg">
                Browse Schedule
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* How it Works / Journey */}
      <section className="py-24 lg:py-40 relative">
        <div className="container mx-auto px-6 text-center mb-24">
          <h2 className="text-sm font-black text-primary uppercase tracking-[0.3em] mb-4">USER JOURNEY</h2>
          <p className="text-4xl md:text-6xl font-black text-white tracking-tighter uppercase underline underline-offset-[12px] decoration-white/5">SIMPLIFIED PROGRESS</p>
        </div>
        
        <div className="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-16 relative">
          {[
            { step: "01", title: "APPLY FOR ENTRY", detail: "Complete your professional bio and business details for verification." },
            { step: "02", title: "CONNECT STRATEGICALLY", detail: "Get matched with peer businesses and relevant community workshops." },
            { step: "03", title: "UNLOCK SCALE", detail: "Leverage the network to find service providers and scaling opportunities." },
          ].map((item, idx) => (
            <motion.div 
              key={idx}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.1 }}
              className="relative group text-center"
            >
              <div className="text-[12rem] font-black text-white/5 absolute -top-32 left-1/2 -translate-x-1/2 select-none group-hover:text-primary/5 transition-colors">{item.step}</div>
              <h3 className="text-3xl font-black text-white mb-6 relative z-10 tracking-tight uppercase">{item.title}</h3>
              <p className="text-white/40 leading-relaxed max-w-[280px] mx-auto relative z-10 font-bold text-lg">{item.detail}</p>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Final Newsletter Section */}
      <section className="py-24 lg:py-40 relative">
         <div className="container mx-auto px-6 relative">
          <div className="glass-card py-24 px-10 md:px-24 rounded-[4rem] text-center max-w-5xl mx-auto shadow-none">
            <h2 className="text-4xl md:text-6xl font-black text-white mb-6 tracking-tighter uppercase">JOIN THE INNER CIRCLE</h2>
            <p className="max-w-2xl mx-auto text-xl text-white/40 font-bold mb-12">
              Weekly insights on business scaling, networking hacks, and early-bird event invites.
            </p>
            <div className="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-lg mx-auto">
              <input
                type="email"
                placeholder="PRO EMAIL ADDRESS"
                className="w-full glass border-white/10 rounded-2xl px-6 py-5 text-white outline-none focus:border-primary transition-all font-black text-sm tracking-widest"
              />
              <button
                className="w-full sm:w-auto btn-premium px-10 py-5 text-sm uppercase tracking-widest"
              >
                JOIN
              </button>
            </div>
            <p className="mt-8 text-xs text-white/10 font-black tracking-[0.3em] uppercase">Trusted by 2,500+ leaders nationwide</p>
          </div>
        </div>
      </section>
    </main>
  );
}

const cn = (...classes: any[]) => classes.filter(Boolean).join(" ");

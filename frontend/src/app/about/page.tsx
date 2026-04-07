"use client";

import { motion } from "framer-motion";
import { Users, Target, ShieldCheck, Heart, ArrowRight } from "lucide-react";
import Link from "next/link";

const values = [
  { name: "Trust First", description: "Every business and user in Sabha is verified for authenticity and professional integrity.", icon: ShieldCheck },
  { name: "Collaborative Growth", description: "We believe we grow faster when we grow together. Collaboration is our core engine.", icon: Users },
  { name: "Global Standard", description: "Bringing world-class community management and professional tools to local businesses.", icon: Target },
  { name: "Heart of Community", description: "Beyond business, we are a family that supports every member's professional journey.", icon: Heart },
];

export default function AboutPage() {
  return (
    <div className="relative isolate min-h-screen pt-20 overflow-hidden bg-background">
      {/* Background Blobs */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
        <div className="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-primary/10 rounded-full blur-[120px] animate-pulse" />
        <div className="absolute bottom-[10%] right-[-10%] w-[600px] h-[600px] bg-accent/10 rounded-full blur-[150px] animate-pulse" />
      </div>

      {/* Hero Section */}
      <section className="relative py-24 lg:py-32 overflow-hidden">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 text-center">
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
          >
            <div className="inline-block px-4 py-2 rounded-full glass border-white/5 mb-8">
              <span className="text-[10px] font-black uppercase tracking-[0.2em] text-white/50">
                Our Genesis & Philosophy
              </span>
            </div>
            <h1 className="text-5xl md:text-8xl font-black mb-8 leading-none tracking-tighter uppercase">
              Connecting the <br />
              <span className="text-gradient">Visionaries.</span>
            </h1>
            <p className="max-w-3xl mx-auto text-xl md:text-2xl text-white/50 font-bold leading-relaxed mb-12">
              SABHA was born out of a realization: networking should be visceral. It should be about building trust and lasting professional alliances.
            </p>
          </motion.div>
        </div>
      </section>

      {/* Our Story / Mission */}
      <section className="py-24 relative">
        <div className="container mx-auto px-6">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <motion.div 
               initial={{ opacity: 0, scale: 0.95 }}
               whileInView={{ opacity: 1, scale: 1 }}
               viewport={{ once: true }}
               className="relative aspect-square rounded-[4rem] overflow-hidden glass-card border-none group"
            >
              <img 
                src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1000" 
                alt="Community" 
                className="w-full h-full object-cover grayscale opacity-30 group-hover:scale-105 group-hover:opacity-50 transition-all duration-700"
              />
              <div className="absolute inset-0 flex flex-col items-center justify-center p-16 text-center">
                <div className="w-24 h-24 rounded-3xl bg-primary/20 flex items-center justify-center mb-8 border border-primary/20 backdrop-blur-xl">
                   <Users className="w-10 h-10 text-primary" />
                </div>
                <p className="text-white/60 text-xl font-bold tracking-tight leading-relaxed uppercase">
                  Community Mixers. Strategic Workshops. Peer-to-Peer Growth.
                </p>
              </div>
            </motion.div>
            
            <motion.div 
              initial={{ opacity: 0, x: 50 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              className="space-y-12"
            >
              <div>
                <div className="flex items-center gap-4 mb-6">
                   <div className="w-2 h-10 bg-primary rounded-full" />
                   <h2 className="text-4xl font-black tracking-tight uppercase">Mission & Vision</h2>
                </div>
                <p className="text-3xl text-white/70 font-bold leading-relaxed">
                  To become the <span className="text-gradient font-black">digital heartbeat</span> of professional communities globally.
                </p>
              </div>

              <div className="space-y-8">
                <p className="text-xl text-white/40 leading-relaxed font-bold">
                  We provide a high-performance platform that simplifies discovery, facilitates showcasing, and fosters a premium ecosystem of trust.
                </p>
                <div className="grid grid-cols-2 gap-12 pt-8">
                  <div className="space-y-2">
                    <p className="text-5xl font-black text-white mb-1 tracking-tighter">500+</p>
                    <p className="text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Verified Entities</p>
                  </div>
                  <div className="space-y-2">
                    <p className="text-5xl font-black text-white mb-1 tracking-tighter">12k+</p>
                    <p className="text-[10px] font-black uppercase tracking-[0.2em] text-white/30">Connections Built</p>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Values Grid */}
      <section className="py-24 lg:py-40 relative">
        <div className="container mx-auto px-6 relative z-10">
          <div className="mx-auto max-w-3xl text-center mb-24">
             <div className="inline-block bg-primary/10 text-primary px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.3em] mb-6">
               Core Paradigms
             </div>
            <h2 className="text-4xl md:text-6xl font-black tracking-tight text-white uppercase underline underline-offset-[12px] decoration-white/5">What We Stand For</h2>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {values.map((v, i) => (
              <motion.div 
                key={v.name}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1 }}
                className="glass-card p-10 rounded-[2.5rem] border-none group hover:bg-white/5"
              >
                <div className="w-16 h-16 rounded-[1.5rem] bg-primary/10 flex items-center justify-center mb-10 group-hover:scale-110 transition-transform">
                  <v.icon className="w-8 h-8 text-primary" />
                </div>
                <h3 className="text-xl font-black mb-4 uppercase tracking-tight group-hover:text-primary transition-colors">{v.name}</h3>
                <p className="text-lg text-white/40 leading-relaxed font-bold">
                  {v.description}
                </p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-24 lg:py-40">
        <div className="container mx-auto px-6">
          <div className="glass shadow-2xl rounded-[4rem] p-16 md:p-24 text-center relative overflow-hidden group">
            <div className="absolute top-[-20%] left-[-20%] w-[400px] h-[400px] bg-primary/10 rounded-full blur-[100px] pointer-events-none" />
            <h2 className="text-4xl md:text-7xl font-black text-white mb-8 tracking-tighter uppercase relative z-10">Ready to Join the<br /><span className="text-gradient">Professional Elite?</span></h2>
            <Link href="/register" className="btn-premium inline-flex relative z-10 px-12 py-5 text-xl">
              Get Started Now <ArrowRight size={24} />
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}

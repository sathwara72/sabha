"use client";

import { motion } from "framer-motion";
import { Users, Target, ShieldCheck, Heart } from "lucide-react";

const values = [
  { name: "Trust First", description: "Every business and user in Sabha is verified for authenticity and professional integrity.", icon: ShieldCheck },
  { name: "Collaborative Growth", description: "We believe we grow faster when we grow together. Collaboration is our core engine.", icon: Users },
  { name: "Global Standard", description: "Bringing world-class community management and professional tools to local businesses.", icon: Target },
  { name: "Heart of Community", description: "Beyond business, we are a family that supports every member's professional journey.", icon: Heart },
];

export default function AboutPage() {
  return (
    <div className="bg-slate-50 min-h-screen">
      {/* Hero Section */}
      <div className="bg-white py-24 sm:py-32 relative overflow-hidden border-b">
        <div className="absolute inset-0 bg-gradient-to-r from-primary/5 to-accent/5 opacity-50" />
        <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2" />
        
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 text-center">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
          >
            <h1 className="text-4xl md:text-7xl font-black tracking-tight text-foreground sm:text-6xl italic uppercase">
              Connecting the <span className="text-gradient">Visionaries.</span>
            </h1>
            <p className="mt-8 text-xl leading-8 text-foreground/50 max-w-2xl mx-auto font-medium italic">
              "Sabha was born out of a realization: networking should be visceral. It should be about building trust and lasting professional alliances."
            </p>
          </motion.div>
        </div>
      </div>

      {/* Our Story */}
      <div className="py-24 sm:py-32">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div className="relative aspect-square rounded-[4rem] overflow-hidden bg-white border shadow-2xl shadow-foreground/5 lg:order-2 group">
              <div className="absolute inset-0 bg-gradient-to-tr from-primary/10 to-accent/10 flex flex-col items-center justify-center p-16 text-center transform group-hover:scale-105 transition-transform duration-700">
                <div className="w-24 h-24 rounded-3xl bg-white flex items-center justify-center shadow-xl mb-8">
                   <Users className="w-10 h-10 text-primary" />
                </div>
                <div className="text-foreground/40 text-xl font-bold italic tracking-tight leading-relaxed">
                  "Visualizing the Sabha Story: Community Mixers, Strategic Workshops, and Peer-to-Peer Growth."
                </div>
              </div>
              <div className="absolute bottom-0 left-0 w-full h-1/3 bg-gradient-to-t from-white to-transparent" />
            </div>
            
            <div className="lg:order-1 space-y-12">
              <div>
                <div className="flex items-center gap-3 mb-6">
                   <div className="w-1.5 h-10 bg-primary rounded-full" />
                   <h2 className="text-3xl font-black italic tracking-tight uppercase">Mission & Vision</h2>
                </div>
                <p className="text-2xl text-foreground/70 font-light italic leading-relaxed">
                  To become the <span className="font-black text-primary">digital heartbeat</span> of professional communities, where every interaction translates into tangible growth.
                </p>
              </div>

              <div className="space-y-6">
                <p className="text-lg text-foreground/50 leading-relaxed font-medium italic">
                  We provide a high-performance platform that simplifies discovery, facilitates showcasing, and fosters a premium ecosystem of trust.
                </p>
                <div className="grid grid-cols-2 gap-8 pt-6">
                  <div>
                    <p className="text-4xl font-black text-primary mb-1 tracking-tighter">500+</p>
                    <p className="text-[10px] font-black uppercase tracking-[0.2em] text-foreground/30">Verified Entities</p>
                  </div>
                  <div>
                    <p className="text-4xl font-black text-primary mb-1 tracking-tighter">12k+</p>
                    <p className="text-[10px] font-black uppercase tracking-[0.2em] text-foreground/30">Connections Built</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Values Grid */}
      <div className="bg-white py-24 sm:py-32 border-y relative overflow-hidden">
        <div className="absolute bottom-0 left-0 w-96 h-96 bg-primary/5 rounded-full blur-[100px] translate-y-1/2 -translate-x-1/2" />
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <div className="mx-auto max-w-2xl text-center mb-24">
             <div className="inline-block bg-primary/10 text-primary px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.3em] mb-6 italic">
               Core Paradigms
             </div>
            <h2 className="text-4xl md:text-5xl font-black tracking-tight text-foreground italic uppercase">What We Stand For</h2>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {values.map((v) => (
              <div key={v.name} className="bg-slate-50 rounded-[2.5rem] p-10 border hover:border-primary/50 transition-all group shadow-xl shadow-foreground/5">
                <div className="w-16 h-16 rounded-[1.5rem] bg-white flex items-center justify-center mb-10 shadow-lg group-hover:scale-110 transition-transform">
                  <v.icon className="w-8 h-8 text-primary" />
                </div>
                <h3 className="text-xl font-black mb-4 italic uppercase tracking-tight group-hover:text-primary transition-colors">{v.name}</h3>
                <p className="text-sm text-foreground/50 leading-relaxed font-medium italic">"{v.description}"</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

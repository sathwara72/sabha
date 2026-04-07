"use client";

import { motion } from "framer-motion";
import { Zap, Camera, Users, Target, Heart, ArrowUpRight } from "lucide-react";
import Link from "next/link";

const galleryImages = [
  { 
    id: 1, 
    url: "https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1080&auto=format&fit=crop",
    title: "Networking Magic",
    category: "Mixer",
    quote: "Connections that turn into corporations."
  },
  { 
    id: 2, 
    url: "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1080&auto=format&fit=crop",
    title: "Strategic Growth",
    category: "Workshop",
    quote: "Learning the pulse of the digital era."
  },
  { 
    id: 3, 
    url: "https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=1080&auto=format&fit=crop",
    title: "Unity in Vision",
    category: "Community",
    quote: "Where peers become partners."
  },
  { 
    id: 4, 
    url: "https://images.unsplash.com/photo-1540317580384-e5d43616b9aa?auto=format&fit=crop&q=80&w=800",
    title: "Innovation Summit",
    category: "Summit",
    quote: "Scaling the peaks of entrepreneurship."
  },
  { 
    id: 5, 
    url: "https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&q=80&w=800",
    title: "Founders Feast",
    category: "Dinner",
    quote: "Building trust over dinner."
  },
  { 
    id: 6, 
    url: "https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=800",
    title: "Startup Synergy",
    category: "Co-working",
    quote: "Shared spaces, shared successes."
  },
];

export default function GalleryPage() {
  return (
    <div className="relative isolate min-h-screen pt-20 overflow-hidden bg-background">
      {/* Background Blobs */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
        <div className="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-primary/10 rounded-full blur-[140px] animate-pulse" />
        <div className="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-accent/10 rounded-full blur-[120px] animate-pulse" />
      </div>

      {/* Immersive Header */}
      <section className="relative py-24 lg:py-32 overflow-hidden border-b border-white/5">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 text-center">
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
          >
             <div className="inline-block px-4 py-2 rounded-full glass border-white/5 mb-8">
              <span className="text-[10px] font-black uppercase tracking-[0.2em] text-white/50">
                Visual Chronicles of Success
              </span>
            </div>
            <h1 className="text-5xl md:text-8xl font-black mb-8 leading-none tracking-tighter uppercase">
              Sabha <span className="text-gradient">Lens.</span>
            </h1>
            <p className="mt-8 text-xl md:text-2xl text-white/50 max-w-2xl mx-auto font-bold leading-relaxed">
              Moments that define our community. Explore the mixers, masterminds, and milestones of Sabha's elite network.
            </p>
          </motion.div>
        </div>
      </section>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-20">
        {/* Gallery Stats Bento */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-24">
          <div className="md:col-span-2 glass p-12 rounded-[3.5rem] border-white/5 flex flex-col justify-between overflow-hidden relative group">
             <div className="absolute top-0 right-0 w-48 h-48 bg-primary/10 rounded-full blur-[80px] group-hover:bg-primary/20 transition-colors" />
             <div className="relative z-10">
               <Camera className="w-12 h-12 text-primary mb-8" />
               <h2 className="text-4xl font-black mb-4 uppercase tracking-tighter text-white">Visual Legacy</h2>
               <p className="text-white/40 text-lg leading-relaxed font-bold">"Every photo tells a story of a business deal made, a partnership formed, or a breakthrough achieved."</p>
             </div>
             <div className="pt-12 flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-primary relative z-10">
                <span className="glass px-6 py-2.5 rounded-full border-white/5">5000+ Captures</span>
                <span className="glass px-6 py-2.5 rounded-full border-white/5">120+ Events</span>
             </div>
          </div>

          <div className="glass p-12 rounded-[3.5rem] border-white/5 flex flex-col justify-center items-center text-center group">
             <div className="w-16 h-16 rounded-3xl bg-primary/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
               <Users className="w-8 h-8 text-primary" />
             </div>
             <h3 className="text-5xl font-black text-white mb-2 uppercase tracking-tight">500+</h3>
             <p className="text-[10px] font-black uppercase tracking-widest text-white/30">Elite Members</p>
          </div>

          <div className="glass p-12 rounded-[3.5rem] border-white/5 flex flex-col justify-center items-center text-center group hover:bg-white/[0.02] transition-colors">
             <div className="w-16 h-16 rounded-3xl bg-accent/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
               <Target className="w-8 h-8 text-accent" />
             </div>
             <h3 className="text-5xl font-black text-white mb-2 uppercase tracking-tight">12+</h3>
             <p className="text-[10px] font-black uppercase tracking-widest text-white/30">Cities Covered</p>
          </div>
        </div>

        {/* Masonry-style Grid */}
        <div className="columns-1 md:columns-2 lg:columns-3 gap-8 space-y-8 mb-40">
          {galleryImages.map((image) => (
            <motion.div
              key={image.id}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              whileHover={{ y: -10 }}
              className="relative group rounded-[3rem] overflow-hidden glass border-white/5 break-inside-avoid"
            >
              <img 
                src={image.url} 
                className="w-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 group-hover:scale-105" 
                alt={image.title}
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-10">
                <div className="glass border-white/20 text-white/80 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest w-fit mb-6">
                  {image.category}
                </div>
                <h3 className="text-3xl font-black text-white mb-4 uppercase tracking-tighter">"{image.title}"</h3>
                <p className="text-white/40 text-sm font-bold uppercase tracking-widest">"{image.quote}"</p>
              </div>
            </motion.div>
          ))}
        </div>

        {/* Final Gallery CTA */}
        <div className="relative py-24 glass rounded-[4rem] border-white/5 overflow-hidden text-center">
          <div className="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full blur-[80px]" />
          <div className="absolute bottom-0 left-0 w-64 h-64 bg-accent/10 rounded-full blur-[80px]" />

          <div className="relative z-10 px-6">
            <div className="inline-flex items-center gap-4 glass px-8 py-4 rounded-full border-white/5 mb-12">
              <Heart className="w-6 h-6 text-primary animate-pulse" />
              <span className="text-[10px] font-black text-white/40 tracking-[0.3em] uppercase">Be part of the next frame</span>
            </div>
            <h2 className="text-4xl md:text-8xl font-black mb-16 uppercase tracking-tighter leading-none">
              Your Business. <br/><span className="text-gradient">Our Spotlight.</span>
            </h2>
            <div className="flex flex-col sm:flex-row justify-center gap-6">
              <Link href="/register" className="btn-premium px-12 py-6 text-lg">
                Join the Community
              </Link>
              <Link href="/events" className="glass border-white/10 text-white px-12 py-6 rounded-3xl font-black text-lg hover:border-primary transition-all flex items-center justify-center gap-3">
                Events Radar
                <ArrowUpRight size={20} />
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

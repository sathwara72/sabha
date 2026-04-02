"use client";

import { motion } from "framer-motion";
import { Zap, Camera, Users, Target, Heart, ArrowUpRight } from "lucide-react";
import Link from "next/link";

const galleryImages = [
  { 
    id: 1, 
    url: "/_next/image?url=%2FUsers%2Fbhaveshsathwara%2F.gemini%2Fantigravity%2Fbrain%2Ff1d73360-eb6c-4af2-b654-b886652608bf%2Fsabha_networking_mixer_1775039898287.png&w=1080&q=75",
    title: "Networking Magic",
    category: "Mixer",
    quote: "Connections that turn into corporations."
  },
  { 
    id: 2, 
    url: "/_next/image?url=%2FUsers%2Fbhaveshsathwara%2F.gemini%2Fantigravity%2Fbrain%2Ff1d73360-eb6c-4af2-b654-b886652608bf%2Fsabha_business_workshop_1775039921602.png&w=1080&q=75",
    title: "Strategic Growth",
    category: "Workshop",
    quote: "Learning the pulse of the digital era."
  },
  { 
    id: 3, 
    url: "/_next/image?url=%2FUsers%2Fbhaveshsathwara%2F.gemini%2Fantigravity%2Fbrain%2Ff1d73360-eb6c-4af2-b654-b886652608bf%2Fsabha_community_growth_1775039940221.png&w=1080&q=75",
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
    <div className="bg-slate-50 min-h-screen">
      {/* Immersive Header */}
      <div className="bg-white py-24 relative overflow-hidden border-b">
        <div className="absolute inset-0 bg-gradient-to-r from-primary/5 to-accent/5 opacity-50" />
        <div className="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3" />
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 text-center">
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
          >
            <h1 className="text-4xl font-black tracking-tight text-foreground sm:text-7xl italic">
              Sabha <span className="text-primary italic">Lens.</span>
            </h1>
            <p className="mt-6 text-xl text-foreground/50 max-w-2xl mx-auto font-medium italic leading-relaxed">
              Moments that define our community. Explore the mixers, masterminds, and milestones of Sabha's elite professional network.
            </p>
          </motion.div>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-20">
        {/* Gallery Stats Bento */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-20">
          <div className="md:col-span-2 bg-white rounded-[2.5rem] p-10 border shadow-xl shadow-foreground/5 flex flex-col justify-between overflow-hidden relative">
             <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl" />
             <div className="relative z-10">
               <Camera className="w-10 h-10 text-primary mb-6" />
               <h2 className="text-3xl font-black mb-2 italic">Visual Legacy</h2>
               <p className="text-foreground/50 leading-relaxed italic font-medium">"Every photo tells a story of a business deal made, a partnership formed, or a breakthrough achieved."</p>
             </div>
             <div className="pt-8 flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-primary relative z-10">
                <span className="bg-primary/10 px-4 py-2 rounded-full border border-primary/20">5000+ Captures</span>
                <span className="bg-primary/10 px-4 py-2 rounded-full border border-primary/20">120+ Events</span>
             </div>
          </div>
          <div className="bg-primary rounded-[2.5rem] p-8 border flex flex-col justify-center items-center text-center shadow-xl shadow-primary/20 overflow-hidden relative group">
             <div className="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity blur-2xl" />
             <Users className="w-8 h-8 mb-4 text-white relative z-10" />
             <h3 className="text-4xl font-black text-white relative z-10 italic">500+</h3>
             <p className="text-[10px] font-black uppercase tracking-widest text-white/60 relative z-10">Elite Members</p>
          </div>
          <div className="bg-white rounded-[2.5rem] p-8 border flex flex-col justify-center items-center text-center transition-all hover:bg-foreground hover:text-white shadow-xl shadow-foreground/5 group overflow-hidden relative">
             <div className="absolute top-0 right-0 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-colors" />
             <Target className="w-8 h-8 mb-4 text-primary relative z-10" />
             <h3 className="text-4xl font-black relative z-10 italic">12+</h3>
             <p className="text-[10px] font-black uppercase tracking-widest text-foreground/40 group-hover:text-white/40 relative z-10">Cities Covered</p>
          </div>
        </div>

        {/* Masonry-style Grid */}
        <div className="columns-1 md:columns-2 lg:columns-3 gap-8 space-y-8">
          {galleryImages.map((image) => (
            <motion.div
              key={image.id}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              className="relative group rounded-[2rem] overflow-hidden border bg-white break-inside-avoid shadow-xl shadow-foreground/5 hover:shadow-2xl transition-all"
            >
              <img 
                src={image.url} 
                className="w-full object-cover group-hover:scale-105 transition-transform duration-700" 
                alt={image.title}
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-8">
                <div className="bg-primary/20 backdrop-blur-md border border-white/20 text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest w-fit mb-4 italic">
                  {image.category}
                </div>
                <h3 className="text-2xl font-black text-white italic mb-2">"{image.title}"</h3>
                <p className="text-white/60 text-sm font-medium italic">"{image.quote}"</p>
              </div>
            </motion.div>
          ))}
        </div>

        {/* Final Gallery CTA */}
        <div className="mt-40 text-center">
          <div className="inline-flex items-center gap-3 bg-white px-6 py-3 rounded-full border shadow-xl shadow-foreground/5 mb-12">
            <Heart className="w-5 h-5 text-primary fill-current" />
            <span className="text-[10px] font-black text-foreground/40 tracking-[0.2em] uppercase">Be part of the next frame</span>
          </div>
          <h2 className="text-4xl md:text-7xl font-black mb-12 italic tracking-tight uppercase">Your Business. <br/><span className="text-gradient">Our Spotlight.</span></h2>
          <div className="flex flex-col sm:flex-row justify-center gap-4">
            <Link 
              href="/register" 
              className="px-12 py-5 rounded-[2rem] bg-primary text-white font-black text-lg shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all hover:scale-105 active:scale-95"
            >
              Join the Community
            </Link>
            <Link 
              href="/events" 
              className="px-12 py-5 rounded-[2rem] border-2 border-foreground text-foreground font-black text-lg hover:bg-foreground hover:text-white transition-all flex items-center justify-center gap-2 active:scale-95"
            >
              Upcoming Events
              <ArrowUpRight className="w-5 h-5" />
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}

"use client";

import { Mail, Phone, MapPin, Send, Globe, MessageCircle, Link as LinkIcon } from "lucide-react";
import { motion } from "framer-motion";

export default function ContactPage() {
  return (
    <div className="bg-slate-50 min-h-screen">
      {/* Hero Section */}
      <div className="bg-white py-24 sm:py-32 relative overflow-hidden border-b">
        <div className="absolute inset-0 bg-gradient-to-r from-primary/5 to-accent/5 opacity-50" />
        <div className="absolute top-0 right-0 w-[600px] h-[600px] bg-primary/5 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3" />
        
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 text-center">
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
          >
            <h1 className="text-4xl md:text-7xl font-black tracking-tight text-foreground sm:text-6xl italic uppercase">
              Get in <span className="text-gradient">Touch.</span>
            </h1>
            <p className="mt-8 text-xl leading-8 text-foreground/50 max-w-2xl mx-auto font-medium italic">
              "Have questions about becoming a member or organizing an event? Our community managers are here to bridge the gap."
            </p>
          </motion.div>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-24">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-20">
          {/* Contact Information */}
          <div className="space-y-16">
            <div>
              <div className="flex items-center gap-3 mb-6">
                 <div className="w-1.5 h-10 bg-primary rounded-full" />
                 <h2 className="text-3xl font-black italic tracking-tight uppercase">Contact Hub</h2>
              </div>
              <p className="text-xl text-foreground/50 leading-relaxed italic font-medium">Reach out through our strategic channels. Precision in communication is our standard.</p>
            </div>

            <div className="space-y-8">
              {[
                { name: "Global Email", detail: "hello@sabha.com", icon: Mail },
                { name: "Direct Signal", detail: "+91 91234 56789", icon: Phone },
                { name: "HQ Coordinates", detail: "Level 4, Business Park, Phase 2, Mumbai - 400001", icon: MapPin },
              ].map((item) => (
                <div key={item.name} className="flex items-start gap-8 group">
                  <div className="w-16 h-16 rounded-3xl bg-white flex items-center justify-center shrink-0 border shadow-xl shadow-foreground/5 group-hover:border-primary/50 transition-all">
                    <item.icon className="w-7 h-7 text-primary" />
                  </div>
                  <div>
                    <h3 className="text-sm font-black text-foreground/30 uppercase tracking-[0.2em] mb-1">{item.name}</h3>
                    <p className="text-lg font-bold text-foreground italic">{item.detail}</p>
                  </div>
                </div>
              ))}
            </div>

            <div>
              <h3 className="text-[10px] font-black text-foreground/30 uppercase tracking-[0.3em] mb-8 border-b pb-4">Social Frequency</h3>
              <div className="flex gap-4">
                {[Globe, LinkIcon, MessageCircle].map((Icon, i) => (
                  <button key={i} className="w-12 h-12 rounded-2xl border bg-white flex items-center justify-center hover:bg-primary transition-all group shadow-sm">
                    <Icon className="w-5 h-5 text-foreground/40 group-hover:text-white transition-colors" />
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* Contact Form */}
          <div className="bg-white p-12 rounded-[3rem] border shadow-2xl shadow-foreground/5 relative overflow-hidden group">
            <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors" />
            <form className="space-y-8 relative z-10" onSubmit={(e) => e.preventDefault()}>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div className="space-y-3">
                  <label className="text-[10px] font-black text-foreground/30 uppercase tracking-widest px-1">Full Identity</label>
                  <input
                    type="text"
                    placeholder="E.g. John Doe"
                    className="w-full bg-slate-50 border rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-bold placeholder:text-foreground/20 italic"
                  />
                </div>
                <div className="space-y-3">
                  <label className="text-[10px] font-black text-foreground/30 uppercase tracking-widest px-1">Email Node</label>
                  <input
                    type="email"
                    placeholder="john@example.com"
                    className="w-full bg-slate-50 border rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-bold placeholder:text-foreground/20 italic"
                  />
                </div>
              </div>

              <div className="space-y-3">
                <label className="text-[10px] font-black text-foreground/30 uppercase tracking-widest px-1">Engagement Subject</label>
                <input
                  type="text"
                  placeholder="Inquiry about strategic membership"
                  className="w-full bg-slate-50 border rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-bold placeholder:text-foreground/20 italic"
                />
              </div>

              <div className="space-y-3">
                <label className="text-[10px] font-black text-foreground/30 uppercase tracking-widest px-1">Detailed Inquiry</label>
                <textarea
                  rows={4}
                  placeholder="How can we help you scale your business ecosystem?"
                  className="w-full bg-slate-50 border rounded-2xl py-4 px-6 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all resize-none font-bold placeholder:text-foreground/20 italic"
                />
              </div>

              <button
                type="submit"
                className="w-full py-5 rounded-[2rem] bg-primary text-white font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-primary/30 hover:bg-primary/90 transition-all flex items-center justify-center gap-3 group active:scale-95"
              >
                Send Message
                <Send className="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
              </button>
            </form>
          </div>
        </div>
      </div>

      {/* Aesthetic Section */}
      <div className="mx-auto max-w-7xl px-6 lg:px-8 pb-24">
        <div className="h-[500px] w-full rounded-[4rem] bg-white border overflow-hidden relative group shadow-2xl shadow-foreground/5">
          <div className="absolute inset-0 bg-gradient-to-tr from-primary/10 via-transparent to-accent/10 pointer-events-none" />
          <div className="absolute inset-0 flex flex-col items-center justify-center text-center p-12">
            <h4 className="text-4xl md:text-6xl font-black text-foreground/10 uppercase italic mb-6 select-none">Strategic Networking</h4>
            <div className="max-w-2xl text-xl text-foreground/40 font-medium italic leading-relaxed">
              "Interaction with our community peaks through exclusive events, strategic mixers and peer-to-peer growth workshops. We are the bridge to your next breakthrough."
            </div>
          </div>
          <div className="absolute bottom-12 left-1/2 -translate-x-1/2 w-2 h-24 bg-gradient-to-b from-primary to-transparent rounded-full opacity-20" />
        </div>
      </div>
    </div>
  );
}

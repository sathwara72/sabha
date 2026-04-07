"use client";

import { Mail, Phone, MapPin, Send, Globe, MessageCircle, Link as LinkIcon, ArrowRight } from "lucide-react";
import { motion } from "framer-motion";

export default function ContactPage() {
  return (
    <div className="relative isolate min-h-screen pt-20 overflow-hidden bg-background font-outfit">
      {/* Background Blobs */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
        <div className="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-primary/10 rounded-full blur-[140px] animate-pulse" />
        <div className="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-accent/10 rounded-full blur-[120px] animate-pulse" />
      </div>

      {/* Hero Header */}
      <section className="relative py-24 lg:py-32 overflow-hidden border-b border-white/5">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 text-center">
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
          >
             <div className="inline-block px-4 py-2 rounded-full glass border-white/5 mb-8">
              <span className="text-[10px] font-black uppercase tracking-[0.2em] text-white/50">
                Strategic Communication Hub
              </span>
            </div>
            <h1 className="text-5xl md:text-8xl font-black mb-8 leading-none tracking-tighter uppercase">
              Get in <span className="text-gradient">Touch.</span>
            </h1>
            <p className="mt-8 text-xl md:text-2xl text-white/50 max-w-2xl mx-auto font-bold leading-relaxed">
              Have questions about membership or events? Our community managers are here to bridge the gap.
            </p>
          </motion.div>
        </div>
      </section>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-24">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-24">
          {/* Contact Information */}
          <div className="space-y-20">
            <div>
              <h2 className="text-4xl font-black text-white mb-6 uppercase tracking-tighter">Contact Channels</h2>
              <p className="text-xl text-white/40 leading-relaxed font-bold">Reach out through our strategic channels. Precision in communication is our standard.</p>
            </div>

            <div className="space-y-12">
              {[
                { name: "Global Email", detail: "hello@sabha.com", icon: Mail },
                { name: "Direct Signal", detail: "+91 91234 56789", icon: Phone },
                { name: "HQ Coordinates", detail: "Level 4, Business Park, Phase 2, Mumbai - 400001", icon: MapPin },
              ].map((item) => (
                <div key={item.name} className="flex items-start gap-8 group">
                  <div className="w-20 h-20 rounded-[2.5rem] glass border-white/5 flex items-center justify-center shrink-0 group-hover:border-primary/50 transition-all">
                    <item.icon className="w-8 h-8 text-primary" />
                  </div>
                  <div>
                    <h3 className="text-[10px] font-black text-white/20 uppercase tracking-[0.25em] mb-2">{item.name}</h3>
                    <p className="text-2xl font-black text-white uppercase tracking-tight">{item.detail}</p>
                  </div>
                </div>
              ))}
            </div>

            <div>
              <h3 className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] mb-8 border-b border-white/5 pb-4">Social Frequency</h3>
              <div className="flex gap-4">
                {[Globe, LinkIcon, MessageCircle].map((Icon, i) => (
                  <button key={i} className="w-16 h-16 rounded-3xl glass border-white/5 flex items-center justify-center hover:bg-primary hover:border-primary transition-all group">
                    <Icon className="w-6 h-6 text-white/30 group-hover:text-white transition-colors" />
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* Contact Form */}
          <div className="glass p-12 rounded-[4rem] border-white/5 relative group">
            <div className="absolute top-0 right-0 w-48 h-48 bg-primary/10 rounded-full blur-[100px] group-hover:bg-primary/20 transition-colors" />
            <form className="space-y-10 relative z-10" onSubmit={(e) => e.preventDefault()}>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div className="space-y-4">
                  <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Identity</label>
                  <input
                    type="text"
                    placeholder="Full Name"
                    className="w-full glass border-white/5 rounded-3xl py-5 px-8 focus:border-primary outline-none transition-all font-bold text-white placeholder:text-white/10"
                  />
                </div>
                <div className="space-y-4">
                  <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Node</label>
                  <input
                    type="email"
                    placeholder="Email Address"
                    className="w-full glass border-white/5 rounded-3xl py-5 px-8 focus:border-primary outline-none transition-all font-bold text-white placeholder:text-white/10"
                  />
                </div>
              </div>

              <div className="space-y-4">
                <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Subject</label>
                <input
                  type="text"
                  placeholder="Inquiry Subject"
                  className="w-full glass border-white/5 rounded-3xl py-5 px-8 focus:border-primary outline-none transition-all font-bold text-white placeholder:text-white/10"
                />
              </div>

              <div className="space-y-4">
                <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Manifesto</label>
                <textarea
                  rows={4}
                  placeholder="How can we help you scale?"
                  className="w-full glass border-white/5 rounded-3xl py-6 px-8 focus:border-primary outline-none transition-all resize-none font-bold text-white placeholder:text-white/10"
                />
              </div>

              <button
                type="submit"
                className="btn-premium w-full py-6 text-sm flex items-center justify-center gap-4 group"
              >
                Launch Message
                <Send className="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
              </button>
            </form>
          </div>
        </div>
      </div>

      {/* Aesthetic Section */}
      <div className="mx-auto max-w-7xl px-6 lg:px-8 pb-32">
        <div className="relative py-24 glass rounded-[5rem] border-white/5 overflow-hidden text-center group">
          <div className="absolute inset-0 bg-gradient-to-tr from-primary/10 via-transparent to-accent/10 pointer-events-none" />
          <div className="relative z-10 px-12">
            <h4 className="text-4xl md:text-7xl font-black text-white/10 uppercase mb-8 select-none group-hover:text-primary/10 transition-colors">Strategic Networking</h4>
            <p className="max-w-3xl mx-auto text-xl md:text-2xl text-white/30 font-bold leading-relaxed">
              Interaction with our community peaks through exclusive events and strategic mixers. We are the bridge to your next breakthrough.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}

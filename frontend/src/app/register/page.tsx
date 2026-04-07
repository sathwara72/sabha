"use client";

import Link from "next/link";
import { User, Mail, Lock, Phone, ArrowRight, ShieldCheck, Zap, Briefcase, Star } from "lucide-react";
import { motion } from "framer-motion";

export default function RegisterPage() {
  return (
    <div className="relative min-h-screen flex items-center justify-center overflow-hidden bg-background font-outfit px-6 pt-32 pb-20">
      {/* Background Blobs */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
        <div className="absolute top-[-5%] right-[-10%] w-[600px] h-[600px] bg-primary/10 rounded-full blur-[140px] animate-pulse" />
        <div className="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-accent/10 rounded-full blur-[120px] animate-pulse delay-700" />
      </div>

      <motion.div 
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        className="relative z-10 w-full max-w-3xl"
      >
        <div className="text-center mb-16">
          <Link href="/" className="inline-flex items-center gap-4 mb-10 group">
            <div className="w-14 h-14 bg-premium rounded-[1.25rem] flex items-center justify-center shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform">
              <Zap className="w-7 h-7 text-white" />
            </div>
            <span className="text-4xl font-black text-white tracking-tighter uppercase">Sabha<span className="text-primary italic-none">.</span></span>
          </Link>
          <h1 className="text-5xl md:text-7xl font-black text-white mb-4 uppercase tracking-tighter leading-none">Join the Elite.</h1>
          <p className="text-white/30 font-black uppercase tracking-[0.4em] text-[10px]">Scale your business with verified industry partners</p>
        </div>

        <div className="glass rounded-[4rem] p-12 md:p-16 border-white/5 relative overflow-hidden">
          <div className="absolute top-0 right-0 w-48 h-48 bg-primary/5 rounded-full blur-3xl pointer-events-none" />
          
          <div className="flex items-center gap-6 glass border-white/10 p-6 rounded-[2rem] mb-12">
            <div className="w-14 h-14 rounded-2xl bg-primary/20 flex items-center justify-center border border-primary/20 shrink-0">
               <ShieldCheck className="w-7 h-7 text-primary" />
            </div>
            <p className="text-xs font-black text-white/40 leading-relaxed uppercase tracking-widest">
              "Your profile will be vetted by our community moderators for professional integrity and authenticity."
            </p>
          </div>

          <form className="grid grid-cols-1 md:grid-cols-2 gap-10" onSubmit={(e) => e.preventDefault()}>
            {/* Full Name */}
            <div className="space-y-4 md:col-span-2">
              <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Business or Founder Identity</label>
              <div className="relative group">
                <User className="absolute left-8 top-1/2 -translate-y-1/2 w-6 h-6 text-white/10 group-focus-within:text-primary transition-colors" />
                <input
                  type="text"
                  placeholder="E.g. John Doe / Acme Corp"
                  className="w-full glass border-white/5 rounded-3xl py-6 pl-20 pr-8 text-white font-bold outline-none focus:border-primary transition-all placeholder:text-white/5"
                />
              </div>
            </div>

            {/* Email */}
            <div className="space-y-4">
              <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Elite Node (Email)</label>
              <div className="relative group">
                <Mail className="absolute left-8 top-1/2 -translate-y-1/2 w-6 h-6 text-white/10 group-focus-within:text-primary transition-colors" />
                <input
                  type="email"
                  placeholder="name@company.com"
                  className="w-full glass border-white/5 rounded-3xl py-6 pl-20 pr-8 text-white font-bold outline-none focus:border-primary transition-all placeholder:text-white/5"
                />
              </div>
            </div>

            {/* Phone */}
            <div className="space-y-4">
              <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Direct Signal (Phone)</label>
              <div className="relative group">
                <Phone className="absolute left-8 top-1/2 -translate-y-1/2 w-6 h-6 text-white/10 group-focus-within:text-primary transition-colors" />
                <input
                  type="tel"
                  placeholder="+91 99999 55555"
                  className="w-full glass border-white/5 rounded-3xl py-6 pl-20 pr-8 text-white font-bold outline-none focus:border-primary transition-all placeholder:text-white/5"
                />
              </div>
            </div>

            {/* Password */}
            <div className="space-y-4">
              <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Secure Key (Password)</label>
              <div className="relative group">
                <Lock className="absolute left-8 top-1/2 -translate-y-1/2 w-6 h-6 text-white/10 group-focus-within:text-primary transition-colors" />
                <input
                  type="password"
                  placeholder="••••••••"
                  className="w-full glass border-white/5 rounded-3xl py-6 pl-20 pr-8 text-white font-bold outline-none focus:border-primary transition-all placeholder:text-white/5"
                />
              </div>
            </div>

            {/* Confirm Password */}
            <div className="space-y-4">
              <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Confirmation</label>
              <div className="relative group">
                <Lock className="absolute left-8 top-1/2 -translate-y-1/2 w-6 h-6 text-white/10 group-focus-within:text-primary transition-colors" />
                <input
                  type="password"
                  placeholder="••••••••"
                  className="w-full glass border-white/5 rounded-3xl py-6 pl-20 pr-8 text-white font-bold outline-none focus:border-primary transition-all placeholder:text-white/5"
                />
              </div>
            </div>

            <div className="md:col-span-2 pt-8">
              <button
                type="submit"
                className="btn-premium w-full py-6 text-lg flex items-center justify-center gap-4 group"
              >
                Create Elite Profile
                <ArrowRight className="w-6 h-6 group-hover:translate-x-2 transition-transform" />
              </button>
            </div>
          </form>

          <div className="mt-16 text-center space-y-8 pt-12 border-t border-white/5">
             <div className="flex flex-col gap-4">
              <span className="text-[10px] font-black text-white/10 uppercase tracking-[0.4em]">Already have access?</span>
              <Link href="/login" className="text-white hover:text-primary transition-colors font-black text-xl uppercase tracking-tighter">Sign in to Node</Link>
            </div>
          </div>
        </div>

        <div className="mt-12 flex justify-center gap-12 opacity-30">
           <div className="flex items-center gap-3">
              <Briefcase className="w-5 h-5 text-primary" />
              <span className="text-[10px] font-black text-white tracking-widest uppercase">Business Focused</span>
           </div>
           <div className="flex items-center gap-3">
              <ShieldCheck className="w-5 h-5 text-accent" />
              <span className="text-[10px] font-black text-white tracking-widest uppercase">Verified Network</span>
           </div>
        </div>
      </motion.div>
    </div>
  );
}

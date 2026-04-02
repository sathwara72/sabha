"use client";

import Link from "next/link";
import { User, Mail, Lock, Phone, ArrowRight, ShieldCheck, Zap, Briefcase } from "lucide-react";
import { motion } from "framer-motion";

export default function RegisterPage() {
  return (
    <div className="relative min-h-[calc(100vh-80px)] flex items-center justify-center overflow-hidden bg-white py-32 px-6">
      {/* Dynamic Background Elements */}
      <div className="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div className="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-primary/10 blur-[150px] rounded-full animate-pulse" />
        <div className="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-accent/5 blur-[150px] rounded-full animate-pulse delay-700" />
        <div className="absolute top-0 left-0 w-full h-full border border-foreground/5 opacity-5 pointer-events-none" />
      </div>

      <motion.div 
        initial={{ opacity: 0, y: 30 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative z-10 w-full max-w-2xl"
      >
        <div className="text-center mb-16">
          <Link href="/" className="inline-flex items-center gap-2 mb-8 group">
            <div className="w-12 h-12 bg-primary rounded-2xl flex items-center justify-center p-2 shadow-lg shadow-primary/30 group-hover:scale-110 transition-transform">
              <Zap className="w-6 h-6 text-white" />
            </div>
            <span className="text-3xl font-black text-foreground italic tracking-widest">Sabha<span className="text-primary">.</span></span>
          </Link>
          <h1 className="text-4xl md:text-6xl font-black text-foreground mb-2 italic tracking-tight">Join the Elite.</h1>
          <p className="text-foreground/40 font-bold uppercase tracking-[0.3em] text-[10px]">Scale your business with verified industry partners</p>
        </div>

        <div className="glass rounded-[3.5rem] p-10 md:p-16 border shadow-2xl shadow-foreground/5 relative">
          <div className="absolute -top-6 -right-6 w-24 h-24 bg-primary/10 rounded-full blur-3xl opacity-20" />
          
          <div className="flex items-center gap-4 bg-primary/5 p-4 rounded-2xl border border-primary/10 mb-12">
            <div className="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20 shrink-0">
               <ShieldCheck className="w-5 h-5 text-primary" />
            </div>
            <p className="text-xs font-bold text-foreground/60 leading-relaxed italic">
              "Your profile will be vetted by our community moderators for professional integrity and authenticity."
            </p>
          </div>

          <form className="grid grid-cols-1 md:grid-cols-2 gap-8" onSubmit={(e) => e.preventDefault()}>
            {/* Full Name */}
            <div className="space-y-3 md:col-span-2">
              <label className="text-[10px] font-black text-foreground/40 uppercase tracking-widest ml-4">Full Business Name / Personal Name</label>
              <div className="relative group">
                <User className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-foreground/20" />
                <input
                  type="text"
                  placeholder="John Doe / Acme Corp"
                  className="w-full bg-white border border-foreground/10 rounded-2xl py-5 pl-16 pr-6 text-foreground font-bold outline-none focus:border-primary transition-all shadow-inner"
                />
              </div>
            </div>

            {/* Email */}
            <div className="space-y-3">
              <label className="text-[10px] font-black text-foreground/40 uppercase tracking-widest ml-4">Elite Email</label>
              <div className="relative group">
                <Mail className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-foreground/20" />
                <input
                  type="email"
                  placeholder="name@company.com"
                  className="w-full bg-white border border-foreground/10 rounded-2xl py-5 pl-16 pr-6 text-foreground font-bold outline-none focus:border-primary transition-all shadow-inner"
                />
              </div>
            </div>

            {/* Phone */}
            <div className="space-y-3">
              <label className="text-[10px] font-black text-foreground/40 uppercase tracking-widest ml-4">Direct Contact</label>
              <div className="relative group">
                <Phone className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-foreground/20" />
                <input
                  type="tel"
                  placeholder="+91 99999 55555"
                  className="w-full bg-white border border-foreground/10 rounded-2xl py-5 pl-16 pr-6 text-foreground font-bold outline-none focus:border-primary transition-all shadow-inner"
                />
              </div>
            </div>

            {/* Password */}
            <div className="space-y-3">
              <label className="text-[10px] font-black text-foreground/40 uppercase tracking-widest ml-4">Secure Password</label>
              <div className="relative group">
                <Lock className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-foreground/20" />
                <input
                  type="password"
                  placeholder="••••••••"
                  className="w-full bg-white border border-foreground/10 rounded-2xl py-5 pl-16 pr-6 text-foreground font-bold outline-none focus:border-primary transition-all shadow-inner"
                />
              </div>
            </div>

            {/* Confirm Password */}
            <div className="space-y-3">
              <label className="text-[10px] font-black text-foreground/40 uppercase tracking-widest ml-4">Confirm Key</label>
              <div className="relative group">
                <Lock className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-foreground/20" />
                <input
                  type="password"
                  placeholder="••••••••"
                  className="w-full bg-white border border-foreground/10 rounded-2xl py-5 pl-16 pr-6 text-foreground font-bold outline-none focus:border-primary transition-all shadow-inner"
                />
              </div>
            </div>

            <div className="md:col-span-2 pt-6">
              <button
                type="submit"
                className="w-full py-5 rounded-2xl bg-primary text-white font-black text-lg shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 group"
              >
                Create Elite Profile
                <ArrowRight className="w-5 h-5 group-hover:translate-x-2 transition-transform" />
              </button>
            </div>
          </form>

          <div className="mt-12 text-center pt-10 border-t border-foreground/5">
            <p className="text-sm font-bold text-foreground/40 uppercase tracking-widest">
              Part of the network?{" "}
              <Link href="/login" className="text-primary hover:underline ml-2 italic">Sign in to Access</Link>
            </p>
          </div>
        </div>

        <div className="mt-12 flex justify-center gap-12 text-foreground/20 text-[10px] font-black uppercase tracking-widest">
           <div className="flex items-center gap-2">
              <Briefcase className="w-4 h-4 text-primary" />
              Business Focused
           </div>
           <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-primary" />
              100% Verified Network
           </div>
        </div>
      </motion.div>
    </div>
  );
}

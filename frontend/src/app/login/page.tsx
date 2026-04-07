"use client";

import Link from "next/link";
import { User, Lock, ArrowRight, ShieldCheck, Zap, Star } from "lucide-react";
import { motion } from "framer-motion";

export default function LoginPage() {
  return (
    <div className="relative min-h-screen flex items-center justify-center overflow-hidden bg-background font-outfit px-6 pt-20 pb-20">
      {/* Background Blobs */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
        <div className="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-primary/10 rounded-full blur-[140px] animate-pulse" />
        <div className="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-accent/10 rounded-full blur-[120px] animate-pulse" />
      </div>

      <motion.div 
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        className="relative z-10 w-full max-w-xl"
      >
        <div className="text-center mb-12">
          <Link href="/" className="inline-flex items-center gap-4 mb-10 group">
            <div className="w-14 h-14 bg-premium rounded-[1.25rem] flex items-center justify-center shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform">
              <Zap className="w-7 h-7 text-white" />
            </div>
            <span className="text-4xl font-black text-white tracking-tighter uppercase">Sabha<span className="text-primary italic-none">.</span></span>
          </Link>
          <h1 className="text-5xl font-black text-white mb-4 uppercase tracking-tighter">Elite Login</h1>
          <p className="text-white/30 font-black uppercase tracking-[0.3em] text-[10px]">A Restricted Community for Professional Growth</p>
        </div>

        <div className="glass rounded-[4rem] p-12 md:p-16 border-white/5 relative overflow-hidden">
          <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl pointer-events-none" />
          
          <div className="flex items-center gap-4 glass border-white/10 px-6 py-3 rounded-full mb-12 w-fit">
            <ShieldCheck className="w-5 h-5 text-primary" />
            <span className="text-[10px] font-black text-white/50 uppercase tracking-widest">Verified Access Node</span>
          </div>

          <form className="space-y-10" onSubmit={(e) => e.preventDefault()}>
            <div className="space-y-4">
              <label className="text-[10px] font-black text-white/20 uppercase tracking-widest px-2">Elite ID (Email)</label>
              <div className="relative group">
                <User className="absolute left-8 top-1/2 -translate-y-1/2 w-6 h-6 text-white/10 group-focus-within:text-primary transition-colors" />
                <input
                  type="email"
                  placeholder="id@sabha.elite"
                  className="w-full glass border-white/5 rounded-3xl py-6 pl-20 pr-8 text-white font-bold outline-none focus:border-primary transition-all placeholder:text-white/5"
                />
              </div>
            </div>

            <div className="space-y-4">
              <div className="flex items-center justify-between px-2">
                <label className="text-[10px] font-black text-white/20 uppercase tracking-widest">Master Key (Password)</label>
                <Link href="/forgot-password" title="Wait for later" className="text-[10px] font-black text-primary hover:text-white transition-colors uppercase tracking-widest">Forgot?</Link>
              </div>
              <div className="relative group">
                <Lock className="absolute left-8 top-1/2 -translate-y-1/2 w-6 h-6 text-white/10 group-focus-within:text-primary transition-colors" />
                <input
                  type="password"
                  placeholder="••••••••"
                  className="w-full glass border-white/5 rounded-3xl py-6 pl-20 pr-8 text-white font-bold outline-none focus:border-primary transition-all placeholder:text-white/5"
                />
              </div>
            </div>

            <button
              type="submit"
              className="btn-premium w-full py-6 text-lg group"
            >
              Unlock Access
              <ArrowRight className="w-6 h-6 group-hover:translate-x-2 transition-transform" />
            </button>
          </form>

          <div className="mt-16 text-center space-y-8 pt-12 border-t border-white/5">
            <p className="text-white/20 text-sm font-bold uppercase tracking-widest max-w-xs mx-auto">"The most powerful tool in business is the person sitting next to you."</p>
            <div className="flex flex-col gap-4">
              <span className="text-[10px] font-black text-white/10 uppercase tracking-[0.4em]">Not a Member?</span>
              <Link href="/register" className="text-white hover:text-primary transition-colors font-black text-xl uppercase tracking-tighter">Join the Elite</Link>
            </div>
          </div>
        </div>

        <div className="mt-12 flex justify-center gap-10 opacity-30">
           <div className="flex items-center gap-3">
              <Star className="w-5 h-5 text-accent fill-accent" />
              <span className="text-[10px] font-black text-white tracking-widest uppercase">Premium Security</span>
           </div>
           <div className="flex items-center gap-3">
              <ShieldCheck className="w-5 h-5 text-primary" />
              <span className="text-[10px] font-black text-white tracking-widest uppercase">Verified Network</span>
           </div>
        </div>
      </motion.div>
    </div>
  );
}

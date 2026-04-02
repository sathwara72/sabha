"use client";

import Link from "next/link";
import { User, Lock, ArrowRight, ShieldCheck, Zap, Star } from "lucide-react";
import { motion } from "framer-motion";

export default function LoginPage() {
  return (
    <div className="relative min-h-[calc(100vh-80px)] flex items-center justify-center overflow-hidden bg-white py-20 px-6">
      {/* Dynamic Background Elements */}
      <div className="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div className="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/10 blur-[120px] rounded-full animate-pulse" />
        <div className="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-accent/5 blur-[120px] rounded-full animate-pulse delay-1000" />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[60%] h-[60%] border border-foreground/5 rounded-full scale-[1.5] rotate-45" />
      </div>

      <motion.div 
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative z-10 w-full max-w-lg"
      >
        <div className="text-center mb-12">
          <Link href="/" className="inline-flex items-center gap-2 mb-8 group">
            <div className="w-12 h-12 bg-primary rounded-2xl flex items-center justify-center p-2 shadow-lg shadow-primary/30 group-hover:scale-110 transition-transform">
              <Zap className="w-6 h-6 text-white" />
            </div>
            <span className="text-3xl font-black text-foreground italic tracking-widest">Sabha<span className="text-primary">.</span></span>
          </Link>
          <h1 className="text-4xl font-black text-foreground mb-2 italic">Elite Login</h1>
          <p className="text-foreground/40 font-bold uppercase tracking-[0.2em] text-xs">A Restricted Community for Professional Growth</p>
        </div>

        <div className="glass rounded-[3rem] p-10 md:p-16 border shadow-2xl shadow-foreground/5">
          <div className="flex items-center gap-3 bg-primary/5 p-4 rounded-2xl border border-primary/10 mb-10">
            <ShieldCheck className="w-5 h-5 text-primary" />
            <span className="text-xs font-bold text-foreground/60 italic">Verified Access Only</span>
          </div>

          <form className="space-y-8" onSubmit={(e) => e.preventDefault()}>
            <div className="space-y-3">
              <label className="text-[10px] font-black text-foreground/40 uppercase tracking-widest ml-4">Elite ID (Email)</label>
              <div className="relative group">
                <div className="absolute inset-0 bg-primary/10 blur-xl opacity-0 group-focus-within:opacity-100 transition-opacity" />
                <div className="relative">
                  <User className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-foreground/20" />
                  <input
                    type="email"
                    placeholder="id@sabha.elite"
                    className="w-full bg-white border border-foreground/10 rounded-2xl py-5 pl-16 pr-6 text-foreground font-bold outline-none focus:border-primary transition-all shadow-inner"
                  />
                </div>
              </div>
            </div>

            <div className="space-y-3">
              <div className="flex items-center justify-between ml-4">
                <label className="text-[10px] font-black text-foreground/40 uppercase tracking-widest">Master Key (Password)</label>
                <Link href="/forgot-password" title="Wait for later" className="text-[10px] font-black text-primary hover:underline uppercase tracking-widest">Forgot?</Link>
              </div>
              <div className="relative group">
                <div className="absolute inset-0 bg-primary/10 blur-xl opacity-0 group-focus-within:opacity-100 transition-opacity" />
                <div className="relative">
                  <Lock className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-foreground/20" />
                  <input
                    type="password"
                    placeholder="••••••••"
                    className="w-full bg-white border border-foreground/10 rounded-2xl py-5 pl-16 pr-6 text-foreground font-bold outline-none focus:border-primary transition-all shadow-inner"
                  />
                </div>
              </div>
            </div>

            <button
              type="submit"
              className="w-full py-5 rounded-2xl bg-primary text-white font-black text-lg shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 group"
            >
              Unlock Access
              <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
            </button>
          </form>

          <div className="mt-12 text-center space-y-6 pt-10 border-t border-foreground/5">
            <p className="text-foreground/30 text-sm font-medium italic">"The most powerful tool in business is the person sitting next to you."</p>
            <p className="text-sm font-bold text-foreground/60 uppercase tracking-widest">
              Not a Member?{" "}
              <Link href="/register" className="text-primary hover:underline ml-2 italic">Join the Elite</Link>
            </p>
          </div>
        </div>

        <div className="mt-8 flex justify-center gap-8 opacity-40">
           <div className="flex items-center gap-2">
              <Star className="w-4 h-4 text-amber-500 fill-current" />
              <span className="text-[10px] font-black text-foreground tracking-widest uppercase">Premium Security</span>
           </div>
           <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-primary" />
              <span className="text-[10px] font-black text-foreground tracking-widest uppercase">Verified Network</span>
           </div>
        </div>
      </motion.div>
    </div>
  );
}

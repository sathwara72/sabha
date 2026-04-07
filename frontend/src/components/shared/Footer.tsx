"use client";

import Link from "next/link";
import { Users, Mail, Phone, MapPin, Globe, MessageSquare, ArrowUpRight, Code, Send, Briefcase } from "lucide-react";

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-[#050505] text-white pt-24 pb-12 border-t border-white/5 relative overflow-hidden">
      {/* Background decoration */}
      <div className="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-[100px] -z-10" />
      <div className="absolute bottom-0 left-0 w-96 h-96 bg-accent/5 rounded-full blur-[100px] -z-10" />

      <div className="mx-auto max-w-7xl px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pb-16 border-b border-white/5">
          {/* Brand Column */}
          <div className="space-y-6">
            <Link href="/" className="flex items-center gap-3 group">
              <div className="w-10 h-10 bg-premium rounded-xl flex items-center justify-center glow-purple">
                <Users className="w-6 h-6 text-white" />
              </div>
              <span className="text-2xl font-black tracking-tighter">SABHA.</span>
            </Link>
            <p className="text-white/40 text-sm leading-relaxed max-w-xs font-medium">
              The premier platform for high-impact professional communities. Connect, collaborate, and scale with global visionaries.
            </p>
            <div className="flex gap-4">
              {[Send, Code, Briefcase].map((Icon, i) => (
                <button key={i} className="w-10 h-10 rounded-xl glass border-white/5 flex items-center justify-center hover:bg-primary hover:border-primary transition-all group active:scale-95">
                  <Icon className="w-5 h-5 text-white/40 group-hover:text-white" />
                </button>
              ))}
            </div>
          </div>

          {/* Quick Links */}
          <div className="space-y-6">
            <h3 className="text-sm font-black uppercase tracking-[0.2em] text-primary">Platform</h3>
            <ul className="space-y-4">
              {[
                { name: "Businesses", href: "/businesses" },
                { name: "Events", href: "/events" },
                { name: "Gallery", href: "/gallery" },
                { name: "About Us", href: "/about" },
              ].map((link) => (
                <li key={link.name}>
                  <Link 
                    href={link.href} 
                    className="text-white/40 hover:text-white text-sm flex items-center gap-2 transition-colors group font-bold tracking-wide uppercase"
                  >
                    {link.name}
                    <ArrowUpRight className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact Details */}
          <div className="space-y-6">
            <h3 className="text-sm font-black uppercase tracking-[0.2em] text-accent">Contact</h3>
            <ul className="space-y-4 text-sm text-white/40">
              <li className="flex items-start gap-3">
                <MapPin className="w-5 h-5 text-accent shrink-0" />
                <span className="font-bold">Level 4, Business Park, Phase 2, Mumbai.</span>
              </li>
              <li className="flex items-center gap-3">
                <Phone className="w-5 h-5 text-accent shrink-0" />
                <span className="font-bold">+91 91234 56789</span>
              </li>
              <li className="flex items-center gap-3">
                <Mail className="w-5 h-5 text-accent shrink-0" />
                <span className="font-bold">hello@sabha.com</span>
              </li>
            </ul>
          </div>

          {/* Newsletter */}
          <div className="space-y-6">
            <h3 className="text-sm font-black uppercase tracking-[0.2em] text-white">Subscribe</h3>
            <p className="text-white/40 text-sm font-bold uppercase tracking-wider">Join 2,500+ professionals today.</p>
            <form className="flex flex-col gap-3" onSubmit={(e) => e.preventDefault()}>
              <input
                type="email"
                placeholder="Business Email"
                className="glass border-white/5 rounded-xl px-4 py-3 text-sm outline-none focus:border-primary transition-colors text-white placeholder:text-white/20"
              />
              <button className="w-full py-3 rounded-xl bg-premium text-white text-sm font-black uppercase tracking-widest hover:opacity-90 active:scale-95 transition-all">
                Join Inner Circle
              </button>
            </form>
          </div>
        </div>

        <div className="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 text-white/20 text-[10px] font-black uppercase tracking-[0.3em]">
          <p>© {currentYear} SABHA COMMUNITY. ALL RIGHTS RESERVED.</p>
          <div className="flex gap-8">
            <Link href="/privacy" className="hover:text-white transition-colors">Privacy Policy</Link>
            <Link href="/terms" className="hover:text-white transition-colors">Terms of Service</Link>
          </div>
        </div>
      </div>
    </footer>
  );
}

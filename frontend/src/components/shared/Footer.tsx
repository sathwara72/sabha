"use client";

import Link from "next/link";
import { Users, Mail, Phone, MapPin, Globe, MessageSquare, ArrowUpRight } from "lucide-react";

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-slate-50 text-foreground pt-24 pb-12 border-t">
      <div className="mx-auto max-w-7xl px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 border-b border-foreground/5 pb-16">
          {/* Brand Column */}
          <div className="space-y-6">
            <Link href="/" className="flex items-center gap-2 group">
              <div className="w-10 h-10 bg-primary rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-primary/20">
                <Users className="w-6 h-6 text-white" />
              </div>
              <span className="text-2xl font-bold">Sabha<span className="text-primary">.</span></span>
            </Link>
            <p className="text-foreground/50 text-sm leading-relaxed max-w-xs">
              Building the future of professional communities. Bridge the gap, Build trust, and Belong to something greater.
            </p>
            <div className="flex gap-4">
              {[Globe, MessageSquare, Mail].map((Icon, i) => (
                <button key={i} className="w-10 h-10 rounded-full border border-foreground/10 flex items-center justify-center hover:bg-primary transition-all group shadow-sm bg-white">
                  <Icon className="w-5 h-5 text-foreground/40 group-hover:text-white" />
                </button>
              ))}
            </div>
          </div>

          {/* Quick Links */}
          <div className="space-y-6">
            <h3 className="text-sm font-bold uppercase tracking-widest text-primary">Platform</h3>
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
                    className="text-foreground/50 hover:text-primary text-sm flex items-center gap-2 transition-colors group font-medium"
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
            <h3 className="text-sm font-bold uppercase tracking-widest text-primary">Contact</h3>
            <ul className="space-y-4 text-sm text-foreground/50">
              <li className="flex items-start gap-3">
                <MapPin className="w-5 h-5 text-primary shrink-0" />
                <span className="font-medium">Level 4, Business Park, Phase 2, Mumbai.</span>
              </li>
              <li className="flex items-center gap-3">
                <Phone className="w-5 h-5 text-primary shrink-0" />
                <span className="font-medium">+91 91234 56789</span>
              </li>
              <li className="flex items-center gap-3">
                <Mail className="w-5 h-5 text-primary shrink-0" />
                <span className="font-medium">hello@sabha.com</span>
              </li>
            </ul>
          </div>

          {/* Newsletter */}
          <div className="space-y-6">
            <h3 className="text-sm font-bold uppercase tracking-widest text-primary">Newsletter</h3>
            <p className="text-foreground/50 text-sm italic font-medium">Get the latest community updates directly in your inbox.</p>
            <form className="flex flex-col gap-3" onSubmit={(e) => e.preventDefault()}>
              <input
                type="email"
                placeholder="Enter your email"
                className="bg-white border border-foreground/10 rounded-xl px-4 py-3 text-sm outline-none focus:border-primary transition-colors shadow-inner"
              />
              <button className="w-full py-3 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/10">
                Subscribe Now
              </button>
            </form>
          </div>
        </div>

        <div className="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 text-foreground/30 text-xs font-medium">
          <p>© {currentYear} Sabha Community Platform. All rights reserved.</p>
          <div className="flex gap-8">
            <Link href="/privacy" className="hover:text-primary transition-colors">Privacy Policy</Link>
            <Link href="/terms" className="hover:text-primary transition-colors">Terms of Service</Link>
            <Link href="/cookies" className="hover:text-primary transition-colors">Cookie Settings</Link>
          </div>
        </div>
      </div>
    </footer>
  );
}

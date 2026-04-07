"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { Menu, X, Users, Calendar, Info, Mail, LogIn, Briefcase, Zap, ArrowRight } from "lucide-react";
import { cn } from "@/lib/utils";

const navigation = [
  { name: "Home", href: "/", icon: Users },
  { name: "Businesses", href: "/businesses", icon: Briefcase },
  { name: "Events", href: "/events", icon: Calendar },
  { name: "Gallery", href: "/gallery", icon: Zap },
  { name: "About", href: "/about", icon: Info },
  { name: "Contact", href: "/contact", icon: Mail },
];

export default function Navbar() {
  const pathname = usePathname();
  const [isScrolled, setIsScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  return (
    <header
      className={cn(
        "fixed inset-x-0 top-0 z-50 transition-all duration-500",
        isScrolled ? "py-4" : "py-6"
      )}
    >
      <div className="container mx-auto px-6">
        <nav
          className={cn(
            "flex items-center justify-between transition-all duration-500 px-8 py-3 rounded-full border border-transparent",
            isScrolled ? "glass shadow-2xl border-white/10" : "bg-transparent"
          )}
          aria-label="Global"
        >
          <div className="flex lg:flex-1">
            <Link href="/" className="flex items-center gap-3 group">
              <div className="w-10 h-10 bg-premium rounded-xl flex items-center justify-center glow-purple group-hover:scale-110 transition-transform">
                <Users className="w-6 h-6 text-white" />
              </div>
              <span className="text-2xl font-black tracking-tighter text-white">
                SABHA<span className="text-primary italic-none">.</span>
              </span>
            </Link>
          </div>

          <div className="flex lg:hidden">
            <button
              type="button"
              className="p-2 text-white/70 hover:text-white"
              onClick={() => setMobileMenuOpen(true)}
            >
              <Menu className="h-6 w-6" aria-hidden="true" />
            </button>
          </div>

          <div className="hidden lg:flex lg:gap-x-10">
            {navigation.map((item) => {
              const isActive = pathname === item.href;
              return (
                <Link
                  key={item.name}
                  href={item.href}
                  className={cn(
                    "text-sm font-bold uppercase tracking-widest transition-all hover:text-white relative py-1",
                    isActive ? "text-white" : "text-white/50"
                  )}
                >
                  {item.name}
                  {isActive && (
                    <motion.span 
                      layoutId="nav-underline"
                      className="absolute inset-x-0 -bottom-1 h-0.5 bg-premium rounded-full" 
                    />
                  )}
                </Link>
              );
            })}
          </div>

          <div className="hidden lg:flex lg:flex-1 lg:justify-end gap-6 items-center">
            <Link
              href="/login"
              className="text-sm font-bold uppercase tracking-widest text-white/50 hover:text-white transition-colors"
            >
              Log in
            </Link>
            <Link
              href="/register"
              className="btn-premium px-6 py-2.5 text-sm"
            >
              Join <ArrowRight size={16} />
            </Link>
          </div>
        </nav>
      </div>

      {/* Mobile menu */}
      <div
        className={cn(
          "lg:hidden fixed inset-0 z-50 transition-all duration-300",
          mobileMenuOpen ? "opacity-100 pointer-events-auto" : "opacity-0 pointer-events-none"
        )}
      >
        <div className="fixed inset-0 bg-black/90 backdrop-blur-xl" />
        <div className="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto px-10 py-10">
          <div className="flex items-center justify-between mb-12">
            <Link href="/" className="flex items-center gap-3" onClick={() => setMobileMenuOpen(false)}>
              <div className="w-10 h-10 bg-premium rounded-xl flex items-center justify-center">
                <Users className="w-6 h-6 text-white" />
              </div>
              <span className="text-2xl font-black text-white">SABHA.</span>
            </Link>
            <button
              type="button"
              className="p-2 text-white/70"
              onClick={() => setMobileMenuOpen(false)}
            >
              <X className="h-8 w-8" aria-hidden="true" />
            </button>
          </div>
          
          <div className="flex flex-col gap-8">
            {navigation.map((item) => (
              <Link
                key={item.name}
                href={item.href}
                className="text-3xl font-black text-white/50 hover:text-white transition-all uppercase tracking-tighter"
                onClick={() => setMobileMenuOpen(false)}
              >
                {item.name}
              </Link>
            ))}
            <hr className="border-white/10 my-4" />
            <Link
              href="/register"
              className="btn-premium text-xl py-5"
              onClick={() => setMobileMenuOpen(false)}
            >
              Join Community <ArrowRight size={20} />
            </Link>
          </div>
        </div>
      </div>
    </header>
  );
}

// Simple motion mock if needed, but framer-motion is installed.
import { motion } from "framer-motion";

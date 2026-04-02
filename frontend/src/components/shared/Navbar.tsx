"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { Menu, X, Users, Calendar, Info, Mail, LogIn, Briefcase, Zap } from "lucide-react";
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
      setIsScrolled(window.scrollY > 10);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  return (
    <header
      className={cn(
        "fixed inset-x-0 top-0 z-50 transition-all duration-300",
        isScrolled
          ? "bg-white/80 backdrop-blur-md border-b shadow-sm py-2"
          : "bg-transparent py-4"
      )}
    >
      <nav className="container mx-auto flex items-center justify-between px-6 lg:px-8" aria-label="Global">
        <div className="flex lg:flex-1">
          <Link href="/" className="-m-1.5 p-1.5 flex items-center gap-2">
            <div className="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/20">
              <Users className="w-6 h-6 text-primary-foreground" />
            </div>
            <span className="text-2xl font-bold tracking-tight text-foreground">
              Sabha<span className="text-primary">.</span>
            </span>
          </Link>
        </div>
        
        <div className="flex lg:hidden">
          <button
            type="button"
            className="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-foreground"
            onClick={() => setMobileMenuOpen(true)}
          >
            <span className="sr-only">Open main menu</span>
            <Menu className="h-6 h-6" aria-hidden="true" />
          </button>
        </div>

        <div className="hidden lg:flex lg:gap-x-8">
          {navigation.map((item) => {
            const isActive = pathname === item.href;
            return (
              <Link
                key={item.name}
                href={item.href}
                className={cn(
                  "text-sm font-semibold leading-6 transition-colors hover:text-primary relative py-1",
                  isActive ? "text-primary px-2" : "text-foreground/70"
                )}
              >
                {item.name}
                {isActive && (
                  <span className="absolute inset-x-0 -bottom-1 h-0.5 bg-primary rounded-full" />
                )}
              </Link>
            );
          })}
        </div>

        <div className="hidden lg:flex lg:flex-1 lg:justify-end gap-4">
          <Link
            href="/login"
            className="text-sm font-semibold leading-6 text-foreground hover:text-primary transition-colors flex items-center gap-2"
          >
            Log in <span aria-hidden="true">&rarr;</span>
          </Link>
          <Link
            href="/register"
            className="rounded-full bg-primary px-6 py-2.5 text-sm font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all hover:scale-105 active:scale-95"
          >
            Join Community
          </Link>
        </div>
      </nav>

      {/* Mobile menu */}
      <div className={cn(
        "lg:hidden fixed inset-0 z-50 transition-all duration-300",
        mobileMenuOpen ? "opacity-100 pointer-events-auto" : "opacity-0 pointer-events-none"
      )}>
        <div className="fixed inset-0 bg-background/95 backdrop-blur-sm" />
        <div className="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-background px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-border">
          <div className="flex items-center justify-between">
            <Link href="/" className="-m-1.5 p-1.5" onClick={() => setMobileMenuOpen(false)}>
              <span className="text-2xl font-bold">Sabha</span>
            </Link>
            <button
              type="button"
              className="-m-2.5 rounded-md p-2.5 text-foreground"
              onClick={() => setMobileMenuOpen(false)}
            >
              <span className="sr-only">Close menu</span>
              <X className="h-6 w-6" aria-hidden="true" />
            </button>
          </div>
          <div className="mt-6 flow-root">
            <div className="-my-6 divide-y divide-border">
              <div className="space-y-2 py-6">
                {navigation.map((item) => (
                  <Link
                    key={item.name}
                    href={item.href}
                    className="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-foreground hover:bg-muted"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    <div className="flex items-center gap-3">
                      <item.icon className="w-5 h-5 text-primary" />
                      {item.name}
                    </div>
                  </Link>
                ))}
              </div>
              <div className="py-6 space-y-3">
                <Link
                  href="/login"
                  className="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-foreground hover:bg-muted"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  Log in
                </Link>
                <Link
                  href="/register"
                  className="block rounded-full bg-primary px-6 py-3 text-center text-base font-semibold leading-7 text-primary-foreground hover:bg-primary/90"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  Join Community
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}

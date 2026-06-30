"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { Menu, X, ArrowRight, LogOut } from "lucide-react";
import { cn } from "@/lib/utils";
import { useAuth } from "@/lib/auth";
import { useLanguage } from "@/lib/language";

const navigation = [
  { name: "Home", href: "/", tKey: "nav.home" },
  { name: "Businesses", href: "/businesses", tKey: "nav.directory" },
  { name: "Events", href: "/events", tKey: "nav.events" },
  { name: "Gallery", href: "/gallery", tKey: "nav.gallery" },
  { name: "About Us", href: "/about", tKey: "nav.about" },
  { name: "Contact Us", href: "/contact", tKey: "nav.contact" },
];

export default function Navbar() {
  const pathname = usePathname();
  const { isAuthenticated, user, openLogin, openRegister, logout } = useAuth();
  const { language, setLanguage, t } = useLanguage();
  const [isScrolled, setIsScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => setIsScrolled(window.scrollY > 16);
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  return (
    <header
      className={cn(
        "fixed inset-x-0 top-0 z-50 transition-all duration-300 border-b",
        isScrolled
          ? "glass border-border shadow-sm"
          : "bg-background/80 backdrop-blur-sm border-transparent"
      )}
    >
      <div className="mx-auto max-w-7xl px-6">
        <nav className="flex h-16 items-center justify-between" aria-label="Global">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-2.5 group">
            <img
              src="/logo.png"
              alt="SABHA"
              className="h-10 w-10 rounded-full object-contain transition-transform group-hover:scale-105"
            />
            <span className="text-xl font-bold tracking-tight text-primary-dark">SABHA</span>
          </Link>

          {/* Desktop nav */}
          <div className="hidden lg:flex lg:items-center lg:gap-1">
            {navigation.map((item) => {
              const isActive = pathname === item.href;
              return (
                <Link
                  key={item.tKey}
                  href={item.href}
                  className={cn(
                    "relative rounded-lg px-3.5 py-2 text-sm font-medium transition-colors",
                    isActive
                      ? "text-primary"
                      : "text-muted hover:text-foreground hover:bg-surface"
                  )}
                >
                  {t(item.tKey)}
                  {isActive && (
                    <motion.span
                      layoutId="nav-underline"
                      className="absolute inset-x-3.5 -bottom-px h-0.5 rounded-full bg-primary"
                    />
                  )}
                </Link>
              );
            })}
          </div>

          {/* Desktop actions */}
          <div className="hidden lg:flex lg:items-center lg:gap-2">
            {isAuthenticated ? (
              <>
                <Link
                  href="/profile"
                  className="rounded-lg px-3.5 py-2 text-sm font-medium text-muted hover:text-foreground hover:bg-surface"
                >
                  {t("nav.profile")}
                </Link>
                {user?.role === "admin" && (
                  <Link
                    href="/admin"
                    className="inline-flex items-center gap-1.5 rounded-lg bg-primary-soft text-primary border border-primary/15 px-4 py-2 text-sm font-semibold hover:opacity-95"
                  >
                    {t("nav.admin")}
                  </Link>
                )}
                <button
                  onClick={logout}
                  className="inline-flex items-center gap-1.5 rounded-lg border border-border px-4 py-2 text-sm font-medium text-muted transition-colors hover:bg-surface hover:text-foreground cursor-pointer"
                >
                  <LogOut size={15} /> {t("nav.logout")}
                </button>
              </>
            ) : (
              <>
                <button
                  onClick={openLogin}
                  className="rounded-lg px-3.5 py-2 text-sm font-medium text-muted transition-colors hover:text-foreground hover:bg-surface cursor-pointer"
                >
                  {t("nav.login")}
                </button>
                <button
                  onClick={openRegister}
                  className="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
                >
                  {t("nav.register")} <ArrowRight size={15} />
                </button>
              </>
            )}

            {/* Language Switcher */}
            <div className="ml-4 border-l border-border pl-4 flex items-center">
              <div className="flex items-center gap-0.5 bg-slate-100 border border-slate-200/50 p-1 rounded-full shadow-inner">
                <button
                  onClick={() => setLanguage("en")}
                  className={cn(
                    "px-3 py-1 text-xs font-bold rounded-full cursor-pointer transition-all duration-200 select-none",
                    language === "en"
                      ? "bg-primary text-white shadow-sm"
                      : "text-muted hover:text-foreground"
                  )}
                >
                  EN
                </button>
                <button
                  onClick={() => setLanguage("gu")}
                  className={cn(
                    "px-3 py-1 text-xs font-bold rounded-full cursor-pointer transition-all duration-200 select-none",
                    language === "gu"
                      ? "bg-primary text-white shadow-sm"
                      : "text-muted hover:text-foreground"
                  )}
                >
                  GU
                </button>
              </div>
            </div>
          </div>

          {/* Mobile toggle */}
          <button
            type="button"
            className="lg:hidden rounded-lg p-2 text-foreground hover:bg-surface"
            onClick={() => setMobileMenuOpen(true)}
            aria-label="Open menu"
          >
            <Menu className="h-6 w-6" />
          </button>
        </nav>
      </div>

      {/* Mobile menu */}
      <div
        className={cn(
          "lg:hidden fixed inset-0 z-50 transition-opacity duration-200",
          mobileMenuOpen ? "opacity-100 pointer-events-auto" : "opacity-0 pointer-events-none"
        )}
      >
        <div
          className="fixed inset-0 bg-slate-900/20 backdrop-blur-sm"
          onClick={() => setMobileMenuOpen(false)}
        />
        <div className="fixed inset-y-0 right-0 w-full max-w-sm overflow-y-auto bg-background px-6 py-6 shadow-xl">
          <div className="mb-8 flex items-center justify-between">
            <Link
              href="/"
              className="flex items-center gap-2.5"
              onClick={() => setMobileMenuOpen(false)}
            >
              <img src="/logo.png" alt="SABHA" className="h-10 w-10 rounded-full object-contain" />
              <span className="text-xl font-bold tracking-tight text-primary-dark">SABHA</span>
            </Link>
            <button
              type="button"
              className="rounded-lg p-2 text-muted hover:bg-surface"
              onClick={() => setMobileMenuOpen(false)}
              aria-label="Close menu"
            >
              <X className="h-6 w-6" />
            </button>
          </div>

          <div className="flex flex-col gap-1">
            {navigation.map((item) => {
              const isActive = pathname === item.href;
              return (
                <Link
                  key={item.tKey}
                  href={item.href}
                  className={cn(
                    "rounded-lg px-4 py-3 text-base font-medium transition-colors",
                    isActive ? "bg-primary-soft text-primary" : "text-foreground hover:bg-surface"
                  )}
                  onClick={() => setMobileMenuOpen(false)}
                >
                  {t(item.tKey)}
                </Link>
              );
            })}
          </div>

          <hr className="my-6 border-border" />

          <div className="flex flex-col gap-3">
            {isAuthenticated ? (
              <>
                <Link
                  href="/profile"
                  onClick={() => setMobileMenuOpen(false)}
                  className="inline-flex items-center justify-center gap-2 rounded-lg border border-border px-4 py-3 text-sm font-semibold text-foreground hover:bg-surface"
                >
                  {t("nav.profile")}
                </Link>
                {user?.role === "admin" && (
                  <Link
                    href="/admin"
                    onClick={() => setMobileMenuOpen(false)}
                    className="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-soft text-primary border border-primary/15 px-4 py-3 text-sm font-semibold"
                  >
                    {t("nav.admin")}
                  </Link>
                )}
                <button
                  onClick={() => {
                    logout();
                    setMobileMenuOpen(false);
                  }}
                  className="inline-flex items-center justify-center gap-2 rounded-lg border border-border px-4 py-3 text-sm font-semibold text-foreground hover:bg-surface cursor-pointer"
                >
                  <LogOut size={16} /> {t("nav.logout")}
                </button>
              </>
            ) : (
              <>
                <button
                  onClick={() => {
                    openLogin();
                    setMobileMenuOpen(false);
                  }}
                  className="rounded-lg border border-border px-4 py-3 text-center text-sm font-semibold text-foreground hover:bg-surface cursor-pointer"
                >
                  {t("nav.login")}
                </button>
                <button
                  onClick={() => {
                    openRegister();
                    setMobileMenuOpen(false);
                  }}
                  className="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-3 text-sm font-semibold text-white cursor-pointer"
                >
                  {t("nav.register")} <ArrowRight size={16} />
                </button>
              </>
            )}

            {/* Mobile Language Switcher */}
            <div className="mt-4 flex items-center justify-between border-t border-border pt-4">
              <span className="text-xs font-semibold text-muted">Language / ભાષા</span>
              <div className="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg">
                <button
                  onClick={() => setLanguage("en")}
                  className={cn(
                    "px-3 py-1.5 text-xs font-bold rounded cursor-pointer transition-colors select-none",
                    language === "en"
                      ? "bg-primary text-white"
                      : "text-muted hover:text-foreground"
                  )}
                >
                  EN
                </button>
                <button
                  onClick={() => setLanguage("gu")}
                  className={cn(
                    "px-3 py-1.5 text-xs font-bold rounded cursor-pointer transition-colors select-none",
                    language === "gu"
                      ? "bg-primary text-white"
                      : "text-muted hover:text-foreground"
                  )}
                >
                  GU
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}

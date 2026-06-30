"use client";

import Link from "next/link";
import { Mail, Phone, MapPin, Globe, Send, MessageCircle } from "lucide-react";
import { useLanguage } from "@/lib/language";

export default function Footer() {
  const { t } = useLanguage();
  const currentYear = new Date().getFullYear();

  return (
    <footer className="border-t border-border bg-white">
      <div className="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div className="grid grid-cols-1 gap-12 pb-12 md:grid-cols-2 lg:grid-cols-3">
          {/* Brand */}
          <div className="space-y-4">
            <Link href="/" className="flex items-center gap-2.5">
              <img src="/logo.png" alt="SABHA" className="h-10 w-10 rounded-full object-contain" />
              <span className="text-xl font-bold tracking-tight text-primary-dark">SABHA</span>
            </Link>
            <p className="max-w-xs text-sm leading-relaxed text-muted">
              {t("footer.tagline")}
            </p>
            <div className="flex gap-2.5">
              {[Globe, Send, MessageCircle].map((Icon, i) => (
                <button
                  key={i}
                  className="flex h-9 w-9 items-center justify-center rounded-lg border border-border text-muted transition-colors hover:border-primary hover:text-primary"
                  aria-label="Social link"
                >
                  <Icon className="h-4 w-4" />
                </button>
              ))}
            </div>
          </div>

          {/* Platform links */}
          <div className="space-y-4">
            <h3 className="text-sm font-semibold text-foreground">{t("footer.platform")}</h3>
            <ul className="space-y-3">
              {[
                { name: t("footer.link_businesses"), href: "/businesses" },
                { name: t("footer.link_events"), href: "/events" },
                { name: t("footer.link_gallery"), href: "/gallery" },
                { name: t("footer.link_about"), href: "/about" },
              ].map((link) => (
                <li key={link.href}>
                  <Link
                    href={link.href}
                    className="text-sm text-muted transition-colors hover:text-primary"
                  >
                    {link.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div className="space-y-4">
            <h3 className="text-sm font-semibold text-foreground">{t("footer.contact")}</h3>
            <ul className="space-y-3 text-sm text-muted">
              <li className="flex items-start gap-3">
                <MapPin className="h-4 w-4 shrink-0 text-primary" />
                <span>Level 4, Business Park, Phase 2, Mumbai</span>
              </li>
              <li className="flex items-center gap-3">
                <Phone className="h-4 w-4 shrink-0 text-primary" />
                <span>+91 91234 56789</span>
              </li>
              <li className="flex items-center gap-3">
                <Mail className="h-4 w-4 shrink-0 text-primary" />
                <span>hello@sabha.com</span>
              </li>
            </ul>
          </div>

        </div>

        <div className="flex flex-col items-center justify-between gap-4 border-t border-border pt-8 text-sm text-muted md:flex-row">
          <p>{t("footer.copyright").replace("2026", String(currentYear))}</p>
          <div className="flex gap-6">
            <Link href="/privacy" className="transition-colors hover:text-primary">
              {t("footer.privacy")}
            </Link>
            <Link href="/terms" className="transition-colors hover:text-primary">
              {t("footer.terms")}
            </Link>
          </div>
        </div>
      </div>
    </footer>
  );
}

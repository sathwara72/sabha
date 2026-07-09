"use client";

import { useEffect, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import {
  ArrowRight,
  Briefcase,
  ChevronRight,
  Calendar,
  MapPin,
  Zap,
  Users,
} from "lucide-react";
import Link from "next/link";
import { fetchBusinesses, fetchEvents, fetchStatistics, fetchHeroImages } from "@/lib/api";
import { assetUrl } from "@/lib/config";
import { useLanguage } from "@/lib/language";
import { useAuth } from "@/lib/auth";

interface Business {
  id: number;
  name: string;
  category: string;
  description: string;
  website?: string;
  logo?: string;
}

interface Event {
  id: number;
  title: string;
  description: string;
  date: string;
  location: string;
  type: string;
  image?: string;
  price_normal?: string;
  price_verified?: string;
}

interface Stat {
  label: string;
  value: string;
}

export default function Home() {
  const { t } = useLanguage();
  const { isAuthenticated, openRegister } = useAuth();
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [events, setEvents] = useState<Event[]>([]);
  const [stats, setStats] = useState<Stat[]>([
    { label: "Active Members", value: "5000+" },
    { label: "Businesses Registered", value: "1200+" },
    { label: "Events Hosted", value: "150+" },
  ]);
  const [heroImages, setHeroImages] = useState<string[]>([
    "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=2000&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=2000&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1505373877841-8d25f7d46678?q=80&w=2000&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=2000&auto=format&fit=crop"
  ]);

  // Map known backend stat labels to translation keys so they localize.
  const translateStatLabel = (label: string) => {
    const map: Record<string, string> = {
      "active members": "stats.active_members",
      "businesses registered": "stats.businesses_registered",
      "events hosted": "stats.events_hosted",
      "connections made": "stats.connections_made",
      "verified members": "stats.verified_members",
      "business exchanged": "stats.business_exchanged",
      "monthly mixers": "stats.monthly_mixers",
      "cities covered": "stats.cities_covered",
      "members": "stats.members",
    };
    const key = map[label.toLowerCase().trim()];
    return key ? t(key) : label;
  };
  const [loading, setLoading] = useState(true);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const [activeFaq, setActiveFaq] = useState<number | null>(null);

  useEffect(() => {
    async function loadData() {
      try {
        const [bizData, eventData, statData, heroData] = await Promise.all([
          fetchBusinesses(),
          fetchEvents(),
          fetchStatistics(),
          fetchHeroImages().catch(() => []),
        ]);
        
        // Defensive uniqueness filters
        const uniqueBiz = (bizData || []).filter(
          (v: any, i: number, a: any[]) => a.findIndex((t: any) => t.id === v.id) === i
        );
        const uniqueEvents = (eventData || []).filter(
          (v: any, i: number, a: any[]) => a.findIndex((t: any) => t.id === v.id) === i
        );
        const uniqueStats = (statData || []).filter(
          (v: any, i: number, a: any[]) => a.findIndex((t: any) => t.label === v.label) === i
        );

        setBusinesses(uniqueBiz);
        setEvents(uniqueEvents);
        if (uniqueStats && uniqueStats.length > 0) setStats(uniqueStats);

        const dynamicHeroImages = (heroData || []).map((item: any) => {
          const path = item.image_path;
          if (path.startsWith("http://") || path.startsWith("https://")) {
            return path;
          }
          return assetUrl(path);
        });

        if (dynamicHeroImages && dynamicHeroImages.length > 0) {
          setHeroImages(dynamicHeroImages);
        }
      } catch (error) {
        console.error("Error loading live data:", error);
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, []);

  useEffect(() => {
    if (heroImages.length === 0) return;
    const timer = setInterval(() => {
      setCurrentImageIndex((prev) => (prev + 1) % heroImages.length);
    }, 5000);
    return () => clearInterval(timer);
  }, [heroImages.length]);

  if (loading) {
    return (
      <div className="flex min-h-[70vh] items-center justify-center bg-background">
        <div className="h-10 w-10 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
      </div>
    );
  }

  return (
    <div className="bg-background">
      {/* Hero */}
      <section className="hero-surface relative overflow-hidden border-b border-border">
        <div className="mx-auto max-w-7xl px-6 py-12 lg:px-8 lg:py-16 grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
          <motion.div
            initial={{ opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            className="flex flex-col items-start text-left space-y-6"
          >
            <span className="inline-flex items-center gap-2 rounded-full bg-primary-soft px-4 py-1.5 text-xs sm:text-sm font-semibold text-primary">
                <span className="h-1.5 w-1.5 rounded-full bg-primary" />
                {t("hero.badge")}
              </span>

            <h1 className="text-4xl font-bold tracking-tight text-foreground sm:text-5xl lg:text-6xl uppercase">
              {t("hero.title")}
            </h1>

            <p className="text-base leading-relaxed text-muted max-w-2xl font-medium">
              {t("hero.subtitle")}
            </p>

            <div className="flex flex-col items-center gap-3 sm:flex-row w-full sm:w-auto">
              {isAuthenticated ? (
                <Link
                  href="/profile"
                  className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-7 py-3.5 text-base font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] sm:w-auto cursor-pointer"
                >
                  {t("nav.profile") || "Go to Profile"} <ArrowRight size={18} />
                </Link>
              ) : (
                <button
                  onClick={openRegister}
                  className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-7 py-3.5 text-base font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] sm:w-auto cursor-pointer"
                >
                  {t("nav.register")} <ArrowRight size={18} />
                </button>
              )}
              <Link
                href="/events"
                className="inline-flex w-full items-center justify-center rounded-xl border border-border bg-white px-7 py-3.5 text-base font-semibold text-foreground transition-colors hover:bg-surface sm:w-auto cursor-pointer"
              >
                {t("hero.cta_events")}
              </Link>
            </div>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, scale: 0.98 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ delay: 0.1, duration: 0.4 }}
            className="relative h-[280px] w-full overflow-hidden rounded-2xl border border-border bg-muted sm:h-[360px] lg:h-[420px] shadow-lg"
          >
            <AnimatePresence mode="wait">
              <motion.img
                key={currentImageIndex}
                src={heroImages[currentImageIndex]}
                alt="Sabha Event"
                initial={{ opacity: 0, scale: 1.05 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.95 }}
                transition={{ duration: 0.6 }}
                className="absolute inset-0 h-full w-full object-cover"
              />
            </AnimatePresence>

            {/* Navigation overlays */}
            <div className="absolute inset-x-0 bottom-4 flex justify-center gap-2 z-10">
              {heroImages.map((_, idx) => (
                <button
                  key={idx}
                  onClick={() => setCurrentImageIndex(idx)}
                  className={`h-2 rounded-full transition-all duration-300 ${
                    currentImageIndex === idx ? "bg-white w-6" : "bg-white/50 w-2"
                  }`}
                  aria-label={`Go to slide ${idx + 1}`}
                />
              ))}
            </div>
          </motion.div>
        </div>
      </section>

      {/* Stats */}
      <section className="border-b border-border bg-surface">
        <div className="mx-auto grid max-w-5xl grid-cols-1 gap-8 px-6 py-14 text-center sm:grid-cols-3">
          {stats.slice(0, 3).map((stat, i) => (
            <motion.div
              key={i}
              initial={{ opacity: 0, y: 12 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.05 }}
            >
              <p className="text-4xl font-bold text-foreground sm:text-5xl">{stat.value}</p>
              <p className="mt-2 text-sm font-medium text-muted">{translateStatLabel(stat.label)}</p>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Core Pillars */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-24 border-b border-border">
        <div className="text-center max-w-3xl mx-auto mb-16">
          <p className="text-sm font-semibold text-primary uppercase tracking-wider">{t("home.pillars_label")}</p>
          <h2 className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
            {t("home.pillars_title")}
          </h2>
          <p className="mt-4 text-sm text-muted leading-relaxed">
            {t("home.pillars_subtitle")}
          </p>
        </div>

        <div className="grid grid-cols-1 gap-8 md:grid-cols-3">
          {[
            {
              title: t("home.pillar_1_title"),
              description: t("home.pillar_1_desc"),
              icon: Briefcase,
              color: "from-blue-500 to-indigo-600",
            },
            {
              title: t("home.pillar_2_title"),
              description: t("home.pillar_2_desc"),
              icon: Users,
              color: "from-emerald-500 to-teal-600",
            },
            {
              title: t("home.pillar_3_title"),
              description: t("home.pillar_3_desc"),
              icon: Zap,
              color: "from-orange-500 to-amber-600",
            }
          ].map((pillar, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.1, duration: 0.5 }}
              className="glass-card flex flex-col p-8 transition-all hover:border-primary/20 group hover:shadow-md"
            >
              <div className={`flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br ${pillar.color} text-white shadow-md mb-6 transition-transform group-hover:scale-105`}>
                <pillar.icon size={26} />
              </div>
              <h3 className="text-lg font-bold text-foreground group-hover:text-primary transition-colors">{pillar.title}</h3>
              <p className="mt-3.5 text-xs leading-relaxed text-muted flex-1">{pillar.description}</p>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Events */}
      <section className="border-y border-border bg-surface">
        <div className="mx-auto max-w-7xl px-6 py-20 lg:py-24">
          <div className="mb-12 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div className="max-w-2xl">
              <p className="text-sm font-semibold text-primary">{t("home.events_label")}</p>
              <h2 className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                {t("home.events_title")}
              </h2>
            </div>
            <Link
              href="/gallery"
              className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary transition-colors hover:opacity-80"
            >
              {t("home.events_view_gallery")} <ArrowRight size={16} />
            </Link>
          </div>

          <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            {events.slice(0, 3).map((event, i) => (
              <motion.div
                key={event.id}
                initial={{ opacity: 0, y: 16 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.05 }}
                className="glass-card group overflow-hidden p-0 hover:shadow-md transition-shadow cursor-pointer"
              >
                <Link href={`/events/${event.id}`} className="block">
                  <div className="relative h-40 w-full overflow-hidden">
                    <img
                      src={
                        event.image ||
                        "https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=800&auto=format&fit=crop"
                      }
                      alt={event.title}
                      className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                  </div>
                  <div className="p-4.5 space-y-2">
                    <h4 className="text-sm font-bold text-foreground group-hover:text-primary transition-colors line-clamp-1">{event.title}</h4>
                    <div className="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted">
                      <span className="inline-flex items-center gap-1">
                        <Calendar size={12} className="text-primary" />
                        {new Date(event.date).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}
                      </span>
                      <span className="inline-flex items-center gap-1 truncate max-w-[150px]">
                        <MapPin size={12} className="text-primary" />
                        {event.location.split(",")[0]}
                      </span>
                    </div>

                    <div className="pt-2 border-t border-border flex items-center justify-between text-[11px] font-semibold">
                      <span className="text-muted">
                        {t("home.events_std")}: <strong className="text-foreground">{event.price_normal || "₹1,499"}</strong>
                      </span>
                      {event.price_verified && event.price_verified !== event.price_normal && (
                        <span className="text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-bold">
                          {t("home.events_verified")}: {event.price_verified}
                        </span>
                      )}
                    </div>
                  </div>
                </Link>
              </motion.div>
            ))}

            {events.length === 0 && !loading && (
              <div className="rounded-xl border border-dashed border-border py-16 text-center text-muted md:col-span-2">
                {t("home.events_none")}
              </div>
            )}

            {/* CTA card */}
            <div className="glass-card flex flex-col justify-between p-7">
              <div>
                <h3 className="text-xl font-bold text-foreground">{t("home.events_cta_title")}</h3>
                <p className="mt-3 text-sm leading-relaxed text-muted">
                  {t("home.events_cta_desc")}
                </p>
              </div>
              <Link
                href="/events"
                className="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white transition-all hover:opacity-90 active:scale-[0.98]"
              >
                {t("home.events_cta_btn")} <ArrowRight size={16} />
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* How it works */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-24">
        <div className="mb-14 max-w-2xl">
          <p className="text-sm font-semibold text-primary">{t("home.how_label")}</p>
          <h2 className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
            {t("home.how_title")}
          </h2>
        </div>

        <div className="grid grid-cols-1 gap-8 md:grid-cols-3">
          {[
            {
              step: "1",
              title: t("home.step1_title"),
              detail: t("home.step1_desc"),
            },
            {
              step: "2",
              title: t("home.step2_title"),
              detail: t("home.step2_desc"),
            },
            {
              step: "3",
              title: t("home.step3_title"),
              detail: t("home.step3_desc"),
            },
          ].map((item, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, y: 16 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.05 }}
              className="glass-card p-7"
            >
              <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white">
                {item.step}
              </div>
              <h3 className="mt-5 text-lg font-semibold text-foreground">{item.title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-muted">{item.detail}</p>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Interactive FAQ Section */}
      <section className="mx-auto max-w-4xl px-6 py-20 border-t border-border">
        <div className="text-center mb-12">
          <p className="text-sm font-semibold text-primary uppercase tracking-wider">{t("home.faq_label")}</p>
          <h2 className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
            {t("home.faq_title")}
          </h2>
        </div>

        <div className="space-y-4">
          {[
            { q: t("home.faq_1_q"), a: t("home.faq_1_a") },
            { q: t("home.faq_2_q"), a: t("home.faq_2_a") },
            { q: t("home.faq_3_q"), a: t("home.faq_3_a") },
            { q: t("home.faq_4_q"), a: t("home.faq_4_a") }
          ].map((item, idx) => {
            const isOpen = activeFaq === idx;
            return (
              <div
                key={idx}
                className="glass-card overflow-hidden border border-border/80 transition-colors"
              >
                <button
                  onClick={() => setActiveFaq(isOpen ? null : idx)}
                  className="w-full flex items-center justify-between p-5 text-left font-bold text-base text-foreground outline-none"
                >
                  <span>{item.q}</span>
                  <span className={`text-primary font-bold text-xl transition-transform duration-200 ${isOpen ? "rotate-45" : ""}`}>
                    +
                  </span>
                </button>
                <AnimatePresence initial={false}>
                  {isOpen && (
                    <motion.div
                      initial={{ height: 0, opacity: 0 }}
                      animate={{ height: "auto", opacity: 1 }}
                      exit={{ height: 0, opacity: 0 }}
                      transition={{ duration: 0.25, ease: "easeInOut" }}
                    >
                      <div className="p-5 pt-0 border-t border-border/10 text-sm leading-relaxed text-muted font-medium bg-surface/30">
                        {item.a}
                      </div>
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>
            );
          })}
        </div>
      </section>

    </div>
  );
}

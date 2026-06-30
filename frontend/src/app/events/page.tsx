"use client";

import { useState, useMemo, useEffect } from "react";
import { Calendar, MapPin, Search, Tag, ArrowRight, Info, Filter, ChevronLeft, ChevronRight, ShieldCheck } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import Link from "next/link";
import { cn } from "@/lib/utils";
import { fetchEvents, getUserBusiness } from "@/lib/api";
import { useAuth } from "@/lib/auth";
import { useLanguage } from "@/lib/language";

export default function EventsPage() {
  const { t } = useLanguage();
  const [events, setEvents] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState("all");
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 9;

  const { isAuthenticated } = useAuth();
  const [isVerifiedMember, setIsVerifiedMember] = useState(false);

  useEffect(() => {
    async function checkVerification() {
      if (isAuthenticated) {
        try {
          const biz = await getUserBusiness();
          if (biz && biz.status === "approved") {
            setIsVerifiedMember(true);
          }
        } catch (e) {
          console.error("Failed to check business verification status:", e);
        }
      } else {
        setIsVerifiedMember(false);
      }
    }
    checkVerification();
  }, [isAuthenticated]);

  useEffect(() => {
    async function loadEvents() {
      try {
        setLoading(true);
        const data = await fetchEvents();
        const liveEvents = (data || []).map((e: any) => {
          const eventDate = new Date(e.date);
          const now = new Date();
          let status = "upcoming";
          
          // Clear time component for accurate comparison
          const dateOnly = new Date(eventDate.getFullYear(), eventDate.getMonth(), eventDate.getDate());
          const todayOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

          if (dateOnly.getTime() === todayOnly.getTime()) {
            status = "current";
          } else if (dateOnly < todayOnly) {
            status = "past";
          }

          return {
            id: e.id,
            title: e.title,
            description: e.description,
            date: eventDate.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }),
            time: eventDate.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" }),
            location: e.location,
            type: e.type,
            status,
            category: e.type,
            attendees: "50+",
            price_normal: e.price_normal || (e.type === "Workshop" ? "Free" : "₹1,499"),
            price_verified: e.price_verified || (e.type === "Workshop" ? "Free" : "₹1,499"),
            image: e.image || "https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=800&auto=format&fit=crop"
          };
        });

        // Sort: current first, then upcoming (ascending by date), then past (descending by date)
        const statusOrder: Record<string, number> = { current: 0, upcoming: 1, past: 2 };
        liveEvents.sort((a: any, b: any) => {
          if (statusOrder[a.status] !== statusOrder[b.status]) {
            return statusOrder[a.status] - statusOrder[b.status];
          }
          const aDate = new Date(a.date).getTime();
          const bDate = new Date(b.date).getTime();
          // upcoming: ascending (sooner first), past: descending (most recent first)
          return a.status === "past" ? bDate - aDate : aDate - bDate;
        });

        setEvents(liveEvents);
      } catch (err) {
        console.error("Failed to fetch events:", err);
      } finally {
        setLoading(false);
      }
    }
    loadEvents();
  }, []);

  const filteredEvents = useMemo(() => {
    return events.filter(event => {
      const matchesFilter = filter === "all" || event.status === filter;
      const matchesSearch = event.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
                            event.category.toLowerCase().includes(searchQuery.toLowerCase());
      return matchesFilter && matchesSearch;
    });
  }, [events, filter, searchQuery]);

  const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
  const paginatedEvents = filteredEvents.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  const handleFilterChange = (f: string) => {
    setFilter(f);
    setCurrentPage(1);
  };

  const handleSearchChange = (query: string) => {
    setSearchQuery(query);
    setCurrentPage(1);
  };

  return (
    <div className="bg-background font-outfit">
      <div className="mx-auto max-w-7xl px-6 py-8 lg:px-8">
        {/* Compact title row */}
        <div className="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <div className="mb-2 flex items-center gap-2.5">
              <span className="h-4 w-1.5 rounded-full bg-accent" />
              <span className="text-sm font-semibold text-accent">{t("events.label")}</span>
            </div>
            <h1 className="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
              {t("events.title")}
            </h1>
            <p className="mt-1 text-sm text-muted font-medium">
              {t("events.subtitle")}
            </p>
          </div>
          <p className="text-sm font-medium text-muted">
            {filteredEvents.length} {filteredEvents.length === 1 ? t("events.event") : t("events.events_count")}
          </p>
        </div>

        {/* Search & Filter Bar */}
        <div className="mb-10 mt-6 flex flex-col gap-4">
          <div className="relative max-w-2xl">
            <Search className="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
            <input
              type="text"
              placeholder={t("events.search_placeholder")}
              className="w-full rounded-xl border border-border bg-white py-3 pl-12 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary font-semibold"
              value={searchQuery}
              onChange={(e) => handleSearchChange(e.target.value)}
            />
          </div>

          <div className="flex flex-col gap-4 md:flex-row md:items-center">
            <div className="flex flex-wrap items-center gap-3">
              <span className="inline-flex items-center gap-2 text-sm font-semibold text-muted">
                <Filter className="h-4 w-4 text-primary" />
                {t("events.status")}
              </span>
              <div className="flex flex-wrap gap-2">
                {[
                  { key: "all", label: t("events.all_events") },
                  { key: "current", label: `🟢 ${t("events.booking_available")}` },
                  { key: "upcoming", label: `🟡 ${t("events.booking_soon")}` },
                  { key: "past", label: `⚫ ${t("events.past")}` },
                ].map(({ key, label }) => (
                  <button
                    key={key}
                    onClick={() => handleFilterChange(key)}
                    className={cn(
                      "rounded-full px-4 py-1.5 text-xs font-semibold transition-all cursor-pointer",
                      filter === key
                        ? "bg-primary text-white shadow-sm"
                        : "border border-border bg-white text-muted hover:bg-surface hover:text-foreground"
                    )}
                  >
                    {label}
                  </button>
                ))}
              </div>
            </div>

            <div className="flex items-center gap-2 rounded-xl bg-primary-soft px-4 py-2.5 text-sm font-medium text-primary md:ml-auto">
              <Info className="h-4 w-4" />
              {t("events.verified_info")}
            </div>
          </div>
        </div>

        {/* Results Grid */}
        {loading ? (
          <div className="py-20 text-center">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
            <p className="mt-3 text-sm text-muted">{t("events.loading")}</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <AnimatePresence mode="popLayout">
              {paginatedEvents.map((event) => (
                <motion.div
                   key={event.id}
                   layout
                   initial={{ opacity: 0, scale: 0.97 }}
                   animate={{ opacity: 1, scale: 1 }}
                   exit={{ opacity: 0, scale: 0.97 }}
                   className="glass-card group flex h-full flex-col overflow-hidden p-0 hover:shadow-md transition-shadow"
                >
                  <div className="relative h-40 w-full overflow-hidden">
                    <img
                      src={event.image}
                      alt={event.title}
                      className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
                    <div className="absolute left-3 top-3">
                      <span className="rounded-full bg-white/90 px-2.5 py-0.5 text-[10px] font-medium text-foreground backdrop-blur">
                        {event.category}
                      </span>
                    </div>
                  </div>

                  <div className="flex flex-1 flex-col p-4.5">
                    <div className="mb-2 flex items-center justify-between">
                      <span className="inline-flex items-center gap-1 text-[10px] font-semibold capitalize text-primary">
                        <Tag size={10} /> {event.type}
                      </span>
                      {event.status === "upcoming" && (
                        <span className="rounded-full bg-amber-50 border border-amber-100 px-2 py-0.5 text-[9px] font-bold text-amber-700 uppercase tracking-wider">
                          {t("events.upcoming")}
                        </span>
                      )}
                      {event.status === "current" && (
                        <span className="rounded-full bg-emerald-50 border border-emerald-100 px-2 py-0.5 text-[9px] font-bold text-emerald-700 uppercase tracking-wider">
                          {t("events.current")}
                        </span>
                      )}
                      {event.status === "past" && (
                        <span className="rounded-full bg-slate-100 border border-slate-200 px-2 py-0.5 text-[9px] font-bold text-slate-500 uppercase tracking-wider">
                          {t("events.past")}
                        </span>
                      )}
                    </div>
 
                    <div className="flex-1">
                      <div className="mb-1 flex items-center gap-1 text-xs text-muted">
                        <Calendar size={12} className="text-primary" /> {event.date}
                      </div>
                      <h3 className="text-sm font-bold text-foreground transition-colors group-hover:text-primary leading-snug line-clamp-1">
                        {event.title}
                      </h3>
                      <p className="mt-1 line-clamp-2 text-xs leading-relaxed text-muted font-medium">
                        {event.description}
                      </p>
                    </div>
 
                    <div className="mt-4 border-t border-border pt-4">
                      {(() => {
                        const hasDiscount = event.price_verified && event.price_verified !== event.price_normal;
                        const displayPrice = isVerifiedMember && hasDiscount ? event.price_verified : event.price_normal;
                        const isFree = displayPrice.toLowerCase() === "free" || displayPrice === "₹0" || displayPrice === "0";
                        return (
                          <div className={cn(
                            "mb-4 flex items-center justify-between gap-3 rounded-xl border px-3.5 py-3 transition-colors",
                            isVerifiedMember && hasDiscount
                              ? "border-emerald-200 bg-emerald-50/30"
                              : "border-border bg-slate-50/50"
                          )}>
                            <div className="flex flex-col gap-0.5">
                              <span className="text-[9px] font-extrabold uppercase tracking-wider text-muted-foreground">
                                {t("events.ticket_price")}
                              </span>
                              <span className="inline-flex items-center gap-1 text-[11px] text-muted font-bold">
                                <MapPin size={11} className="text-primary" /> {event.attendees} {t("events.going")}
                              </span>
                            </div>
                            <div className="flex flex-col items-end">
                              <div className="flex items-center gap-1.5">
                                {isVerifiedMember && hasDiscount && (
                                  <span className="text-xs font-semibold text-muted-foreground line-through decoration-slate-400">
                                    {event.price_normal}
                                  </span>
                                )}
                                <span className={cn(
                                  "text-lg font-black leading-none",
                                  isFree ? "text-emerald-600 uppercase" : "text-primary"
                                )}>
                                  {displayPrice}
                                </span>
                              </div>
                              {hasDiscount && !isVerifiedMember && (
                                <span className="inline-flex items-center gap-0.5 rounded bg-emerald-50 px-1.5 py-0.5 text-[9px] font-extrabold text-emerald-600 mt-1 border border-emerald-100/60">
                                  <ShieldCheck size={9} className="shrink-0" />
                                  {t("events.verified")}: {event.price_verified}
                                </span>
                              )}
                              {isVerifiedMember && hasDiscount && (
                                <span className="inline-flex items-center gap-0.5 rounded bg-emerald-100 px-1.5 py-0.5 text-[9px] font-extrabold text-emerald-800 mt-1">
                                  <ShieldCheck size={9} className="shrink-0" />
                                  {t("events.verified_discount")}
                                </span>
                              )}
                            </div>
                          </div>
                        );
                      })()}

                      {event.status === "current" && (
                        <Link
                          href={`/events/${event.id}`}
                          className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]"
                        >
                          {t("events.book_ticket")} <ArrowRight size={16} />
                        </Link>
                      )}

                      {event.status === "upcoming" && (
                        <Link
                          href={`/events/${event.id}`}
                          className="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50/50 px-5 py-3 text-sm font-semibold text-amber-700 transition-all hover:bg-amber-50 active:scale-[0.98]"
                        >
                          {t("events.booking_soon")} <ArrowRight size={16} />
                        </Link>
                      )}

                      {event.status === "past" && (
                        <div className="w-full text-center py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 font-semibold text-sm">
                          {t("events.closed")}
                        </div>
                      )}
                    </div>
                  </div>
                </motion.div>
              ))}
            </AnimatePresence>
          </div>
        )}

        {filteredEvents.length === 0 && !loading && (
          <div className="rounded-2xl border border-dashed border-border py-24 text-center">
            <h3 className="text-xl font-semibold text-foreground">{t("events.no_events")}</h3>
            <p className="mx-auto mt-2 max-w-xs text-sm text-muted">
              {t("events.no_events_hint")}
            </p>
          </div>
        )}

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="mt-16 flex items-center justify-center gap-3">
            <button
              onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
              className="flex h-11 w-11 items-center justify-center rounded-xl border border-border bg-white text-foreground transition-colors hover:bg-surface cursor-pointer"
            >
              <ChevronLeft size={20} />
            </button>
            <div className="flex gap-2">
              {[...Array(totalPages)].map((_, i) => (
                <button
                  key={i}
                  onClick={() => setCurrentPage(i + 1)}
                  className={cn(
                    "h-11 w-11 rounded-xl text-sm font-semibold transition-all cursor-pointer",
                    currentPage === i + 1
                      ? "bg-primary text-white shadow-sm"
                      : "border border-border bg-white text-muted hover:bg-surface hover:text-foreground"
                  )}
                >
                  {i + 1}
                </button>
              ))}
            </div>
            <button
              onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
              className="flex h-11 w-11 items-center justify-center rounded-xl border border-border bg-white text-foreground transition-colors hover:bg-surface cursor-pointer"
            >
              <ChevronRight size={20} />
            </button>
          </div>
        )}
      </div>
    </div>
  );
}

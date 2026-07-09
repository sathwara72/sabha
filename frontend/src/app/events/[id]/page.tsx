"use client";

import { useParams, useRouter } from "next/navigation";
import {
  ArrowLeft, Calendar, MapPin, Clock,
  Users, Zap, Star, ArrowUpRight,
  CheckCircle2, Info, ShieldCheck, X, ChevronLeft, ChevronRight, Upload
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { useAuth } from "@/lib/auth";
import { useState, useEffect } from "react";
import { fetchEvents, getUserBusiness, reserveEventSeat } from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";
import { useLanguage } from "@/lib/language";
import Link from "next/link";

interface Speaker {
  name: string;
  role: string;
  bio: string;
}

interface Member {
  name: string;
  role: string;
}

interface EventItem {
  id: number;
  title: string;
  date: string;
  time: string;
  type: string;
  category: string;
  description: string;
  location: string;
  price_normal: string;
  price_verified: string;
  attendees: string;
  agenda: string[];
  speakers: Speaker[];
  members: Member[];
  status?: string;
  gallery_images?: any[];
  image?: string;
}


export default function EventDetailsPage() {
  const { id } = useParams();
  const router = useRouter();
  const { t } = useLanguage();
  const { isAuthenticated, user, openLogin } = useAuth();

  const [event, setEvent] = useState<EventItem | null>(null);
  const [loading, setLoading] = useState(true);
  const [isVerifiedMember, setIsVerifiedMember] = useState(false);

  // Reservation states
  const [isReserving, setIsReserving] = useState(false);
  const [paymentFile, setPaymentFile] = useState<File | null>(null);
  const [paymentPreview, setPaymentPreview] = useState<string>("");
  const [reservingLoading, setReservingLoading] = useState(false);
  const [reserveSuccess, setReserveSuccess] = useState(false);
  const [reserveError, setReserveError] = useState("");

  // Lightbox state for media gallery
  const [lightboxIndex, setLightboxIndex] = useState<number | null>(null);

  const getMediaUrl = (path: string) => {
    if (!path) return "";
    if (path.startsWith("http://") || path.startsWith("https://")) {
      return path;
    }
    return `${API_ORIGIN}${path}`;
  };

  const isVideoFile = (path?: string) => {
    if (!path) return false;
    const ext = path.split(".").pop()?.toLowerCase();
    return ["mp4", "mov", "avi", "webm", "mkv"].includes(ext || "");
  };

  const openLightbox = (index: number) => {
    setLightboxIndex(index);
  };

  const closeLightbox = () => {
    setLightboxIndex(null);
  };

  const nextMedia = () => {
    if (event?.gallery_images && lightboxIndex !== null) {
      setLightboxIndex((lightboxIndex + 1) % event.gallery_images.length);
    }
  };

  const prevMedia = () => {
    if (event?.gallery_images && lightboxIndex !== null) {
      setLightboxIndex((lightboxIndex - 1 + event.gallery_images.length) % event.gallery_images.length);
    }
  };

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
    async function loadEvent() {
      try {
        setLoading(true);
        const list = await fetchEvents();
        const matched = list.find((e: any) => e.id.toString() === id) as any;
        if (matched) {
          const matchedDate = new Date(matched.date);

          // Determine status
          const now = new Date();
          let status = "current";
          const dateOnly = new Date(matchedDate.getFullYear(), matchedDate.getMonth(), matchedDate.getDate());
          const todayOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

          if (dateOnly.getTime() === todayOnly.getTime()) {
            status = "current";
          } else if (dateOnly < todayOnly) {
            status = "past";
          }

          // Map approved registrations to members attending list
          const membersAttending = (matched.approved_registrations || [])
            .filter((reg: any) => reg.user)
            .map((reg: any) => {
              const u = reg.user;
              const role = u.designation
                ? (u.company ? `${u.designation} @ ${u.company}` : u.designation)
                : "SABHA Member";
              return {
                name: u.name,
                role: role
              };
            });

          setEvent({
            id: matched.id,
            title: matched.title,
            description: matched.description,
            location: matched.location,
            type: matched.type,
            category: matched.type,
            date: matchedDate.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }),
            time: matchedDate.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" }),
            price_normal: matched.price_normal || (matched.type === "Workshop" ? "Free" : "₹1,499"),
            price_verified: matched.price_verified || (matched.type === "Workshop" ? "Free" : "₹1,499"),
            attendees: (matched.approved_registrations || matched.approvedRegistrations)?.length?.toString() || "0",
            agenda: matched.agenda || [],
            speakers: matched.speakers || [],
            members: membersAttending,
            status,
            gallery_images: matched.gallery_images || [],
            image: matched.image ? getMediaUrl(matched.image) : "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800&auto=format&fit=crop",
          });
        } else {
          setEvent(null);
        }
      } catch (err) {
        console.error("Failed to load event details:", err);
      } finally {
        setLoading(false);
      }
    }
    if (id) {
      loadEvent();
    }
  }, [id]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background font-outfit">
        <div className="text-center space-y-3">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="text-sm font-medium text-muted">{t("eventDetail.loading")}</p>
        </div>
      </div>
    );
  }

  if (!event) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center bg-background font-outfit text-center p-6">
        <h2 className="text-2xl font-bold text-foreground">{t("eventDetail.not_found_title")}</h2>
        <p className="mt-2 text-sm text-muted">{t("eventDetail.not_found_desc")}</p>
        <button
          onClick={() => router.push("/events")}
          className="mt-6 inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]"
        >
          {t("eventDetail.back_to_events")}
        </button>
      </div>
    );
  }

  return (
    <div className="bg-background font-outfit min-h-screen pb-2">
      {/* Hero Section with Background Image */}
      <section className="relative overflow-hidden bg-slate-950 py-6 lg:py-6 text-white min-h-[260px] flex items-center border-b border-border">
        {/* Background Image with overlay */}
        {event.image && (
          <div className="absolute inset-0 z-0">
            <img
              src={event.image}
              alt=""
              className="h-full w-full object-cover opacity-35"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-900/30" />
          </div>
        )}

        {/* Content on top */}
        <div className="relative z-10 mx-auto max-w-7xl px-6 lg:px-8 w-full">
          <button
            onClick={() => router.back()}
            className="group mb-4 inline-flex items-center gap-1.5 text-xs font-semibold text-white/80 transition-colors hover:text-white cursor-pointer bg-white/10 backdrop-blur-sm px-3 py-1 rounded-xl border border-white/10"
          >
            <ArrowLeft className="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5" />
            {t("eventDetail.back_all_events")}
          </button>

          <div className="max-w-3xl space-y-3.5">
            <div className="flex flex-wrap items-center gap-2">
              <span className="rounded-full bg-white px-3 py-0.5 text-xs font-semibold text-primary shadow-sm">
                {event.category}
              </span>
              <span className="inline-flex items-center gap-1.5 text-xs text-slate-900 bg-white px-2.5 py-0.5 rounded-full shadow-sm font-medium">
                <Users className="h-3.5 w-3.5 text-primary animate-pulse" /> {event.attendees} {t("eventDetail.registered")}
              </span>
            </div>

            <h1 className="text-2xl font-extrabold tracking-tight text-white sm:text-3xl lg:text-4xl drop-shadow-md">
              {event.title}
            </h1>

            <div className="flex flex-wrap gap-x-3 gap-y-1.5 text-xs text-white/90 pt-1">
              <span className="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1 rounded-xl border border-white/10"><Calendar className="h-3.5 w-3.5 text-white" /> {event.date}</span>
              <span className="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1 rounded-xl border border-white/10"><Clock className="h-3.5 w-3.5 text-white" /> {event.time}</span>
              <span className="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1 rounded-xl border border-white/10"><MapPin className="h-3.5 w-3.5 text-white" /> {event.location}</span>
            </div>
          </div>
        </div>
      </section>

      {/* Split Body Layout */}
      <div className="mx-auto max-w-7xl px-6 py-6 lg:px-4">
        <div className="grid grid-cols-1 gap-2 lg:grid-cols-3">
          {/* Left Column: Descriptions, Speakers, Agenda, Members, Gallery */}
          <div className="space-y-3 lg:col-span-2">
            {event.description && (
              <section className="glass-card p-4">
                <div className="mb-3 flex items-center gap-2">
                  <span className="h-4 w-0.8 rounded-full bg-primary" />
                  <h2 className="text-xs font-bold uppercase tracking-wider text-muted-foreground">{t("eventDetail.about_event")}</h2>
                </div>
                <p className="text-xs sm:text-sm leading-relaxed text-muted font-medium">
                  {event.description}
                </p>
              </section>
            )}

            {event.agenda && event.agenda.length > 0 && (
              <section className="glass-card p-4">
                <div className="mb-3 flex items-center gap-2">
                  <span className="h-4 w-0.8 rounded-full bg-primary" />
                  <h2 className="text-xs font-bold uppercase tracking-wider text-muted-foreground">{t("eventDetail.agenda")}</h2>
                </div>
                <div className="space-y-1.5">
                  {event.agenda.map((item, i) => (
                    <div key={i} className="flex items-center gap-2.5 py-1.5 border-b border-dashed border-slate-100 last:border-0">
                      <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[10px] font-semibold text-primary">
                        {i + 1}
                      </span>
                      <span className="text-xs font-medium text-foreground">{item}</span>
                    </div>
                  ))}
                </div>
              </section>
            )}

            {event.speakers && event.speakers.length > 0 && (
              <section className="glass-card p-4">
                <div className="mb-3 flex items-center gap-2">
                  <span className="h-4 w-0.8 rounded-full bg-primary" />
                  <h2 className="text-xs font-bold uppercase tracking-wider text-muted-foreground">{t("eventDetail.speakers")}</h2>
                </div>
                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                  {event.speakers.map(speaker => (
                    <div key={speaker.name} className="flex items-start gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 animate-fade-in">
                      <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-sm font-bold text-primary">
                        {speaker.name.charAt(0)}
                      </div>
                      <div className="min-w-0 flex-1">
                        <h4 className="text-xs font-semibold text-foreground leading-tight">{speaker.name}</h4>
                        <p className="text-[10px] font-medium text-primary leading-tight mt-0.5">{speaker.role}</p>
                        <p className="mt-1.5 text-xs text-muted leading-relaxed line-clamp-2">{speaker.bio}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </section>
            )}

            {event.members && (
              <section className="glass-card p-4">
                <div className="mb-3 flex items-center gap-2">
                  <span className="h-4 w-0.8 rounded-full bg-primary" />
                  <h2 className="text-xs font-bold uppercase tracking-wider text-muted-foreground">{t("eventDetail.members_attending")}</h2>
                </div>
                {event.members.length === 0 ? (
                  <p className="text-xs text-muted italic pl-1">No members have registered for this event yet.</p>
                ) : (
                  <div className="grid grid-cols-2 gap-2 md:grid-cols-3">
                    {event.members.map(member => (
                      <div key={member.name} className="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <div className="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-[10px] font-bold text-muted border border-border">
                          {member.name.split(" ").map(n => n[0]).join("")}
                        </div>
                        <div className="min-w-0">
                          <p className="truncate text-xs font-semibold text-foreground leading-tight">{member.name}</p>
                          <p className="truncate text-[9px] text-muted leading-tight mt-0.5">{member.role}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </section>
            )}

            {event.gallery_images && event.gallery_images.length > 0 && (
              <section className="glass-card p-4 animate-fade-in">
                <div className="mb-3 flex items-center gap-2">
                  <span className="h-4 w-0.8 rounded-full bg-primary" />
                  <h2 className="text-xs font-bold uppercase tracking-wider text-muted-foreground">{t("eventDetail.event_gallery")}</h2>
                </div>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                  {event.gallery_images.map((img: any, idx: number) => {
                    const isVideo = isVideoFile(img.image_path);
                    return (
                      <div
                        key={img.id || idx}
                        onClick={() => openLightbox(idx)}
                        className="overflow-hidden rounded-xl relative cursor-pointer group aspect-video hover:shadow-md transition-all duration-300 border border-border bg-slate-100"
                      >
                        {isVideo ? (
                          <div className="w-full h-full relative bg-slate-900 flex items-center justify-center">
                            <video
                              src={getMediaUrl(img.image_path)}
                              className="w-full h-full object-cover"
                              muted
                              preload="metadata"
                            />
                            <div className="absolute inset-0 bg-black/30 flex items-center justify-center group-hover:bg-black/50 transition-colors">
                              <span className="p-2 bg-white/20 backdrop-blur rounded-full text-white">
                                <svg className="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                  <path d="M8 5v14l11-7z" />
                                </svg>
                              </span>
                            </div>
                          </div>
                        ) : (
                          <img
                            src={getMediaUrl(img.image_path)}
                            alt={img.caption || `Event photo ${idx + 1}`}
                            className="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300"
                          />
                        )}
                        {img.caption && (
                          <div className="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <p className="text-[10px] text-white font-semibold truncate">{img.caption}</p>
                          </div>
                        )}
                      </div>
                    );
                  })}
                </div>
              </section>
            )}
          </div>

          {/* Right Column - Sticky Sidebar */}
          <div className="space-y-4 lg:sticky lg:top-20 h-fit">
            {/* Booking Card */}
            <div className="glass-card p-4">
              <p className="text-[9px] font-bold uppercase tracking-wider text-muted-foreground mb-2">{t("eventDetail.ticket_prices")}</p>
              <div className="space-y-1.5 text-center">
                {/* Standard Price */}
                <div className="py-1.5 px-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                  <span className="text-[11px] font-semibold text-muted-foreground">{t("eventDetail.standard")}:</span>
                  <span className="text-sm font-bold text-foreground">{event.price_normal}</span>
                </div>

                {/* Verified Price */}
                <div
                  onClick={() => {
                    if (!isVerifiedMember) {
                      router.push("/profile?tab=business");
                    }
                  }}
                  className={`py-1.5 px-2.5 rounded-xl border flex items-center justify-between cursor-pointer transition-colors ${isVerifiedMember
                      ? "bg-emerald-50 border-emerald-200 text-emerald-700"
                      : "bg-white border-border hover:bg-emerald-50/20 hover:border-emerald-200 text-muted-foreground group"
                    }`}
                >
                  <span className="text-[11px] font-semibold text-emerald-600 flex items-center gap-0.5"><ShieldCheck size={11} /> {t("eventDetail.verified")}:</span>
                  <div className="text-right">
                    <span className="text-sm font-bold text-foreground">{event.price_verified}</span>
                    {!isVerifiedMember && <span className="block text-[7px] font-semibold text-emerald-600">{t("eventDetail.click_to_get")} &rarr;</span>}
                  </div>
                </div>
              </div>

              {/* Booking Status display */}
              <div className="mt-2.5 border-t border-border pt-2.5 w-full text-center space-y-2">
                {event.status === "upcoming" && (
                  <span className="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-100 uppercase tracking-wide w-full justify-center">
                    {t("eventDetail.booking_soon")}
                  </span>
                )}
                {event.status === "current" && (
                  <span className="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-100 uppercase tracking-wide w-full justify-center">
                    {t("eventDetail.booking_available")}
                  </span>
                )}
                {event.status === "past" && (
                  <span className="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500 border border-slate-200 uppercase tracking-wide w-full justify-center">
                    {t("eventDetail.booking_closed")}
                  </span>
                )}

                {event.status === "current" && (
                  <button
                    onClick={() => {
                      if (!isAuthenticated) {
                        openLogin();
                      } else {
                        setIsReserving(true);
                      }
                    }}
                    className="group inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] w-full cursor-pointer"
                  >
                    {t("eventDetail.reserve_seat")}
                    <ArrowUpRight size={13} className="transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                  </button>
                )}

                {event.status === "upcoming" && (
                  <button disabled className="inline-flex items-center justify-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50/50 px-4 py-2 text-xs font-semibold text-amber-700 w-full opacity-80">
                    {t("eventDetail.booking_soon")}
                  </button>
                )}
              </div>
            </div>

            {/* Event Details Card */}
            <div className="glass-card p-4">
              <h3 className="border-b border-border pb-2 text-xs font-bold uppercase tracking-wider text-foreground">{t("eventDetail.details")}</h3>
              <div className="space-y-3 pt-3">
                <div className="flex gap-2.5">
                  <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <MapPin className="h-3.5 w-3.5" />
                  </div>
                  <div>
                    <p className="text-[9px] font-semibold text-muted">{t("eventDetail.location")}</p>
                    <p className="mt-0.5 text-xs font-medium text-foreground">{event.location}</p>
                  </div>
                </div>
                <div className="flex gap-2.5">
                  <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <Zap className="h-3.5 w-3.5" />
                  </div>
                  <div>
                    <p className="text-[9px] font-semibold text-muted">{t("eventDetail.format")}</p>
                    <p className="mt-0.5 text-xs font-medium text-foreground">{event.type} {t("eventDetail.interactive")}</p>
                  </div>
                </div>
              </div>

              <div className="mt-4 space-y-2">
                <div className="flex items-center gap-1.5 rounded-xl bg-primary-soft px-2.5 py-1.5 text-[11px] font-medium text-primary">
                  <CheckCircle2 className="h-3 w-3" />
                  {t("eventDetail.limited_capacity")}
                </div>
                <div className="flex items-center gap-1.5 px-1 text-[11px] text-muted">
                  <Info size={12} />
                  {t("eventDetail.register_before")}
                </div>
              </div>
            </div>

            {/* Good to Know */}
            <div className="rounded-2xl border border-border bg-primary p-4 text-white">
              <h4 className="text-xs font-bold uppercase tracking-wider">{t("eventDetail.good_to_know")}</h4>
              <p className="mt-1.5 text-xs leading-relaxed text-white/80">
                {t("eventDetail.good_to_know_desc")}
              </p>
              <div className="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-white/15 px-2.5 py-1.5">
                <Star className="h-3.5 w-3.5 text-white" fill="currentColor" />
                <span className="text-[11px] font-semibold text-white">{t("eventDetail.great_experience")}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Lightbox Modal */}
      {lightboxIndex !== null && event?.gallery_images && event.gallery_images.length > 0 && (
        <div
          className="fixed inset-0 z-[200] flex items-center justify-center bg-black/90 backdrop-blur-sm"
          onClick={closeLightbox}
        >
          {/* Close button */}
          <button
            onClick={closeLightbox}
            className="absolute top-4 right-4 z-20 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer"
            aria-label="Close lightbox"
          >
            <X className="h-6 w-6" />
          </button>

          {/* Prev button */}
          {event.gallery_images.length > 1 && (
            <button
              onClick={(e) => { e.stopPropagation(); prevMedia(); }}
              className="absolute left-4 z-20 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer"
              aria-label="Previous"
            >
              <ChevronLeft className="h-6 w-6" />
            </button>
          )}

          {/* Media */}
          <div
            className="relative max-w-4xl max-h-[80vh] w-full mx-16 flex items-center justify-center"
            onClick={(e) => e.stopPropagation()}
          >
            {(() => {
              const img = event.gallery_images![lightboxIndex];
              if (!img) return null;
              const isVideo = isVideoFile(img.image_path);
              return isVideo ? (
                <video
                  src={getMediaUrl(img.image_path)}
                  controls
                  autoPlay
                  className="max-h-[80vh] max-w-full rounded-xl shadow-2xl object-contain"
                />
              ) : (
                <img
                  src={getMediaUrl(img.image_path)}
                  alt={img.caption || `Event photo ${lightboxIndex + 1}`}
                  className="max-h-[80vh] max-w-full rounded-xl shadow-2xl object-contain"
                />
              );
            })()}
            {event.gallery_images[lightboxIndex]?.caption && (
              <div className="absolute bottom-0 left-0 right-0 px-4 py-3 bg-gradient-to-t from-black/70 to-transparent rounded-b-xl">
                <p className="text-sm text-white font-medium text-center">
                  {event.gallery_images[lightboxIndex].caption}
                </p>
              </div>
            )}
          </div>

          {/* Next button */}
          {event.gallery_images.length > 1 && (
            <button
              onClick={(e) => { e.stopPropagation(); nextMedia(); }}
              className="absolute right-4 z-20 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer"
              aria-label="Next"
            >
              <ChevronRight className="h-6 w-6" />
            </button>
          )}

          {/* Counter */}
          <div className="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 text-white/70 text-xs font-semibold tracking-wider">
            {lightboxIndex + 1} / {event.gallery_images.length}
          </div>
        </div>
      )}

      {/* Seat Reservation Modal */}
      <AnimatePresence>
        {isReserving && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setIsReserving(false)}
              className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
            />
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 15 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 15 }}
              className="relative w-full max-w-md overflow-hidden rounded-2xl bg-white border border-border p-5 shadow-2xl z-10"
            >
              {/* Header */}
              <div className="flex items-center justify-between border-b border-border pb-3 mb-3">
                <div>
                  <h3 className="text-sm font-bold text-foreground">{t("eventDetail.reserve_seat_title")}</h3>
                  <p className="text-[10px] text-muted-foreground mt-0.5">{event.title}</p>
                </div>
                <button
                  onClick={() => setIsReserving(false)}
                  className="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 transition-colors cursor-pointer"
                >
                  <X size={16} />
                </button>
              </div>

              {reserveSuccess ? (
                <div className="text-center py-4 space-y-3">
                  <div className="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-green-50 text-green-600 border border-green-200">
                    <CheckCircle2 size={20} />
                  </div>
                  <div>
                    <h4 className="text-xs font-bold text-foreground">{t("eventDetail.reservation_requested")}</h4>
                    <p className="text-[11px] text-muted-foreground mt-1 max-w-xs mx-auto">
                      {t("eventDetail.reservation_requested_desc")}
                    </p>
                  </div>
                  <button
                    onClick={() => {
                      setIsReserving(false);
                      setReserveSuccess(false);
                    }}
                    className="w-full inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm hover:opacity-90 active:scale-[0.98] cursor-pointer"
                  >
                    {t("eventDetail.close")}
                  </button>
                </div>
              ) : (
                <form
                  onSubmit={async (e) => {
                    e.preventDefault();
                    if (!paymentFile) {
                      setReserveError(t("eventDetail.payment_required"));
                      return;
                    }
                    setReservingLoading(true);
                    setReserveError("");
                    try {
                      const data = new FormData();
                      data.append("payment_screenshot", paymentFile);
                      data.append("ticket_type", isVerifiedMember ? "verified" : "standard");
                      const priceStr = isVerifiedMember ? event.price_verified : event.price_normal;
                      const amountPaid = priceStr.toLowerCase() === "free" ? "0" : priceStr.replace(/[^0-9]/g, "");
                      data.append("amount_paid", amountPaid);
                      await reserveEventSeat(event.id, data);
                      setReserveSuccess(true);
                      setPaymentFile(null);
                      setPaymentPreview("");
                    } catch (err: any) {
                      setReserveError(err.message || t("eventDetail.reserve_failed"));
                    } finally {
                      setReservingLoading(false);
                    }
                  }}
                  className="space-y-3"
                >
                  {reserveError && (
                    <div className="rounded-xl bg-red-50 border border-red-100 p-2.5 text-center text-xs font-semibold text-red-600">
                      {reserveError}
                    </div>
                  )}

                  {/* Personal details (prefilled) */}
                  <div className="space-y-1.5 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <h4 className="text-[9px] font-bold uppercase tracking-wider text-muted-foreground mb-0.5">{t("eventDetail.your_personal_details")}</h4>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                      <div className="min-w-0">
                        <span className="text-[9px] text-muted-foreground font-semibold block">{t("eventDetail.full_name")}</span>
                        <span className="font-bold text-foreground block truncate">{user?.name}</span>
                      </div>
                      <div className="min-w-0">
                        <span className="text-[9px] text-muted-foreground font-semibold block">{t("eventDetail.email_address")}</span>
                        <span className="font-bold text-foreground block truncate" title={user?.email}>{user?.email}</span>
                      </div>
                      <div className="min-w-0">
                        <span className="text-[9px] text-muted-foreground font-semibold block">{t("eventDetail.phone_number")}</span>
                        <span className="font-bold text-foreground block truncate">{user?.phone || t("eventDetail.phone_not_provided")}</span>
                      </div>
                    </div>
                  </div>

                  {/* Ticket Price Details */}
                  <div className="space-y-1.5">
                    <label className="text-[11px] font-bold text-muted-foreground block">{t("eventDetail.select_ticket_type")}</label>
                    <div className="grid grid-cols-2 gap-2">
                      {/* Standard Ticket Choice */}
                      <div
                        className={`p-2.5 rounded-xl border flex flex-col justify-between transition-all ${!isVerifiedMember
                            ? "border-primary bg-primary-soft/30 text-foreground"
                            : "border-border bg-slate-50 text-muted-foreground"
                          }`}
                      >
                        <div className="flex items-center justify-between">
                          <span className="text-[9px] font-bold uppercase tracking-wider">{t("eventDetail.standard")}</span>
                          {!isVerifiedMember && <div className="h-2.5 w-2.5 rounded-full border-2 border-primary bg-primary flex items-center justify-center"><div className="h-1 w-1 rounded-full bg-white" /></div>}
                        </div>
                        <span className="text-sm font-extrabold text-foreground mt-0.5">{event.price_normal}</span>
                      </div>

                      {/* Verified Member Ticket Choice */}
                      <div
                        onClick={() => {
                          if (!isVerifiedMember) {
                            router.push("/profile?tab=business");
                          }
                        }}
                        className={`p-2.5 rounded-xl border flex flex-col justify-between cursor-pointer transition-all ${isVerifiedMember
                            ? "border-primary bg-primary-soft/30 text-foreground"
                            : "border-border bg-white hover:border-emerald-200 hover:bg-emerald-50/20 text-muted-foreground group"
                          }`}
                      >
                        <div className="flex items-center justify-between">
                          <span className="text-[9px] font-bold uppercase tracking-wider text-emerald-600 flex items-center gap-0.5"><ShieldCheck size={10} /> {t("eventDetail.verified")}</span>
                          {isVerifiedMember ? (
                            <div className="h-2.5 w-2.5 rounded-full border-2 border-primary bg-primary flex items-center justify-center"><div className="h-1 w-1 rounded-full bg-white" /></div>
                          ) : (
                            <span className="text-[7px] bg-emerald-50 text-emerald-700 px-1 py-0.5 rounded font-bold border border-emerald-100 uppercase tracking-wide group-hover:scale-105 transition-transform">{t("eventDetail.get_price")}</span>
                          )}
                        </div>
                        <div className="mt-0.5 flex flex-col">
                          <span className="text-sm font-extrabold text-foreground">{event.price_verified}</span>
                          {!isVerifiedMember && <span className="text-[7px] font-semibold text-emerald-600 mt-0.5">{t("eventDetail.requires_business")} &rarr;</span>}
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Payment Screenshot Upload */}
                  <div className="space-y-1.5">
                    <label className="text-[11px] font-bold text-muted-foreground block">{t("eventDetail.upload_payment")}</label>
                    <div className="flex items-center justify-center w-full">
                      <label className="flex flex-col items-center justify-center w-full h-22 border-2 border-dashed border-border rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors">
                        {paymentPreview ? (
                          <div className="relative h-full w-full p-1.5 flex items-center justify-center gap-3">
                            <img src={paymentPreview} alt="Payment SS" className="h-full object-contain rounded-lg border border-border" />
                            <span className="text-xs text-muted-foreground font-semibold truncate max-w-[150px]">{paymentFile?.name}</span>
                          </div>
                        ) : (
                          <div className="flex flex-col items-center justify-center py-3">
                            <Upload className="w-6 h-6 text-muted-foreground mb-1" />
                            <p className="text-[10px] text-muted-foreground font-semibold">{t("eventDetail.click_upload")}</p>
                          </div>
                        )}
                        <input
                          type="file"
                          accept="image/*"
                          onChange={(e) => {
                            const f = e.target.files?.[0] || null;
                            setPaymentFile(f);
                            if (f) setPaymentPreview(URL.createObjectURL(f));
                          }}
                          className="hidden"
                        />
                      </label>
                    </div>
                  </div>

                  {/* Submit */}
                  <button
                    type="submit"
                    disabled={reservingLoading}
                    className="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                  >
                    {reservingLoading ? t("eventDetail.requesting") : t("eventDetail.submit_reservation")}
                  </button>
                </form>
              )}
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}

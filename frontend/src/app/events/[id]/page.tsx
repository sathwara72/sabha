"use client";

import { useParams, useRouter } from "next/navigation";
import {
  ArrowLeft, Calendar, MapPin, Clock,
  Users, Zap, Star, ArrowUpRight,
  CheckCircle2, Info, ShieldCheck, X, ChevronLeft, ChevronRight
} from "lucide-react";
import { motion } from "framer-motion";
import { useAuth } from "@/lib/auth";
import { useState, useEffect } from "react";
import { fetchEvents, getUserBusiness } from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";

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
}

const mockEvents = [
  { id: 1, title: "Modern Business Networking Mixer", date: "Oct 12, 2026", time: "6:30 PM", type: "Physical", category: "Networking", description: "Connect with over 50 local entrepreneurs and service providers in a structured networking environment.", location: "The Grand Ballroom, Mumbai", price_normal: "₹2,499", price_verified: "₹1,499", attendees: "120+", agenda: ["6:30 PM - Welcome Drinks", "7:00 PM - Speed Networking", "8:00 PM - Open Floor", "9:00 PM - Closing Ceremony"], speakers: [{ name: "Ananya Iyer", role: "Networking Expert", bio: "Renowned connector and community strategist." }], members: [{ name: "Ravi Sharma", role: "Founder, TechWave" }, { name: "Pooja Verma", role: "CEO, DesignFlow" }, { name: "Amit Shah", role: "Director, BuildCo" }, { name: "Neha Gupta", role: "Marketing Lead, Nexus" }, { name: "Karan Mehta", role: "Founder, Zenith" }, { name: "Sara Khan", role: "Partner, Summit" }] },
  { id: 2, title: "Next.js & Laravel: Scaling to Millions", date: "Oct 15, 2026", time: "2:00 PM", type: "Virtual", category: "Workshop", description: "Deep dive into full-stack architecture with industry experts. Learn how to scale your community platform.", location: "Zoom (Member Access)", price_normal: "Free", price_verified: "Free", attendees: "500+", agenda: ["2:00 PM - System Design", "3:30 PM - Performance Tactics", "4:30 PM - Q&A Session"], speakers: [{ name: "Vikram Seth", role: "CTO, ScalingHub", bio: "Built and scaled 3 SaaS platforms to 1M+ MAU." }], members: [{ name: "Dev Patel", role: "Engineer, CloudOps" }, { name: "Aisha Rao", role: "Founder, Stackly" }, { name: "Rohit Nair", role: "CTO, Finbox" }, { name: "Meera Joshi", role: "Lead, DataForge" }] },
];

export default function EventDetailsPage() {
  const { id } = useParams();
  const router = useRouter();
  const { isAuthenticated } = useAuth();
  
  const [event, setEvent] = useState<EventItem | null>(null);
  const [loading, setLoading] = useState(true);
  const [isVerifiedMember, setIsVerifiedMember] = useState(false);

  // Lightbox state for media gallery
  const [lightboxIndex, setLightboxIndex] = useState<number | null>(null);

  const getMediaUrl = (path: string) => {
    if (!path) return "";
    if (path.startsWith("http://") || path.startsWith("https://")) {
      return path;
    }
    return `${API_ORIGIN}${path}`;
  };

  const isVideoFile = (path: string) => {
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
        const matched = list.find((e: any) => e.id.toString() === id);
        if (matched) {
          const matchedDate = new Date(matched.date);
          
          // Determine status
          const now = new Date();
          let status = "upcoming";
          const dateOnly = new Date(matchedDate.getFullYear(), matchedDate.getMonth(), matchedDate.getDate());
          const todayOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

          if (dateOnly.getTime() === todayOnly.getTime()) {
            status = "current";
          } else if (dateOnly < todayOnly) {
            status = "past";
          }

          const mockDetail = mockEvents.find(
            e => e.title.toLowerCase() === matched.title.toLowerCase() || e.id.toString() === id
          );
          if (mockDetail) {
            setEvent({
              ...mockDetail,
              id: matched.id,
              title: matched.title,
              description: matched.description,
              location: matched.location,
              type: matched.type,
              category: matched.type,
              date: matchedDate.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }),
              time: matchedDate.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" }),
              price_normal: matched.price_normal || mockDetail.price_normal,
              price_verified: matched.price_verified || mockDetail.price_verified,
              status,
              gallery_images: matched.gallery_images || [],
            });
          } else {
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
              attendees: "50+",
              agenda: ["Registration & Welcome", "Expert Panel Discussion", "Q&A Session", "Networking Mixer"],
              speakers: [{ name: "Community Speaker", role: "Industry Expert", bio: "Sharing experience and best practices." }],
              members: [{ name: "Admin User", role: "SABHA Admin" }],
              status,
              gallery_images: matched.gallery_images || [],
            });
          }
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
          <p className="text-sm font-medium text-muted">Loading event details...</p>
        </div>
      </div>
    );
  }

  if (!event) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center bg-background font-outfit text-center p-6">
        <h2 className="text-2xl font-bold text-foreground">Event not found</h2>
        <p className="mt-2 text-sm text-muted">The requested event listing does not exist or has been removed.</p>
        <button
          onClick={() => router.push("/events")}
          className="mt-6 inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]"
        >
          Back to events
        </button>
      </div>
    );
  }

  return (
    <div className="bg-background font-outfit">
      {/* Header */}
      <section className="hero-surface border-b border-border">
        <div className="mx-auto max-w-7xl px-6 py-5 lg:px-8 lg:py-6">
          <button
            onClick={() => router.back()}
            className="group mb-4 inline-flex items-center gap-1.5 text-xs font-semibold text-muted transition-colors hover:text-primary cursor-pointer"
          >
            <ArrowLeft className="h-3.5 w-3.5 transition-transform group-hover:-translate-x-0.5" />
            Back to all events
          </button>

          <div className="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
            <motion.div
              initial={{ opacity: 0, y: 12 }}
              animate={{ opacity: 1, y: 0 }}
              className="max-w-3xl space-y-3"
            >
              <div className="flex flex-wrap items-center gap-3">
                <span className="rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-medium text-primary">
                  {event.category}
                </span>
                <span className="inline-flex items-center gap-1 text-xs text-muted">
                  <Users className="h-3.5 w-3.5 text-primary" /> {event.attendees} registered
                </span>
              </div>
              <h1 className="text-2xl font-bold tracking-tight text-foreground sm:text-3xl lg:text-4xl">
                {event.title}
              </h1>
              <div className="flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-muted">
                <span className="inline-flex items-center gap-1.5"><Calendar className="h-3.5 w-3.5 text-primary" /> {event.date}</span>
                <span className="inline-flex items-center gap-1.5"><Clock className="h-3.5 w-3.5 text-primary" /> {event.time}</span>
                <span className="inline-flex items-center gap-1.5"><MapPin className="h-3.5 w-3.5 text-primary" /> {event.location}</span>
              </div>
            </motion.div>

            <div className="glass-card flex items-center justify-between gap-4 p-4 w-full lg:w-auto lg:min-w-[280px] lg:flex-col lg:justify-center">
              <div className="text-left lg:text-center w-full">
                <p className="text-[10px] font-bold uppercase tracking-wider text-muted mb-1">Ticket price</p>
                {isVerifiedMember ? (
                  <div>
                    <h3 className="text-2xl font-bold text-foreground">{event.price_verified}</h3>
                    <span className="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 mt-1.5 bg-emerald-50 px-2 py-0.5 rounded-md">
                      <ShieldCheck size={12} /> Verified Member Price
                    </span>
                  </div>
                ) : (
                  <div className="space-y-1">
                    <h3 className="text-2xl font-bold text-foreground">{event.price_normal}</h3>
                    {event.price_verified && event.price_verified !== event.price_normal && (
                      <p className="text-xs font-semibold text-muted-foreground">
                        {event.price_verified} for Verified Members
                      </p>
                    )}
                  </div>
                )}

                {/* Booking Status display */}
                <div className="mt-3.5 border-t border-border pt-3 w-full text-center">
                  {event.status === "upcoming" && (
                    <span className="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-100 uppercase tracking-wide w-full justify-center">
                      Booking open soon
                    </span>
                  )}
                  {event.status === "current" && (
                    <span className="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-100 uppercase tracking-wide w-full justify-center">
                      Booking Available
                    </span>
                  )}
                  {event.status === "past" && (
                    <span className="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500 border border-slate-200 uppercase tracking-wide w-full justify-center">
                      Booking Closed
                    </span>
                  )}
                </div>
              </div>

              {event.status === "current" && (
                <button className="group inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4.5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] w-auto lg:w-full cursor-pointer">
                  Reserve seat
                  <ArrowUpRight size={15} className="transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                </button>
              )}

              {event.status === "upcoming" && (
                <button disabled className="inline-flex items-center justify-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50/50 px-4.5 py-2.5 text-sm font-semibold text-amber-700 w-auto lg:w-full opacity-80">
                  Booking open soon
                </button>
              )}
            </div>
          </div>
        </div>
      </section>

      <div className="mx-auto max-w-7xl px-6 py-10 lg:px-8">
        <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
          {/* Main Content */}
          <div className="space-y-8 lg:col-span-2">
            <section>
              <div className="mb-4 flex items-center gap-2.5">
                <span className="h-5 w-1 rounded-full bg-primary" />
                <h2 className="text-lg font-bold text-foreground">About this event</h2>
              </div>
              <p className="text-sm leading-relaxed text-muted font-medium">
                {event.description}
              </p>
            </section>

            <section>
              <div className="mb-4 flex items-center gap-2.5">
                <span className="h-5 w-1 rounded-full bg-primary" />
                <h2 className="text-lg font-bold text-foreground">Agenda</h2>
              </div>
              <div className="space-y-2">
                {event.agenda.map((item, i) => (
                  <div key={i} className="glass-card flex items-center gap-3 p-3">
                    <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-semibold text-primary">
                      {i + 1}
                    </span>
                    <span className="text-sm font-medium text-foreground">{item}</span>
                  </div>
                ))}
              </div>
            </section>

            <section>
              <div className="mb-4 flex items-center gap-2.5">
                <span className="h-5 w-1 rounded-full bg-primary" />
                <h2 className="text-lg font-bold text-foreground">Speakers</h2>
              </div>
              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {event.speakers.map(speaker => (
                  <div key={speaker.name} className="glass-card flex items-start gap-4 p-4 animate-fade-in">
                    <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-lg font-bold text-primary">
                      {speaker.name.charAt(0)}
                    </div>
                    <div className="min-w-0 flex-1">
                      <h4 className="text-sm font-semibold text-foreground leading-tight">{speaker.name}</h4>
                      <p className="text-xs font-medium text-primary leading-tight mt-0.5">{speaker.role}</p>
                      <p className="mt-2 text-xs text-muted leading-relaxed line-clamp-2">{speaker.bio}</p>
                    </div>
                  </div>
                ))}
              </div>
            </section>

            {event.members && event.members.length > 0 && (
              <section>
                <div className="mb-4 flex items-center gap-2.5">
                  <span className="h-5 w-1 rounded-full bg-primary" />
                  <h2 className="text-lg font-bold text-foreground">Members Attending</h2>
                </div>
                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
                  {event.members.map(member => (
                    <div key={member.name} className="glass-card flex items-center gap-3 p-3">
                      <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-surface text-xs font-bold text-muted border border-border">
                        {member.name.split(" ").map(n => n[0]).join("")}
                      </div>
                      <div className="min-w-0">
                        <p className="truncate text-xs font-semibold text-foreground leading-tight">{member.name}</p>
                        <p className="truncate text-[10px] text-muted leading-tight mt-0.5">{member.role}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </section>
            )}

            {event.gallery_images && event.gallery_images.length > 0 && (
              <section className="animate-fade-in">
                <div className="mb-4 flex items-center gap-2.5">
                  <span className="h-5 w-1 rounded-full bg-primary" />
                  <h2 className="text-lg font-bold text-foreground">Event Gallery</h2>
                </div>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-4">
                  {event.gallery_images.map((img: any, idx: number) => {
                    const isVideo = isVideoFile(img.image_path);
                    return (
                      <div
                        key={img.id || idx}
                        onClick={() => openLightbox(idx)}
                        className="glass-card p-0 overflow-hidden relative cursor-pointer group aspect-video hover:shadow-md transition-all duration-300 border border-border bg-surface/30"
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
                                <svg className="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                  <path d="M8 5v14l11-7z"/>
                                </svg>
                              </span>
                            </div>
                          </div>
                        ) : (
                          <img
                            src={getMediaUrl(img.image_path)}
                            alt={img.caption || `Event photo ${idx + 1}`}
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                          />
                        )}
                        {img.caption && (
                          <div className="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent p-2.5 opacity-0 group-hover:opacity-100 transition-opacity">
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

          {/* Details & Registration */}
          <div className="space-y-6">
            <div className="glass-card p-5">
              <h3 className="border-b border-border pb-3 text-base font-semibold text-foreground">Details</h3>
              <div className="space-y-4 pt-4">
                <div className="flex gap-3">
                  <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <MapPin className="h-4 w-4" />
                  </div>
                  <div>
                    <p className="text-[10px] font-semibold text-muted">Location</p>
                    <p className="mt-0.5 text-sm font-medium text-foreground">{event.location}</p>
                  </div>
                </div>
                <div className="flex gap-3">
                  <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <Zap className="h-4 w-4" />
                  </div>
                  <div>
                    <p className="text-[10px] font-semibold text-muted">Format</p>
                    <p className="mt-0.5 text-sm font-medium text-foreground">{event.type} interactive</p>
                  </div>
                </div>
              </div>

              <div className="mt-5 space-y-2.5">
                <div className="flex items-center gap-2 rounded-xl bg-primary-soft px-3 py-2 text-xs font-medium text-primary">
                  <CheckCircle2 className="h-3.5 w-3.5" />
                  Limited capacity
                </div>
                <div className="flex items-center gap-2 px-1 text-xs text-muted">
                  <Info size={14} />
                  Register before early access closure.
                </div>
              </div>
            </div>

            <div className="rounded-2xl border border-border bg-primary p-5 text-white">
              <h4 className="text-base font-bold">Good to know</h4>
              <p className="mt-2 text-xs leading-relaxed text-white/80">
                All events are organized for meaningful professional networking. We want every member
                to leave with new connections.
              </p>
              <div className="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-white/15 px-3 py-2">
                <Star className="h-4 w-4 text-white" fill="currentColor" />
                <span className="text-xs font-semibold text-white">Great experience</span>
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
    </div>
  );
}

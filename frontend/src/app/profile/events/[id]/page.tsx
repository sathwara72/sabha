"use client";

import { useParams, useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { useAuth } from "@/lib/auth";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import {
  ArrowLeft,
  Calendar,
  MapPin,
  Clock,
  CheckCircle2,
  AlertCircle,
  XCircle,
  Ticket,
  Download,
  Share2,
  Users,
  CreditCard,
  ChevronRight,
  Info,
  ShieldCheck,
} from "lucide-react";
import { fetchEvents, getUserRegistrations } from "@/lib/api";
import { assetUrl } from "@/lib/config";
import { useLanguage } from "@/lib/language";

// Mirrors the mock list in profile page
const statusConfig: Record<
  string,
  { tKey: string; color: string; bg: string; border: string; icon: any }
> = {
  confirmed: {
    tKey: "bookingDetail.status_confirmed",
    color: "text-emerald-700",
    bg: "bg-emerald-50",
    border: "border-emerald-200",
    icon: CheckCircle2,
  },
  approved: {
    tKey: "bookingDetail.status_confirmed",
    color: "text-emerald-700",
    bg: "bg-emerald-50",
    border: "border-emerald-200",
    icon: CheckCircle2,
  },
  pending: {
    tKey: "bookingDetail.status_pending",
    color: "text-amber-700",
    bg: "bg-amber-50",
    border: "border-amber-200",
    icon: AlertCircle,
  },
  rejected: {
    tKey: "bookingDetail.status_rejected",
    color: "text-red-700",
    bg: "bg-red-50",
    border: "border-red-200",
    icon: XCircle,
  },
};

export default function BookingDetailPage() {
  const { id } = useParams();
  const router = useRouter();
  const { t } = useLanguage();
  const { isAuthenticated, isReady } = useAuth();
  const [event, setEvent] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [copied, setCopied] = useState(false);

  useEffect(() => {
    if (isReady && !isAuthenticated) {
      router.replace("/");
    }
  }, [isReady, isAuthenticated, router]);

  useEffect(() => {
    async function loadRegistration() {
      if (!isReady || !isAuthenticated) return;
      try {
        setLoading(true);
        const registrations = await getUserRegistrations();
        const found = registrations.find((r: any) => r.id.toString() === id);
        if (found && found.event) {
          const matchedDate = new Date(found.event.date);
          
          setEvent({
            id: found.id,
            title: found.event.title,
            date: matchedDate.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }),
            time: matchedDate.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" }),
            location: found.event.location,
            status: found.status,
            price: Number(found.amount_paid) === 0 ? "Free" : `₹${Number(found.amount_paid).toLocaleString("en-IN")}`,
            ticket_no: found.ticket_number,
            is_attended: found.is_attended,
            image: assetUrl(found.event.cover_image) || "/placeholder-event.jpg",
            category: found.event.type || "Event",
            type: found.event.type === "Virtual" ? "Virtual" : "Physical",
            attendees: "100+",
            description: found.event.description || "",
            agenda: ["Registration & Welcome", "Expert Panel Discussion", "Q&A Session", "Networking Mixer"],
            notes: found.status === "confirmed" || found.status === "approved"
              ? "Please carry your QR code / ticket number at entry. Dress code: Business Formal." 
              : found.status === "rejected"
              ? `Your registration request was rejected. Reason: ${found.rejection_reason || "None specified"}`
              : "Your payment is under verification. You will receive a confirmation email once approved.",
            paid_on: new Date(found.created_at).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }),
          });
        } else {
          setEvent(null);
        }
      } catch (err) {
        console.error("Failed to load registration detail:", err);
      } finally {
        setLoading(false);
      }
    }
    loadRegistration();
  }, [id, isReady, isAuthenticated]);

  const handleCopy = () => {
    if (event) {
      navigator.clipboard.writeText(event.ticket_no);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  if (!isReady || !isAuthenticated || loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background font-outfit">
        <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
      </div>
    );
  }

  if (!event) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center bg-background gap-4 px-6 text-center">
        <div className="text-6xl">🎟️</div>
        <h2 className="text-xl font-bold text-foreground">{t("bookingDetail.not_found_title")}</h2>
        <p className="text-sm text-muted">
          {t("bookingDetail.not_found_desc")}
        </p>
        <Link
          href="/profile?tab=events"
          className="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white mt-2"
        >
          <ArrowLeft size={16} /> {t("bookingDetail.back_my_events")}
        </Link>
      </div>
    );
  }

  const status = statusConfig[event.status] ?? statusConfig.pending;
  const StatusIcon = status.icon;
  const statusLabel = t(status.tKey);

  return (
    <div className="min-h-screen bg-background font-outfit pb-20">
      {/* Top bar */}
      <div className="sticky top-0 z-30 border-b border-border bg-white/90 backdrop-blur-md">
        <div className="mx-auto max-w-5xl px-6 py-4 flex items-center gap-3">
          <Link
            href="/profile?tab=events"
            className="inline-flex items-center gap-1.5 text-sm font-semibold text-muted hover:text-primary transition-colors"
          >
            <ArrowLeft size={16} />
            {t("bookingDetail.my_events")}
          </Link>
          <ChevronRight size={14} className="text-muted-foreground" />
          <span className="text-sm font-semibold text-foreground line-clamp-1">
            {event.title}
          </span>
        </div>
      </div>

      <div className="mx-auto max-w-5xl px-6 py-10 space-y-8">
        {/* Hero Image */}
        <motion.div
          initial={{ opacity: 0, y: 16 }}
          animate={{ opacity: 1, y: 0 }}
          className="relative h-56 sm:h-72 rounded-2xl overflow-hidden border border-border shadow-lg"
        >
          <img
            src={event.image}
            alt={event.title}
            className="h-full w-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent" />
          <div className="absolute bottom-5 left-5 right-5 flex items-end justify-between gap-4">
            <div>
              <span className="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full mb-2">
                {event.type} · {event.category}
              </span>
              <h1 className="text-xl sm:text-2xl font-extrabold text-white leading-tight">
                {event.title}
              </h1>
            </div>
            {/* Status badge */}
            <div
              className={`shrink-0 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold border ${status.bg} ${status.color} ${status.border}`}
            >
              <StatusIcon size={13} />
              {statusLabel}
            </div>
          </div>
        </motion.div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* ── Left: Main Details ── */}
          <div className="lg:col-span-2 space-y-6">

            {/* Status Banner */}
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.05 }}
              className={`flex items-start gap-3 rounded-xl border p-4 ${status.bg} ${status.border}`}
            >
              <StatusIcon className={`h-5 w-5 mt-0.5 shrink-0 ${status.color}`} />
              <div>
                <p className={`text-sm font-bold ${status.color}`}>
                  {t("bookingDetail.booking_status")}: {statusLabel}
                </p>
                <p className={`text-xs font-medium mt-0.5 ${status.color} opacity-80`}>
                  {event.status === "confirmed" && t("bookingDetail.confirmed_msg")}
                  {event.status === "pending" && t("bookingDetail.pending_msg")}
                  {event.status === "rejected" && t("bookingDetail.rejected_msg")}
                </p>
              </div>
            </motion.div>

            {/* Event Info */}
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
              className="glass-card p-6 space-y-4"
            >
              <h2 className="text-base font-bold text-foreground border-b border-border pb-2">
                {t("bookingDetail.event_details")}
              </h2>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="flex items-start gap-3">
                  <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <Calendar size={16} />
                  </div>
                  <div>
                    <p className="text-[10px] font-bold text-muted uppercase tracking-wider">
                      {t("bookingDetail.date")}
                    </p>
                    <p className="text-sm font-semibold text-foreground mt-0.5">
                      {event.date}
                    </p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <Clock size={16} />
                  </div>
                  <div>
                    <p className="text-[10px] font-bold text-muted uppercase tracking-wider">
                      {t("bookingDetail.time")}
                    </p>
                    <p className="text-sm font-semibold text-foreground mt-0.5">
                      {event.time}
                    </p>
                  </div>
                </div>
                <div className="flex items-start gap-3 sm:col-span-2">
                  <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <MapPin size={16} />
                  </div>
                  <div>
                    <p className="text-[10px] font-bold text-muted uppercase tracking-wider">
                      {t("bookingDetail.venue")}
                    </p>
                    <p className="text-sm font-semibold text-foreground mt-0.5">
                      {event.location}
                    </p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <Users size={16} />
                  </div>
                  <div>
                    <p className="text-[10px] font-bold text-muted uppercase tracking-wider">
                      {t("bookingDetail.attendees")}
                    </p>
                    <p className="text-sm font-semibold text-foreground mt-0.5">
                      {event.attendees} {t("bookingDetail.registered")}
                    </p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <CreditCard size={16} />
                  </div>
                  <div>
                    <p className="text-[10px] font-bold text-muted uppercase tracking-wider">
                      {t("bookingDetail.amount_paid")}
                    </p>
                    <p className="text-sm font-semibold text-foreground mt-0.5">
                      {event.price}{" "}
                      <span className="text-muted font-medium">
                        {t("bookingDetail.on")} {event.paid_on}
                      </span>
                    </p>
                  </div>
                </div>
              </div>

              {event.description && (
                <div className="pt-3 border-t border-border">
                  <p className="text-xs text-muted leading-relaxed font-medium">
                    {event.description}
                  </p>
                </div>
              )}
            </motion.div>

            {/* Agenda */}
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.15 }}
              className="glass-card p-6 space-y-4"
            >
              <h2 className="text-base font-bold text-foreground border-b border-border pb-2">
                {t("bookingDetail.agenda")}
              </h2>
              <ol className="space-y-3">
                {event.agenda.map((item: any, i: number) => (
                  <li key={i} className="flex items-start gap-3">
                    <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-white text-[10px] font-bold mt-0.5">
                      {i + 1}
                    </span>
                    <span className="text-sm font-medium text-foreground">
                      {item}
                    </span>
                  </li>
                ))}
              </ol>
            </motion.div>

            {/* Notes */}
            {event.notes && (
              <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.2 }}
                className="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4"
              >
                <Info className="h-5 w-5 text-blue-500 shrink-0 mt-0.5" />
                <p className="text-xs font-semibold text-blue-800">
                  {event.notes}
                </p>
              </motion.div>
            )}
          </div>

          {/* ── Right: Ticket Card ── */}
          <div className="space-y-5">
            <motion.div
              initial={{ opacity: 0, scale: 0.97 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 0.1 }}
              className="glass-card overflow-hidden border border-border"
            >
              {/* Ticket Header */}
              <div className="bg-primary px-5 py-4">
                <div className="flex items-center gap-2 text-white">
                  <Ticket size={16} />
                  <span className="text-sm font-bold">{t("bookingDetail.your_ticket")}</span>
                </div>
              </div>

              {/* Dashed divider */}
              <div className="flex items-center justify-between px-5 py-3 border-y border-dashed border-border">
                <div
                  className={`flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold border ${status.bg} ${status.color} ${status.border}`}
                >
                  <StatusIcon size={12} />
                  {statusLabel}
                </div>
                {event.is_attended && (
                  <div className="flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold border border-emerald-100 bg-emerald-50 text-emerald-700 animate-pulse">
                    <CheckCircle2 size={12} />
                    Attended
                  </div>
                )}
              </div>

              <div className="px-5 py-5 space-y-4">
                <div>
                  <p className="text-[10px] font-bold text-muted uppercase tracking-wider">
                    {t("bookingDetail.ticket_number")}
                  </p>
                  <div className="flex items-center gap-2 mt-1">
                    <p className="text-sm font-extrabold text-foreground font-mono tracking-wider">
                      {event.ticket_no || "Pending Approval"}
                    </p>
                    {event.ticket_no && (
                      <button
                        onClick={handleCopy}
                        className="text-primary hover:opacity-70 transition-opacity"
                        title="Copy ticket number"
                      >
                        <AnimatePresence mode="wait">
                          {copied ? (
                            <motion.span
                              key="check"
                              initial={{ scale: 0 }}
                              animate={{ scale: 1 }}
                              exit={{ scale: 0 }}
                              className="inline-block"
                            >
                              <CheckCircle2 size={15} className="text-emerald-500" />
                            </motion.span>
                          ) : (
                            <motion.span
                              key="copy"
                              initial={{ scale: 0 }}
                              animate={{ scale: 1 }}
                              exit={{ scale: 0 }}
                              className="inline-block"
                            >
                              <Share2 size={15} />
                            </motion.span>
                          )}
                        </AnimatePresence>
                      </button>
                    )}
                  </div>
                </div>

                <div>
                  <p className="text-[10px] font-bold text-muted uppercase tracking-wider">
                    {t("bookingDetail.registered_for")}
                  </p>
                  <p className="text-sm font-semibold text-foreground mt-1 leading-snug">
                    {event.title}
                  </p>
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <p className="text-[10px] font-bold text-muted uppercase tracking-wider">
                      {t("bookingDetail.date")}
                    </p>
                    <p className="text-xs font-semibold text-foreground mt-0.5">
                      {event.date}
                    </p>
                  </div>
                  <div>
                    <p className="text-[10px] font-bold text-muted uppercase tracking-wider">
                      {t("bookingDetail.paid")}
                    </p>
                    <p className="text-xs font-semibold text-foreground mt-0.5">
                      {event.price}
                    </p>
                  </div>
                </div>

                {/* QR code / Barcode visual */}
                {event.ticket_no && (event.status === "confirmed" || event.status === "approved") ? (
                  <div className="mt-2 flex flex-col items-center justify-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <img 
                      src={`https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(event.ticket_no)}`} 
                      alt="Ticket QR Code" 
                      className="w-32 h-32 border border-slate-200 rounded-lg p-1 bg-white"
                    />
                    <span className="text-[9px] font-bold text-muted-foreground uppercase tracking-widest">Scan QR at Entrance</span>
                  </div>
                ) : (
                  <div className="mt-2 flex justify-center">
                    <div className="h-14 w-full rounded-lg bg-gradient-to-r from-slate-900 via-slate-700 to-slate-900 flex items-center justify-center gap-px px-4 overflow-hidden">
                      {Array.from({ length: 38 }).map((_, i) => (
                        <div
                          key={i}
                          className="bg-white rounded-sm"
                          style={{
                            height: `${Math.random() * 60 + 30}%`,
                            width: i % 3 === 0 ? "3px" : "2px",
                            opacity: Math.random() * 0.5 + 0.5,
                          }}
                        />
                      ))}
                    </div>
                  </div>
                )}
              </div>

              {/* Actions */}
              {event.status === "confirmed" && (
                <div className="border-t border-border px-5 py-4 space-y-2">
                  <button
                    onClick={() => window.print()}
                    className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90"
                  >
                    <Download size={14} />
                    {t("bookingDetail.download_ticket")}
                  </button>
                </div>
              )}
            </motion.div>

            {/* View Event CTA */}
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
            >
              <Link
                href={`/events/${event.id}`}
                className="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-white px-4 py-2.5 text-xs font-bold text-foreground transition-all hover:bg-surface"
              >
                {t("bookingDetail.view_event_page")} <ChevronRight size={14} />
              </Link>
            </motion.div>

            {/* Trust note */}
            <div className="flex items-center gap-2 rounded-xl border border-border bg-surface p-3">
              <ShieldCheck size={15} className="text-primary shrink-0" />
              <p className="text-[10px] font-semibold text-muted">
                {t("bookingDetail.trust_note")}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

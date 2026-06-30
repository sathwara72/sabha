"use client";

import { useParams, useRouter } from "next/navigation";
import { useEffect, useState, useCallback } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Link from "next/link";
import {
  ArrowLeft, Calendar, MapPin, ChevronLeft, ChevronRight,
  X, Download, ZoomIn, Image as ImageIcon, Search, Grid3X3
} from "lucide-react";
import { fetchEvents, fetchGallery } from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";
import { useLanguage } from "@/lib/language";

interface GalleryItem {
  id: number;
  event_id: number | null;
  image_path: string;
  caption: string | null;
  created_at: string;
}

interface EventItem {
  id: number;
  title: string;
  date: string;
  location: string;
  image?: string;
  [key: string]: any;
}

export default function EventGalleryDetailPage() {
  const { id } = useParams();
  const router = useRouter();
  const { t } = useLanguage();

  const [event, setEvent] = useState<EventItem | null>(null);
  const [photos, setPhotos] = useState<GalleryItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [lightboxIndex, setLightboxIndex] = useState<number | null>(null);
  const [search, setSearch] = useState("");

  const getMediaUrl = (path: string) => {
    if (!path) return "";
    if (path.startsWith("http://") || path.startsWith("https://")) return path;
    return `${API_ORIGIN}${path}`;
  };

  useEffect(() => {
    async function load() {
      try {
        setLoading(true);
        const [evtData, galData] = await Promise.all([fetchEvents(), fetchGallery()]);
        const foundEvt = evtData.find((e: EventItem) => e.id === Number(id));
        const eventPhotos = galData.filter((g: GalleryItem) => g.event_id === Number(id));
        setEvent(foundEvt || null);
        setPhotos(eventPhotos);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [id]);

  // Keyboard navigation for lightbox
  const handleKeyDown = useCallback((e: KeyboardEvent) => {
    if (lightboxIndex === null) return;
    if (e.key === "ArrowRight") setLightboxIndex(i => i !== null ? Math.min(i + 1, filtered.length - 1) : 0);
    if (e.key === "ArrowLeft") setLightboxIndex(i => i !== null ? Math.max(i - 1, 0) : 0);
    if (e.key === "Escape") setLightboxIndex(null);
  }, [lightboxIndex]);

  useEffect(() => {
    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, [handleKeyDown]);

  const handleDownload = async (url: string, caption: string | null, index: number) => {
    try {
      const response = await fetch(url);
      const blob = await response.blob();
      const blobUrl = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = blobUrl;
      a.download = `sabha-event-photo-${index + 1}.jpg`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(blobUrl);
    } catch {
      // Fallback: open in new tab
      window.open(url, "_blank");
    }
  };

  const filtered = photos.filter(p =>
    !search || (p.caption && p.caption.toLowerCase().includes(search.toLowerCase()))
  );

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background">
        <div className="text-center space-y-3">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="text-sm text-muted font-medium">{t("gallery.loading_gallery")}</p>
        </div>
      </div>
    );
  }

  if (!event) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center bg-background gap-4 text-center px-6">
        <div className="text-6xl">📁</div>
        <h2 className="text-xl font-bold text-foreground">{t("eventDetail.not_found_title")}</h2>
        <p className="text-sm text-muted">{t("gallery.folder_not_exist")}</p>
        <Link href="/gallery" className="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white">
          <ArrowLeft size={16} /> {t("gallery.back_to_gallery")}
        </Link>
      </div>
    );
  }

  const currentPhoto = lightboxIndex !== null ? filtered[lightboxIndex] : null;

  return (
    <div className="min-h-screen bg-background font-outfit">

      {/* Sticky Top Bar */}
      <div className="sticky top-0 z-30 border-b border-border bg-white/90 backdrop-blur-md">
        <div className="mx-auto max-w-7xl px-6 py-3.5 flex items-center justify-between gap-4">
          <div className="flex items-center gap-3 min-w-0">
            <Link href="/gallery" className="inline-flex items-center gap-1.5 text-sm font-semibold text-muted hover:text-primary transition-colors shrink-0">
              <ArrowLeft size={16} /> {t("gallery.label")}
            </Link>
            <span className="text-muted-foreground">/</span>
            <span className="text-sm font-bold text-foreground line-clamp-1">{event.title}</span>
          </div>
          <span className="shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold text-primary bg-primary-soft px-3 py-1 rounded-full border border-primary/10">
            <Grid3X3 size={12} /> {photos.length} {t("gallery.photos")}
          </span>
        </div>
      </div>

      {/* Hero Banner */}
      <div className="relative h-52 sm:h-64 overflow-hidden">
        <img
          src={event.image || "https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=1400&auto=format&fit=crop"}
          alt={event.title}
          className="h-full w-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent" />
        <div className="absolute bottom-0 left-0 right-0 px-6 pb-6">
          <div className="mx-auto max-w-7xl">
            <span className="inline-flex items-center gap-1.5 rounded-full bg-primary/90 px-3 py-1 text-[10px] font-bold text-white mb-2">
              📁 {t("gallery.event_folder")}
            </span>
            <h1 className="text-2xl sm:text-3xl font-extrabold text-white leading-tight">{event.title}</h1>
            <div className="flex flex-wrap items-center gap-4 mt-2 text-xs text-white/70 font-medium">
              <span className="flex items-center gap-1.5"><Calendar size={13} />{new Date(event.date).toLocaleDateString("en-US", { day: "numeric", month: "long", year: "numeric" })}</span>
              {event.location && <span className="flex items-center gap-1.5"><MapPin size={13} />{event.location}</span>}
            </div>
          </div>
        </div>
      </div>

      {/* Search + Count Bar */}
      <div className="border-b border-border bg-surface">
        <div className="mx-auto max-w-7xl px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
          <p className="text-sm font-semibold text-muted">
            {t("gallery.showing")} <span className="text-foreground font-bold">{filtered.length}</span> {t("gallery.of")} <span className="text-foreground font-bold">{photos.length}</span> {t("gallery.photos")}
          </p>
          <div className="relative w-full sm:w-64">
            <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" />
            <input
              type="text"
              placeholder={t("gallery.search_placeholder")}
              value={search}
              onChange={e => setSearch(e.target.value)}
              className="w-full rounded-xl border border-border bg-white pl-9 pr-4 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground font-medium"
            />
          </div>
        </div>
      </div>

      {/* Photo Grid */}
      <section className="mx-auto max-w-7xl px-6 py-10">
        {filtered.length === 0 ? (
          <div className="flex flex-col items-center justify-center py-24 gap-4 text-center">
            <ImageIcon size={40} className="text-muted-foreground/30" />
            <p className="text-sm text-muted font-medium">{search ? t("gallery.no_photos_match") : t("gallery.no_photos_folder")}</p>
          </div>
        ) : (
          <div className="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
            {filtered.map((photo, idx) => (
              <motion.div
                key={photo.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: (idx % 8) * 0.05 }}
                className="group relative break-inside-avoid overflow-hidden rounded-2xl border border-border bg-white shadow-sm cursor-pointer"
                onClick={() => setLightboxIndex(idx)}
              >
                <img
                  src={getMediaUrl(photo.image_path)}
                  alt={photo.caption || `Photo ${idx + 1}`}
                  className="w-full object-cover transition-transform duration-500 group-hover:scale-105"
                  loading="lazy"
                />
                {/* Hover Overlay */}
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-between p-4">
                  <div className="flex justify-end">
                    <button
                      onClick={e => { e.stopPropagation(); handleDownload(getMediaUrl(photo.image_path), photo.caption, idx); }}
                      className="h-8 w-8 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-sm flex items-center justify-center text-white transition-colors"
                      title="Download photo"
                    >
                      <Download size={14} />
                    </button>
                  </div>
                  <div>
                    <span className="inline-flex items-center gap-1 mb-1.5 rounded-full bg-white/20 backdrop-blur-sm px-2.5 py-0.5 text-[10px] font-bold text-white uppercase">
                      <ZoomIn size={10} /> {t("gallery.click_to_zoom")}
                    </span>
                    {photo.caption && (
                      <p className="text-xs font-semibold text-white line-clamp-2">{photo.caption}</p>
                    )}
                  </div>
                </div>

                {/* Photo number badge */}
                <div className="absolute top-3 left-3 h-6 w-6 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center text-[10px] font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity">
                  {idx + 1}
                </div>
              </motion.div>
            ))}
          </div>
        )}
      </section>

      {/* ──── LIGHTBOX ──── */}
      <AnimatePresence>
        {lightboxIndex !== null && currentPhoto && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 bg-black/97 backdrop-blur-md flex flex-col"
            onClick={() => setLightboxIndex(null)}
          >
            {/* Lightbox Header */}
            <div
              className="flex items-center justify-between px-6 py-4 border-b border-white/10 shrink-0"
              onClick={e => e.stopPropagation()}
            >
              <div>
                <h4 className="text-base font-bold text-white">{event.title}</h4>
                <p className="text-xs text-white/50 mt-0.5">
                  {lightboxIndex + 1} {t("gallery.of")} {filtered.length}
                </p>
              </div>
              <div className="flex items-center gap-2">
                <button
                  onClick={() => handleDownload(getMediaUrl(currentPhoto.image_path), currentPhoto.caption, lightboxIndex)}
                  className="inline-flex items-center gap-1.5 rounded-xl bg-white/10 hover:bg-white/20 px-3 py-2 text-xs font-semibold text-white transition-colors"
                >
                  <Download size={14} /> {t("gallery.download")}
                </button>
                <button
                  onClick={() => setLightboxIndex(null)}
                  className="h-9 w-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors"
                >
                  <X size={18} />
                </button>
              </div>
            </div>

            {/* Lightbox Main Image */}
            <div className="flex-1 flex items-center justify-between gap-4 px-4 py-4 min-h-0" onClick={e => e.stopPropagation()}>
              {/* Prev */}
              <button
                onClick={() => setLightboxIndex(i => Math.max((i ?? 0) - 1, 0))}
                disabled={lightboxIndex === 0}
                className="shrink-0 h-12 w-12 rounded-full bg-white/10 hover:bg-white/20 disabled:opacity-20 flex items-center justify-center text-white transition-colors"
              >
                <ChevronLeft size={24} />
              </button>

              {/* Image */}
              <div className="flex-1 flex items-center justify-center min-h-0 max-h-[72vh]">
                <AnimatePresence mode="wait">
                  <motion.img
                    key={lightboxIndex}
                    src={getMediaUrl(currentPhoto.image_path)}
                    alt={currentPhoto.caption || "Photo"}
                    initial={{ opacity: 0, scale: 0.96 }}
                    animate={{ opacity: 1, scale: 1 }}
                    exit={{ opacity: 0, scale: 0.96 }}
                    transition={{ duration: 0.2 }}
                    className="max-w-full max-h-[72vh] object-contain rounded-xl shadow-2xl"
                    onClick={e => e.stopPropagation()}
                  />
                </AnimatePresence>
              </div>

              {/* Next */}
              <button
                onClick={() => setLightboxIndex(i => Math.min((i ?? 0) + 1, filtered.length - 1))}
                disabled={lightboxIndex === filtered.length - 1}
                className="shrink-0 h-12 w-12 rounded-full bg-white/10 hover:bg-white/20 disabled:opacity-20 flex items-center justify-center text-white transition-colors"
              >
                <ChevronRight size={24} />
              </button>
            </div>

            {/* Caption + Thumbnail Strip */}
            <div className="shrink-0 border-t border-white/10 px-6 py-4" onClick={e => e.stopPropagation()}>
              {currentPhoto.caption && (
                <p className="text-center text-sm font-medium text-white/80 mb-3">{currentPhoto.caption}</p>
              )}
              {/* Thumbnail strip */}
              <div className="flex items-center justify-center gap-2 overflow-x-auto pb-1 max-w-3xl mx-auto">
                {filtered.map((p, i) => (
                  <button
                    key={p.id}
                    onClick={() => setLightboxIndex(i)}
                    className={`shrink-0 h-12 w-16 rounded-lg overflow-hidden border-2 transition-all ${
                      i === lightboxIndex
                        ? "border-primary scale-110 shadow-lg shadow-primary/30"
                        : "border-white/20 opacity-50 hover:opacity-80"
                    }`}
                  >
                    <img src={getMediaUrl(p.image_path)} alt="" className="h-full w-full object-cover" />
                  </button>
                ))}
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

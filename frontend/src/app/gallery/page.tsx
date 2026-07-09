"use client";

import { useEffect, useState, useMemo } from "react";
import { motion, AnimatePresence } from "framer-motion";
import {
  Camera, Users, Target, Folder, Image, Film,
  Play, X, ChevronLeft, ChevronRight, Maximize2,
  Calendar, Eye
} from "lucide-react";
import Link from "next/link";
import PageHeader from "@/components/shared/PageHeader";
import { fetchEvents, fetchGallery, fetchStatistics } from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";
import { useLanguage } from "@/lib/language";

interface GalleryItem {
  id: number;
  event_id: number | null;
  image_path: string;
  caption: string | null;
  created_at: string;
}

export default function GalleryPage() {
  const { t } = useLanguage();
  const [events, setEvents] = useState<any[]>([]);
  const [gallery, setGallery] = useState<GalleryItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState({
    members: "500+",
    cities: "12+"
  });

  // Lightbox / Slider popup states
  const [activeSliderMedia, setActiveSliderMedia] = useState<GalleryItem[] | null>(null);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [singleActiveMedia, setSingleActiveMedia] = useState<GalleryItem | null>(null);

  const getMediaUrl = (path: string) => {
    if (!path) return "";
    if (path.startsWith("http://") || path.startsWith("https://")) {
      return path;
    }
    return `${API_ORIGIN}${path}`;
  };

  useEffect(() => {
    async function loadData() {
      try {
        setLoading(true);
        const [evtData, galData, statData] = await Promise.all([
          fetchEvents(),
          fetchGallery(),
          fetchStatistics().catch(() => [])
        ]);
        setEvents(evtData || []);
        setGallery(galData || []);

        const foundMembers = statData.find((s: any) => s.label.toLowerCase().includes("member") || s.label.toLowerCase().includes("professional"));
        const foundCities = statData.find((s: any) => s.label.toLowerCase().includes("city") || s.label.toLowerCase().includes("cities"));

        setStats({
          members: foundMembers ? foundMembers.value : "500+",
          cities: foundCities ? foundCities.value : "12+"
        });
      } catch (err) {
        console.error("Failed to load gallery:", err);
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, []);

  const isVideoFile = (path?: string) => {
    if (!path) return false;
    const ext = path.split(".").pop()?.toLowerCase();
    return ["mp4", "mov", "avi", "webm", "mkv"].includes(ext || "");
  };

  // Group gallery items by event
  const groupedGallery = useMemo(() => {
    const folders: Record<number, GalleryItem[]> = {};
    const common: GalleryItem[] = [];

    gallery.forEach((item) => {
      if (item.event_id) {
        if (!folders[item.event_id]) {
          folders[item.event_id] = [];
        }
        folders[item.event_id].push(item);
      } else {
        common.push(item);
      }
    });

    return { folders, common };
  }, [gallery]);

  // Merge events with their grouped photos/videos
  const eventFolders = useMemo(() => {
    return events
      .map((evt) => {
        const items = groupedGallery.folders[evt.id] || [];
        return {
          ...evt,
          media: items,
        };
      })
      .filter((folder) => folder.media.length > 0);
  }, [events, groupedGallery.folders]);

  const handleOpenFolder = (media: GalleryItem[]) => {
    setActiveSliderMedia(media);
    setCurrentIndex(0);
  };

  const handleNext = () => {
    if (activeSliderMedia) {
      setCurrentIndex((prev) => (prev + 1) % activeSliderMedia.length);
    }
  };

  const handlePrev = () => {
    if (activeSliderMedia) {
      setCurrentIndex((prev) => (prev - 1 + activeSliderMedia.length) % activeSliderMedia.length);
    }
  };

  return (
    <div className="bg-background font-outfit min-h-screen">
      {/* Header */}
      <PageHeader
        kicker={t("gallery.label")}
        title={t("gallery.title")}
        subtitle={t("gallery.subtitle")}
      />

      {/* Stats Section */}
      <section className="border-b border-border bg-surface">
        <div className="mx-auto max-w-7xl px-6 py-4">
          <div className="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div className="glass-card flex items-start gap-4 p-5 md:col-span-2">
              <div className="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                <Camera size={20} />
              </div>
              <div className="space-y-1">
                <h2 className="text-base font-semibold text-foreground">{t("gallery.visual_legacy_title")}</h2>
                <p className="text-xs leading-relaxed text-muted">
                  {t("gallery.visual_legacy_desc")}
                </p>
              </div>
            </div>

            <div className="glass-card flex items-center gap-4 p-5">
              <div className="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                <Users size={20} />
              </div>
              <div>
                <p className="text-3xl font-bold text-foreground leading-none">{stats.members}</p>
                <p className="mt-1.5 text-xs font-medium text-muted">{t("gallery.members")}</p>
              </div>
            </div>

            <div className="glass-card flex items-center gap-4 p-5">
              <div className="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                <Target size={20} />
              </div>
              <div>
                <p className="text-3xl font-bold text-foreground leading-none">{stats.cities}</p>
                <p className="mt-1.5 text-xs font-medium text-muted">{t("gallery.cities")}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Main Gallery Section */}
      <section className="mx-auto max-w-7xl px-6 py-16 lg:py-10 space-y-10">
        {loading ? (
          <div className="py-20 text-center">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
            <p className="mt-3 text-sm text-muted">{t("gallery.loading")}</p>
          </div>
        ) : (
          <>
            {/* 1. Event Folders Section */}
            <div className="space-y-3">
              <div className="flex items-center gap-3">
                <div className="h-8 w-1 rounded-full bg-primary" />
                <h2 className="text-2xl font-bold text-foreground">{t("gallery.event_folders")}</h2>
              </div>
              <p className="text-sm text-muted">{t("gallery.event_folders_desc")}</p>

              {eventFolders.length === 0 ? (
                <p className="text-sm text-muted italic">{t("gallery.no_folders")}</p>
              ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                  {eventFolders.map((folder) => {
                    const firstItem = folder.media[0];
                    const isVideo = firstItem ? isVideoFile(firstItem.image_path) : false;

                    return (
                      <Link
                        key={folder.id}
                        href={`/gallery/event/${folder.id}`}
                        className="group relative cursor-pointer block"
                      >
                      <motion.div
                        whileHover={{ y: -5 }}
                        className="group relative cursor-pointer"
                      >
                        {/* Folder Backing Effect */}
                        <div className="absolute inset-0 bg-primary/10 rounded-2xl translate-x-2 translate-y-2 group-hover:translate-x-3 group-hover:translate-y-3 transition-transform" />
                        <div className="absolute inset-0 bg-primary/5 rounded-2xl translate-x-1 translate-y-1" />

                        {/* Main Folder Card */}
                        <div className="glass-card p-0 overflow-hidden relative border border-border bg-white rounded-2xl shadow-sm z-10 flex flex-col h-80">
                          {/* Event Cover Image / Video Preview */}
                          <div className="relative h-48 w-full bg-slate-900 overflow-hidden">
                            {firstItem ? (
                              isVideo ? (
                                <video
                                  src={getMediaUrl(firstItem.image_path)}
                                  className="h-full w-full object-cover opacity-80"
                                  muted
                                  preload="metadata"
                                />
                              ) : (
                                <img
                                  src={getMediaUrl(firstItem.image_path)}
                                  alt={folder.title}
                                  className="h-full w-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-500"
                                />
                              )
                            ) : (
                              <div className="h-full w-full bg-slate-200 flex items-center justify-center text-muted">
                                {t("gallery.no_cover")}
                              </div>
                            )}
                            <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
                            <div className="absolute top-4 left-4">
                              <span className="flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1 text-xs font-semibold text-foreground shadow-sm">
                                <Folder size={13} className="text-primary" /> {t("gallery.event_folder")}
                              </span>
                            </div>
                            <div className="absolute bottom-4 right-4">
                              <span className="text-xs font-semibold text-white bg-primary px-2.5 py-1 rounded-md shadow-sm">
                                {folder.media.length} {t("gallery.items")}
                              </span>
                            </div>
                          </div>

                          {/* Folder Info */}
                          <div className="p-5 flex-1 flex flex-col justify-between">
                            <div>
                              <h3 className="text-lg font-bold text-foreground group-hover:text-primary transition-colors line-clamp-1">
                                {folder.title}
                              </h3>
                              <p className="text-xs text-muted flex items-center gap-1.5 mt-1.5 font-medium">
                                <Calendar size={12} className="text-primary" /> 
                                {new Date(folder.date).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}
                              </p>
                            </div>
                            <span className="text-xs font-bold text-primary group-hover:underline inline-flex items-center gap-1 mt-3">
                              {t("gallery.view_all_photos")} <Eye size={12} />
                            </span>
                          </div>
                        </div>
                      </motion.div>
                      </Link>
                    );
                  })}
                </div>
              )}
            </div>

            {/* 2. Common Gallery Section */}
            <div className="space-y-3 pt-4 border-t border-border">
              <div className="flex items-center gap-3">
                <div className="h-8 w-1 rounded-full bg-accent" />
                <h2 className="text-2xl font-bold text-foreground">{t("gallery.common_gallery")}</h2>
              </div>
              <p className="text-sm text-muted">{t("gallery.common_gallery_desc")}</p>

              {groupedGallery.common.length === 0 ? (
                <p className="text-sm text-muted italic">{t("gallery.no_common")}</p>
              ) : (
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  {groupedGallery.common.map((item) => {
                    const isVideo = isVideoFile(item.image_path);

                    return (
                      <motion.div
                        key={item.id}
                        initial={{ opacity: 0, y: 15 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        onClick={() => setSingleActiveMedia(item)}
                        className={`group relative aspect-square overflow-hidden rounded-2xl border cursor-pointer ${
                          isVideo 
                            ? "border-accent/40 shadow-[0_0_15px_rgba(244,63,94,0.08)] bg-slate-950" 
                            : "border-border bg-white shadow-sm"
                        }`}
                      >
                        {isVideo ? (
                          <div className="relative w-full h-full flex items-center justify-center bg-black">
                            <video
                              src={getMediaUrl(item.image_path)}
                              className="w-full h-full object-cover opacity-80"
                              muted
                              preload="metadata"
                            />
                            <div className="absolute inset-0 bg-black/30 group-hover:bg-black/50 transition-colors" />
                            {/* Play button overlay for video */}
                            <div className="absolute h-10 w-10 rounded-full bg-accent text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                              <Play size={18} className="ml-0.5" />
                            </div>
                            <span className="absolute bottom-2.5 left-2.5 flex items-center gap-1 text-[9px] font-bold text-white bg-accent px-2 py-0.5 rounded-full z-10 uppercase tracking-wider">
                              <Film size={9} /> {t("gallery.video")}
                            </span>
                            {item.caption && (
                              <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/35 to-transparent p-3 opacity-0 transition-opacity duration-300 group-hover:opacity-100 flex flex-col justify-end pt-8">
                                <h3 className="text-[11px] font-semibold text-white line-clamp-2">{item.caption}</h3>
                              </div>
                            )}
                          </div>
                        ) : (
                          <div className="relative w-full h-full">
                            <img
                              src={getMediaUrl(item.image_path)}
                              className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                              alt={item.caption || "Gallery image"}
                            />
                            <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent p-3.5 opacity-0 transition-opacity duration-300 group-hover:opacity-100 flex flex-col justify-end">
                              <span className="mb-1.5 w-fit rounded-full bg-white/15 px-2 py-0.5 text-[9px] font-bold text-white backdrop-blur-sm uppercase">
                                {t("gallery.image")}
                              </span>
                              {item.caption && <h3 className="text-xs font-semibold text-white line-clamp-2">{item.caption}</h3>}
                            </div>
                          </div>
                        )}
                      </motion.div>
                    );
                  })}
                </div>
              )}
            </div>
          </>
        )}
      </section>

      {/* Lightbox / Slider Popup Modal */}
      <AnimatePresence>
        {activeSliderMedia && activeSliderMedia.length > 0 && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 bg-black/95 backdrop-blur-md flex flex-col justify-between p-6"
          >
            {/* Header / Controls */}
            <div className="flex items-center justify-between text-white border-b border-white/10 pb-4">
              <div>
                <h4 className="text-lg font-bold">{t("gallery.event_photos_videos")}</h4>
                <p className="text-xs text-white/60">
                  {t("gallery.item")} {currentIndex + 1} {t("gallery.of")} {activeSliderMedia.length}
                </p>
              </div>
              <button
                onClick={() => setActiveSliderMedia(null)}
                className="h-10 w-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white cursor-pointer transition-colors"
              >
                <X size={20} />
              </button>
            </div>

            {/* Main Content (Slider Item) */}
            <div className="flex-1 flex items-center justify-between gap-6 py-6 relative">
              {/* Previous Button */}
              {activeSliderMedia.length > 1 && (
                <button
                  onClick={handlePrev}
                  className="hidden sm:flex h-14 w-14 rounded-full bg-white/10 hover:bg-white/20 items-center justify-center text-white cursor-pointer transition-colors z-10 shrink-0"
                >
                  <ChevronLeft size={28} />
                </button>
              )}

              {/* Slider Asset Container */}
              <div className="flex-1 h-full flex flex-col items-center justify-center max-w-4xl mx-auto">
                <AnimatePresence mode="wait">
                  <motion.div
                    key={currentIndex}
                    initial={{ opacity: 0, scale: 0.95 }}
                    animate={{ opacity: 1, scale: 1 }}
                    exit={{ opacity: 0, scale: 0.95 }}
                    className="relative w-full h-full max-h-[70vh] rounded-2xl overflow-hidden shadow-2xl flex items-center justify-center"
                  >
                    {isVideoFile(activeSliderMedia[currentIndex].image_path) ? (
                      <video
                        src={getMediaUrl(activeSliderMedia[currentIndex].image_path)}
                        className="w-full h-full max-h-[70vh] object-contain rounded-xl"
                        controls
                        autoPlay
                      />
                    ) : (
                      <img
                        src={getMediaUrl(activeSliderMedia[currentIndex].image_path)}
                        alt={activeSliderMedia[currentIndex].caption || "Slideshow"}
                        className="w-full h-full object-contain rounded-xl"
                      />
                    )}
                  </motion.div>
                </AnimatePresence>
              </div>

              {/* Next Button */}
              {activeSliderMedia.length > 1 && (
                <button
                  onClick={handleNext}
                  className="hidden sm:flex h-14 w-14 rounded-full bg-white/10 hover:bg-white/20 items-center justify-center text-white cursor-pointer transition-colors z-10 shrink-0"
                >
                  <ChevronRight size={28} />
                </button>
              )}
            </div>

            {/* Bottom Section (Caption) */}
            <div className="text-center text-white space-y-4 py-4 max-w-3xl mx-auto border-t border-white/10 w-full">
              <p className="text-sm font-medium">
                {activeSliderMedia[currentIndex].caption || t("gallery.no_description")}
              </p>
              
              {/* Pagination indicators */}
              <div className="flex justify-center gap-1.5 overflow-x-auto py-1.5 max-w-full">
                {activeSliderMedia.map((_, i) => (
                  <button
                    key={i}
                    onClick={() => setCurrentIndex(i)}
                    className={`h-2 rounded-full transition-all shrink-0 cursor-pointer ${
                      i === currentIndex ? "w-6 bg-primary" : "w-2 bg-white/30"
                    }`}
                  />
                ))}
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Single Media Lightbox Modal */}
      <AnimatePresence>
        {singleActiveMedia && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 bg-black/95 backdrop-blur-md flex flex-col justify-between p-6"
          >
            {/* Header / Controls */}
            <div className="flex items-center justify-between text-white border-b border-white/10 pb-4">
              <div>
                <h4 className="text-lg font-bold">{t("gallery.community_gallery")}</h4>
                <p className="text-xs text-white/60">{t("gallery.common_upload")}</p>
              </div>
              <button
                onClick={() => setSingleActiveMedia(null)}
                className="h-10 w-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white cursor-pointer transition-colors"
              >
                <X size={20} />
              </button>
            </div>

            {/* Content Container */}
            <div className="flex-1 flex items-center justify-center py-6">
              <div className="max-w-4xl w-full h-full max-h-[70vh] rounded-2xl overflow-hidden shadow-2xl flex items-center justify-center">
                {isVideoFile(singleActiveMedia.image_path) ? (
                  <video
                    src={getMediaUrl(singleActiveMedia.image_path)}
                    className="w-full h-full max-h-[70vh] object-contain rounded-xl"
                    controls
                    autoPlay
                  />
                ) : (
                  <img
                    src={getMediaUrl(singleActiveMedia.image_path)}
                    alt={singleActiveMedia.caption || "Gallery item"}
                    className="w-full h-full object-contain rounded-xl"
                  />
                )}
              </div>
            </div>

            {/* Bottom Caption */}
            <div className="text-center text-white py-4 max-w-3xl mx-auto border-t border-white/10 w-full">
              <p className="text-sm font-medium">
                {singleActiveMedia.caption || t("gallery.no_description")}
              </p>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

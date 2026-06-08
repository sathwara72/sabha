"use client";

import { useEffect, useState, useMemo } from "react";
import { motion, AnimatePresence } from "framer-motion";
import {
  Camera, Users, Target, Folder, Image, Film,
  Play, X, ChevronLeft, ChevronRight, Maximize2,
  Calendar, Eye
} from "lucide-react";
import PageHeader from "@/components/shared/PageHeader";
import { fetchEvents, fetchGallery, fetchStatistics } from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";

interface GalleryItem {
  id: number;
  event_id: number | null;
  image_path: string;
  caption: string | null;
  created_at: string;
}

export default function GalleryPage() {
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

  const isVideoFile = (path: string) => {
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
        kicker="Gallery"
        title="Moments from our community"
        subtitle="Explore the mixers, masterminds, and milestones that bring our members together."
      />

      {/* Stats Section */}
      <section className="border-b border-border bg-surface">
        <div className="mx-auto max-w-7xl px-6 py-12">
          <div className="grid grid-cols-1 gap-6 md:grid-cols-4">
            <div className="glass-card flex flex-col justify-between p-7 md:col-span-2">
              <div>
                <div className="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft text-primary">
                  <Camera size={22} />
                </div>
                <h2 className="text-xl font-semibold text-foreground">A visual legacy</h2>
                <p className="mt-3 text-sm leading-relaxed text-muted">
                  Every folder captures a unique Sabha event. Look inside to see the connections, keynote speaker panels, and workshops that inspire our network.
                </p>
              </div>
              <div className="mt-6 flex flex-wrap items-center gap-3">
                <span className="rounded-full bg-primary-soft px-4 py-1.5 text-sm font-medium text-primary">
                  {gallery.length} uploads
                </span>
                <span className="rounded-full bg-primary-soft px-4 py-1.5 text-sm font-medium text-primary">
                  {eventFolders.length} event folders
                </span>
              </div>
            </div>

            <div className="glass-card flex flex-col items-center justify-center p-7 text-center">
              <div className="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft text-primary">
                <Users size={22} />
              </div>
              <p className="text-4xl font-bold text-foreground sm:text-5xl">{stats.members}</p>
              <p className="mt-2 text-sm font-medium text-muted">Members</p>
            </div>

            <div className="glass-card flex flex-col items-center justify-center p-7 text-center">
              <div className="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft text-primary">
                <Target size={22} />
              </div>
              <p className="text-4xl font-bold text-foreground sm:text-5xl">{stats.cities}</p>
              <p className="mt-2 text-sm font-medium text-muted">Cities covered</p>
            </div>
          </div>
        </div>
      </section>

      {/* Main Gallery Section */}
      <section className="mx-auto max-w-7xl px-6 py-16 lg:py-20 space-y-16">
        {loading ? (
          <div className="py-20 text-center">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
            <p className="mt-3 text-sm text-muted">Loading gallery media...</p>
          </div>
        ) : (
          <>
            {/* 1. Event Folders Section */}
            <div className="space-y-6">
              <div className="flex items-center gap-3">
                <div className="h-8 w-1 rounded-full bg-primary" />
                <h2 className="text-2xl font-bold text-foreground">Event Folders</h2>
              </div>
              <p className="text-sm text-muted">Photos and videos grouped by community event. Click a folder to open its slideshow.</p>

              {eventFolders.length === 0 ? (
                <p className="text-sm text-muted italic">No event folders populated yet.</p>
              ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                  {eventFolders.map((folder) => {
                    const firstItem = folder.media[0];
                    const isVideo = firstItem ? isVideoFile(firstItem.image_path) : false;

                    return (
                      <motion.div
                        key={folder.id}
                        whileHover={{ y: -5 }}
                        onClick={() => handleOpenFolder(folder.media)}
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
                                No Cover
                              </div>
                            )}
                            <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
                            <div className="absolute top-4 left-4">
                              <span className="flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1 text-xs font-semibold text-foreground shadow-sm">
                                <Folder size={13} className="text-primary" /> Event Folder
                              </span>
                            </div>
                            <div className="absolute bottom-4 right-4">
                              <span className="text-xs font-semibold text-white bg-primary px-2.5 py-1 rounded-md shadow-sm">
                                {folder.media.length} items
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
                              View Slideshow <Eye size={12} />
                            </span>
                          </div>
                        </div>
                      </motion.div>
                    );
                  })}
                </div>
              )}
            </div>

            {/* 2. Common Gallery Section */}
            <div className="space-y-6 pt-6 border-t border-border">
              <div className="flex items-center gap-3">
                <div className="h-8 w-1 rounded-full bg-accent" />
                <h2 className="text-2xl font-bold text-foreground">Common Gallery</h2>
              </div>
              <p className="text-sm text-muted">Miscellaneous photos and videos from around the community. Click to expand.</p>

              {groupedGallery.common.length === 0 ? (
                <p className="text-sm text-muted italic">No common photos or videos uploaded yet.</p>
              ) : (
                <div className="columns-1 gap-6 space-y-6 md:columns-2 lg:columns-3">
                  {groupedGallery.common.map((item) => {
                    const isVideo = isVideoFile(item.image_path);

                    return (
                      <motion.div
                        key={item.id}
                        initial={{ opacity: 0, y: 15 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        onClick={() => setSingleActiveMedia(item)}
                        className={`group relative break-inside-avoid overflow-hidden rounded-2xl border cursor-pointer ${
                          isVideo 
                            ? "border-accent/40 shadow-[0_0_15px_rgba(244,63,94,0.08)] bg-slate-950" 
                            : "border-border bg-white shadow-sm"
                        }`}
                      >
                        {isVideo ? (
                          <div className="relative w-full aspect-video flex items-center justify-center bg-black">
                            <video
                              src={getMediaUrl(item.image_path)}
                              className="w-full h-full object-cover opacity-80"
                              muted
                              preload="metadata"
                            />
                            <div className="absolute inset-0 bg-black/30 group-hover:bg-black/50 transition-colors" />
                            {/* Play button overlay for video */}
                            <div className="absolute h-14 w-14 rounded-full bg-accent text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                              <Play size={24} className="ml-1" />
                            </div>
                            <span className="absolute bottom-3 left-3 flex items-center gap-1 text-[10px] font-bold text-white bg-accent px-2 py-0.5 rounded-full z-10 uppercase tracking-wider">
                              <Film size={10} /> Video
                            </span>
                          </div>
                        ) : (
                          <div className="relative">
                            <img
                              src={getMediaUrl(item.image_path)}
                              className="w-full object-cover transition-transform duration-500 group-hover:scale-105"
                              alt={item.caption || "Gallery image"}
                            />
                            <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent p-6 opacity-0 transition-opacity duration-300 group-hover:opacity-100 flex flex-col justify-end">
                              <span className="mb-2 w-fit rounded-full bg-white/15 px-3 py-1 text-[10px] font-bold text-white backdrop-blur-sm uppercase">
                                Image
                              </span>
                              {item.caption && <h3 className="text-sm font-semibold text-white">{item.caption}</h3>}
                            </div>
                          </div>
                        )}
                        {/* Display caption for video card */}
                        {isVideo && item.caption && (
                          <div className="p-4 bg-slate-950 text-white border-t border-accent/25">
                            <h3 className="text-sm font-medium line-clamp-1">{item.caption}</h3>
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
                <h4 className="text-lg font-bold">Event Photos & Videos</h4>
                <p className="text-xs text-white/60">
                  Item {currentIndex + 1} of {activeSliderMedia.length}
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
                {activeSliderMedia[currentIndex].caption || "No description provided."}
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
                <h4 className="text-lg font-bold">Community Gallery</h4>
                <p className="text-xs text-white/60">Common Upload</p>
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
                {singleActiveMedia.caption || "No description provided."}
              </p>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

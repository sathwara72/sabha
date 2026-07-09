"use client";

import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import { fetchEvents, fetchGallery, uploadGalleryImage, deleteGalleryImage } from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";
import {
  Upload, Image, Film, Plus,
  CheckCircle2, AlertCircle, Calendar,
  FileText, X, ZoomIn, ChevronLeft, ChevronRight,
  Trash2
} from "lucide-react";
import ConfirmModal from "@/components/shared/ConfirmModal";

export default function AdminGalleryPage() {
  const [events, setEvents] = useState<any[]>([]);
  const [gallery, setGallery] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  // Upload modal states
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedEventId, setSelectedEventId] = useState("");
  const [caption, setCaption] = useState("");
  const [mediaFile, setMediaFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState("");

  // Lightbox viewer state
  const [selectedMedia, setSelectedMedia] = useState<any | null>(null);
  const [lightboxIndex, setLightboxIndex] = useState<number>(0);

  // Delete states
  const [deleteId, setDeleteId] = useState<number | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);

  const handleConfirmDelete = async () => {
    if (!deleteId) return;
    setIsDeleting(true);
    setError("");
    try {
      await deleteGalleryImage(deleteId);
      await loadData();
      setDeleteId(null);
    } catch (err: any) {
      setError(err.message || "Failed to delete gallery image.");
    } finally {
      setIsDeleting(false);
    }
  };

  useEffect(() => {
    loadData();
  }, []);

  async function loadData() {
    try {
      setLoading(true);
      const [evtData, galData] = await Promise.all([
        fetchEvents(),
        fetchGallery(),
      ]);
      setEvents(evtData || []);
      setGallery(galData || []);
    } catch (err) {
      console.error("Error loading gallery data:", err);
    } finally {
      setLoading(false);
    }
  }

  // Scroll lock for upload modal
  useEffect(() => {
    if (isModalOpen || selectedMedia) {
      document.body.style.overflow = "hidden";
      document.documentElement.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
      document.documentElement.style.overflow = "";
    }
    return () => {
      document.body.style.overflow = "";
      document.documentElement.style.overflow = "";
    };
  }, [isModalOpen, selectedMedia]);

  // Keyboard nav for lightbox
  useEffect(() => {
    if (!selectedMedia) return;
    const handler = (e: KeyboardEvent) => {
      if (e.key === "Escape") setSelectedMedia(null);
      if (e.key === "ArrowRight") navigateLightbox(1);
      if (e.key === "ArrowLeft") navigateLightbox(-1);
    };
    window.addEventListener("keydown", handler);
    return () => window.removeEventListener("keydown", handler);
  }, [selectedMedia, lightboxIndex, gallery]);

  const openLightbox = (item: any, index: number) => {
    setSelectedMedia(item);
    setLightboxIndex(index);
  };

  const navigateLightbox = (dir: number) => {
    const newIndex = (lightboxIndex + dir + gallery.length) % gallery.length;
    setLightboxIndex(newIndex);
    setSelectedMedia(gallery[newIndex]);
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      setMediaFile(e.target.files[0]);
      setError("");
    }
  };

  const getMediaUrl = (path: string) => {
    if (!path) return "";
    if (path.startsWith("http://") || path.startsWith("https://")) return path;
    return `${API_ORIGIN}${path}`;
  };

  const isVideoFile = (path?: string) => {
    if (!path) return false;
    const ext = path.split(".").pop()?.toLowerCase();
    return ["mp4", "mov", "avi", "webm", "mkv"].includes(ext || "");
  };

  const handleUpload = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!mediaFile) {
      setError("Please select an image or video file to upload.");
      return;
    }
    setUploading(true);
    setError("");
    setSuccess(false);
    try {
      const formData = new FormData();
      formData.append("image", mediaFile);
      if (selectedEventId) formData.append("event_id", selectedEventId);
      if (caption) formData.append("caption", caption);

      await uploadGalleryImage(formData);
      setSuccess(true);
      setCaption("");
      setMediaFile(null);
      setSelectedEventId("");
      const fileInput = document.getElementById("gallery-file-input") as HTMLInputElement;
      if (fileInput) fileInput.value = "";
      await loadData();
      setTimeout(() => {
        setSuccess(false);
        setIsModalOpen(false);
      }, 1500);
    } catch (err: any) {
      setError(err.message || "Failed to upload media.");
    } finally {
      setUploading(false);
    }
  };

  return (
    <div className="space-y-3">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div className="flex flex-col">
          <h1 className="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Gallery management</h1>
          <p className="text-xs text-muted">Upload and manage images and videos for the community gallery</p>
        </div>
        <button
          onClick={() => { setSuccess(false); setError(""); setIsModalOpen(true); }}
          className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] whitespace-nowrap self-start sm:self-auto"
        >
          <Plus size={14} /> Add Gallery
        </button>
      </div>

      {/* Grid */}
      <div className="space-y-4 pt-2">
        <div className="flex items-center justify-between border-b border-border pb-2.5">
          <h2 className="text-sm font-bold text-foreground">Uploaded Media</h2>
          <span className="text-xs font-semibold text-muted">
            {gallery.length} {gallery.length === 1 ? "item" : "items"}
          </span>
        </div>

        {loading ? (
          <div className="py-20 text-center">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
            <p className="mt-3 text-sm text-muted">Loading uploaded media...</p>
          </div>
        ) : gallery.length === 0 ? (
          <div className="glass-card py-20 text-center text-muted border border-dashed border-border rounded-xl">
            No gallery media uploaded yet. Click the "Add Gallery" button above to upload your first file.
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {gallery.map((item, index) => {
              const isVideo = isVideoFile(item.image_path);
              const matchedEvent = events.find(e => e.id === item.event_id);
              return (
                <div
                  key={item.id}
                  onClick={() => openLightbox(item, index)}
                  className="glass-card p-0 overflow-hidden flex flex-col group relative cursor-pointer"
                >
                  <div className="relative h-40 w-full bg-slate-900 overflow-hidden">
                    {isVideo ? (
                      <video
                        src={getMediaUrl(item.image_path)}
                        className="h-full w-full object-cover"
                        muted preload="metadata"
                      />
                    ) : (
                      <img
                        src={getMediaUrl(item.image_path)}
                        alt={item.caption || "Gallery image"}
                        className="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                      />
                    )}

                    {/* Hover overlay */}
                    <div className="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                      <ZoomIn className="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" size={28} />
                    </div>

                    {/* Badge */}
                    <div className="absolute top-2 left-2 z-10">
                      <span className="flex items-center gap-1 text-[10px] font-bold text-white bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full">
                        {isVideo ? <><Film size={10} className="text-accent" /> Video</> : <><Image size={10} className="text-primary-soft" /> Image</>}
                      </span>
                    </div>

                    {/* Delete button */}
                    <button
                      type="button"
                      onClick={(e) => {
                        e.stopPropagation();
                        setDeleteId(item.id);
                      }}
                      className="absolute top-2 right-2 z-20 rounded-xl bg-red-50/90 border border-red-100 p-2 text-red-600 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all shadow-sm md:opacity-0 md:group-hover:opacity-100 duration-200"
                      title="Delete media"
                    >
                      <Trash2 size={13} />
                    </button>
                  </div>

                  <div className="p-3 flex-1 flex flex-col gap-1">
                    {matchedEvent ? (
                      <span className="text-[10px] font-semibold text-primary bg-primary-soft px-2 py-0.5 rounded-md flex items-center gap-1 w-fit max-w-full truncate">
                        <Calendar size={10} /> {matchedEvent.title}
                      </span>
                    ) : (
                      <span className="text-[10px] font-semibold text-muted bg-surface border border-border px-2 py-0.5 rounded-md flex items-center gap-1 w-fit">
                        Common Folder
                      </span>
                    )}
                    <p className="text-xs text-foreground font-medium line-clamp-2">
                      {item.caption || "No caption added"}
                    </p>
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>

      {/* ─── Lightbox Viewer Portal ─── */}
      {selectedMedia && createPortal(
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          {/* Backdrop */}
          <div
            className="fixed inset-0 bg-black/90 backdrop-blur-md"
            onClick={() => setSelectedMedia(null)}
          />

          {/* Close */}
          <button
            onClick={() => setSelectedMedia(null)}
            className="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2 transition-colors"
          >
            <X size={20} />
          </button>

          {/* Prev */}
          {gallery.length > 1 && (
            <button
              onClick={(e) => { e.stopPropagation(); navigateLightbox(-1); }}
              className="absolute left-4 top-1/2 -translate-y-1/2 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2.5 transition-colors"
            >
              <ChevronLeft size={22} />
            </button>
          )}

          {/* Next */}
          {gallery.length > 1 && (
            <button
              onClick={(e) => { e.stopPropagation(); navigateLightbox(1); }}
              className="absolute right-4 top-1/2 -translate-y-1/2 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2.5 transition-colors"
            >
              <ChevronRight size={22} />
            </button>
          )}

          {/* Media */}
          <div className="relative z-40 max-w-5xl w-full mx-auto px-14 flex flex-col items-center gap-4">
            {isVideoFile(selectedMedia.image_path) ? (
              <video
                src={getMediaUrl(selectedMedia.image_path)}
                controls
                autoPlay
                className="max-h-[75vh] max-w-full rounded-2xl shadow-2xl"
              />
            ) : (
              <img
                src={getMediaUrl(selectedMedia.image_path)}
                alt={selectedMedia.caption || "Gallery image"}
                className="max-h-[75vh] max-w-full object-contain rounded-2xl shadow-2xl"
              />
            )}

            {/* Caption bar */}
            <div className="flex flex-col items-center gap-1 text-center">
              {(() => {
                const matchedEvent = events.find(e => e.id === selectedMedia.event_id);
                return matchedEvent ? (
                  <span className="text-[11px] font-semibold text-white/60 flex items-center gap-1">
                    <Calendar size={11} /> {matchedEvent.title}
                  </span>
                ) : (
                  <span className="text-[11px] font-semibold text-white/40">Common Folder</span>
                );
              })()}
              {selectedMedia.caption && (
                <p className="text-sm font-medium text-white/80 max-w-xl">{selectedMedia.caption}</p>
              )}
              <p className="text-[10px] text-white/30">{lightboxIndex + 1} / {gallery.length}</p>
            </div>
          </div>
        </div>,
        document.body
      )}

      {/* ─── Upload Modal Portal ─── */}
      {isModalOpen && createPortal(
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onClick={() => setIsModalOpen(false)} />
          <div className="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl border border-border animate-in fade-in zoom-in-95 duration-200">
            <div className="flex items-start justify-between border-b border-border pb-3 mb-4">
              <h2 className="text-base font-bold text-foreground flex items-center gap-2">
                <Upload size={16} className="text-primary" /> Upload Media
              </h2>
              <button onClick={() => setIsModalOpen(false)} className="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors">
                <X size={18} />
              </button>
            </div>

            {error && (
              <div className="rounded-xl bg-red-50 border border-red-100 p-3 text-xs font-semibold text-red-600 flex items-center gap-2 mb-4">
                <AlertCircle size={14} className="shrink-0" /><span>{error}</span>
              </div>
            )}
            {success && (
              <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-xs font-semibold text-emerald-600 flex items-center gap-2 justify-center mb-4">
                <CheckCircle2 size={14} className="shrink-0" /><span>Uploaded successfully!</span>
              </div>
            )}

            <form onSubmit={handleUpload} className="space-y-4">
              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-foreground">Select File (Image or Video)</label>
                <div className="relative border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer flex flex-col items-center justify-center">
                  <input
                    id="gallery-file-input"
                    type="file"
                    accept="image/*,video/*"
                    onChange={handleFileChange}
                    className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                    required
                  />
                  <Upload className="h-6 w-6 text-primary mb-2" />
                  <span className="text-xs font-semibold text-foreground text-center line-clamp-1 px-2">
                    {mediaFile ? mediaFile.name : "Click to select image or video"}
                  </span>
                  <span className="text-[10px] text-muted-foreground mt-1">Images or Videos up to 50MB</span>
                </div>
              </div>

              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-foreground">Associate with Event</label>
                <select
                  value={selectedEventId}
                  onChange={(e) => setSelectedEventId(e.target.value)}
                  className="w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary font-medium"
                >
                  <option value="">Common / No Event Folder</option>
                  {events.map((evt) => (
                    <option key={evt.id} value={evt.id}>{evt.title} ({new Date(evt.date).getFullYear()})</option>
                  ))}
                </select>
                <p className="text-[10px] text-muted-foreground">Associating with an event groups this media into an Event Folder.</p>
              </div>

              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-foreground">Caption (Optional)</label>
                <div className="relative">
                  <FileText className="absolute left-3 top-3 h-3.5 w-3.5 text-muted-foreground" />
                  <textarea
                    rows={2}
                    value={caption}
                    onChange={(e) => setCaption(e.target.value)}
                    placeholder="Enter caption..."
                    className="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-4 text-xs text-foreground outline-none focus:border-primary resize-none"
                  />
                </div>
              </div>

              <div className="flex justify-end gap-2 border-t border-border pt-4 mt-2">
                <button type="button" onClick={() => setIsModalOpen(false)} className="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50 active:scale-95 transition-colors">
                  Cancel
                </button>
                <button type="submit" disabled={uploading} className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer transition-all">
                  {uploading ? "Uploading..." : <>Upload <Plus size={14} /></>}
                </button>
              </div>
            </form>
          </div>
        </div>,
        document.body
      )}
      {/* Confirm Delete Modal */}
      <ConfirmModal
        isOpen={deleteId !== null}
        title="Delete Gallery Media"
        message="Are you sure you want to delete this gallery media? This action cannot be undone."
        confirmLabel="Delete"
        cancelLabel="Cancel"
        variant="danger"
        isLoading={isDeleting}
        onConfirm={handleConfirmDelete}
        onCancel={() => setDeleteId(null)}
      />
    </div>
  );
}

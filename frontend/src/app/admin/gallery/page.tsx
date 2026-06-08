"use client";

import { useEffect, useState } from "react";
import { fetchEvents, fetchGallery, uploadGalleryImage } from "@/lib/api";
import {
  Upload, Image, Film, Trash, Plus,
  Info, CheckCircle2, AlertCircle, Calendar,
  FileText
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";

export default function AdminGalleryPage() {
  const [events, setEvents] = useState<any[]>([]);
  const [gallery, setGallery] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  // Form states
  const [selectedEventId, setSelectedEventId] = useState("");
  const [caption, setCaption] = useState("");
  const [mediaFile, setMediaFile] = useState<File | null>(null);

  const [uploading, setUploading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState("");

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

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      setMediaFile(e.target.files[0]);
      setError("");
    }
  };

  const getMediaUrl = (path: string) => {
    if (!path) return "";
    if (path.startsWith("http://") || path.startsWith("https://")) {
      return path;
    }
    return `http://localhost:8000${path}`;
  };

  const isVideoFile = (path: string) => {
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
      if (selectedEventId) {
        formData.append("event_id", selectedEventId);
      }
      if (caption) {
        formData.append("caption", caption);
      }

      await uploadGalleryImage(formData);
      setSuccess(true);
      setCaption("");
      setMediaFile(null);
      setSelectedEventId("");
      
      // Reset file input
      const fileInput = document.getElementById("gallery-file-input") as HTMLInputElement;
      if (fileInput) fileInput.value = "";

      await loadData();
      setTimeout(() => setSuccess(false), 3000);
    } catch (err: any) {
      setError(err.message || "Failed to upload media.");
    } finally {
      setUploading(false);
    }
  };

  const inputClass = "w-full rounded-xl border border-border bg-white px-4 py-3 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary";
  const labelClass = "text-sm font-medium text-foreground";

  return (
    <div className="space-y-10 max-w-6xl">
      <div className="flex flex-col gap-2">
        <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Gallery management</h1>
        <p className="text-sm text-muted">Upload and manage images and videos for the community gallery</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Upload Form */}
        <div className="glass-card p-6 h-fit space-y-6">
          <h2 className="text-lg font-bold text-foreground border-b border-border pb-3 flex items-center gap-2">
            <Upload size={18} className="text-primary" /> Upload Media
          </h2>

          {error && (
            <div className="rounded-xl bg-red-50 border border-red-100 p-3 text-center text-xs font-semibold text-red-600 flex items-center gap-2">
              <AlertCircle size={14} className="shrink-0" />
              <span>{error}</span>
            </div>
          )}

          {success && (
            <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center text-xs font-semibold text-emerald-600 flex items-center gap-2 justify-center">
              <CheckCircle2 size={14} className="shrink-0" />
              <span>Uploaded successfully!</span>
            </div>
          )}

          <form onSubmit={handleUpload} className="space-y-4">
            <div className="space-y-1.5">
              <label className={labelClass}>Select File (Image or Video)</label>
              <div className="relative border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer flex flex-col items-center justify-center">
                <input
                  id="gallery-file-input"
                  type="file"
                  accept="image/*,video/*"
                  onChange={handleFileChange}
                  className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                  required
                />
                <Upload className="h-8 w-8 text-primary mb-2" />
                <span className="text-xs font-semibold text-foreground text-center">
                  {mediaFile ? mediaFile.name : "Click to select image or video"}
                </span>
                <span className="text-[10px] text-muted-foreground mt-1">Images or Videos up to 50MB</span>
              </div>
            </div>

            <div className="space-y-1.5">
              <label className={labelClass}>Associate with Event</label>
              <select
                value={selectedEventId}
                onChange={(e) => setSelectedEventId(e.target.value)}
                className={inputClass}
              >
                <option value="">Common / No Event Folder</option>
                {events.map((evt) => (
                  <option key={evt.id} value={evt.id}>
                    {evt.title} ({new Date(evt.date).getFullYear()})
                  </option>
                ))}
              </select>
              <p className="text-[10px] text-muted">Associating with an event groups this media into an interactive Event Folder in the gallery.</p>
            </div>

            <div className="space-y-1.5">
              <label className={labelClass}>Caption (Optional)</label>
              <div className="relative">
                <FileText className="absolute left-3.5 top-3.5 h-4 w-4 text-muted-foreground" />
                <textarea
                  rows={2}
                  value={caption}
                  onChange={(e) => setCaption(e.target.value)}
                  placeholder="Enter caption..."
                  className={`${inputClass} pl-11 resize-none`}
                />
              </div>
            </div>

            <button
              type="submit"
              disabled={uploading}
              className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
            >
              {uploading ? "Uploading..." : <>Upload Media <Plus size={16} /></>}
            </button>
          </form>
        </div>

        {/* Media Grid */}
        <div className="lg:col-span-2 space-y-6">
          <div className="flex items-center justify-between border-b border-border pb-3">
            <h2 className="text-lg font-bold text-foreground">Uploaded Media</h2>
            <span className="text-sm font-semibold text-muted">
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
              No gallery media uploaded yet. Use the panel on the left to add your first file.
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
              {gallery.map((item) => {
                const isVideo = isVideoFile(item.image_path);
                const matchedEvent = events.find(e => e.id === item.event_id);

                return (
                  <div key={item.id} className="glass-card p-0 overflow-hidden flex flex-col group relative">
                    <div className="relative h-40 w-full bg-slate-900 overflow-hidden">
                      {isVideo ? (
                        <video
                          src={getMediaUrl(item.image_path)}
                          className="h-full w-full object-cover"
                          muted
                          preload="metadata"
                        />
                      ) : (
                        <img
                          src={getMediaUrl(item.image_path)}
                          alt={item.caption || "Gallery image"}
                          className="h-full w-full object-cover group-hover:scale-105 transition-transform"
                        />
                      )}
                      
                      {/* Media format badge */}
                      <div className="absolute top-2 left-2 z-10">
                        <span className="flex items-center gap-1 text-[10px] font-bold text-white bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full">
                          {isVideo ? (
                            <><Film size={10} className="text-accent" /> Video</>
                          ) : (
                            <><Image size={10} className="text-primary-soft" /> Image</>
                          )}
                        </span>
                      </div>
                    </div>

                    <div className="p-3 flex-1 flex flex-col justify-between gap-3">
                      <div>
                        {matchedEvent ? (
                          <span className="text-[10px] font-semibold text-primary bg-primary-soft px-2 py-0.5 rounded-md flex items-center gap-1 w-fit mb-1 max-w-full truncate">
                            <Calendar size={10} /> {matchedEvent.title}
                          </span>
                        ) : (
                          <span className="text-[10px] font-semibold text-muted bg-surface border border-border px-2 py-0.5 rounded-md flex items-center gap-1 w-fit mb-1">
                            Common Folder
                          </span>
                        )}
                        <p className="text-xs text-foreground font-medium line-clamp-2 mt-1">
                          {item.caption || "No caption added"}
                        </p>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

"use client";

import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import { fetchHeroImages, uploadHeroImage, deleteHeroImage } from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";
import {
  Upload, Image, Plus, Trash2,
  CheckCircle2, AlertCircle, X, ZoomIn
} from "lucide-react";
import ConfirmModal from "@/components/shared/ConfirmModal";

interface HeroImageItem {
  id: number;
  image_path: string;
  title: string | null;
  caption: string | null;
  created_at: string;
}

export default function AdminHeroSliderPage() {
  const [heroImages, setHeroImages] = useState<HeroImageItem[]>([]);
  const [loading, setLoading] = useState(true);

  // Upload modal states
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [mediaFile, setMediaFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState("");

  // Lightbox preview state
  const [previewImage, setPreviewImage] = useState<string | null>(null);

  // Delete states
  const [deleteId, setDeleteId] = useState<number | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  async function loadData() {
    try {
      setLoading(true);
      const data = await fetchHeroImages();
      setHeroImages(data || []);
    } catch (err) {
      console.error("Error loading hero images:", err);
    } finally {
      setLoading(false);
    }
  }

  // Scroll lock for modals
  useEffect(() => {
    if (isModalOpen || previewImage) {
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
  }, [isModalOpen, previewImage]);

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

  const handleUpload = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!mediaFile) {
      setError("Please select an image file to upload.");
      return;
    }
    setUploading(true);
    setError("");
    setSuccess(false);
    try {
      const formData = new FormData();
      formData.append("image", mediaFile);

      await uploadHeroImage(formData);
      setSuccess(true);
      setMediaFile(null);
      const fileInput = document.getElementById("hero-file-input") as HTMLInputElement;
      if (fileInput) fileInput.value = "";
      await loadData();
      setTimeout(() => {
        setSuccess(false);
        setIsModalOpen(false);
      }, 1500);
    } catch (err: any) {
      setError(err.message || "Failed to upload hero image.");
    } finally {
      setUploading(false);
    }
  };

  const handleDelete = (id: number) => {
    setDeleteId(id);
  };

  const handleConfirmDelete = async () => {
    if (!deleteId) return;
    setIsDeleting(true);
    setError("");
    try {
      await deleteHeroImage(deleteId);
      await loadData();
      setDeleteId(null);
    } catch (err: any) {
      setError(err.message || "Failed to delete hero image.");
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <div className="space-y-3">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div className="flex flex-col">
          <h1 className="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Hero Slider Management</h1>
          <p className="text-xs text-muted">Upload and manage rotating hero slider banner images displayed on the homepage</p>
        </div>
        <button
          onClick={() => { setSuccess(false); setError(""); setIsModalOpen(true); }}
          className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] whitespace-nowrap self-start sm:self-auto"
        >
          <Plus size={14} /> Add Hero Image
        </button>
      </div>

      {/* Grid */}
      <div className="space-y-4 pt-2">
        <div className="flex items-center justify-between border-b border-border pb-2.5">
          <h2 className="text-sm font-bold text-foreground">Slider Images</h2>
          <span className="text-xs font-semibold text-muted">
            {heroImages.length} {heroImages.length === 1 ? "image" : "images"}
          </span>
        </div>

        {loading ? (
          <div className="py-20 text-center">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
            <p className="mt-3 text-sm text-muted">Loading slider images...</p>
          </div>
        ) : heroImages.length === 0 ? (
          <div className="glass-card py-20 text-center text-muted border border-dashed border-border rounded-xl">
            No custom hero images uploaded yet. The homepage will display the default system images. Click "Add Hero Image" to upload your first slider image.
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {heroImages.map((item) => (
              <div
                key={item.id}
                className="glass-card p-0 overflow-hidden flex flex-col group relative"
              >
                <div className="relative h-40 w-full bg-slate-900 overflow-hidden">
                  <img
                    src={getMediaUrl(item.image_path)}
                    alt={item.title || "Hero banner"}
                    className="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                  />

                  {/* Hover Actions */}
                  <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-3">
                    <button
                      onClick={() => setPreviewImage(getMediaUrl(item.image_path))}
                      className="p-2 bg-white/20 hover:bg-white/35 text-white rounded-lg transition-colors cursor-pointer"
                      title="Preview Image"
                    >
                      <ZoomIn size={18} />
                    </button>
                    <button
                      onClick={() => handleDelete(item.id)}
                      className="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors cursor-pointer"
                      title="Delete Image"
                    >
                      <Trash2 size={18} />
                    </button>
                  </div>

                  {/* Badge */}
                  <div className="absolute top-2 left-2 z-10">
                    <span className="flex items-center gap-1 text-[10px] font-bold text-white bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full">
                      <Image size={10} className="text-primary-soft" /> Slider Banner
                    </span>
                  </div>
                </div>

                <div className="p-3 flex-1 flex flex-col gap-1 bg-white">
                  <p className="text-[11px] text-muted">
                    Uploaded: {new Date(item.created_at).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}
                  </p>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* ─── Lightbox Preview Portal ─── */}
      {previewImage && createPortal(
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div
            className="fixed inset-0 bg-black/90 backdrop-blur-md"
            onClick={() => setPreviewImage(null)}
          />
          <button
            onClick={() => setPreviewImage(null)}
            className="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2 transition-colors cursor-pointer"
          >
            <X size={20} />
          </button>
          <div className="relative z-40 max-w-5xl max-h-[85vh] w-full flex items-center justify-center">
            <img
              src={previewImage}
              alt="Hero Preview"
              className="max-h-[80vh] max-w-full object-contain rounded-2xl shadow-2xl"
            />
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
                <Upload size={16} className="text-primary" /> Upload Hero Image
              </h2>
              <button onClick={() => setIsModalOpen(false)} className="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors cursor-pointer">
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
                <label className="text-xs font-semibold text-foreground">Select Slider Image</label>
                <div className="relative border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer flex flex-col items-center justify-center">
                  <input
                    id="hero-file-input"
                    type="file"
                    accept="image/*"
                    onChange={handleFileChange}
                    className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                    required
                  />
                  <Upload className="h-6 w-6 text-primary mb-2" />
                  <span className="text-xs font-semibold text-foreground text-center line-clamp-1 px-2">
                    {mediaFile ? mediaFile.name : "Click to select banner image"}
                  </span>
                  <span className="text-[10px] text-muted-foreground mt-1">Recommended aspect ratio: 21:9 or landscape</span>
                </div>
              </div>

              <div className="flex justify-end gap-2 border-t border-border pt-4 mt-2">
                <button type="button" onClick={() => setIsModalOpen(false)} className="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50 active:scale-95 transition-colors cursor-pointer">
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
        title="Delete Hero Slider Image"
        message="Are you sure you want to delete this hero slider image? This action cannot be undone."
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

"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import Link from "next/link";
import { createPortal } from "react-dom";
import {
  fetchEvents,
  getAllEventRegistrations,
  approveEventRegistration,
  rejectEventRegistration,
  toggleAttendance,
  fetchGallery,
  uploadGalleryImage,
  deleteGalleryImage
} from "@/lib/api";
import { API_ORIGIN, assetUrl } from "@/lib/config";
import {
  ChevronLeft, Calendar, MapPin, Tag, IndianRupee,
  Users, Image as ImageIcon, Search, CheckCircle2,
  XCircle, Clock, AlertCircle, Upload, Plus, Film,
  ZoomIn, Trash2, X, ChevronRight, Check, FileText
} from "lucide-react";
import ConfirmModal from "@/components/shared/ConfirmModal";

export default function AdminEventDetailPage() {
  const params = useParams();
  const router = useRouter();
  const eventId = Number(params?.id);

  const [activeTab, setActiveTab] = useState<"registrations" | "gallery">("registrations");

  // Event & Registrations State
  const [event, setEvent] = useState<any | null>(null);
  const [loading, setLoading] = useState(true);
  const [registrations, setRegistrations] = useState<any[]>([]);
  const [registrationsLoading, setRegistrationsLoading] = useState(false);
  const [memberSearch, setMemberSearch] = useState("");
  const [memberFilter, setMemberFilter] = useState<"all" | "pending" | "approved" | "rejected">("all");
  const [currentPage, setCurrentPage] = useState(1);
  const ITEMS_PER_PAGE = 10;

  // Action states for Registrations
  const [actionLoadingId, setActionLoadingId] = useState<number | null>(null);
  const [rejectModalId, setRejectModalId] = useState<number | null>(null);
  const [rejectReason, setRejectReason] = useState("");

  // Gallery State
  const [gallery, setGallery] = useState<any[]>([]);
  const [galleryLoading, setGalleryLoading] = useState(false);

  // Gallery Upload Modal State
  const [isUploadModalOpen, setIsUploadModalOpen] = useState(false);
  const [mediaFiles, setMediaFiles] = useState<File[]>([]);
  const [uploading, setUploading] = useState(false);
  const [uploadError, setUploadError] = useState("");

  // Lightbox Viewer State
  const [selectedMedia, setSelectedMedia] = useState<any | null>(null);
  const [lightboxIndex, setLightboxIndex] = useState<number>(0);

  // Delete Media State
  const [deleteMediaId, setDeleteMediaId] = useState<number | null>(null);
  const [isDeletingMedia, setIsDeletingMedia] = useState(false);

  useEffect(() => {
    if (eventId) {
      loadEventData();
    }
  }, [eventId]);

  async function loadEventData() {
    try {
      setLoading(true);
      const allEvents = await fetchEvents();
      const foundEvent = (allEvents || []).find((e: any) => Number(e.id) === eventId);
      if (foundEvent) {
        setEvent(foundEvent);
      } else {
        setEvent(null);
      }
    } catch (err) {
      console.error("Failed to fetch event details:", err);
    } finally {
      setLoading(false);
    }

    loadRegistrations();
    loadGallery();
  }

  async function loadRegistrations() {
    try {
      setRegistrationsLoading(true);
      const data = await getAllEventRegistrations();
      const eventRegs = (data || []).filter((reg: any) => Number(reg.event_id) === eventId);
      setRegistrations(eventRegs);
    } catch (err) {
      console.error("Failed to load event registrations:", err);
    } finally {
      setRegistrationsLoading(false);
    }
  }

  async function loadGallery() {
    try {
      setGalleryLoading(true);
      const data = await fetchGallery();
      const eventMedia = (data || []).filter((item: any) => Number(item.event_id) === eventId);
      setGallery(eventMedia);
    } catch (err) {
      console.error("Failed to load event gallery:", err);
    } finally {
      setGalleryLoading(false);
    }
  }

  // Registration Actions
  const handleApprove = async (id: number) => {
    setActionLoadingId(id);
    try {
      await approveEventRegistration(id);
      await loadRegistrations();
    } catch (err) {
      console.error("Failed to approve registration:", err);
    } finally {
      setActionLoadingId(null);
    }
  };

  const handleRejectSubmit = async () => {
    if (!rejectModalId || !rejectReason.trim()) return;
    setActionLoadingId(rejectModalId);
    try {
      await rejectEventRegistration(rejectModalId, rejectReason);
      setRejectModalId(null);
      setRejectReason("");
      await loadRegistrations();
    } catch (err) {
      console.error("Failed to reject registration:", err);
    } finally {
      setActionLoadingId(null);
    }
  };

  const handleToggleAttendance = async (id: number) => {
    setActionLoadingId(id);
    try {
      await toggleAttendance(id);
      await loadRegistrations();
    } catch (err) {
      console.error("Failed to toggle attendance:", err);
    } finally {
      setActionLoadingId(null);
    }
  };

  // Gallery Upload Handlers
  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length > 0) {
      setMediaFiles(Array.from(e.target.files));
      setUploadError("");
    }
  };

  const handleUploadSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (mediaFiles.length === 0) {
      setUploadError("Please select at least one image, video, or ZIP archive.");
      return;
    }
    setUploading(true);
    setUploadError("");

    try {
      const formData = new FormData();
      formData.append("event_id", String(eventId));
      mediaFiles.forEach((file) => {
        formData.append("images[]", file);
      });

      await uploadGalleryImage(formData);
      setMediaFiles([]);
      const fileInput = document.getElementById("event-gallery-input") as HTMLInputElement;
      if (fileInput) fileInput.value = "";
      setIsUploadModalOpen(false);
      await loadGallery();
    } catch (err: any) {
      setUploadError(err.message || "Failed to upload gallery media.");
    } finally {
      setUploading(false);
    }
  };

  const handleDeleteMedia = async () => {
    if (!deleteMediaId) return;
    setIsDeletingMedia(true);
    try {
      await deleteGalleryImage(deleteMediaId);
      await loadGallery();
      setDeleteMediaId(null);
    } catch (err) {
      console.error("Failed to delete gallery media:", err);
    } finally {
      setIsDeletingMedia(false);
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

  // Filtered Registrations
  const filteredRegistrations = registrations.filter((reg) => {
    const matchesFilter =
      memberFilter === "all" || reg.status === memberFilter;

    const query = memberSearch.toLowerCase();
    const matchesSearch =
      !query ||
      (reg.user?.name && reg.user.name.toLowerCase().includes(query)) ||
      (reg.user?.email && reg.user.email.toLowerCase().includes(query)) ||
      (reg.user?.phone && reg.user.phone.toLowerCase().includes(query)) ||
      (reg.ticket_number && reg.ticket_number.toLowerCase().includes(query));

    return matchesFilter && matchesSearch;
  });

  const totalPages = Math.ceil(filteredRegistrations.length / ITEMS_PER_PAGE);
  const paginatedRegistrations = filteredRegistrations.slice(
    (currentPage - 1) * ITEMS_PER_PAGE,
    currentPage * ITEMS_PER_PAGE
  );

  if (loading) {
    return (
      <div className="py-20 text-center">
        <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
        <p className="mt-3 text-sm text-muted">Loading event details...</p>
      </div>
    );
  }

  if (!event) {
    return (
      <div className="py-16 text-center space-y-4">
        <h2 className="text-xl font-bold text-foreground">Event Not Found</h2>
        <p className="text-sm text-muted">The requested event could not be found or has been removed.</p>
        <Link
          href="/admin/events"
          className="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90"
        >
          <ChevronLeft size={16} /> Back to Events List
        </Link>
      </div>
    );
  }

  return (
    <div className="space-y-5 pb-10">
      {/* Top Navigation Bar */}
      <div className="flex items-center justify-between">
        <Link
          href="/admin/events"
          className="inline-flex items-center gap-1.5 text-xs font-bold text-muted hover:text-foreground transition-colors group"
        >
          <ChevronLeft size={16} className="group-hover:-translate-x-0.5 transition-transform" />
          Back to Events List
        </Link>
      </div>

      {/* Event Overview Card */}
      <div className="glass-card p-5 rounded-2xl border border-border bg-white shadow-sm flex flex-col md:flex-row gap-5">
        {event.image && (
          <div className="w-full md:w-56 h-40 rounded-xl overflow-hidden bg-slate-900 shrink-0 relative">
            <img
              src={assetUrl(event.image)}
              alt={event.title}
              className="w-full h-full object-cover"
            />
            <span className="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary text-white">
              {event.type || "Event"}
            </span>
          </div>
        )}

        <div className="flex-1 space-y-3">
          <div className="flex flex-wrap items-center justify-between gap-2">
            <h1 className="text-xl sm:text-2xl font-bold text-foreground tracking-tight">{event.title}</h1>
          </div>

          <p className="text-xs text-muted line-clamp-2">{event.description}</p>

          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1 border-t border-border/60">
            <div className="flex items-center gap-2">
              <div className="p-2 rounded-lg bg-primary-soft text-primary">
                <Calendar size={14} />
              </div>
              <div>
                <p className="text-[10px] font-semibold text-muted">Date</p>
                <p className="text-xs font-bold text-foreground">{new Date(event.date).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}</p>
              </div>
            </div>

            <div className="flex items-center gap-2">
              <div className="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                <MapPin size={14} />
              </div>
              <div>
                <p className="text-[10px] font-semibold text-muted">Location</p>
                <p className="text-xs font-bold text-foreground truncate max-w-[120px]">{event.location}</p>
              </div>
            </div>

            <div className="flex items-center gap-2">
              <div className="p-2 rounded-lg bg-blue-50 text-blue-600">
                <Users size={14} />
              </div>
              <div>
                <p className="text-[10px] font-semibold text-muted">Registrations</p>
                <p className="text-xs font-bold text-foreground">{registrations.length} Total</p>
              </div>
            </div>

            <div className="flex items-center gap-2">
              <div className="p-2 rounded-lg bg-amber-50 text-amber-600">
                <ImageIcon size={14} />
              </div>
              <div>
                <p className="text-[10px] font-semibold text-muted">Event Gallery</p>
                <p className="text-xs font-bold text-foreground">{gallery.length} Media</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Tabs Navigation */}
      <div className="flex items-center gap-2 border-b border-border pb-0">
        <button
          onClick={() => setActiveTab("registrations")}
          className={`inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold transition-all border-b-2 ${
            activeTab === "registrations"
              ? "border-primary text-primary bg-primary-soft/30 rounded-t-xl"
              : "border-transparent text-muted hover:text-foreground"
          }`}
        >
          <Users size={15} /> Registrations & Bookings
          <span className="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-foreground">
            {registrations.length}
          </span>
        </button>

        <button
          onClick={() => setActiveTab("gallery")}
          className={`inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold transition-all border-b-2 ${
            activeTab === "gallery"
              ? "border-primary text-primary bg-primary-soft/30 rounded-t-xl"
              : "border-transparent text-muted hover:text-foreground"
          }`}
        >
          <ImageIcon size={15} /> Event Gallery
          <span className="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-foreground">
            {gallery.length}
          </span>
        </button>
      </div>

      {/* TAB 1: REGISTRATIONS & BOOKINGS */}
      {activeTab === "registrations" && (
        <div className="space-y-4">
          {/* Controls Bar */}
          <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white p-3 rounded-xl border border-border shadow-sm">
            {/* Filter Tabs */}
            <div className="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0">
              {(["all", "pending", "approved", "rejected"] as const).map((filter) => (
                <button
                  key={filter}
                  onClick={() => { setMemberFilter(filter); setCurrentPage(1); }}
                  className={`px-3 py-1.5 text-xs font-bold rounded-lg capitalize transition-all whitespace-nowrap ${
                    memberFilter === filter
                      ? "bg-slate-900 text-white shadow-sm"
                      : "bg-surface text-muted hover:text-foreground hover:bg-slate-100"
                  }`}
                >
                  {filter}
                </button>
              ))}
            </div>

            {/* Search */}
            <div className="relative w-full sm:w-64">
              <Search className="absolute left-3 top-2.5 h-3.5 w-3.5 text-muted" />
              <input
                type="text"
                placeholder="Search member, email, ticket..."
                value={memberSearch}
                onChange={(e) => { setMemberSearch(e.target.value); setCurrentPage(1); }}
                className="w-full rounded-xl border border-border bg-surface py-2 pl-9 pr-3 text-xs text-foreground outline-none focus:border-primary"
              />
            </div>
          </div>

          {/* Registrations Table */}
          {registrationsLoading ? (
            <div className="py-16 text-center">
              <div className="inline-block animate-spin rounded-full h-7 w-7 border-3 border-primary border-t-transparent" />
              <p className="mt-2 text-xs text-muted">Loading registrations...</p>
            </div>
          ) : filteredRegistrations.length === 0 ? (
            <div className="glass-card py-16 text-center text-muted border border-dashed border-border rounded-xl">
              No registrations found matching your filter.
            </div>
          ) : (
            <div className="glass-card p-0 overflow-hidden border border-border rounded-2xl bg-white shadow-sm">
              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="border-b border-border bg-slate-50/70 text-[11px] font-bold text-muted uppercase tracking-wider">
                      <th className="py-3 px-4">Member</th>
                      <th className="py-3 px-4">Contact</th>
                      <th className="py-3 px-4">Ticket No.</th>
                      <th className="py-3 px-4 text-center">Seats</th>
                      <th className="py-3 px-4">Status</th>
                      <th className="py-3 px-4">Check-in</th>
                      <th className="py-3 px-4 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-border text-xs">
                    {paginatedRegistrations.map((reg) => {
                      const user = reg.user || {};
                      const isPending = reg.status === "pending";
                      const isApproved = reg.status === "approved";
                      const isRejected = reg.status === "rejected";

                      return (
                        <tr key={reg.id} className="hover:bg-slate-50/60 transition-colors">
                          <td className="py-3 px-4 font-semibold text-foreground">
                            <div className="flex flex-col">
                              <span>{user.name || "N/A"}</span>
                              <span className="text-[10px] text-muted font-normal">{user.designation || "Member"}</span>
                            </div>
                          </td>

                          <td className="py-3 px-4 text-muted">
                            <div className="flex flex-col">
                              <span>{user.email || "N/A"}</span>
                              <span className="text-[10px] text-muted">{user.phone || ""}</span>
                            </div>
                          </td>

                          <td className="py-3 px-4 font-mono font-bold text-foreground">
                            {reg.ticket_number || `#${reg.id}`}
                          </td>

                          <td className="py-3 px-4 text-center font-bold text-foreground">
                            {reg.quantity || 1}
                          </td>

                          <td className="py-3 px-4">
                            {isApproved && (
                              <span className="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-600 border border-emerald-100">
                                <CheckCircle2 size={11} /> Approved
                              </span>
                            )}
                            {isPending && (
                              <span className="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold text-amber-600 border border-amber-100">
                                <Clock size={11} /> Pending
                              </span>
                            )}
                            {isRejected && (
                              <span className="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-[10px] font-bold text-red-600 border border-red-100">
                                <XCircle size={11} /> Rejected
                              </span>
                            )}
                          </td>

                          <td className="py-3 px-4">
                            <button
                              onClick={() => handleToggleAttendance(reg.id)}
                              disabled={actionLoadingId === reg.id || !isApproved}
                              className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all ${
                                reg.attended
                                  ? "bg-emerald-600 text-white"
                                  : "bg-slate-100 text-muted hover:bg-slate-200"
                              } ${!isApproved ? "opacity-50 cursor-not-allowed" : "cursor-pointer"}`}
                            >
                              {reg.attended ? <><Check size={11} /> Attended</> : "Mark Present"}
                            </button>
                          </td>

                          <td className="py-3 px-4 text-right">
                            <div className="flex items-center justify-end gap-1.5">
                              {isPending && (
                                <>
                                  <button
                                    onClick={() => handleApprove(reg.id)}
                                    disabled={actionLoadingId === reg.id}
                                    className="px-2.5 py-1 rounded-lg bg-emerald-500 text-white text-[10px] font-bold hover:bg-emerald-600 transition-colors shadow-sm disabled:opacity-50"
                                  >
                                    Approve
                                  </button>
                                  <button
                                    onClick={() => { setRejectModalId(reg.id); setRejectReason(""); }}
                                    disabled={actionLoadingId === reg.id}
                                    className="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 border border-red-100 text-[10px] font-bold hover:bg-red-600 hover:text-white transition-colors shadow-sm disabled:opacity-50"
                                  >
                                    Reject
                                  </button>
                                </>
                              )}
                              {isApproved && (
                                <button
                                  onClick={() => { setRejectModalId(reg.id); setRejectReason(""); }}
                                  disabled={actionLoadingId === reg.id}
                                  className="px-2.5 py-1 rounded-lg bg-slate-100 text-muted hover:bg-red-50 hover:text-red-600 text-[10px] font-bold transition-colors"
                                >
                                  Revoke
                                </button>
                              )}
                            </div>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>

              {/* Pagination */}
              {totalPages > 1 && (
                <div className="flex items-center justify-between border-t border-border px-4 py-3 bg-slate-50/50 text-xs">
                  <span className="text-muted">
                    Showing {(currentPage - 1) * ITEMS_PER_PAGE + 1} to {Math.min(currentPage * ITEMS_PER_PAGE, filteredRegistrations.length)} of {filteredRegistrations.length}
                  </span>
                  <div className="flex items-center gap-1">
                    <button
                      onClick={() => setCurrentPage((p) => Math.max(p - 1, 1))}
                      disabled={currentPage === 1}
                      className="p-1 rounded-lg border border-border hover:bg-slate-100 disabled:opacity-40"
                    >
                      <ChevronLeft size={16} />
                    </button>
                    <span className="px-2 font-bold text-foreground">
                      {currentPage} / {totalPages}
                    </span>
                    <button
                      onClick={() => setCurrentPage((p) => Math.min(p + 1, totalPages))}
                      disabled={currentPage === totalPages}
                      className="p-1 rounded-lg border border-border hover:bg-slate-100 disabled:opacity-40"
                    >
                      <ChevronRight size={16} />
                    </button>
                  </div>
                </div>
              )}
            </div>
          )}
        </div>
      )}

      {/* TAB 2: EVENT GALLERY */}
      {activeTab === "gallery" && (
        <div className="space-y-4">
          {/* Header Action Bar */}
          <div className="flex items-center justify-between border-b border-border pb-3">
            <div>
              <h2 className="text-sm font-bold text-foreground">Event Gallery Photos & Videos</h2>
              <p className="text-xs text-muted">Upload and manage media specifically for "{event.title}"</p>
            </div>

            <button
              onClick={() => { setUploadError(""); setIsUploadModalOpen(true); }}
              className="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-95 shadow-sm"
            >
              <Plus size={14} /> Add Event Media
            </button>
          </div>

          {/* Media Grid */}
          {galleryLoading ? (
            <div className="py-16 text-center">
              <div className="inline-block animate-spin rounded-full h-7 w-7 border-3 border-primary border-t-transparent" />
              <p className="mt-2 text-xs text-muted">Loading event gallery...</p>
            </div>
          ) : gallery.length === 0 ? (
            <div className="glass-card py-16 text-center text-muted border border-dashed border-border rounded-xl">
              No media uploaded for this event yet. Click "Add Event Media" button to upload photos, videos, or ZIP archives!
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              {gallery.map((item, index) => {
                const isVideo = isVideoFile(item.image_path);
                return (
                  <div
                    key={item.id}
                    onClick={() => { setSelectedMedia(item); setLightboxIndex(index); }}
                    className="glass-card p-0 overflow-hidden flex flex-col group relative cursor-pointer border border-border hover:border-primary/50 transition-all rounded-xl shadow-sm"
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
                          alt={item.caption || "Event photo"}
                          className="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                      )}

                      {/* Hover Overlay */}
                      <div className="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                        <ZoomIn className="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" size={28} />
                      </div>

                      {/* Format Badge */}
                      <div className="absolute top-2 left-2 z-10">
                        <span className="flex items-center gap-1 text-[10px] font-bold text-white bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded-full">
                          {isVideo ? <><Film size={10} className="text-amber-400" /> Video</> : <><ImageIcon size={10} className="text-primary-soft" /> Image</>}
                        </span>
                      </div>

                      {/* Delete Button */}
                      <button
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation();
                          setDeleteMediaId(item.id);
                        }}
                        className="absolute top-2 right-2 z-20 rounded-xl bg-red-50/90 border border-red-100 p-2 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm md:opacity-0 md:group-hover:opacity-100 duration-200"
                        title="Delete media"
                      >
                        <Trash2 size={13} />
                      </button>
                    </div>

                    <div className="p-3 flex-1 flex flex-col gap-1">
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
      )}

      {/* Rejection Modal */}
      {rejectModalId !== null && createPortal(
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onClick={() => setRejectModalId(null)} />
          <div className="relative w-full max-w-sm rounded-2xl bg-white p-5 shadow-2xl border border-border animate-in fade-in zoom-in-95 duration-200">
            <h3 className="text-sm font-bold text-foreground mb-2">Reject Registration</h3>
            <p className="text-xs text-muted mb-3">Please state a reason for rejecting this registration request.</p>
            <textarea
              rows={3}
              value={rejectReason}
              onChange={(e) => setRejectReason(e.target.value)}
              placeholder="Enter rejection reason..."
              className="w-full rounded-xl border border-border bg-surface p-2.5 text-xs text-foreground outline-none focus:border-primary resize-none mb-4"
            />
            <div className="flex justify-end gap-2">
              <button onClick={() => setRejectModalId(null)} className="px-3 py-1.5 rounded-xl border border-border text-xs font-bold text-foreground hover:bg-slate-100">
                Cancel
              </button>
              <button onClick={handleRejectSubmit} disabled={!rejectReason.trim()} className="px-3 py-1.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 disabled:opacity-50">
                Confirm Reject
              </button>
            </div>
          </div>
        </div>,
        document.body
      )}

      {/* Gallery Upload Modal Portal */}
      {isUploadModalOpen && createPortal(
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onClick={() => setIsUploadModalOpen(false)} />
          <div className="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl border border-border animate-in fade-in zoom-in-95 duration-200">
            <div className="flex items-start justify-between border-b border-border pb-3 mb-4">
              <h2 className="text-base font-bold text-foreground flex items-center gap-2">
                <Upload size={16} className="text-primary" /> Upload Event Media
              </h2>
              <button onClick={() => setIsUploadModalOpen(false)} className="rounded-lg p-1 text-muted hover:bg-slate-100 hover:text-foreground">
                <X size={18} />
              </button>
            </div>

            {uploadError && (
              <div className="rounded-xl bg-red-50 border border-red-100 p-3 text-xs font-semibold text-red-600 flex items-center gap-2 mb-4">
                <AlertCircle size={14} className="shrink-0" /><span>{uploadError}</span>
              </div>
            )}

            <form onSubmit={handleUploadSubmit} className="space-y-4">
              <div className="space-y-1.5">
                <label className="text-xs font-semibold text-foreground flex items-center justify-between">
                  <span>Select Files (Images, Videos or ZIP Archives)</span>
                  <span className="text-[10px] text-primary font-bold bg-primary-soft px-1.5 py-0.5 rounded">Multi-Select Enabled</span>
                </label>
                <div className="relative border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer flex flex-col items-center justify-center">
                  <input
                    id="event-gallery-input"
                    type="file"
                    multiple
                    accept="image/*,video/*,.zip,application/zip,application/x-zip-compressed"
                    onChange={handleFileChange}
                    className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                    required
                  />
                  <Upload className="h-6 w-6 text-primary mb-2" />
                  <span className="text-xs font-semibold text-foreground text-center line-clamp-1 px-2">
                    {mediaFiles.length > 0
                      ? `${mediaFiles.length} file${mediaFiles.length > 1 ? "s" : ""} selected (${mediaFiles.map(f => f.name).join(", ")})`
                      : "Click to select single/multiple files or ZIP archive"}
                  </span>
                  <span className="text-[10px] text-muted-foreground mt-1">Select multiple images/videos or ZIP archive (up to 100MB)</span>
                </div>
              </div>

              <div className="flex justify-end gap-2 border-t border-border pt-4 mt-2">
                <button type="button" onClick={() => setIsUploadModalOpen(false)} className="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50">
                  Cancel
                </button>
                <button type="submit" disabled={uploading} className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90 disabled:opacity-60 cursor-pointer">
                  {uploading ? "Uploading..." : <>Upload <Plus size={14} /></>}
                </button>
              </div>
            </form>
          </div>
        </div>,
        document.body
      )}

      {/* Lightbox Viewer Portal */}
      {selectedMedia && createPortal(
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/90 backdrop-blur-md" onClick={() => setSelectedMedia(null)} />
          <button onClick={() => setSelectedMedia(null)} className="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2">
            <X size={20} />
          </button>

          {gallery.length > 1 && (
            <button onClick={(e) => { e.stopPropagation(); const nextIdx = (lightboxIndex - 1 + gallery.length) % gallery.length; setLightboxIndex(nextIdx); setSelectedMedia(gallery[nextIdx]); }} className="absolute left-4 top-1/2 -translate-y-1/2 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2.5">
              <ChevronLeft size={22} />
            </button>
          )}

          {gallery.length > 1 && (
            <button onClick={(e) => { e.stopPropagation(); const nextIdx = (lightboxIndex + 1) % gallery.length; setLightboxIndex(nextIdx); setSelectedMedia(gallery[nextIdx]); }} className="absolute right-4 top-1/2 -translate-y-1/2 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2.5">
              <ChevronRight size={22} />
            </button>
          )}

          <div className="relative z-40 max-w-5xl w-full mx-auto px-14 flex flex-col items-center gap-4">
            {isVideoFile(selectedMedia.image_path) ? (
              <video src={getMediaUrl(selectedMedia.image_path)} controls autoPlay className="max-h-[75vh] max-w-full rounded-2xl shadow-2xl" />
            ) : (
              <img src={getMediaUrl(selectedMedia.image_path)} alt={selectedMedia.caption || "Event image"} className="max-h-[75vh] max-w-full object-contain rounded-2xl shadow-2xl" />
            )}
            <div className="flex flex-col items-center gap-1 text-center">
              {selectedMedia.caption && <p className="text-sm font-medium text-white/80 max-w-xl">{selectedMedia.caption}</p>}
              <p className="text-[10px] text-white/30">{lightboxIndex + 1} / {gallery.length}</p>
            </div>
          </div>
        </div>,
        document.body
      )}

      {/* Confirm Delete Media Modal */}
      <ConfirmModal
        isOpen={deleteMediaId !== null}
        title="Delete Event Media"
        message="Are you sure you want to delete this media item from this event? This action cannot be undone."
        confirmLabel="Delete"
        cancelLabel="Cancel"
        variant="danger"
        isLoading={isDeletingMedia}
        onConfirm={handleDeleteMedia}
        onCancel={() => setDeleteMediaId(null)}
      />
    </div>
  );
}

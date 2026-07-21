"use client";

import { useState, useEffect } from "react";
import { createPortal } from "react-dom";
import Link from "next/link";
import { createEventAdmin, updateEventAdmin, fetchEvents, getAllEventRegistrations } from "@/lib/api";
import { assetUrl } from "@/lib/config";
import {
  Calendar, Info, PlusCircle, CheckCircle2, Eye, Pencil, X, Search, Mail, ChevronLeft, ChevronRight, Upload
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";

export default function AdminEventsPage() {
  const [formData, setFormData] = useState({
    title: "",
    description: "",
    date: "",
    location: "",
    type: "Mixer",
    price_normal: "",
    price_verified: "",
  });
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [editingEventId, setEditingEventId] = useState<number | null>(null);

  const [events, setEvents] = useState<any[]>([]);
  const [eventsLoading, setEventsLoading] = useState(true);

  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);

  const [agenda, setAgenda] = useState<string[]>([]);
  const [speakers, setSpeakers] = useState<{ name: string; role: string; bio: string }[]>([]);
  const [imageFile, setImageFile] = useState<File | null>(null);
  const [imagePreview, setImagePreview] = useState<string>("");

  const [selectedEvent, setSelectedEvent] = useState<any | null>(null);
  const [registrations, setRegistrations] = useState<any[]>([]);
  const [registrationsLoading, setRegistrationsLoading] = useState(false);
  const [memberSearch, setMemberSearch] = useState("");
  const [memberFilter, setMemberFilter] = useState<"all" | "pending" | "approved" | "rejected">("all");
  const [currentPage, setCurrentPage] = useState(1);
  const ITEMS_PER_PAGE = 10;

  const handleViewMembers = async (event: any) => {
    setSelectedEvent(event);
    setRegistrationsLoading(true);
    setRegistrations([]);
    setMemberSearch("");
    setMemberFilter("all");
    setCurrentPage(1);
    try {
      const data = await getAllEventRegistrations();
      const eventRegistrations = (data || []).filter((reg: any) => reg.event_id === event.id);
      setRegistrations(eventRegistrations);
    } catch (error) {
      console.error("Failed to load event registrations", error);
    } finally {
      setRegistrationsLoading(false);
    }
  };

  const loadEvents = async () => {
    try {
      setEventsLoading(true);
      const data = await fetchEvents();
      setEvents(data || []);
    } catch (error) {
      console.error("Failed to load events", error);
    } finally {
      setEventsLoading(false);
    }
  };

  useEffect(() => {
    loadEvents();
  }, []);

  const handleEditEvent = (evt: any) => {
    setEditingEventId(evt.id);
    
    let formattedDate = "";
    if (evt.date) {
      const d = new Date(evt.date);
      if (!isNaN(d.getTime())) {
        formattedDate = d.toISOString().split("T")[0];
      }
    }

    setFormData({
      title: evt.title || "",
      description: evt.description || "",
      date: formattedDate,
      location: evt.location || "",
      type: evt.type || "Mixer",
      price_normal: evt.price_normal || "",
      price_verified: evt.price_verified || "",
    });

    setAgenda(Array.isArray(evt.agenda) ? evt.agenda : []);
    setSpeakers(Array.isArray(evt.speakers) ? evt.speakers : []);
    setImageFile(null);
    setImagePreview(evt.image ? assetUrl(evt.image) : "");
    setIsCreateModalOpen(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      const payload = new FormData();
      payload.append("title", formData.title);
      payload.append("description", formData.description);
      payload.append("date", formData.date);
      payload.append("location", formData.location);
      payload.append("type", formData.type);
      payload.append("price_normal", formData.price_normal);
      payload.append("price_verified", formData.price_verified);
      payload.append("agenda", JSON.stringify(agenda.filter(item => item.trim() !== "")));
      payload.append("speakers", JSON.stringify(speakers.filter(s => s.name.trim() !== "")));

      if (imageFile) {
        payload.append("image", imageFile);
      }

      if (editingEventId) {
        await updateEventAdmin(editingEventId, payload);
      } else {
        await createEventAdmin(payload);
      }

      setSuccess(true);
      setFormData({
        title: "",
        description: "",
        date: "",
        location: "",
        type: "Mixer",
        price_normal: "",
        price_verified: "",
      });
      setAgenda([]);
      setSpeakers([]);
      setImageFile(null);
      setImagePreview("");
      setEditingEventId(null);
      loadEvents();
      setTimeout(() => {
        setSuccess(false);
        setIsCreateModalOpen(false);
      }, 1500);
    } catch (error) {
      alert(editingEventId ? "Failed to update event." : "Failed to create event.");
    } finally {
      setLoading(false);
    }
  };

  const inputClass = "w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary";
  const labelClass = "text-xs font-semibold text-foreground";

  return (
    <div className="space-y-3 max-w-6xl">
      {/* Header section with Create Button */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 className="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Events</h1>
          <p className="text-xs text-muted">Manage and monitor community events</p>
        </div>
        <button
          onClick={() => {
            setEditingEventId(null);
            setFormData({
              title: "",
              description: "",
              date: "",
              location: "",
              type: "Mixer",
              price_normal: "",
              price_verified: "",
            });
            setAgenda([]);
            setSpeakers([]);
            setImageFile(null);
            setImagePreview("");
            setIsCreateModalOpen(true);
          }}
          className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
        >
          <PlusCircle size={16} />
          Create Event
        </button>
      </div>

      <div className="space-y-3 pt-1">
        <div className="glass-card overflow-hidden p-0">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="bg-surface border-b border-border">
                <th className="px-4 py-2.5 text-xs font-bold text-muted">Event Details</th>
                <th className="px-4 py-2.5 text-xs font-bold text-muted">Date & Location</th>
                <th className="px-4 py-2.5 text-xs font-bold text-muted">Normal Price</th>
                <th className="px-4 py-2.5 text-xs font-bold text-muted">Verified Price</th>
                <th className="px-4 py-2.5 text-xs font-bold text-muted text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {eventsLoading ? (
                <tr>
                  <td colSpan={5} className="py-8 text-center text-xs text-muted font-medium">
                    <div className="inline-block animate-spin rounded-full h-4 w-4 border-2 border-primary border-t-transparent mr-2" />
                    Loading registered events...
                  </td>
                </tr>
              ) : events.length === 0 ? (
                <tr>
                  <td colSpan={5} className="py-10 text-center text-xs text-muted font-medium">
                    No registered events found. Create one above to get started.
                  </td>
                </tr>
              ) : (
                events.map((evt) => (
                  <tr key={evt.id} className="transition-colors hover:bg-surface/50">
                    <td className="px-4 py-2.5">
                      <div>
                        <p className="text-xs font-bold text-foreground">{evt.title}</p>
                        <span className="inline-block rounded-full bg-primary-soft px-2 py-0.5 text-[10px] font-bold text-primary mt-0.5">
                          {evt.type}
                        </span>
                      </div>
                    </td>
                    <td className="px-4 py-2.5">
                      <p className="text-xs font-semibold text-foreground">
                        {new Date(evt.date).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}
                      </p>
                      <p className="text-[10px] text-muted-foreground mt-0.5">{evt.location}</p>
                    </td>
                    <td className="px-4 py-2.5 font-bold text-xs text-foreground">
                      {evt.price_normal || "N/A"}
                    </td>
                    <td className="px-4 py-2.5 font-bold text-xs text-primary">
                      {evt.price_verified || "N/A"}
                    </td>
                    <td className="px-4 py-2.5 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <Link
                          href={`/admin/events/${evt.id}`}
                          id={`view-members-${evt.id}`}
                          className="inline-flex items-center gap-1 rounded-lg bg-primary-soft hover:bg-primary hover:text-white px-2.5 py-1.5 text-[11px] font-bold text-primary transition-all cursor-pointer"
                        >
                          <Eye size={12} />
                          View
                        </Link>
                        <button
                          onClick={() => handleEditEvent(evt)}
                          id={`edit-event-${evt.id}`}
                          className="inline-flex items-center gap-1 rounded-lg bg-amber-50 hover:bg-amber-500 hover:text-white px-2.5 py-1.5 text-[11px] font-bold text-amber-700 transition-all cursor-pointer border border-amber-200/50"
                        >
                          <Pencil size={12} />
                          Edit
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* <div className="glass-card p-6 flex items-start gap-4">
        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
          <Calendar className="h-6 w-6" />
        </div>
        <div>
          <h4 className="text-lg font-semibold text-foreground mb-1">Tips for event pricing</h4>
          <p className="text-sm leading-relaxed text-muted">
            Offering discount prices for verified members (verified business profile added) encourages normal users to submit their business details and receipt screenshot, boosting directory listings and community growth.
          </p>
        </div>
      </div> */}

      {/* View Members Modal */}
      <AnimatePresence>
        {selectedEvent && (
          <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="bg-white rounded-2xl w-full max-w-2xl overflow-hidden border border-border shadow-2xl flex flex-col max-h-[85vh]"
            >
              {/* Modal Header */}
              <div className="flex items-center justify-between px-6 py-4 border-b border-border bg-surface">
                <div>
                  <h3 className="text-lg font-bold text-foreground">{selectedEvent.title}</h3>
                  <p className="text-xs text-muted-foreground mt-0.5">
                    {selectedEvent.type} • {new Date(selectedEvent.date).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })} • {selectedEvent.location}
                  </p>
                </div>
                <button
                  onClick={() => setSelectedEvent(null)}
                  className="p-1.5 rounded-lg text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors cursor-pointer"
                >
                  <X size={18} />
                </button>
              </div>

              {/* Modal Filters & Search */}
              <div className="px-6 py-4 border-b border-border bg-white flex flex-col sm:flex-row gap-3 items-center justify-between">
                {/* Search Bar */}
                <div className="relative w-full sm:max-w-xs">
                  <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <input
                    type="text"
                    placeholder="Search by name or email..."
                    value={memberSearch}
                    onChange={(e) => {
                      setMemberSearch(e.target.value);
                      setCurrentPage(1);
                    }}
                    className="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-border outline-none transition-colors focus:border-primary placeholder:text-muted-foreground/60"
                  />
                </div>

                {/* Filter Tabs */}
                <div className="flex items-center gap-1 bg-surface p-1 rounded-xl border border-border self-stretch sm:self-auto justify-center">
                  {(["all", "pending", "approved", "rejected"] as const).map((status) => (
                    <button
                      key={status}
                      onClick={() => {
                        setMemberFilter(status);
                        setCurrentPage(1);
                      }}
                      className={`px-3 py-1.5 text-xs font-semibold rounded-lg capitalize transition-all cursor-pointer ${memberFilter === status
                          ? "bg-white text-foreground shadow-sm"
                          : "text-muted hover:text-foreground"
                        }`}
                    >
                      {status}
                    </button>
                  ))}
                </div>
              </div>

              {/* Modal Content / Member List */}
              <div className="flex-1 overflow-y-auto p-6 space-y-4">
                {registrationsLoading ? (
                  <div className="py-20 text-center text-sm text-muted">
                    <div className="inline-block animate-spin rounded-full h-6 w-6 border-2 border-primary border-t-transparent mr-2" />
                    Loading registered members...
                  </div>
                ) : (() => {
                  const filtered = registrations.filter((reg) => {
                    const matchesSearch =
                      reg.user?.name?.toLowerCase().includes(memberSearch.toLowerCase()) ||
                      reg.user?.email?.toLowerCase().includes(memberSearch.toLowerCase());
                    const matchesFilter =
                      memberFilter === "all" || reg.status === memberFilter;
                    return matchesSearch && matchesFilter;
                  });

                  if (filtered.length === 0) {
                    return (
                      <div className="py-16 text-center text-sm text-muted border border-dashed border-border rounded-xl">
                        No members found matching the criteria.
                      </div>
                    );
                  }

                  const totalItems = filtered.length;
                  const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE) || 1;
                  const activePage = Math.min(currentPage, totalPages);
                  const startIndex = (activePage - 1) * ITEMS_PER_PAGE;
                  const paginated = filtered.slice(startIndex, startIndex + ITEMS_PER_PAGE);

                  return (
                    <div className="flex flex-wrap gap-4">
                      {paginated.map((reg) => (
                        <div key={reg.id} className="glass-card p-4 border border-border flex flex-col justify-between gap-3.5 hover:border-primary/40 transition-all hover:shadow-md rounded-2xl bg-surface w-full sm:w-[calc(50%-8px)] md:w-[calc(33.33%-11px)] max-w-[340px]">
                          {/* Attendee Info Header */}
                          <div className="flex items-center gap-3">
                            <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-base font-bold text-primary">
                              {reg.user?.name?.[0] ?? "?"}
                            </div>
                            <div className="min-w-0 flex-1">
                              <h4 className="text-sm font-bold text-foreground truncate">{reg.user?.name || "Unknown"}</h4>
                              <p className="text-xs text-muted-foreground flex items-center gap-1.5 mt-0.5 truncate">
                                <Mail size={12} className="shrink-0 text-muted" />
                                <span className="truncate">{reg.user?.email}</span>
                              </p>
                            </div>
                          </div>

                          {/* Subtle Separator */}
                          <div className="border-t border-border/80" />

                          {/* Member Booking Info */}
                          <div className="flex items-center justify-between gap-2 text-xs">
                            <div className="min-w-0">
                              <span className={`inline-block rounded px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider ${reg.ticket_type === "verified"
                                  ? "text-emerald-700 bg-emerald-50 border border-emerald-100"
                                  : "text-muted bg-slate-100 border border-slate-200"
                                }`}>
                                {reg.ticket_type === "verified" ? "Sabha Member" : "Standard"}
                              </span>
                              <p className="text-[10px] text-muted-foreground mt-1.5 font-mono truncate">
                                Tkt: {reg.ticket_number || "Pending"}
                              </p>
                            </div>

                            <div className="flex flex-col items-end gap-1.5 shrink-0">
                              <div>
                                <span className={`px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wide inline-block ${reg.status === "pending"
                                    ? "bg-amber-50 text-amber-700 border border-amber-100"
                                    : reg.status === "approved" || reg.status === "confirmed"
                                      ? "bg-emerald-50 text-emerald-700 border border-emerald-100"
                                      : "bg-red-50 text-red-700 border border-red-100"
                                  }`}>
                                  {reg.status}
                                </span>
                              </div>
                              {reg.is_attended && (
                                <span className="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-600 bg-emerald-50/50 px-1.5 py-0.5 rounded-md border border-emerald-100/50">
                                  Attended
                                </span>
                              )}
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  );
                })()}
              </div>

              {/* Pagination Controls Bar */}
              {!registrationsLoading && registrations.length > 0 && (
                (() => {
                  const filtered = registrations.filter((reg) => {
                    const matchesSearch =
                      reg.user?.name?.toLowerCase().includes(memberSearch.toLowerCase()) ||
                      reg.user?.email?.toLowerCase().includes(memberSearch.toLowerCase());
                    const matchesFilter =
                      memberFilter === "all" || reg.status === memberFilter;
                    return matchesSearch && matchesFilter;
                  });
                  const totalItems = filtered.length;
                  const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE) || 1;
                  const activePage = Math.min(currentPage, totalPages);

                  if (totalItems === 0) return null;

                  return (
                    <div className="px-6 py-3 border-t border-border bg-white flex items-center justify-between text-xs">
                      <span className="text-muted-foreground">
                        Showing <span className="font-semibold text-foreground">{Math.min(totalItems, (activePage - 1) * ITEMS_PER_PAGE + 1)}</span> to{" "}
                        <span className="font-semibold text-foreground">{Math.min(totalItems, activePage * ITEMS_PER_PAGE)}</span> of{" "}
                        <span className="font-semibold text-foreground">{totalItems}</span> members
                      </span>

                      {totalPages > 1 && (
                        <div className="flex items-center gap-2">
                          <button
                            disabled={activePage === 1}
                            onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
                            className="p-1 rounded-lg border border-border bg-white text-muted-foreground hover:bg-slate-50 disabled:opacity-50 disabled:hover:bg-white transition-all cursor-pointer"
                          >
                            <ChevronLeft size={16} />
                          </button>
                          <span className="text-muted-foreground font-medium">
                            Page <span className="text-foreground">{activePage}</span> of {totalPages}
                          </span>
                          <button
                            disabled={activePage === totalPages}
                            onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
                            className="p-1 rounded-lg border border-border bg-white text-muted-foreground hover:bg-slate-50 disabled:opacity-50 disabled:hover:bg-white transition-all cursor-pointer"
                          >
                            <ChevronRight size={16} />
                          </button>
                        </div>
                      )}
                    </div>
                  );
                })()
              )}

              {/* Modal Footer */}
              <div className="px-6 py-4 border-t border-border bg-surface flex justify-between items-center text-xs text-muted-foreground">
                <div>
                  Total: <span className="font-bold text-foreground">{registrations.length}</span> registrations
                </div>
                <button
                  onClick={() => setSelectedEvent(null)}
                  className="rounded-xl border border-border bg-white px-4 py-2 text-xs font-semibold text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-sm"
                >
                  Close
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
      {/* Create Event Modal */}
      <AnimatePresence>
        {isCreateModalOpen && (
          <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="bg-white rounded-2xl w-full max-w-xl overflow-hidden border border-border shadow-2xl flex flex-col max-h-[90vh]"
            >
              {/* Modal Header */}
              <div className="flex items-center justify-between px-5 py-3.5 border-b border-border bg-surface">
                <div>
                  <h3 className="text-base font-bold text-foreground">
                    {editingEventId ? "Edit Event" : "Create New Event"}
                  </h3>
                  <p className="text-[11px] text-muted-foreground mt-0.5">
                    {editingEventId ? "Update the details of this event" : "Fill in the details below to publish an event"}
                  </p>
                </div>
                <button
                  type="button"
                  onClick={() => setIsCreateModalOpen(false)}
                  className="p-1 rounded-lg text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors cursor-pointer"
                >
                  <X size={16} />
                </button>
              </div>

              {/* Modal Form */}
              <form onSubmit={handleSubmit} className="flex flex-col overflow-hidden">
                <div className="p-5 space-y-4 overflow-y-auto max-h-[60vh]">
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                    <div className="space-y-1">
                      <label className={labelClass}>Event title</label>
                      <input
                        required
                        type="text"
                        placeholder="e.g. Sabha networking night"
                        className={inputClass}
                        value={formData.title}
                        onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                      />
                    </div>
                    <div className="space-y-1">
                      <label className={labelClass}>Location</label>
                      <input
                        required
                        type="text"
                        placeholder="e.g. Aloft Hotel, Ahmedabad"
                        className={inputClass}
                        value={formData.location}
                        onChange={(e) => setFormData({ ...formData, location: e.target.value })}
                      />
                    </div>

                    <div className="space-y-1">
                      <label className={labelClass}>Date</label>
                      <input
                        required
                        type="date"
                        className={inputClass}
                        value={formData.date}
                        onChange={(e) => setFormData({ ...formData, date: e.target.value })}
                      />
                    </div>
                    <div className="space-y-1">
                      <label className={labelClass}>Category</label>
                      <select
                        className={inputClass}
                        value={formData.type}
                        onChange={(e) => setFormData({ ...formData, type: e.target.value })}
                      >
                        <option value="Mixer">Mixer</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Summit">Summit</option>
                      </select>
                    </div>

                    <div className="space-y-1">
                      <label className={labelClass}>Price for Normal Member</label>
                      <input
                        required
                        type="text"
                        placeholder="e.g. ₹2,499 or Free"
                        className={inputClass}
                        value={formData.price_normal}
                        onChange={(e) => setFormData({ ...formData, price_normal: e.target.value })}
                      />
                    </div>
                    <div className="space-y-1">
                      <label className={labelClass}>Price for Verified Member</label>
                      <input
                        required
                        type="text"
                        placeholder="e.g. ₹1,499 or Free"
                        className={inputClass}
                        value={formData.price_verified}
                        onChange={(e) => setFormData({ ...formData, price_verified: e.target.value })}
                      />
                    </div>

                    <div className="space-y-1 sm:col-span-2">
                      <label className={labelClass}>Description</label>
                      <textarea
                        required
                        rows={2}
                        placeholder="What is this event about?"
                        className={inputClass}
                        value={formData.description}
                        onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                      />
                    </div>

                    {/* Cover Image Upload */}
                    <div className="space-y-1 sm:col-span-2">
                      <label className={labelClass}>Event Cover Image</label>
                      <div className="mt-1 flex items-center gap-4">
                        {imagePreview ? (
                          <div className="relative h-20 w-32 rounded-xl overflow-hidden border border-border bg-slate-50 group">
                            <img
                              src={imagePreview}
                              alt="Preview"
                              className="h-full w-full object-cover"
                            />
                            <button
                              type="button"
                              onClick={() => {
                                setImageFile(null);
                                setImagePreview("");
                              }}
                              className="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-semibold cursor-pointer"
                            >
                              Remove
                            </button>
                          </div>
                        ) : (
                          <label className="flex h-20 w-32 cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-border bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <Upload className="h-5 w-5 text-muted-foreground" />
                            <span className="text-[10px] text-muted-foreground mt-1 font-semibold">Upload Image</span>
                            <input
                              type="file"
                              accept="image/*"
                              className="hidden"
                              onChange={(e) => {
                                const file = e.target.files?.[0];
                                if (file) {
                                  setImageFile(file);
                                  const reader = new FileReader();
                                  reader.onloadend = () => {
                                    setImagePreview(reader.result as string);
                                  };
                                  reader.readAsDataURL(file);
                                }
                              }}
                            />
                          </label>
                        )}
                        <div className="text-[10px] text-muted-foreground">
                          <p className="font-semibold">Upload a high-quality cover photo.</p>
                          <p>Supports PNG, JPG, GIF, WebP (max 5MB).</p>
                        </div>
                      </div>
                    </div>

                    {/* Agenda Section */}
                    <div className="sm:col-span-2 border-t border-border pt-3">
                      <div className="flex items-center justify-between mb-2">
                        <label className={labelClass}>Agenda Items</label>
                        <button
                          type="button"
                          onClick={() => setAgenda([...agenda, ""])}
                          className="text-[10px] font-bold text-primary hover:underline flex items-center gap-1 cursor-pointer"
                        >
                          + Add Item
                        </button>
                      </div>
                      {agenda.length === 0 ? (
                        <p className="text-[10px] text-muted-foreground italic">No agenda items added yet.</p>
                      ) : (
                        <div className="space-y-2 max-h-[150px] overflow-y-auto pr-1">
                          {agenda.map((item, idx) => (
                            <div key={idx} className="flex items-center gap-2">
                              <span className="text-[10px] font-bold text-muted-foreground w-4">{idx + 1}.</span>
                              <input
                                type="text"
                                placeholder="e.g. Registration & Welcome"
                                className={inputClass}
                                value={item}
                                onChange={(e) => {
                                  const newAgenda = [...agenda];
                                  newAgenda[idx] = e.target.value;
                                  setAgenda(newAgenda);
                                }}
                              />
                              <button
                                type="button"
                                onClick={() => setAgenda(agenda.filter((_, i) => i !== idx))}
                                className="text-red-500 hover:text-red-700 p-1 cursor-pointer"
                              >
                                <X size={14} />
                              </button>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>

                    {/* Speakers Section */}
                    <div className="sm:col-span-2 border-t border-border pt-3">
                      <div className="flex items-center justify-between mb-2">
                        <label className={labelClass}>Speakers</label>
                        <button
                          type="button"
                          onClick={() => setSpeakers([...speakers, { name: "", role: "", bio: "" }])}
                          className="text-[10px] font-bold text-primary hover:underline flex items-center gap-1 cursor-pointer"
                        >
                          + Add Speaker
                        </button>
                      </div>
                      {speakers.length === 0 ? (
                        <p className="text-[10px] text-muted-foreground italic">No speakers added yet.</p>
                      ) : (
                        <div className="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                          {speakers.map((speaker, idx) => (
                            <div key={idx} className="p-3 bg-slate-50 rounded-xl border border-slate-200 relative space-y-2">
                              <button
                                type="button"
                                onClick={() => setSpeakers(speakers.filter((_, i) => i !== idx))}
                                className="absolute top-2 right-2 text-red-500 hover:text-red-700 cursor-pointer"
                              >
                                <X size={14} />
                              </button>
                              <div className="grid grid-cols-2 gap-2">
                                <div className="space-y-0.5">
                                  <label className="text-[10px] font-semibold text-muted-foreground">Speaker Name</label>
                                  <input
                                    required
                                    type="text"
                                    placeholder="Name"
                                    className={inputClass}
                                    value={speaker.name}
                                    onChange={(e) => {
                                      const newSpeakers = [...speakers];
                                      newSpeakers[idx] = { ...newSpeakers[idx], name: e.target.value };
                                      setSpeakers(newSpeakers);
                                    }}
                                  />
                                </div>
                                <div className="space-y-0.5">
                                  <label className="text-[10px] font-semibold text-muted-foreground">Role / Designation</label>
                                  <input
                                    type="text"
                                    placeholder="Role (e.g. CEO)"
                                    className={inputClass}
                                    value={speaker.role}
                                    onChange={(e) => {
                                      const newSpeakers = [...speakers];
                                      newSpeakers[idx] = { ...newSpeakers[idx], role: e.target.value };
                                      setSpeakers(newSpeakers);
                                    }}
                                  />
                                </div>
                              </div>
                              <div className="space-y-0.5">
                                <label className="text-[10px] font-semibold text-muted-foreground">Short Bio</label>
                                <textarea
                                  rows={1}
                                  placeholder="Brief description..."
                                  className={inputClass}
                                  value={speaker.bio}
                                  onChange={(e) => {
                                    const newSpeakers = [...speakers];
                                    newSpeakers[idx] = { ...newSpeakers[idx], bio: e.target.value };
                                    setSpeakers(newSpeakers);
                                  }}
                                />
                              </div>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>
                  </div>
                </div>

                {/* Modal Footer */}
                <div className="px-5 py-3 border-t border-border bg-surface flex flex-col sm:flex-row items-center justify-between gap-3">
                  <div className="flex items-center gap-1.5 text-muted-foreground text-[10px] self-start sm:self-auto">
                    <Info size={12} className="text-primary shrink-0" />
                    <span>Appears on public site immediately.</span>
                  </div>

                  <div className="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <button
                      type="button"
                      onClick={() => setIsCreateModalOpen(false)}
                      className="w-1/2 sm:w-auto rounded-xl border border-border bg-white px-3 py-1.5 text-[11px] font-semibold text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-sm"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      disabled={loading}
                      className="w-1/2 sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-3.5 py-1.5 text-[11px] font-semibold text-white transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer shadow-sm"
                    >
                      {success ? (
                        <motion.span initial={{ scale: 0.5 }} animate={{ scale: 1 }} className="flex items-center gap-1">
                          {editingEventId ? "Updated" : "Created"} <CheckCircle2 size={12} />
                        </motion.span>
                      ) : (
                        loading ? (editingEventId ? "Updating..." : "Creating...") : (
                          editingEventId ? <>Update Event <CheckCircle2 size={12} /></> : <>Create Event <PlusCircle size={12} /></>
                        )
                      )}
                    </button>
                  </div>
                </div>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}

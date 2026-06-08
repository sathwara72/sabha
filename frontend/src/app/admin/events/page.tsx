"use client";

import { useState, useEffect } from "react";
import { createEventAdmin, fetchEvents } from "@/lib/api";
import {
  Calendar, Info, PlusCircle, CheckCircle2
} from "lucide-react";
import { motion } from "framer-motion";

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
  
  const [events, setEvents] = useState<any[]>([]);
  const [eventsLoading, setEventsLoading] = useState(true);

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

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      await createEventAdmin(formData);
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
      loadEvents();
      setTimeout(() => setSuccess(false), 3000);
    } catch (error) {
      alert("Failed to create event.");
    } finally {
      setLoading(false);
    }
  };

  const inputClass = "w-full rounded-xl border border-border bg-white px-4 py-3 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary";
  const labelClass = "text-sm font-medium text-foreground";

  return (
    <div className="space-y-10 max-w-4xl">
      <div className="flex flex-col gap-2">
        <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Create event</h1>
        <p className="text-sm text-muted">Add a new community event</p>
      </div>

      <div className="glass-card p-6">
        <form className="space-y-6" onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-2">
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
            <div className="space-y-2">
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
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-2">
              <label className={labelClass}>Date</label>
              <input
                required
                type="date"
                className={inputClass}
                value={formData.date}
                onChange={(e) => setFormData({ ...formData, date: e.target.value })}
              />
            </div>
            <div className="space-y-2">
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
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-2">
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
            <div className="space-y-2">
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
          </div>

          <div className="space-y-2">
            <label className={labelClass}>Description</label>
            <textarea
              required
              rows={4}
              placeholder="What is this event about?"
              className={inputClass}
              value={formData.description}
              onChange={(e) => setFormData({ ...formData, description: e.target.value })}
            />
          </div>

          <div className="pt-5 flex flex-col md:flex-row items-center justify-between gap-5 border-t border-border">
            <div className="flex items-center gap-2 text-muted">
              <Info size={15} className="text-primary" />
              <p className="text-sm">Published events appear on the public site right away.</p>
            </div>

            <button
              disabled={loading}
              className="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
            >
              {success ? (
                <motion.span initial={{ scale: 0.5 }} animate={{ scale: 1 }} className="flex items-center gap-2">
                  Created <CheckCircle2 size={16} />
                </motion.span>
              ) : (
                loading ? "Processing..." : <>Create event <PlusCircle size={16} /></>
              )}
            </button>
          </div>
        </form>
      </div>

      <div className="space-y-4 border-t border-border pt-8">
        <div className="flex items-center justify-between">
          <div>
            <h2 className="text-xl font-bold tracking-tight text-foreground">Registered Events</h2>
            <p className="text-sm text-muted">View existing events and their member prices</p>
          </div>
          <span className="text-sm font-semibold text-muted">
            {events.length} {events.length === 1 ? "event" : "events"}
          </span>
        </div>

        <div className="glass-card overflow-hidden p-0">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="bg-surface border-b border-border">
                <th className="px-6 py-4 text-sm font-semibold text-muted">Event Details</th>
                <th className="px-6 py-4 text-sm font-semibold text-muted">Date & Location</th>
                <th className="px-6 py-4 text-sm font-semibold text-muted">Normal Price</th>
                <th className="px-6 py-4 text-sm font-semibold text-muted">Verified Price</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {eventsLoading ? (
                <tr>
                  <td colSpan={4} className="py-10 text-center text-sm text-muted">
                    <div className="inline-block animate-spin rounded-full h-5 w-5 border-2 border-primary border-t-transparent mr-2" />
                    Loading registered events...
                  </td>
                </tr>
              ) : events.length === 0 ? (
                <tr>
                  <td colSpan={4} className="py-12 text-center text-sm text-muted">
                    No registered events found. Create one above to get started.
                  </td>
                </tr>
              ) : (
                events.map((evt) => (
                  <tr key={evt.id} className="transition-colors hover:bg-surface">
                    <td className="px-6 py-4">
                      <div>
                        <p className="text-sm font-semibold text-foreground">{evt.title}</p>
                        <span className="inline-block rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary mt-1">
                          {evt.type}
                        </span>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <p className="text-sm font-medium text-foreground">
                        {new Date(evt.date).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}
                      </p>
                      <p className="text-xs text-muted-foreground mt-0.5">{evt.location}</p>
                    </td>
                    <td className="px-6 py-4 font-semibold text-sm text-foreground">
                      {evt.price_normal || "N/A"}
                    </td>
                    <td className="px-6 py-4 font-semibold text-sm text-primary">
                      {evt.price_verified || "N/A"}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      <div className="glass-card p-6 flex items-start gap-4">
        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
          <Calendar className="h-6 w-6" />
        </div>
        <div>
          <h4 className="text-lg font-semibold text-foreground mb-1">Tips for event pricing</h4>
          <p className="text-sm leading-relaxed text-muted">
            Offering discount prices for verified members (verified business profile added) encourages normal users to submit their business details and receipt screenshot, boosting directory listings and community growth.
          </p>
        </div>
      </div>
    </div>
  );
}

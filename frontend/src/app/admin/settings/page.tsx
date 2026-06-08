"use client";

import { useEffect, useState } from "react";
import { fetchSettings, updateSettingsAdmin } from "@/lib/api";
import {
  Settings as SettingsIcon, Save, CheckCircle2, AlertCircle,
  RefreshCw, Plus, Trash2, Mail, Clock, ShieldCheck
} from "lucide-react";

interface Coordinator {
  city: string;
  contact: string;
  phone: string;
  email: string;
}

export default function AdminSettingsPage() {
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [successMsg, setSuccessMsg] = useState("");
  const [errorMsg, setErrorMsg] = useState("");

  const [contactEmail, setContactEmail] = useState("");
  const [responseTime, setResponseTime] = useState("");
  const [coordinators, setCoordinators] = useState<Coordinator[]>([]);

  useEffect(() => {
    loadData();
  }, []);

  async function loadData() {
    try {
      setLoading(true);
      setErrorMsg("");
      const data = await fetchSettings();
      setContactEmail(data.contact_email || "hello@sabha.global");
      setResponseTime(data.response_time || "Within 1 Business Day");
      
      let coords: Coordinator[] = [];
      if (data.coordinators) {
        try {
          coords = typeof data.coordinators === "string" 
            ? JSON.parse(data.coordinators) 
            : data.coordinators;
        } catch (e) {
          console.error("Failed to parse coordinators JSON:", e);
        }
      }
      setCoordinators(coords);
    } catch (err) {
      console.error("Error loading settings:", err);
      setErrorMsg("Failed to load settings from database.");
    } finally {
      setLoading(false);
    }
  }

  const handleCoordinatorChange = (index: number, field: keyof Coordinator, value: string) => {
    const updated = [...coordinators];
    updated[index] = {
      ...updated[index],
      [field]: value
    };
    setCoordinators(updated);
  };

  const handleAddCoordinator = () => {
    setCoordinators([
      ...coordinators,
      { city: "New Coordinator", contact: "", phone: "", email: "" }
    ]);
  };

  const handleRemoveCoordinator = (index: number) => {
    setCoordinators(coordinators.filter((_, i) => i !== index));
  };

  const handleSave = async () => {
    setSaving(true);
    setErrorMsg("");
    setSuccessMsg("");

    try {
      const payload = {
        contact_email: contactEmail,
        response_time: responseTime,
        coordinators: coordinators
      };
      await updateSettingsAdmin(payload);
      setSuccessMsg("Site settings updated successfully!");
      await loadData();
      setTimeout(() => setSuccessMsg(""), 4000);
    } catch (err: any) {
      setErrorMsg(err.message || "Failed to save settings.");
    } finally {
      setSaving(false);
    }
  };

  const inputClass = "w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold";
  const labelClass = "text-xs font-semibold text-muted mb-1 block";

  return (
    <div className="space-y-10 max-w-5xl">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div className="flex flex-col gap-2">
          <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Site Settings</h1>
          <p className="text-sm text-muted">Dynamically manage website contact details, coordinator rosters, and details</p>
        </div>
        <button
          onClick={loadData}
          className="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-border bg-white text-muted hover:bg-surface hover:text-foreground cursor-pointer transition-colors"
          title="Refresh Data"
        >
          <RefreshCw size={16} />
        </button>
      </div>

      {successMsg && (
        <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-sm font-semibold text-emerald-800 flex items-center gap-3">
          <CheckCircle2 className="h-5 w-5 text-emerald-600 shrink-0" />
          <span>{successMsg}</span>
        </div>
      )}

      {errorMsg && (
        <div className="rounded-xl bg-red-50 border border-red-100 p-4 text-sm font-semibold text-red-800 flex items-center gap-3">
          <AlertCircle className="h-5 w-5 text-red-600 shrink-0" />
          <span>{errorMsg}</span>
        </div>
      )}

      {loading ? (
        <div className="py-20 text-center">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="mt-3 text-sm text-muted">Loading configurations...</p>
        </div>
      ) : (
        <div className="space-y-8">
          {/* General Section */}
          <div className="glass-card p-6 space-y-6">
            <h3 className="text-lg font-bold text-foreground border-b border-border pb-3 flex items-center gap-2">
              <SettingsIcon size={18} className="text-primary" /> General Contact Info
            </h3>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className={labelClass}>General Contact Email</label>
                <input
                  type="email"
                  value={contactEmail}
                  onChange={(e) => setContactEmail(e.target.value)}
                  className={inputClass}
                  placeholder="e.g. hello@sabha.global"
                />
              </div>

              <div>
                <label className={labelClass}>Expected Response Time</label>
                <input
                  type="text"
                  value={responseTime}
                  onChange={(e) => setResponseTime(e.target.value)}
                  className={inputClass}
                  placeholder="e.g. Within 1 Business Day"
                />
              </div>
            </div>
          </div>

          {/* Coordinators Section */}
          <div className="glass-card p-6 space-y-6">
            <div className="flex items-center justify-between border-b border-border pb-3">
              <h3 className="text-lg font-bold text-foreground flex items-center gap-2">
                <Mail size={18} className="text-primary" /> Regional Coordinators
              </h3>
              <button
                type="button"
                onClick={handleAddCoordinator}
                className="inline-flex items-center gap-1 text-xs font-bold text-primary bg-primary-soft hover:opacity-90 px-3.5 py-2 rounded-xl transition-all cursor-pointer"
              >
                <Plus size={14} /> Add Coordinator
              </button>
            </div>

            {coordinators.length === 0 ? (
              <p className="text-xs text-muted italic text-center py-6">No coordinators added. Click "Add Coordinator" to define one.</p>
            ) : (
              <div className="space-y-4">
                {coordinators.map((coordinator, idx) => (
                  <div key={idx} className="p-5 rounded-2xl border border-border bg-surface/30 relative grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                      <label className={labelClass}>Region / Title</label>
                      <input
                        type="text"
                        value={coordinator.city}
                        onChange={(e) => handleCoordinatorChange(idx, "city", e.target.value)}
                        className={inputClass}
                        placeholder="e.g. Mumbai Coordinator"
                      />
                    </div>
                    <div>
                      <label className={labelClass}>Contact Person</label>
                      <input
                        type="text"
                        value={coordinator.contact}
                        onChange={(e) => handleCoordinatorChange(idx, "contact", e.target.value)}
                        className={inputClass}
                        placeholder="e.g. Ravi Sharma"
                      />
                    </div>
                    <div>
                      <label className={labelClass}>Phone Number</label>
                      <input
                        type="text"
                        value={coordinator.phone}
                        onChange={(e) => handleCoordinatorChange(idx, "phone", e.target.value)}
                        className={inputClass}
                        placeholder="e.g. +91 98200 12345"
                      />
                    </div>
                    <div className="flex gap-2 items-center">
                      <div className="flex-1">
                        <label className={labelClass}>Email Address</label>
                        <input
                          type="email"
                          value={coordinator.email}
                          onChange={(e) => handleCoordinatorChange(idx, "email", e.target.value)}
                          className={inputClass}
                          placeholder="e.g. mumbai@sabha.global"
                        />
                      </div>
                      <button
                        type="button"
                        onClick={() => handleRemoveCoordinator(idx)}
                        className="h-10 w-10 shrink-0 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center hover:bg-red-100 transition-colors cursor-pointer self-end mb-0.5"
                        title="Delete Coordinator"
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Action Bar */}
          <div className="flex items-center justify-end pt-4 gap-4">
            <button
              onClick={handleSave}
              disabled={saving}
              className="inline-flex items-center gap-1.5 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
            >
              <Save size={15} />
              {saving ? "Saving settings..." : "Save Settings"}
            </button>
          </div>
        </div>
      )}

      <div className="glass-card p-6 flex items-start gap-4">
        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
          <ShieldCheck className="h-6 w-6" />
        </div>
        <div>
          <h4 className="text-lg font-semibold text-foreground mb-1">About dynamic configs</h4>
          <p className="text-sm leading-relaxed text-muted font-medium">
            Modifications saved here are immediately populated to the Contact page. Dynamic configurations maintain uniform coordination values across all member interactions.
          </p>
        </div>
      </div>
    </div>
  );
}

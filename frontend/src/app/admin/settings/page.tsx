"use client";

import { useEffect, useState } from "react";
import { fetchSettings, updateSettingsAdmin, uploadGalleryImage } from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";
import {
  Settings as SettingsIcon, Save, CheckCircle2, AlertCircle,
  RefreshCw, Plus, Trash2, Mail, ShieldCheck, Users
} from "lucide-react";

interface Coordinator {
  city: string;
  contact: string;
  phone: string;
  email: string;
}

interface Trustee {
  name: string;
  role: string;
  company: string;
  avatar: string;
}

export default function AdminSettingsPage() {
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [successMsg, setSuccessMsg] = useState("");
  const [errorMsg, setErrorMsg] = useState("");

  const [contactEmail, setContactEmail] = useState("");
  const [responseTime, setResponseTime] = useState("");
  const [coordinators, setCoordinators] = useState<Coordinator[]>([]);
  const [trustees, setTrustees] = useState<Trustee[]>([]);

  useEffect(() => { loadData(); }, []);

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

      let trs: Trustee[] = [];
      if (data.trustees) {
        try {
          trs = typeof data.trustees === "string"
            ? JSON.parse(data.trustees)
            : data.trustees;
        } catch (e) {
          console.error("Failed to parse trustees JSON:", e);
        }
      }
      setTrustees(trs);
    } catch (err) {
      console.error("Error loading settings:", err);
      setErrorMsg("Failed to load settings from database.");
    } finally {
      setLoading(false);
    }
  }

  const handleCoordinatorChange = (index: number, field: keyof Coordinator, value: string) => {
    const updated = [...coordinators];
    updated[index] = { ...updated[index], [field]: value };
    setCoordinators(updated);
  };

  const handleAddCoordinator = () => {
    setCoordinators([...coordinators, { city: "New Coordinator", contact: "", phone: "", email: "" }]);
  };

  const handleRemoveCoordinator = (index: number) => {
    setCoordinators(coordinators.filter((_, i) => i !== index));
  };

  const handleTrusteeChange = (index: number, field: keyof Trustee, value: string) => {
    const updated = [...trustees];
    updated[index] = { ...updated[index], [field]: value };
    setTrustees(updated);
  };

  const handleAddTrustee = () => {
    setTrustees([...trustees, { name: "New Trustee", role: "", company: "", avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150&auto=format&fit=crop" }]);
  };

  const handleRemoveTrustee = (index: number) => {
    setTrustees(trustees.filter((_, i) => i !== index));
  };

  const handleSave = async () => {
    setSaving(true);
    setErrorMsg("");
    setSuccessMsg("");
    try {
      await updateSettingsAdmin({ 
        contact_email: contactEmail, 
        response_time: responseTime, 
        coordinators, 
        trustees 
      });
      setSuccessMsg("Site settings updated successfully!");
      await loadData();
      setTimeout(() => setSuccessMsg(""), 4000);
    } catch (err: any) {
      setErrorMsg(err.message || "Failed to save settings.");
    } finally {
      setSaving(false);
    }
  };

  const inputClass = "w-full rounded-lg border border-border bg-white px-3 py-1.5 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold";
  const labelClass = "text-[10px] font-bold text-muted uppercase tracking-wider mb-0.5 block";

  return (
    <div className="space-y-3">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div className="flex flex-col">
          <h1 className="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Site Settings</h1>
          <p className="text-xs text-muted">Manage website contact details and coordinator roster</p>
        </div>
        <button
          onClick={loadData}
          className="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-border bg-white text-muted hover:bg-surface hover:text-foreground cursor-pointer transition-colors self-start sm:self-auto"
          title="Refresh Data"
        >
          <RefreshCw size={14} />
        </button>
      </div>

      {/* Alerts */}
      {successMsg && (
        <div className="rounded-xl bg-emerald-50 border border-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-800 flex items-center gap-2">
          <CheckCircle2 className="h-4 w-4 text-emerald-600 shrink-0" />
          <span>{successMsg}</span>
        </div>
      )}
      {errorMsg && (
        <div className="rounded-xl bg-red-50 border border-red-100 px-3 py-2 text-xs font-semibold text-red-800 flex items-center gap-2">
          <AlertCircle className="h-4 w-4 text-red-600 shrink-0" />
          <span>{errorMsg}</span>
        </div>
      )}

      {loading ? (
        <div className="py-20 text-center">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="mt-3 text-sm text-muted">Loading configurations...</p>
        </div>
      ) : (
        <div className="space-y-3">
          {/* General Contact */}
          <div className="glass-card p-4 space-y-3">
            <h3 className="text-xs font-bold text-foreground border-b border-border pb-2 flex items-center gap-1.5">
              <SettingsIcon size={14} className="text-primary" /> General Contact Info
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
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

          {/* Coordinators */}
          <div className="glass-card p-4 space-y-3">
            <div className="flex items-center justify-between border-b border-border pb-2">
              <h3 className="text-xs font-bold text-foreground flex items-center gap-1.5">
                <Mail size={14} className="text-primary" /> Regional Coordinators
              </h3>
              <button
                type="button"
                onClick={handleAddCoordinator}
                className="inline-flex items-center gap-1 text-[11px] font-bold text-primary bg-primary-soft hover:opacity-90 px-2.5 py-1.5 rounded-lg transition-all cursor-pointer"
              >
                <Plus size={12} /> Add Coordinator
              </button>
            </div>

            {coordinators.length === 0 ? (
              <p className="text-xs text-muted italic text-center py-4">No coordinators added. Click "Add Coordinator" to define one.</p>
            ) : (
              <div className="space-y-2">
                {coordinators.map((coordinator, idx) => (
                  <div key={idx} className="p-3 rounded-xl border border-border bg-surface/30 grid grid-cols-2 md:grid-cols-4 gap-2 items-end">
                    <div>
                      <label className={labelClass}>Region / Title</label>
                      <input
                        type="text"
                        value={coordinator.city}
                        onChange={(e) => handleCoordinatorChange(idx, "city", e.target.value)}
                        className={inputClass}
                        placeholder="e.g. Mumbai"
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
                        onChange={(e) => {
                          const val = e.target.value.replace(/\D/g, "").slice(0, 10);
                          handleCoordinatorChange(idx, "phone", val);
                        }}
                        className={inputClass}
                        placeholder="10-digit mobile number"
                      />
                    </div>
                    <div className="flex gap-1.5 items-end">
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
                        className="h-[30px] w-[30px] shrink-0 rounded-lg bg-red-50 text-red-600 border border-red-100 flex items-center justify-center hover:bg-red-100 transition-colors cursor-pointer mb-px"
                        title="Delete Coordinator"
                      >
                        <Trash2 size={13} />
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Trustees & Leadership */}
          <div className="glass-card p-4 space-y-3">
            <div className="flex items-center justify-between border-b border-border pb-2">
              <h3 className="text-xs font-bold text-foreground flex items-center gap-1.5">
                <Users size={14} className="text-primary" /> Trustees & Committee Members
              </h3>
              <button
                type="button"
                onClick={handleAddTrustee}
                className="inline-flex items-center gap-1 text-[11px] font-bold text-primary bg-primary-soft hover:opacity-90 px-2.5 py-1.5 rounded-lg transition-all cursor-pointer"
              >
                <Plus size={12} /> Add Trustee
              </button>
            </div>

            {trustees.length === 0 ? (
              <p className="text-xs text-muted italic text-center py-4">No trustees added. Click "Add Trustee" to define one.</p>
            ) : (
              <div className="space-y-2">
                {trustees.map((trustee, idx) => (
                  <div key={idx} className="p-3 rounded-xl border border-border bg-surface/30 grid grid-cols-2 md:grid-cols-4 gap-2 items-end">
                    <div>
                      <div className="flex items-center gap-1 mb-0.5">
                        {trustee.avatar && (
                          <img
                            src={trustee.avatar.startsWith("http") ? trustee.avatar : `${API_ORIGIN}${trustee.avatar}`}
                            alt=""
                            className="h-3.5 w-3.5 rounded-full object-cover border border-border shrink-0"
                          />
                        )}
                        <label className="text-[10px] font-bold text-muted uppercase tracking-wider">Name</label>
                      </div>
                      <input
                        type="text"
                        value={trustee.name}
                        onChange={(e) => handleTrusteeChange(idx, "name", e.target.value)}
                        className={inputClass}
                        placeholder="e.g. Ravi Sharma"
                      />
                    </div>
                    <div>
                      <label className={labelClass}>Role / Title</label>
                      <input
                        type="text"
                        value={trustee.role}
                        onChange={(e) => handleTrusteeChange(idx, "role", e.target.value)}
                        className={inputClass}
                        placeholder="e.g. President & Trustee"
                      />
                    </div>
                    <div>
                      <label className={labelClass}>Company / Organization</label>
                      <input
                        type="text"
                        value={trustee.company}
                        onChange={(e) => handleTrusteeChange(idx, "company", e.target.value)}
                        className={inputClass}
                        placeholder="e.g. Founder, Vertex Solutions"
                      />
                    </div>
                    <div className="flex gap-1.5 items-end">
                      <div className="flex-1">
                        <label className={labelClass}>Avatar URL</label>
                        <div className="flex gap-1">
                          <input
                            type="text"
                            value={trustee.avatar}
                            onChange={(e) => handleTrusteeChange(idx, "avatar", e.target.value)}
                            className={inputClass}
                            placeholder="e.g. https://images.unsplash.com/..."
                          />
                          <label className="h-[30px] px-2 rounded-lg border border-border bg-surface hover:bg-surface/70 text-[10px] font-bold text-muted flex items-center justify-center cursor-pointer transition-colors shrink-0">
                            Upload
                            <input
                              type="file"
                              accept="image/*"
                              className="hidden"
                              onChange={async (e) => {
                                const file = e.target.files?.[0];
                                if (!file) return;
                                try {
                                  const formData = new FormData();
                                  formData.append("image", file);
                                  const result = await uploadGalleryImage(formData);
                                  if (result.success && result.image_url) {
                                    handleTrusteeChange(idx, "avatar", result.image_url);
                                  }
                                } catch (err: any) {
                                  alert(err.message || "Failed to upload image.");
                                }
                              }}
                            />
                          </label>
                        </div>
                      </div>
                      <button
                        type="button"
                        onClick={() => handleRemoveTrustee(idx)}
                        className="h-[30px] w-[30px] shrink-0 rounded-lg bg-red-50 text-red-600 border border-red-100 flex items-center justify-center hover:bg-red-100 transition-colors cursor-pointer mb-px"
                        title="Delete Trustee"
                      >
                        <Trash2 size={13} />
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Action Bar */}
          <div className="flex items-center justify-end">
            <button
              onClick={handleSave}
              disabled={saving}
              className="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
            >
              <Save size={13} />
              {saving ? "Saving..." : "Save Settings"}
            </button>
          </div>
        </div>
      )}

      {/* Info card */}
      {/* <div className="glass-card p-4 flex items-center gap-3">
        <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
          <ShieldCheck className="h-4 w-4" />
        </div>
        <div>
          <h4 className="text-xs font-bold text-foreground mb-0.5">About dynamic configs</h4>
          <p className="text-[11px] leading-relaxed text-muted-foreground">
            Modifications saved here are immediately reflected on the Contact page. Coordinator values are synced across all member interactions.
          </p>
        </div>
      </div> */}
    </div>
  );
}

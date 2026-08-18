"use client";

import { useEffect, useState } from "react";
import { fetchSettings, updateSettingsAdmin, uploadGalleryImage, fetchStatistics, updateStatistic } from "@/lib/api";
import { API_ORIGIN, assetUrl } from "@/lib/config";
import {
  Settings as SettingsIcon, Save, CheckCircle2, AlertCircle,
  RefreshCw, Plus, Trash2, Mail, ShieldCheck, Users, Share2, Upload, Calendar, Info, BarChart3, Layers
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

interface Milestone {
  year: string;
  title: string;
  description: string;
}

const defaultMilestones: Milestone[] = [
  {
    year: "2024",
    title: "Foundation & Vision",
    description: "SABHA was conceptualized by community visionaries to create a unified ecosystem that fosters trust, business referrals, and professional advancement."
  },
  {
    year: "2025",
    title: "Directory & Chapters Launch",
    description: "Introduced our digital business directory platform and registered 200+ local enterprises. Launched regional chapters across Mumbai, Pune, and Delhi."
  },
  {
    year: "2026",
    title: "Harmony Mixers & Scale",
    description: "Grown to 500+ active verified businesses. Hosted 50+ corporate networking meets, generating millions in business referrals and mutual trade."
  }
];

export default function AdminSettingsPage() {
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [successMsg, setSuccessMsg] = useState("");
  const [errorMsg, setErrorMsg] = useState("");

  const [activeTab, setActiveTab] = useState<"general" | "about">("general");
  const [contactEmail, setContactEmail] = useState("");
  const [contactPhone, setContactPhone] = useState("");
  const [contactAddress, setContactAddress] = useState("");
  const [responseTime, setResponseTime] = useState("");
  const [instagramUrl, setInstagramUrl] = useState("");
  const [whatsappUrl, setWhatsappUrl] = useState("");
  const [facebookUrl, setFacebookUrl] = useState("");
  const [coordinators, setCoordinators] = useState<Coordinator[]>([]);
  const [trustees, setTrustees] = useState<Trustee[]>([]);
  const [milestones, setMilestones] = useState<Milestone[]>([]);
  const [stats, setStats] = useState<any[]>([]);
  const [editStatValues, setEditStatValues] = useState<Record<number, { label: string; value: string }>>({});
  const [uploadingIdx, setUploadingIdx] = useState<number | null>(null);

  useEffect(() => { loadData(); }, []);

  async function loadData() {
    try {
      setLoading(true);
      setErrorMsg("");
      const data = await fetchSettings();
      setContactEmail(data.contact_email || "hello@sabha.global");
      setContactPhone(data.contact_phone || "+91 95377 33567");
      setContactAddress(data.contact_address || "Ahmedabad, Gujarat, India");
      setResponseTime(data.response_time || "Within 1 Business Day");
      setInstagramUrl(data.instagram_url || "");
      setWhatsappUrl(data.whatsapp_url || "");
      setFacebookUrl(data.facebook_url || "");
      
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

      let ms: Milestone[] = [];
      if (data.milestones !== undefined && data.milestones !== null) {
        try {
          ms = typeof data.milestones === "string"
            ? JSON.parse(data.milestones)
            : data.milestones;
        } catch (e) {
          console.error("Failed to parse milestones JSON:", e);
        }
      } else {
        ms = defaultMilestones;
      }
      setMilestones(ms);

      // Load Statistics
      const statData = await fetchStatistics().catch(() => []);
      const visibleStats = statData || [];
      setStats(visibleStats);
      const initialStatEdits: Record<number, { label: string; value: string }> = {};
      visibleStats.forEach((item: any) => {
        initialStatEdits[item.id] = { label: item.label, value: item.value };
      });
      setEditStatValues(initialStatEdits);
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

  const handleMilestoneChange = (index: number, field: keyof Milestone, value: string) => {
    const updated = [...milestones];
    updated[index] = { ...updated[index], [field]: value };
    setMilestones(updated);
  };

  const handleAddMilestone = () => {
    setMilestones([...milestones, { year: new Date().getFullYear().toString(), title: "New Milestone", description: "" }]);
  };

  const handleRemoveMilestone = (index: number) => {
    setMilestones(milestones.filter((_, i) => i !== index));
  };

  const handleStatInputChange = (id: number, field: "label" | "value", text: string) => {
    setEditStatValues((prev) => ({ ...prev, [id]: { ...prev[id], [field]: text } }));
  };

  const handleSave = async () => {
    setSaving(true);
    setErrorMsg("");
    setSuccessMsg("");
    try {
      await updateSettingsAdmin({ 
        contact_email: contactEmail, 
        contact_phone: contactPhone,
        contact_address: contactAddress,
        response_time: responseTime,
        instagram_url: instagramUrl,
        whatsapp_url: whatsappUrl,
        facebook_url: facebookUrl, 
        coordinators, 
        trustees,
        milestones 
      });

      for (const stat of stats) {
        const editVal = editStatValues[stat.id];
        if (editVal && editVal.label && editVal.value) {
          await updateStatistic(stat.id, editVal).catch(() => {});
        }
      }
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
    
    
    

      {/* Tabs Bar */}
      <div className="flex items-center gap-2 border-b border-border pb-2.5">
        <button
          type="button"
          onClick={() => setActiveTab("general")}
          className={`inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer ${
            activeTab === "general"
              ? "bg-primary text-white shadow-sm"
              : "bg-white text-muted hover:bg-slate-50 hover:text-foreground border border-border/80"
          }`}
        >
          <SettingsIcon size={14} />
          <span>General Settings</span>
        </button>

        <button
          type="button"
          onClick={() => setActiveTab("about")}
          className={`inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer ${
            activeTab === "about"
              ? "bg-primary text-white shadow-sm"
              : "bg-white text-muted hover:bg-slate-50 hover:text-foreground border border-border/80"
          }`}
        >
          <Info size={14} />
          <span>About Us Settings</span>
          {/* <span className="ml-0.5 rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-black">
            3 Sections
          </span> */}
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
          {activeTab === "general" ? (
            <>
              {/* General Contact */}
              <div className="glass-card p-4 space-y-3">
                <h3 className="text-xs font-bold text-foreground border-b border-border pb-2 flex items-center gap-1.5">
                  <SettingsIcon size={14} className="text-primary" /> General Contact Info
                </h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label className={labelClass}>Footer Contact Email</label>
                    <input
                      type="email"
                      value={contactEmail}
                      onChange={(e) => setContactEmail(e.target.value)}
                      className={inputClass}
                      placeholder="e.g. hello@sabha.global"
                    />
                  </div>
                  <div>
                    <label className={labelClass}>Footer Mobile / Phone Number</label>
                    <input
                      type="text"
                      maxLength={10}
                      value={contactPhone}
                      onChange={(e) => setContactPhone(e.target.value.replace(/\D/g, "").slice(0, 10))}
                      className={inputClass}
                      placeholder="10-digit mobile number"
                    />
                  </div>
                  <div>
                    <label className={labelClass}>Footer Contact Address</label>
                    <input
                      type="text"
                      value={contactAddress}
                      onChange={(e) => setContactAddress(e.target.value)}
                      className={inputClass}
                      placeholder="e.g. Ahmedabad, Gujarat, India"
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

              {/* Social Links */}
              <div className="glass-card p-4 space-y-3">
                <h3 className="text-xs font-bold text-foreground border-b border-border pb-2 flex items-center gap-1.5">
                  <Share2 size={14} className="text-primary" /> Footer Social Media Links
                </h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                  <div>
                    <label className={labelClass}>Instagram URL</label>
                    <input
                      type="text"
                      value={instagramUrl}
                      onChange={(e) => setInstagramUrl(e.target.value)}
                      className={inputClass}
                      placeholder="https://instagram.com/yourpage"
                    />
                  </div>
                  <div>
                    <label className={labelClass}>WhatsApp Number / Link</label>
                    <input
                      type="text"
                      value={whatsappUrl}
                      onChange={(e) => setWhatsappUrl(e.target.value)}
                      className={inputClass}
                      placeholder="https://wa.me/919876543210"
                    />
                  </div>
                  <div>
                    <label className={labelClass}>Facebook URL</label>
                    <input
                      type="text"
                      value={facebookUrl}
                      onChange={(e) => setFacebookUrl(e.target.value)}
                      className={inputClass}
                      placeholder="https://facebook.com/yourpage"
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
                            maxLength={10}
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
            </>
          ) : (
            <>
              {/* Section 1: About Us Statistics */}
              <div className="glass-card p-4 space-y-3">
                <h3 className="text-xs font-bold text-foreground border-b border-border pb-2 flex items-center gap-1.5">
                  <BarChart3 size={14} className="text-primary" /> About Us Statistics Cards
                </h3>

                {stats.length === 0 ? (
                  <p className="text-xs text-muted italic text-center py-4">No statistics available.</p>
                ) : (
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {stats.map((stat) => {
                      const currentEdit = editStatValues[stat.id] || { label: "", value: "" };

                      return (
                        <div key={stat.id} className="p-3.5 rounded-xl border border-border bg-surface/30 space-y-2.5">
                          <div className="flex items-center justify-between">
                            <span className="flex items-center gap-1 text-[10px] font-extrabold text-primary bg-primary-soft px-2 py-0.5 rounded-md">
                              <Layers size={10} /> Stat #{stat.id}
                            </span>
                            <span className="text-[10px] text-muted-foreground font-semibold truncate max-w-[150px]">
                              Live: <span className="text-foreground font-bold">{stat.value}</span> — {stat.label}
                            </span>
                          </div>

                          <div className="grid grid-cols-2 gap-2">
                            <div className="space-y-0.5">
                              <label className={labelClass}>Stat Label</label>
                              <input
                                type="text"
                                value={currentEdit.label}
                                onChange={(e) => handleStatInputChange(stat.id, "label", e.target.value)}
                                className={inputClass}
                                placeholder="e.g. Success Stories"
                              />
                            </div>
                            <div className="space-y-0.5">
                              <label className={labelClass}>Stat Value</label>
                              <input
                                type="text"
                                value={currentEdit.value}
                                onChange={(e) => handleStatInputChange(stat.id, "value", e.target.value)}
                                className={inputClass}
                                placeholder="e.g. 2500+"
                              />
                            </div>
                          </div>
                        </div>
                      );
                    })}
                  </div>
                )}
              </div>

              {/* Section 2: Trustees & Leadership */}
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
                  <div className="space-y-2.5">
                    {trustees.map((trustee, idx) => (
                      <div key={idx} className="p-3.5 rounded-xl border border-border bg-surface/30 flex flex-col md:flex-row md:items-center gap-3">
                        {/* Avatar Preview Thumbnail + Upload Button */}
                        <div className="flex items-center gap-3 shrink-0">
                          <div className="relative h-12 w-12 rounded-full border-2 border-primary/30 bg-white overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                            {trustee.avatar ? (
                              <img
                                src={assetUrl(trustee.avatar)}
                                alt={trustee.name}
                                className="h-full w-full object-cover"
                              />
                            ) : (
                              <span className="text-base font-bold text-primary">{trustee.name?.[0] ?? "?"}</span>
                            )}
                          </div>

                          <label className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-border bg-white hover:bg-slate-50 text-xs font-bold text-foreground cursor-pointer transition-all shadow-xs shrink-0">
                            <Upload size={13} className={uploadingIdx === idx ? "animate-bounce text-primary" : "text-primary"} />
                            <span>{uploadingIdx === idx ? "Uploading..." : "Choose Image"}</span>
                            <input
                              type="file"
                              accept="image/*"
                              className="hidden"
                              disabled={uploadingIdx === idx}
                              onChange={async (e) => {
                                const file = e.target.files?.[0];
                                if (!file) return;
                                try {
                                  setUploadingIdx(idx);
                                  const formData = new FormData();
                                  formData.append("image", file);
                                  const result = await uploadGalleryImage(formData);
                                  if (result.success && result.image_url) {
                                    handleTrusteeChange(idx, "avatar", result.image_url);
                                  }
                                } catch (err: any) {
                                  alert(err.message || "Failed to upload image.");
                                } finally {
                                  setUploadingIdx(null);
                                }
                              }}
                            />
                          </label>
                        </div>

                        {/* Inputs Grid */}
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-2.5 flex-1 min-w-0">
                          <div>
                            <label className="text-[10px] font-bold text-muted uppercase tracking-wider block mb-0.5">Name</label>
                            <input
                              type="text"
                              value={trustee.name}
                              onChange={(e) => handleTrusteeChange(idx, "name", e.target.value)}
                              className={inputClass}
                              placeholder="e.g. Ravi Sharma"
                            />
                          </div>
                          <div>
                            <label className="text-[10px] font-bold text-muted uppercase tracking-wider block mb-0.5">Role / Title</label>
                            <input
                              type="text"
                              value={trustee.role}
                              onChange={(e) => handleTrusteeChange(idx, "role", e.target.value)}
                              className={inputClass}
                              placeholder="e.g. President & Trustee"
                            />
                          </div>
                          <div>
                            <label className="text-[10px] font-bold text-muted uppercase tracking-wider block mb-0.5">Company / Organization</label>
                            <input
                              type="text"
                              value={trustee.company}
                              onChange={(e) => handleTrusteeChange(idx, "company", e.target.value)}
                              className={inputClass}
                              placeholder="e.g. Founder, Vertex Solutions"
                            />
                          </div>
                        </div>

                        {/* Remove Button */}
                        <button
                          type="button"
                          onClick={() => handleRemoveTrustee(idx)}
                          className="h-8 w-8 shrink-0 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center hover:bg-rose-100 transition-colors cursor-pointer self-end md:self-center"
                          title="Delete Trustee"
                        >
                          <Trash2 size={14} />
                        </button>
                      </div>
                    ))}
                  </div>
                )}
              </div>

              {/* Section 3: Timeline Milestones */}
              <div className="glass-card p-4 space-y-3">
                <div className="flex items-center justify-between border-b border-border pb-2">
                  <h3 className="text-xs font-bold text-foreground flex items-center gap-1.5">
                    <Calendar size={14} className="text-primary" /> About Us Timeline Milestones
                  </h3>
                  <button
                    type="button"
                    onClick={handleAddMilestone}
                    className="inline-flex items-center gap-1 text-[11px] font-bold text-primary bg-primary-soft hover:opacity-90 px-2.5 py-1.5 rounded-lg transition-all cursor-pointer"
                  >
                    <Plus size={12} /> Add Milestone
                  </button>
                </div>

                {milestones.length === 0 ? (
                  <p className="text-xs text-muted italic text-center py-4">No milestones defined. Click "Add Milestone" to create one.</p>
                ) : (
                  <div className="space-y-2.5">
                    {milestones.map((m, idx) => (
                      <div key={idx} className="p-3 rounded-xl border border-border bg-surface/30 grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-end">
                        <div className="sm:col-span-2">
                          <label className={labelClass}>Year</label>
                          <input
                            type="text"
                            value={m.year}
                            onChange={(e) => handleMilestoneChange(idx, "year", e.target.value)}
                            className={inputClass}
                            placeholder="e.g. 2024"
                          />
                        </div>
                        <div className="sm:col-span-4">
                          <label className={labelClass}>Milestone Title</label>
                          <input
                            type="text"
                            value={m.title}
                            onChange={(e) => handleMilestoneChange(idx, "title", e.target.value)}
                            className={inputClass}
                            placeholder="e.g. Foundation & Vision"
                          />
                        </div>
                        <div className="sm:col-span-5">
                          <label className={labelClass}>Description</label>
                          <input
                            type="text"
                            value={m.description}
                            onChange={(e) => handleMilestoneChange(idx, "description", e.target.value)}
                            className={inputClass}
                            placeholder="e.g. SABHA was conceptualized..."
                          />
                        </div>
                        <div className="sm:col-span-1 flex justify-end">
                          <button
                            type="button"
                            onClick={() => handleRemoveMilestone(idx)}
                            className="h-8 w-8 shrink-0 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center hover:bg-rose-100 transition-colors cursor-pointer"
                            title="Delete Milestone"
                          >
                            <Trash2 size={13} />
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </>
          )}

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

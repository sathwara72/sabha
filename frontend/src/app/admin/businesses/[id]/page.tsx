"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { createPortal } from "react-dom";
import {
  fetchAllBusinessesAdmin, approveBusiness, rejectBusiness
} from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";
import {
  ShieldCheck, XCircle, CheckCircle2,
  ArrowLeft, Receipt, X, Globe, MapPin, Phone,
  Mail, Calendar, Users, Clock, Briefcase, Link2
} from "lucide-react";

interface Business {
  id: number;
  name: string;
  category: string;
  description: string;
  website?: string;
  status: string;
  is_verified: boolean;
  logo?: string;
  cover_image?: string;
  location?: string;
  phone?: string;
  tagline?: string;
  payment_screenshot?: string;
  email?: string;
  linkedin?: string;
  instagram?: string;
  youtube?: string;
  twitter?: string;
  whatsapp?: string;
  hours?: string;
  founded?: string;
  team_size?: string;
  projects?: string;
  services?: string[];
  rejection_reason?: string;
  user?: any;
}

function getMediaUrl(path?: string) {
  if (!path) return "";
  if (path.startsWith("http://") || path.startsWith("https://")) return path;
  return `${API_ORIGIN}${path}`;
}

interface ServiceItem {
  title: string;
  desc?: string;
}

function parseServices(services: any): ServiceItem[] {
  if (!services) return [];
  let servicesData: any = [];
  if (Array.isArray(services)) {
    servicesData = services;
  } else if (typeof services === "string") {
    try {
      servicesData = JSON.parse(services);
    } catch (e) {
      servicesData = services.split(",").map((s: string) => s.trim());
    }
  }

  if (Array.isArray(servicesData)) {
    return servicesData.map((s: any) => {
      if (s && typeof s === "object") {
        return {
          title: s.title || "",
          desc: s.desc || ""
        };
      } else {
        const titleStr = String(s || "");
        return {
          title: titleStr,
          desc: ""
        };
      }
    }).filter((s: any) => s.title);
  }
  return [];
}

export default function AdminBusinessDetailPage() {
  const { id } = useParams();
  const router = useRouter();
  const [business, setBusiness] = useState<Business | null>(null);
  const [loading, setLoading] = useState(true);
  const [paymentModalUrl, setPaymentModalUrl] = useState<string | null>(null);

  useEffect(() => {
    if (id) {
      loadBusiness();
    }
  }, [id]);

  async function loadBusiness() {
    try {
      setLoading(true);
      const data = await fetchAllBusinessesAdmin();
      const matched = data.find((biz: any) => biz.id.toString() === id);
      setBusiness(matched || null);
    } catch (error) {
      console.error("Error loading business detail:", error);
    } finally {
      setLoading(false);
    }
  }

  async function handleApprove() {
    if (!business) return;
    try {
      await approveBusiness(business.id);
      loadBusiness();
    } catch {
      alert("Approval failed");
    }
  }

  async function handleReject() {
    if (!business) return;
    const reason = prompt("Please enter the reason for rejection:");
    if (reason === null) return;
    if (!reason.trim()) {
      alert("Rejection reason is required.");
      return;
    }
    try {
      await rejectBusiness(business.id, reason.trim());
      loadBusiness();
    } catch {
      alert("Rejection failed");
    }
  }

  const statusBadgeColor: Record<string, string> = {
    approved: "bg-emerald-500/10 text-emerald-500 border-emerald-500/20 backdrop-blur-md",
    pending: "bg-amber-500/10 text-amber-500 border-amber-500/20 backdrop-blur-md",
    rejected: "bg-rose-500/10 text-rose-500 border-rose-500/20 backdrop-blur-md",
  };

  if (loading) {
    return (
      <div className="flex h-[60vh] items-center justify-center font-outfit">
        <div className="text-center space-y-3">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="text-sm font-medium text-muted">Loading business details...</p>
        </div>
      </div>
    );
  }

  if (!business) {
    return (
      <div className="py-20 text-center space-y-4 font-outfit">
        <h2 className="text-xl font-bold text-foreground">Business Not Found</h2>
        <p className="text-sm text-muted">The requested business details could not be found or loaded.</p>
        <button
          onClick={() => router.push("/admin/businesses")}
          className="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98]"
        >
          <ArrowLeft size={14} /> Back to Businesses
        </button>
      </div>
    );
  }

  return (
    <div className="space-y-4 font-outfit">
      {/* Top sticky-feel Action bar */}
      <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div className="flex items-center gap-3">
          <button
            onClick={() => router.push("/admin/businesses")}
            className="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all shadow-sm cursor-pointer"
          >
            <ArrowLeft size={15} />
          </button>
          <div>
            <h1 className="text-lg font-black text-slate-800 tracking-tight">Business Profile Review</h1>
            <p className="text-[10px] text-slate-400">Detailed verification and approval workspace</p>
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-1.5">
          {business.status === "pending" && (
            <>
              <button
                onClick={handleApprove}
                className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-3.5 py-2 text-xs font-bold text-white transition-all hover:opacity-95 shadow-sm active:scale-[0.98] cursor-pointer"
              >
                <CheckCircle2 size={13} /> Approve Submission
              </button>
              <button
                onClick={handleReject}
                className="inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-600 transition-all hover:bg-rose-100 active:scale-[0.98] cursor-pointer"
              >
                <XCircle size={13} /> Reject Submission
              </button>
            </>
          )}
          {business.payment_screenshot && (
            <button
              onClick={() => setPaymentModalUrl(getMediaUrl(business.payment_screenshot))}
              className="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-xl px-3.5 py-2 hover:bg-amber-100 transition-all cursor-pointer shadow-sm active:scale-[0.98]"
            >
              <Receipt size={13} /> View Payment Receipt
            </button>
          )}
        </div>
      </div>

      {/* Main Grid Content */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
        {/* Left Column: Business details main view */}
        <div className="lg:col-span-2 space-y-4">
          {/* Cover & Brand card */}
          <div className="bg-white rounded-3xl border border-slate-200/60 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
            <div className="relative h-40 sm:h-52 w-full bg-slate-900">
              {business.cover_image ? (
                <img
                  src={getMediaUrl(business.cover_image)}
                  alt={business.name}
                  className="w-full h-full object-cover opacity-90"
                />
              ) : (
                <div className="w-full h-full bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-950" />
              )}
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/20 to-transparent" />
              
              {/* Floating badges on cover image */}
              <div className="absolute top-3 right-3 flex items-center gap-2">
                <span className={`px-3 py-0.5 rounded-full text-[9px] font-extrabold border uppercase tracking-widest shadow-lg ${statusBadgeColor[business.status] || "bg-white/10 text-white border-white/20"}`}>
                  {business.status}
                </span>
              </div>

              {/* Float Logo */}
              <div className="absolute -bottom-8 left-6 h-20 w-20 sm:h-24 sm:w-24 rounded-2xl border-4 border-white shadow-xl bg-white overflow-hidden flex items-center justify-center">
                {business.logo ? (
                  <img
                    src={getMediaUrl(business.logo)}
                    alt={business.name}
                    className="w-full h-full object-cover"
                  />
                ) : (
                  <span className="text-3xl font-black text-indigo-600">{business.name?.[0] ?? "?"}</span>
                )}
              </div>
            </div>

            <div className="pt-10 px-5 pb-5 space-y-4">
              <div>
                <h2 className="text-xl font-black text-slate-800 leading-tight">{business.name}</h2>
                {business.tagline && (
                  <p className="text-[11px] text-slate-400 italic mt-1">{business.tagline}</p>
                )}
              </div>

              {/* Info grid */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2.5 border-t border-slate-100/70">
                <div className="space-y-2.5">
                  <div className="flex items-center gap-2 bg-slate-50/50 p-1.5 rounded-xl border border-slate-100/30">
                    <div className="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                      <Briefcase size={12} />
                    </div>
                    <div className="text-[11px]">
                      <p className="text-[9px] text-slate-400 font-semibold uppercase leading-none">Category</p>
                      <p className="font-extrabold text-slate-800 mt-0.5">{business.category}</p>
                    </div>
                  </div>

                  {business.location && (
                    <div className="flex items-center gap-2 bg-slate-50/50 p-1.5 rounded-xl border border-slate-100/30">
                      <div className="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                        <MapPin size={12} />
                      </div>
                      <div className="text-[11px]">
                        <p className="text-[9px] text-slate-400 font-semibold uppercase leading-none">Location</p>
                        <p className="font-bold text-slate-700 mt-0.5">{business.location}</p>
                      </div>
                    </div>
                  )}

                  {business.website && (
                    <div className="flex items-center gap-2 bg-slate-50/50 p-1.5 rounded-xl border border-slate-100/30">
                      <div className="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                        <Link2 size={12} />
                      </div>
                      <div className="text-[11px] truncate">
                        <p className="text-[9px] text-slate-400 font-semibold uppercase leading-none">Website</p>
                        <a href={business.website} target="_blank" rel="noopener noreferrer" className="font-bold text-indigo-600 hover:underline truncate block max-w-[180px] mt-0.5">
                          {business.website}
                        </a>
                      </div>
                    </div>
                  )}
                </div>

                <div className="space-y-2.5">
                  {business.phone && (
                    <div className="flex items-center gap-2 bg-slate-50/50 p-1.5 rounded-xl border border-slate-100/30">
                      <div className="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                        <Phone size={12} />
                      </div>
                      <div className="text-[11px]">
                        <p className="text-[9px] text-slate-400 font-semibold uppercase leading-none">Business Phone</p>
                        <a href={`tel:${business.phone}`} className="font-bold text-indigo-600 hover:underline mt-0.5 block">{business.phone}</a>
                      </div>
                    </div>
                  )}

                  {business.email && (
                    <div className="flex items-center gap-2 bg-slate-50/50 p-1.5 rounded-xl border border-slate-100/30">
                      <div className="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                        <Mail size={12} />
                      </div>
                      <div className="text-[11px] truncate">
                        <p className="text-[9px] text-slate-400 font-semibold uppercase leading-none">Business Email</p>
                        <a href={`mailto:${business.email}`} className="font-bold text-indigo-600 hover:underline truncate block max-w-[180px] mt-0.5">{business.email}</a>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>

          {/* Business Description */}
          <div className="bg-white rounded-3xl border border-slate-200/60 p-5 shadow-sm space-y-3">
            <h3 className="text-xs font-bold text-slate-800 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-2">
              <span className="h-1.5 w-1.5 rounded-full bg-indigo-600" /> Business Description
            </h3>
            <p className="text-xs text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50/55 p-3.5 rounded-2xl border border-slate-100/60">
              {business.description || "No description provided."}
            </p>
          </div>

          {/* Services list */}
          {(() => {
            const parsedServices = parseServices(business.services);
            if (parsedServices.length === 0) return null;
            return (
              <div className="bg-white rounded-3xl border border-slate-200/60 p-5 shadow-sm space-y-3">
                <h3 className="text-xs font-bold text-slate-800 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-2">
                  <span className="h-1.5 w-1.5 rounded-full bg-indigo-600" /> Services Offered
                </h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  {parsedServices.map((service, idx) => (
                    <div key={idx} className="bg-gradient-to-br from-slate-50 to-white border border-slate-200/50 p-3 rounded-2xl space-y-1 hover:border-slate-300 transition-all">
                      <h4 className="text-xs font-bold text-slate-800 flex items-center gap-2">
                        <span className="h-2 w-2 rounded-full bg-indigo-500" />
                        {service.title}
                      </h4>
                      {service.desc && (
                        <p className="text-[11px] text-slate-500 leading-relaxed pl-4">{service.desc}</p>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            );
          })()}

          {/* Social Channels */}
          {(business.linkedin || business.instagram || business.youtube || business.twitter || business.whatsapp) && (
            <div className="bg-white rounded-3xl border border-slate-200/60 p-5 shadow-sm space-y-3">
              <h3 className="text-xs font-bold text-slate-800 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-2">
                <span className="h-1.5 w-1.5 rounded-full bg-indigo-600" /> Social Channels
              </h3>
              <div className="flex flex-wrap gap-2">
                {business.linkedin && (
                  <a href={business.linkedin} target="_blank" rel="noopener noreferrer" className="bg-indigo-50/50 hover:bg-indigo-100/70 border border-indigo-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-indigo-700 transition-all active:scale-[0.98]">
                    LinkedIn
                  </a>
                )}
                {business.instagram && (
                  <a href={business.instagram} target="_blank" rel="noopener noreferrer" className="bg-rose-50/50 hover:bg-rose-100/70 border border-rose-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-rose-700 transition-all active:scale-[0.98]">
                    Instagram
                  </a>
                )}
                {business.youtube && (
                  <a href={business.youtube} target="_blank" rel="noopener noreferrer" className="bg-red-50/50 hover:bg-red-100/70 border border-red-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-red-700 transition-all active:scale-[0.98]">
                    YouTube
                  </a>
                )}
                {business.twitter && (
                  <a href={business.twitter} target="_blank" rel="noopener noreferrer" className="bg-sky-50/50 hover:bg-sky-100/70 border border-sky-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-sky-700 transition-all active:scale-[0.98]">
                    Twitter
                  </a>
                )}
                {business.whatsapp && (
                  <a href={`https://wa.me/${business.whatsapp}`} target="_blank" rel="noopener noreferrer" className="bg-emerald-50/50 hover:bg-emerald-100/70 border border-emerald-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-emerald-700 transition-all active:scale-[0.98]">
                    WhatsApp
                  </a>
                )}
              </div>
            </div>
          )}
        </div>

        {/* Right Column: Owner & Stats Cards */}
        <div className="space-y-4">
          {/* Owner details card - Ultra Unique Gradient look */}
          <div className="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl border border-white/5 p-5 shadow-xl space-y-3 relative overflow-hidden">
            <div className="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-primary/20 blur-2xl pointer-events-none" />
            <div className="absolute -left-8 -bottom-8 h-24 w-24 rounded-full bg-violet-600/10 blur-2xl pointer-events-none" />
            
            <h3 className="text-xs font-bold uppercase tracking-widest text-indigo-300 flex items-center gap-2 border-b border-white/10 pb-2.5 relative z-10">
              <ShieldCheck size={15} className="text-indigo-400" /> Submitted By (Owner)
            </h3>
            
            {business.user ? (
              <div className="space-y-3 relative z-10">
                <div className="flex items-center gap-3">
                  <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-base font-bold text-white border border-white/10 shadow-inner">
                    {business.user.name?.[0] ?? "?"}
                  </div>
                  <div>
                    <h4 className="text-xs font-black text-white leading-tight">{business.user.name}</h4>
                    <span className="inline-block rounded-md bg-indigo-500/20 border border-indigo-400/20 px-2 py-0.5 text-[9px] font-bold text-indigo-300 mt-1 uppercase tracking-wider">
                      {business.user.role || "Member"}
                    </span>
                  </div>
                </div>

                <div className="space-y-2 text-xs pt-3 border-t border-white/10 text-indigo-200/80">
                  {business.user.email && (
                    <div className="flex items-start gap-2">
                      <span className="font-bold text-indigo-300 w-16 shrink-0">Email:</span>
                      <a href={`mailto:${business.user.email}`} className="text-white hover:underline truncate block max-w-[160px]">{business.user.email}</a>
                    </div>
                  )}
                  {business.user.phone && (
                    <div className="flex items-start gap-2">
                      <span className="font-bold text-indigo-300 w-16 shrink-0">Phone:</span>
                      <a href={`tel:${business.user.phone}`} className="text-white hover:underline">{business.user.phone}</a>
                    </div>
                  )}
                  {business.user.company && (
                    <div className="flex items-start gap-2">
                      <span className="font-bold text-indigo-300 w-16 shrink-0">Company:</span>
                      <span className="text-slate-200">{business.user.company}</span>
                    </div>
                  )}
                  {business.user.designation && (
                    <div className="flex items-start gap-2">
                      <span className="font-bold text-indigo-300 w-16 shrink-0">Role:</span>
                      <span className="text-slate-200">{business.user.designation}</span>
                    </div>
                  )}
                  {business.user.city && (
                    <div className="flex items-start gap-2">
                      <span className="font-bold text-indigo-300 w-16 shrink-0">City:</span>
                      <span className="text-slate-200">{business.user.city}</span>
                    </div>
                  )}
                </div>
              </div>
            ) : (
              <p className="text-xs text-indigo-200/60 italic">No owner information registered.</p>
            )}
          </div>

          {/* Operational Details Card */}
          {(business.founded || business.team_size || business.hours || business.projects) && (
            <div className="bg-white rounded-3xl border border-slate-200/60 p-5 shadow-sm space-y-3">
              <h3 className="text-xs font-bold uppercase tracking-widest text-slate-800 border-b border-slate-100 pb-2.5">
                Operational Details
              </h3>
              <div className="space-y-2 text-xs">
                {business.founded && (
                  <div className="flex justify-between items-center bg-slate-50/50 p-2 rounded-xl border border-slate-100/30">
                    <span className="font-semibold text-slate-500 flex items-center gap-1.5"><Calendar size={12} className="text-slate-400" /> Founded</span>
                    <span className="font-extrabold text-slate-800">{business.founded}</span>
                  </div>
                )}
                {business.team_size && (
                  <div className="flex justify-between items-center bg-slate-50/50 p-2 rounded-xl border border-slate-100/30">
                    <span className="font-semibold text-slate-500 flex items-center gap-1.5"><Users size={12} className="text-slate-400" /> Team Size</span>
                    <span className="font-extrabold text-slate-800">{business.team_size}</span>
                  </div>
                )}
                {business.hours && (
                  <div className="flex justify-between items-start gap-3 bg-slate-50/50 p-2 rounded-xl border border-slate-100/30">
                    <span className="font-semibold text-slate-500 flex items-center gap-1.5"><Clock size={12} className="text-slate-400" /> Hours</span>
                    <span className="font-extrabold text-slate-800 text-right max-w-[140px]">{business.hours}</span>
                  </div>
                )}
                {business.projects && (
                  <div className="flex justify-between items-center bg-slate-50/50 p-2 rounded-xl border border-slate-100/30">
                    <span className="font-semibold text-slate-500 flex items-center gap-1.5"><Briefcase size={12} className="text-slate-400" /> Projects</span>
                    <span className="font-extrabold text-slate-800">{business.projects}</span>
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Rejection reason */}
          {business.status === "rejected" && business.rejection_reason && (
            <div className="bg-rose-50 border border-rose-200/80 rounded-3xl p-5 text-xs space-y-2 shadow-sm">
              <h3 className="font-black text-rose-700 flex items-center gap-1.5">
                <XCircle size={15} /> Rejection Notice
              </h3>
              <p className="text-rose-600 leading-relaxed bg-white/70 p-3 rounded-2xl border border-rose-100">{business.rejection_reason}</p>
            </div>
          )}
        </div>
      </div>

      {/* Payment Screenshot Lightbox Modal */}
      {typeof window !== "undefined" && paymentModalUrl && createPortal(
        <div className="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div
            className="fixed inset-0 bg-black/85 backdrop-blur-md"
            onClick={() => setPaymentModalUrl(null)}
          />
          <button
            onClick={() => setPaymentModalUrl(null)}
            className="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2 transition-colors cursor-pointer"
          >
            <X size={20} />
          </button>
          <div className="relative z-40 max-w-2xl w-full flex flex-col items-center gap-4">
            <div className="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-2 text-white text-xs font-semibold flex items-center gap-2">
              <Receipt size={14} className="text-amber-300" /> Payment Screenshot
            </div>
            <img
              src={paymentModalUrl}
              alt="Payment Screenshot"
              className="max-h-[80vh] max-w-full object-contain rounded-2xl shadow-2xl border border-white/10"
            />
          </div>
        </div>,
        document.body
      )}
    </div>
  );
}

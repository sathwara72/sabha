"use client";

import { useEffect, useState } from "react";
import { useAuth } from "@/lib/auth";
import { useRouter } from "next/navigation";
import Link from "next/link";
import {
  User, Mail, Lock, Briefcase, Globe, CheckCircle2,
  ShieldCheck, Clock, XCircle, AlertCircle, Upload, Eye, MapPin,
  Phone, MessageCircle, Layers, Camera, LogOut, ChevronRight, Calendar
} from "lucide-react";
import { InstagramIcon, YoutubeIcon, TwitterIcon, LinkedinIcon, WhatsappIcon } from "@/components/SocialIcons";
import { getUserBusiness, submitBusiness, updateProfile } from "@/lib/api";
import { assetUrl } from "@/lib/config";
import { useLanguage } from "@/lib/language";

export default function ProfilePage() {
  const { isAuthenticated, isReady, user, updateLocalUser, openLogin, logout } = useAuth();
  const { t } = useLanguage();
  const router = useRouter();

  // Active section in the side menu
  const [activeTab, setActiveTab] = useState<"profile" | "business" | "events">("profile");

  // Registered events mock list
  const [registeredEvents, setRegisteredEvents] = useState([
    {
      id: 1,
      title: "Sabha Grand Business Summit 2026",
      date: "Oct 12, 2026",
      location: "Ahmedabad Exhibition Center, Gujarat",
      status: "confirmed",
      price: "₹1,499",
      image: "https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=800&auto=format&fit=crop"
    },
    {
      id: 2,
      title: "Harmony Networking Mixer",
      date: "Oct 15, 2026",
      location: "DoubleTree by Hilton, Pune",
      status: "pending",
      price: "₹499",
      image: "https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=800&auto=format&fit=crop"
    }
  ]);

  // Avatar upload
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [avatarPreview, setAvatarPreview] = useState<string>("");

  // User Profile fields
  const [profileName, setProfileName] = useState("");
  const [profileEmail, setProfileEmail] = useState("");
  const [profilePassword, setProfilePassword] = useState("");
  const [profilePhone, setProfilePhone] = useState("");
  const [profileCity, setProfileCity] = useState("");
  const [profileDesignation, setProfileDesignation] = useState("");
  const [profileCompany, setProfileCompany] = useState("");
  const [profileBio, setProfileBio] = useState("");
  
  const [profileLoading, setProfileLoading] = useState(false);
  const [profileSuccess, setProfileSuccess] = useState("");
  const [profileError, setProfileError] = useState("");

  // Business profile state
  const [business, setBusiness] = useState<any>(null);
  const [bizLoading, setBizLoading] = useState(true);

  // Business Submission fields
  const [bizName, setBizName] = useState("");
  const [bizCategory, setBizCategory] = useState("Software Development");
  const [bizWebsite, setBizWebsite] = useState("");
  const [bizDescription, setBizDescription] = useState("");
  const [bizTagline, setBizTagline] = useState("");
  const [bizLocation, setBizLocation] = useState("");
  const [bizHours, setBizHours] = useState("");
  const [bizFounded, setBizFounded] = useState("");
  const [bizTeamSize, setBizTeamSize] = useState("");
  const [bizProjects, setBizProjects] = useState("");
  const [bizPhone, setBizPhone] = useState("");
  const [bizEmail, setBizEmail] = useState("");
  const [bizLinkedin, setBizLinkedin] = useState("");
  const [bizInstagram, setBizInstagram] = useState("");
  const [bizYoutube, setBizYoutube] = useState("");
  const [bizTwitter, setBizTwitter] = useState("");
  const [bizWhatsapp, setBizWhatsapp] = useState("");
  const [bizServices, setBizServices] = useState("");

  const [paymentFile, setPaymentFile] = useState<File | null>(null);
  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [coverFile, setCoverFile] = useState<File | null>(null);

  const [bizSubmitting, setBizSubmitting] = useState(false);
  const [bizSuccess, setBizSuccess] = useState("");
  const [bizError, setBizError] = useState("");

  const [isEditingBusiness, setIsEditingBusiness] = useState(false);

  const categories = [
    "Software Development",
    "Supply Chain",
    "Digital Marketing",
    "Construction",
    "Financial Services",
    "Renewables",
    "Creative Agency",
    "Venture Capital"
  ];

  useEffect(() => {
    if (isReady && !isAuthenticated) {
      router.replace("/");
      openLogin();
    }
  }, [isReady, isAuthenticated, router, openLogin]);

  useEffect(() => {
    if (typeof window !== "undefined") {
      const params = new URLSearchParams(window.location.search);
      if (params.get("tab") === "business") {
        setActiveTab("business");
      }
    }
  }, []);

  useEffect(() => {
    if (user) {
      setProfileName(user.name);
      setProfileEmail(user.email);
      setProfilePhone(user.phone || "");
      setProfileCity(user.city || "");
      setProfileDesignation(user.designation || "");
      setProfileCompany(user.company || "");
      setProfileBio(user.bio || "");
    }
  }, [user]);

  useEffect(() => {
    if (isAuthenticated) {
      loadUserBusiness();
    }
  }, [isAuthenticated]);

  async function loadUserBusiness() {
    try {
      setBizLoading(true);
      const biz = await getUserBusiness();
      setBusiness(biz);
      if (biz) {
        setBizName(biz.name);
        setBizCategory(biz.category);
        setBizWebsite(biz.website || "");
        setBizDescription(biz.description || "");
        setBizTagline(biz.tagline || "");
        setBizLocation(biz.location || "");
        setBizHours(biz.hours || "");
        setBizFounded(biz.founded || "");
        setBizTeamSize(biz.team_size || "");
        setBizProjects(biz.projects || "");
        setBizPhone(biz.phone || "");
        setBizEmail(biz.email || "");
        setBizLinkedin(biz.linkedin || "");
        setBizInstagram(biz.instagram || "");
        setBizYoutube(biz.youtube || "");
        setBizTwitter(biz.twitter || "");
        setBizWhatsapp(biz.whatsapp || "");
        setBizServices(Array.isArray(biz.services) ? biz.services.join(", ") : (biz.services || ""));
      }
    } catch (err) {
      console.error("Failed to load user business:", err);
    } finally {
      setBizLoading(false);
    }
  }

  const handleUpdateProfile = async (e: React.FormEvent) => {
    e.preventDefault();
    setProfileSuccess("");
    setProfileError("");
    setProfileLoading(true);

    try {
      const formData = new FormData();
      formData.append("name", profileName);
      formData.append("email", profileEmail);
      if (profilePassword) formData.append("password", profilePassword);
      formData.append("phone", profilePhone);
      formData.append("city", profileCity);
      formData.append("designation", profileDesignation);
      formData.append("company", profileCompany);
      formData.append("bio", profileBio);
      if (avatarFile) formData.append("avatar", avatarFile);

      const res = await updateProfile(formData);
      updateLocalUser({
        name: res.user.name,
        email: res.user.email,
        role: res.user.role,
        phone: res.user.phone,
        city: res.user.city,
        designation: res.user.designation,
        company: res.user.company,
        bio: res.user.bio,
        avatar: res.user.avatar
      });
      setAvatarFile(null);
      setProfileSuccess(t("profile.profile_success"));
      setProfilePassword("");
    } catch (err: any) {
      setProfileError(err.message || t("profile.profile_error"));
    } finally {
      setProfileLoading(false);
    }
  };

  const handleBusinessSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setBizSuccess("");
    setBizError("");
    setBizSubmitting(true);

    if (!paymentFile && !business) {
      setBizError(t("profile.biz_payment_required"));
      setBizSubmitting(false);
      return;
    }

    try {
      const formData = new FormData();
      formData.append("name", bizName);
      formData.append("category", bizCategory);
      formData.append("website", bizWebsite);
      formData.append("description", bizDescription);
      formData.append("tagline", bizTagline);
      formData.append("location", bizLocation);
      formData.append("hours", bizHours);
      formData.append("founded", bizFounded);
      formData.append("team_size", bizTeamSize);
      formData.append("projects", bizProjects);
      formData.append("phone", bizPhone);
      formData.append("email", bizEmail);
      formData.append("linkedin", bizLinkedin);
      formData.append("instagram", bizInstagram);
      formData.append("youtube", bizYoutube);
      formData.append("twitter", bizTwitter);
      formData.append("whatsapp", bizWhatsapp);
      formData.append("services", bizServices);

      if (paymentFile) {
        formData.append("payment_screenshot", paymentFile);
      }
      if (logoFile) {
        formData.append("logo", logoFile);
      }
      if (coverFile) {
        formData.append("cover_image", coverFile);
      }

      await submitBusiness(formData);
      setBizSuccess(t("profile.biz_success"));
      setIsEditingBusiness(false);
      setPaymentFile(null);
      setLogoFile(null);
      setCoverFile(null);
      loadUserBusiness();
    } catch (err: any) {
      setBizError(err.message || "Failed to submit business details.");
    } finally {
      setBizSubmitting(false);
    }
  };

  if (!isReady || !isAuthenticated || !user) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background font-outfit">
        <div className="text-center space-y-3">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="text-sm font-medium text-muted">{t("profile.loading")}</p>
        </div>
      </div>
    );
  }

  const inputClass = "w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary font-semibold";
  const labelClass = "text-xs font-bold text-muted-foreground mb-1 block";
  const avatarSrc = avatarPreview || assetUrl(user.avatar);

  return (
    <div className="min-h-screen bg-background font-outfit py-12 px-6">
      <div className="mx-auto max-w-6xl space-y-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* ===== Side Menu ===== */}
          <aside className="lg:col-span-3 lg:sticky lg:top-24 space-y-4">
            {/* Identity card */}
            <div className="glass-card p-5 flex flex-col items-center text-center">
              <div className="relative">
                <div className="h-20 w-20 rounded-2xl overflow-hidden bg-primary/10 border border-border flex items-center justify-center shadow-sm">
                  {avatarSrc ? (
                    <img src={avatarSrc} alt="Profile" className="h-full w-full object-cover" />
                  ) : (
                    <span className="text-3xl font-bold text-primary uppercase">{user.name?.[0] ?? "?"}</span>
                  )}
                </div>
                <label className="absolute -bottom-2 -right-2 h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center cursor-pointer shadow-md transition-opacity hover:opacity-90" title="Upload photo">
                  <Camera size={14} />
                  <input
                    type="file"
                    accept="image/*"
                    onChange={(e) => {
                      const f = e.target.files?.[0] || null;
                      setAvatarFile(f);
                      if (f) setAvatarPreview(URL.createObjectURL(f));
                      setActiveTab("profile");
                    }}
                    className="hidden"
                  />
                </label>
              </div>
              <h2 className="mt-3 text-base font-bold text-foreground leading-tight">{user.name}</h2>
              <p className="text-[11px] text-muted font-medium truncate max-w-full">{user.email}</p>
              <span className="mt-2 inline-flex items-center gap-1 rounded-full bg-primary-soft border border-primary/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-primary">
                {user.role === "admin" ? t("profile.administrator") : t("profile.member")}
              </span>
              {avatarFile && (
                <p className="mt-2 text-[10px] font-semibold text-amber-600">{t("profile.photo_pending")}</p>
              )}
            </div>

            {/* Navigation */}
            <nav className="glass-card p-2 space-y-1">
              <button
                type="button"
                onClick={() => setActiveTab("profile")}
                className={`w-full flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition-colors cursor-pointer ${
                  activeTab === "profile"
                    ? "bg-primary text-white shadow-sm"
                    : "text-foreground hover:bg-surface"
                }`}
              >
                <User size={17} className={activeTab === "profile" ? "text-white" : "text-primary"} />
                <span className="flex-1 text-left">{t("profile.tab_profile")}</span>
                <ChevronRight size={15} className={activeTab === "profile" ? "text-white/80" : "text-muted-foreground"} />
              </button>

              <button
                type="button"
                onClick={() => setActiveTab("business")}
                className={`w-full flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition-colors cursor-pointer ${
                  activeTab === "business"
                    ? "bg-primary text-white shadow-sm"
                    : "text-foreground hover:bg-surface"
                }`}
              >
                <Briefcase size={17} className={activeTab === "business" ? "text-white" : "text-primary"} />
                <span className="flex-1 text-left">{t("profile.tab_business")}</span>
                {business && !bizLoading && (
                  <span
                    className={`h-2 w-2 rounded-full ${
                      business.status === "approved"
                        ? "bg-emerald-500"
                        : business.status === "rejected"
                        ? "bg-red-500"
                        : "bg-amber-500"
                    }`}
                    title={business.status}
                  />
                )}
                <ChevronRight size={15} className={activeTab === "business" ? "text-white/80" : "text-muted-foreground"} />
              </button>

              <button
                type="button"
                onClick={() => setActiveTab("events")}
                className={`w-full flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition-colors cursor-pointer ${
                  activeTab === "events"
                    ? "bg-primary text-white shadow-sm"
                    : "text-foreground hover:bg-surface"
                }`}
              >
                <Calendar size={17} className={activeTab === "events" ? "text-white" : "text-primary"} />
                <span className="flex-1 text-left">{t("profile.tab_events")}</span>
                <span className={`inline-flex h-5 items-center justify-center rounded-full px-2 text-[10px] font-bold ${
                  activeTab === "events"
                    ? "bg-white/20 text-white"
                    : "bg-primary-soft text-primary border border-primary/10"
                }`}>
                  {registeredEvents.length}
                </span>
                <ChevronRight size={15} className={activeTab === "events" ? "text-white/80" : "text-muted-foreground"} />
              </button>

              <div className="my-1 border-t border-border" />

              <button
                type="button"
                onClick={() => { logout(); router.replace("/"); }}
                className="w-full flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50 cursor-pointer"
              >
                <LogOut size={17} />
                <span className="flex-1 text-left">{t("profile.logout")}</span>
              </button>
            </nav>
          </aside>

          {/* ===== Content ===== */}
          <div className="lg:col-span-9 space-y-6">

          {/* Personal Settings */}
          {activeTab === "profile" && (
          <div className="glass-card p-6 space-y-6 h-fit">
            <div className="flex items-center gap-2 border-b border-border pb-3">
              <User size={18} className="text-primary" />
              <div>
                <h2 className="text-lg font-bold text-foreground leading-tight">{t("profile.profile_title")}</h2>
                <p className="text-[11px] text-muted font-medium">{t("profile.profile_subtitle")}</p>
              </div>
            </div>

            {profileError && (
              <div className="rounded-xl bg-red-50 border border-red-100 p-3 text-center text-xs font-semibold text-red-600">
                {profileError}
              </div>
            )}

            {profileSuccess && (
              <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center text-xs font-semibold text-emerald-600">
                {profileSuccess}
              </div>
            )}

            <form onSubmit={handleUpdateProfile} className="space-y-4">
              <div>
                <label className={labelClass}>{t("profile.full_name")}</label>
                <div className="relative">
                  <User className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <input
                    type="text"
                    required
                    value={profileName}
                    onChange={(e) => setProfileName(e.target.value)}
                    className={`${inputClass} pl-10`}
                  />
                </div>
              </div>

              <div>
                <label className={labelClass}>{t("profile.email")}</label>
                <div className="relative">
                  <Mail className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <input
                    type="email"
                    required
                    value={profileEmail}
                    onChange={(e) => setProfileEmail(e.target.value)}
                    className={`${inputClass} pl-10`}
                  />
                </div>
              </div>

              <div>
                <label className={labelClass}>{t("profile.phone")}</label>
                <div className="relative">
                  <Phone className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <input
                    type="text"
                    value={profilePhone}
                    onChange={(e) => setProfilePhone(e.target.value)}
                    placeholder="e.g. +91 98200 12345"
                    className={`${inputClass} pl-10`}
                  />
                </div>
              </div>

              <div>
                <label className={labelClass}>{t("profile.city")}</label>
                <div className="relative">
                  <MapPin className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <input
                    type="text"
                    value={profileCity}
                    onChange={(e) => setProfileCity(e.target.value)}
                    placeholder="e.g. Ahmedabad"
                    className={`${inputClass} pl-10`}
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>{t("profile.designation")}</label>
                  <input
                    type="text"
                    value={profileDesignation}
                    onChange={(e) => setProfileDesignation(e.target.value)}
                    placeholder="e.g. Co-Founder"
                    className={inputClass}
                  />
                </div>
                <div>
                  <label className={labelClass}>{t("profile.company")}</label>
                  <input
                    type="text"
                    value={profileCompany}
                    onChange={(e) => setProfileCompany(e.target.value)}
                    placeholder="e.g. TechCorp"
                    className={inputClass}
                  />
                </div>
              </div>

              <div>
                <label className={labelClass}>{t("profile.bio")}</label>
                <textarea
                  rows={3}
                  value={profileBio}
                  onChange={(e) => setProfileBio(e.target.value)}
                  placeholder="Tell us about your professional background..."
                  className={`${inputClass} resize-none`}
                />
              </div>

              <div>
                <label className={labelClass}>{t("profile.password")}</label>
                <div className="relative">
                  <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                  <input
                    type="password"
                    placeholder="New password"
                    value={profilePassword}
                    onChange={(e) => setProfilePassword(e.target.value)}
                    className={`${inputClass} pl-10`}
                  />
                </div>
              </div>

              <button
                type="submit"
                disabled={profileLoading}
                className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
              >
                {profileLoading ? t("profile.saving_btn") : t("profile.save_btn")}
              </button>
            </form>
          </div>
          )}

          {/* Business Details Section */}
          {activeTab === "business" && (
          <div className="space-y-6">
            <div className="glass-card p-6 space-y-6">
              <div className="flex items-center justify-between border-b border-border pb-3">
                <h2 className="text-lg font-bold text-foreground flex items-center gap-2">
                  <Briefcase size={18} className="text-primary" /> {t("profile.business_title")}
                </h2>
                {business && !bizLoading && (
                  <span className={`inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold ${
                    business.status === "approved"
                      ? "bg-emerald-50 text-emerald-600 border border-emerald-200/50"
                      : business.status === "rejected"
                      ? "bg-red-50 text-red-600 border border-red-200/50"
                      : "bg-amber-50 text-amber-600 border border-amber-200/50"
                  }`}>
                    {business.status === "approved" && <ShieldCheck size={14} />}
                    {business.status === "rejected" && <XCircle size={14} />}
                    {business.status === "pending" && <Clock size={14} />}
                    <span className="capitalize">{business.status}</span>
                  </span>
                )}
              </div>

              {bizLoading ? (
                <div className="py-12 text-center">
                  <div className="inline-block animate-spin rounded-full h-6 w-6 border-2 border-primary border-t-transparent" />
                  <p className="mt-2 text-xs text-muted">{t("profile.checking_business")}</p>
                </div>
              ) : (
                <>
                  {/* ── Progress Steps ── */}
                  <div className="flex items-center gap-0 mb-2">
                    {/* Step 1 */}
                    <div className="flex flex-col items-center flex-1">
                      <div className={`h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-colors ${
                        !business
                          ? "bg-primary border-primary text-white"
                          : "bg-emerald-500 border-emerald-500 text-white"
                      }`}>
                        {business ? <CheckCircle2 size={16} /> : "1"}
                      </div>
                      <p className="text-[10px] font-bold text-center mt-1 text-foreground leading-tight">{t("profile.step_payment")}</p>
                    </div>
                    <div className={`flex-1 h-0.5 -mt-4 mx-1 rounded ${business ? "bg-emerald-400" : "bg-border"}`} />
                    {/* Step 2 */}
                    <div className="flex flex-col items-center flex-1">
                      <div className={`h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-colors ${
                        business?.status === "approved"
                          ? "bg-primary border-primary text-white"
                          : business
                          ? "bg-amber-400 border-amber-400 text-white"
                          : "bg-white border-border text-muted-foreground"
                      }`}>
                        {business?.status === "approved" ? "2" : <Clock size={14} />}
                      </div>
                      <p className={`text-[10px] font-bold text-center mt-1 leading-tight ${business?.status === "approved" ? "text-foreground" : "text-muted"}`}>{t("profile.step_approval")}</p>
                    </div>
                    <div className={`flex-1 h-0.5 -mt-4 mx-1 rounded ${business?.status === "approved" ? "bg-primary/40" : "bg-border"}`} />
                    {/* Step 3 */}
                    <div className="flex flex-col items-center flex-1">
                      <div className={`h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-colors ${
                        business?.status === "approved" && isEditingBusiness
                          ? "bg-primary border-primary text-white"
                          : business?.status === "approved"
                          ? "bg-emerald-500 border-emerald-500 text-white"
                          : "bg-white border-border text-muted-foreground"
                      }`}>
                        {business?.status === "approved" && !isEditingBusiness ? <CheckCircle2 size={16} /> : "3"}
                      </div>
                      <p className={`text-[10px] font-bold text-center mt-1 leading-tight ${business?.status === "approved" ? "text-foreground" : "text-muted"}`}>{t("profile.step_details")}</p>
                    </div>
                  </div>

                  {bizError && (
                    <div className="rounded-xl bg-red-50 border border-red-100 p-3 text-center text-xs font-semibold text-red-600">
                      {bizError}
                    </div>
                  )}
                  {bizSuccess && (
                    <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center text-xs font-semibold text-emerald-600">
                      {bizSuccess}
                    </div>
                  )}

                  {/* ══════════════════════════════════════════════
                      STEP 1: No business yet — payment upload ONLY
                     ══════════════════════════════════════════════ */}
                  {!business && (
                    <div className="space-y-6">
                      <div className="rounded-xl bg-blue-50 border border-blue-100 p-4 flex items-start gap-3">
                        <AlertCircle className="h-5 w-5 text-blue-500 shrink-0 mt-0.5" />
                        <div>
                          <p className="text-sm font-bold text-blue-800">{t("profile.how_it_works")}</p>
                          <ol className="mt-1.5 space-y-1 text-xs text-blue-700 font-medium list-decimal list-inside">
                            <li>{t("profile.biz_step1")}</li>
                            <li>{t("profile.biz_step2")}</li>
                            <li>{t("profile.biz_step3")}</li>
                          </ol>
                        </div>
                      </div>

                      <form onSubmit={handleBusinessSubmit} className="space-y-5">
                        <div className="space-y-2">
                          <h4 className="text-sm font-bold text-foreground flex items-center gap-2">
                            <Upload size={15} className="text-primary" /> {t("profile.upload_payment_title")}
                          </h4>
                          <p className="text-xs text-muted font-medium">{t("profile.upload_payment_desc")}</p>

                          <label className="mt-3 flex flex-col items-center justify-center border-2 border-dashed border-primary/30 rounded-2xl p-10 bg-primary-soft/20 hover:bg-primary-soft/30 transition-colors cursor-pointer relative group">
                            <input
                              type="file"
                              accept="image/*"
                              required
                              onChange={(e) => setPaymentFile(e.target.files?.[0] || null)}
                              className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                            />
                            {paymentFile ? (
                              <>
                                <CheckCircle2 className="h-10 w-10 text-emerald-500 mb-3" />
                                <span className="text-sm font-bold text-foreground text-center">{paymentFile.name}</span>
                                <span className="text-xs text-emerald-600 font-semibold mt-1">{t("profile.screenshot_selected")} ✓</span>
                              </>
                            ) : (
                              <>
                                <div className="h-14 w-14 rounded-2xl bg-primary-soft flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                  <Upload className="h-7 w-7 text-primary" />
                                </div>
                                <span className="text-sm font-semibold text-foreground text-center">{t("profile.click_upload_receipt")}</span>
                                <span className="text-[11px] text-muted mt-1">{t("profile.file_hint")}</span>
                              </>
                            )}
                          </label>
                        </div>

                        <button
                          type="submit"
                          disabled={bizSubmitting || !paymentFile}
                          className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50 cursor-pointer"
                        >
                          {bizSubmitting ? t("profile.biz_submitting") : t("profile.biz_submit_payment")}
                        </button>
                      </form>
                    </div>
                  )}

                  {/* ══════════════════════════════════════════════
                      STEP 2: Payment submitted — waiting for admin
                     ══════════════════════════════════════════════ */}
                  {business && business.status === "pending" && (
                    <div className="space-y-4">
                      <div className="rounded-xl bg-amber-50 border border-amber-100 p-5 flex items-start gap-3">
                        <Clock className="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                        <div>
                          <p className="font-bold text-amber-800">{t("profile.payment_review_title")}</p>
                          <p className="font-medium text-xs mt-1 text-amber-700">{t("profile.payment_review_desc")}</p>
                        </div>
                      </div>

                      {/* Locked details notice */}
                      <div className="rounded-2xl border border-dashed border-border bg-surface/40 p-8 flex flex-col items-center justify-center text-center gap-3">
                        <div className="h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                          <Briefcase size={22} className="text-muted-foreground/50" />
                        </div>
                        <p className="text-sm font-bold text-foreground">{t("profile.details_locked_title")}</p>
                        <p className="text-xs text-muted font-medium max-w-xs">{t("profile.details_locked_desc")}</p>
                      </div>

                      {business.payment_screenshot && (
                        <div>
                          <span className="text-[10px] uppercase font-bold text-muted block mb-2">{t("profile.submitted_screenshot")}</span>
                          <div className="relative rounded-xl border border-border overflow-hidden h-32 w-52 bg-slate-900 group">
                            <img src={assetUrl(business.payment_screenshot)} alt="Payment Screenshot" className="h-full w-full object-cover group-hover:scale-105 transition-transform" />
                            <a href={assetUrl(business.payment_screenshot)} target="_blank" rel="noreferrer" className="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-bold gap-1">
                              <Eye size={14} /> {t("profile.view_full")}
                            </a>
                          </div>
                        </div>
                      )}
                    </div>
                  )}

                  {/* ══════════════════════════════════════════════
                      REJECTED: Let user re-upload payment
                     ══════════════════════════════════════════════ */}
                  {business && business.status === "rejected" && (
                    <div className="space-y-5">
                      <div className="rounded-xl bg-red-50 border border-red-100 p-4 flex items-start gap-3">
                        <XCircle className="h-5 w-5 text-red-600 shrink-0 mt-0.5" />
                        <div>
                          <p className="font-bold text-red-800">{t("profile.payment_rejected_title")}</p>
                          <p className="font-bold text-xs mt-1 text-red-700">{t("profile.biz_reason")}: <span className="underline">{business.rejection_reason || t("profile.rejected_default_reason")}</span></p>
                          <p className="font-medium text-xs mt-2 text-red-700">{t("profile.rejected_resubmit_hint")}</p>
                        </div>
                      </div>
                      <form onSubmit={handleBusinessSubmit} className="space-y-4">
                        <label className="flex flex-col items-center justify-center border-2 border-dashed border-red-200 rounded-2xl p-8 bg-red-50/30 hover:bg-red-50/50 transition-colors cursor-pointer relative">
                          <input type="file" accept="image/*" required onChange={(e) => setPaymentFile(e.target.files?.[0] || null)} className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                          <Upload className="h-8 w-8 text-red-400 mb-2" />
                          <span className="text-sm font-semibold text-foreground">{paymentFile ? paymentFile.name : t("profile.upload_new_screenshot")}</span>
                          <span className="text-[11px] text-muted mt-1">{t("profile.file_hint")}</span>
                        </label>
                        <button type="submit" disabled={bizSubmitting || !paymentFile} className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white disabled:opacity-50 hover:opacity-90">
                          {bizSubmitting ? t("profile.biz_resubmitting") : t("profile.biz_resubmit_payment")}
                        </button>
                      </form>
                    </div>
                  )}

                  {/* ══════════════════════════════════════════════
                      STEP 3: APPROVED — show business details form / view
                     ══════════════════════════════════════════════ */}
                  {business && business.status === "approved" && (
                    <div className="space-y-6">
                      {/* Approved banner */}
                      <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-4 flex items-start gap-3">
                        <CheckCircle2 className="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" />
                        <div>
                          <p className="font-bold text-emerald-800">{t("profile.approved_title")}</p>
                          <p className="font-medium text-xs mt-1 text-emerald-700">{t("profile.approved_desc")}</p>
                        </div>
                      </div>

                      {!isEditingBusiness ? (
                        /* ── View mode ── */
                        <div className="space-y-8">
                          <div className="relative rounded-2xl overflow-hidden border border-border h-48 sm:h-60 bg-slate-900 shadow-sm">
                            <img src={business.cover_image ? assetUrl(business.cover_image) : "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1200&auto=format&fit=crop"} alt="Business Cover" className="h-full w-full object-cover opacity-85" />
                            <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
                            <div className="absolute bottom-4 left-6 flex items-end gap-4">
                              <div className="h-16 w-16 sm:h-20 sm:w-20 rounded-xl bg-white border border-border p-1.5 shadow-md flex items-center justify-center overflow-hidden shrink-0">
                                {business.logo ? <img src={assetUrl(business.logo)} alt="Logo" className="h-full w-full object-contain" /> : <span className="text-2xl font-bold text-primary">{business.name?.[0] ?? "?"}</span>}
                              </div>
                              <div className="text-white pb-1">
                                <h3 className="text-lg sm:text-xl font-bold">{business.name || t("profile.your_business")}</h3>
                                <p className="text-xs font-semibold text-white/80">{business.tagline || t("profile.verified_member")}</p>
                              </div>
                            </div>
                          </div>

                          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="glass-card p-4 flex flex-col gap-1 border border-border/60">
                              <span className="text-[10px] uppercase font-bold text-muted-foreground flex items-center gap-1"><Briefcase size={12} className="text-primary" /> {t("profile.biz_category")}</span>
                              <p className="text-xs font-extrabold text-foreground">{business.category || "—"}</p>
                            </div>
                            <div className="glass-card p-4 flex flex-col gap-1 border border-border/60">
                              <span className="text-[10px] uppercase font-bold text-muted-foreground flex items-center gap-1"><MapPin size={12} className="text-primary" /> {t("profile.biz_location")}</span>
                              <p className="text-xs font-extrabold text-foreground">{business.location || "—"}</p>
                            </div>
                            <div className="glass-card p-4 flex flex-col gap-1 border border-border/60">
                              <span className="text-[10px] uppercase font-bold text-muted-foreground flex items-center gap-1"><Clock size={12} className="text-primary" /> {t("profile.biz_hours")}</span>
                              <p className="text-xs font-extrabold text-foreground">{business.hours || "—"}</p>
                            </div>
                          </div>

                          {business.description && (
                            <div>
                              <span className="text-[10px] uppercase font-bold text-muted">{t("profile.about_company")}</span>
                              <p className="text-xs leading-relaxed text-muted mt-1 font-medium bg-surface/30 p-4 rounded-xl border border-border/60">{business.description}</p>
                            </div>
                          )}

                          {business.services && business.services.length > 0 && (
                            <div>
                              <span className="text-[10px] uppercase font-bold text-muted block mb-2">{t("profile.core_services")}</span>
                              <div className="flex flex-wrap gap-2">
                                {(Array.isArray(business.services) ? business.services : [business.services]).map((s: any, idx: number) => (
                                  <span key={idx} className="rounded-full bg-primary-soft border border-primary/10 px-3 py-1 text-xs font-semibold text-primary">{s}</span>
                                ))}
                              </div>
                            </div>
                          )}

                          <div className="pt-4 border-t border-border">
                            <button type="button" onClick={() => setIsEditingBusiness(true)} className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 cursor-pointer">
                              {t("profile.biz_edit")}
                            </button>
                          </div>
                        </div>
                      ) : (
                        /* ── Edit form (only visible after approval) ── */
                        <form onSubmit={handleBusinessSubmit} className="space-y-6">
                          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                              <label className={labelClass}>{t("profile.biz_name")}</label>
                              <input type="text" required value={bizName} onChange={(e) => setBizName(e.target.value)} placeholder="E.g. Vertex Solutions" className={inputClass} />
                            </div>
                            <div>
                              <label className={labelClass}>{t("profile.biz_category")}</label>
                              <select value={bizCategory} onChange={(e) => setBizCategory(e.target.value)} className={inputClass}>
                                {categories.map((cat) => <option key={cat} value={cat}>{cat}</option>)}
                              </select>
                            </div>
                          </div>

                          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                              <label className={labelClass}>{t("profile.biz_tagline")}</label>
                              <input type="text" value={bizTagline} onChange={(e) => setBizTagline(e.target.value)} placeholder="E.g. Enterprise Cloud Architecture" className={inputClass} />
                            </div>
                            <div>
                              <label className={labelClass}>{t("profile.biz_location")}</label>
                              <input type="text" value={bizLocation} onChange={(e) => setBizLocation(e.target.value)} placeholder="E.g. Mumbai, Maharashtra" className={inputClass} />
                            </div>
                          </div>

                          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                              <label className={labelClass}>{t("profile.biz_website")}</label>
                              <div className="relative">
                                <Globe className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                <input type="url" value={bizWebsite} onChange={(e) => setBizWebsite(e.target.value)} placeholder="e.g. https://vertex.solutions" className={`${inputClass} pl-10`} />
                              </div>
                            </div>
                            <div>
                              <label className={labelClass}>{t("profile.biz_hours")}</label>
                              <div className="relative">
                                <Clock className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                <input type="text" value={bizHours} onChange={(e) => setBizHours(e.target.value)} placeholder="e.g. 9:00 AM - 6:00 PM (Mon - Fri)" className={`${inputClass} pl-10`} />
                              </div>
                            </div>
                          </div>

                          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                              <label className={labelClass}>{t("profile.biz_founded")}</label>
                              <input type="text" value={bizFounded} onChange={(e) => setBizFounded(e.target.value)} placeholder="e.g. 2018" className={inputClass} />
                            </div>
                            <div>
                              <label className={labelClass}>{t("profile.biz_team")}</label>
                              <input type="text" value={bizTeamSize} onChange={(e) => setBizTeamSize(e.target.value)} placeholder="e.g. 45+ engineers" className={inputClass} />
                            </div>
                          </div>

                          <div className="space-y-1.5">
                            <label className={labelClass}>{t("profile.about_company")}</label>
                            <textarea rows={4} required value={bizDescription} onChange={(e) => setBizDescription(e.target.value)} placeholder="Provide a comprehensive summary of your services, goals, and history..." className={`${inputClass} resize-none`} />
                          </div>

                          <div className="space-y-1.5">
                            <label className={labelClass}>{t("profile.biz_services")}</label>
                            <div className="relative">
                              <Layers className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                              <input type="text" value={bizServices} onChange={(e) => setBizServices(e.target.value)} placeholder="e.g. Cloud Migration, Custom ERP, AI Integrations" className={`${inputClass} pl-10`} />
                            </div>
                          </div>

                          <div className="space-y-4 border-t border-border pt-4">
                            <h4 className="text-sm font-bold text-foreground">{t("profile.contact_channels")}</h4>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                <label className={labelClass}>{t("profile.biz_email")}</label>
                                <input type="email" value={bizEmail} onChange={(e) => setBizEmail(e.target.value)} placeholder="e.g. contact@vertex.solutions" className={inputClass} />
                              </div>
                              <div>
                                <label className={labelClass}>{t("profile.biz_phone")}</label>
                                <input type="text" value={bizPhone} onChange={(e) => setBizPhone(e.target.value)} placeholder="e.g. +91 22 5550 1928" className={inputClass} />
                              </div>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                              <div>
                                <label className={labelClass}>{t("profile.biz_instagram")}</label>
                                <div className="relative"><InstagramIcon size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" /><input type="url" value={bizInstagram} onChange={(e) => setBizInstagram(e.target.value)} placeholder="https://instagram.com/handle" className={`${inputClass} pl-10`} /></div>
                              </div>
                              <div>
                                <label className={labelClass}>{t("profile.biz_youtube")}</label>
                                <div className="relative"><YoutubeIcon size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" /><input type="url" value={bizYoutube} onChange={(e) => setBizYoutube(e.target.value)} placeholder="https://youtube.com/c/channel" className={`${inputClass} pl-10`} /></div>
                              </div>
                              <div>
                                <label className={labelClass}>{t("profile.biz_twitter")}</label>
                                <div className="relative"><TwitterIcon size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" /><input type="url" value={bizTwitter} onChange={(e) => setBizTwitter(e.target.value)} placeholder="https://twitter.com/handle" className={`${inputClass} pl-10`} /></div>
                              </div>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                <label className={labelClass}>{t("profile.biz_linkedin")}</label>
                                <div className="relative"><LinkedinIcon size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" /><input type="url" value={bizLinkedin} onChange={(e) => setBizLinkedin(e.target.value)} placeholder="https://linkedin.com/company/name" className={`${inputClass} pl-10`} /></div>
                              </div>
                              <div>
                                <label className={labelClass}>{t("profile.biz_whatsapp")}</label>
                                <div className="relative"><MessageCircle className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" /><input type="text" value={bizWhatsapp} onChange={(e) => setBizWhatsapp(e.target.value)} placeholder="e.g. +919820012345" className={`${inputClass} pl-10`} /></div>
                              </div>
                            </div>
                          </div>

                          <div className="space-y-4 border-t border-border pt-4">
                            <h4 className="text-sm font-bold text-foreground">{t("profile.branding_photos")}</h4>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                              <div>
                                <label className={labelClass}>{t("profile.biz_logo")}</label>
                                <div className="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative">
                                  <input type="file" accept="image/*" onChange={(e) => setLogoFile(e.target.files?.[0] || null)} className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                                  <Upload className="h-6 w-6 text-primary mb-2" />
                                  <span className="text-[11px] font-semibold text-foreground text-center">{logoFile ? logoFile.name : t("profile.choose_logo")}</span>
                                  <span className="text-[9px] text-muted-foreground mt-1">{t("profile.logo_hint")}</span>
                                </div>
                              </div>
                              <div>
                                <label className={labelClass}>{t("profile.biz_cover")}</label>
                                <div className="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative">
                                  <input type="file" accept="image/*" onChange={(e) => setCoverFile(e.target.files?.[0] || null)} className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                                  <Upload className="h-6 w-6 text-primary mb-2" />
                                  <span className="text-[11px] font-semibold text-foreground text-center">{coverFile ? coverFile.name : t("profile.choose_cover")}</span>
                                  <span className="text-[9px] text-muted-foreground mt-1">{t("profile.cover_hint")}</span>
                                </div>
                              </div>
                            </div>
                          </div>

                          <div className="flex gap-4 pt-4 border-t border-border">
                            <button type="button" onClick={() => setIsEditingBusiness(false)} className="flex-1 rounded-xl border border-border bg-white px-4 py-3 text-sm font-semibold text-foreground transition-colors hover:bg-surface cursor-pointer">
                              {t("profile.biz_cancel")}
                            </button>
                            <button type="submit" disabled={bizSubmitting} className="flex-[2] inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer">
                              {bizSubmitting ? t("profile.biz_saving") : t("profile.biz_save_details")}
                            </button>
                          </div>
                        </form>
                      )}
                    </div>
                  )}
                </>
              )}
            </div>
          </div>
          )}




          {/* Registered Events Section */}
          {activeTab === "events" && (
            <div className="space-y-6">
              <div className="glass-card p-6 space-y-6">
                <div>
                  <h2 className="text-lg font-bold text-foreground flex items-center gap-2 border-b border-border pb-3">
                    <Calendar size={18} className="text-primary" /> {t("profile.events_title")}
                  </h2>
                  <p className="mt-2 text-xs text-muted">{t("profile.events_subtitle")}</p>
                </div>

                {registeredEvents.length === 0 ? (
                  <div className="rounded-xl border border-dashed border-border py-12 text-center text-sm text-muted">
                    {t("profile.no_events")}
                  </div>
                ) : (
                  <div className="space-y-4">
                    {registeredEvents.map((evt) => (
                      <Link
                        key={evt.id}
                        href={`/profile/events/${evt.id}`}
                        className="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-border bg-slate-50/50 hover:bg-white hover:shadow-sm hover:border-primary/20 transition-all group"
                      >
                        <div className="h-16 w-28 rounded-lg overflow-hidden shrink-0 bg-slate-100 border border-border">
                          <img src={evt.image} alt={evt.title} className="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        </div>
                        <div className="min-w-0 flex-1">
                          <h4 className="text-sm font-bold text-foreground truncate group-hover:text-primary transition-colors">{evt.title}</h4>
                          <div className="flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-muted font-medium mt-1">
                            <span className="inline-flex items-center gap-1 flex-wrap"><Calendar size={12} /> {evt.date}</span>
                            <span className="inline-flex items-center gap-1 flex-wrap"><MapPin size={12} /> {evt.location}</span>
                            <span className="font-semibold text-foreground">{t("profile.paid")}: {evt.price}</span>
                          </div>
                        </div>
                        <div className="shrink-0 flex flex-col sm:items-end gap-2 pt-2 sm:pt-0">
                          <span className={`inline-flex items-center gap-1 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wide border ${
                            evt.status === "confirmed"
                              ? "bg-emerald-50 text-emerald-700 border-emerald-200/50"
                              : "bg-amber-50 text-amber-700 border-amber-200/50"
                          }`}>
                            {evt.status === "confirmed" ? (
                              <><CheckCircle2 size={12} /> {t("profile.event_confirmed")}</>
                            ) : (
                              <><Clock size={12} /> {t("profile.event_pending")}</>
                            )}
                          </span>
                          <span className="text-[10px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5">
                            {t("profile.view_details")} <ChevronRight size={12} />
                          </span>
                        </div>
                      </Link>
                    ))}
                  </div>
                )}
              </div>
            </div>
          )}
          </div>
        </div>
      </div>
    </div>
  );
}

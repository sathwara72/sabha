"use client";

import { useEffect, useState } from "react";
import { useAuth } from "@/lib/auth";
import { useRouter } from "next/navigation";
import {
  User, Mail, Lock, Briefcase, Globe, CheckCircle2,
  ShieldCheck, Clock, XCircle, AlertCircle, Upload, Eye, MapPin,
  Phone, MessageCircle, Layers, Camera, LogOut, ChevronRight
} from "lucide-react";
import { InstagramIcon, YoutubeIcon, TwitterIcon, LinkedinIcon, WhatsappIcon } from "@/components/SocialIcons";
import { getUserBusiness, submitBusiness, updateProfile } from "@/lib/api";
import { assetUrl } from "@/lib/config";

export default function ProfilePage() {
  const { isAuthenticated, isReady, user, updateLocalUser, openLogin, logout } = useAuth();
  const router = useRouter();

  // Active section in the side menu
  const [activeTab, setActiveTab] = useState<"profile" | "business">("profile");

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
      setProfileSuccess("Profile updated successfully!");
      setProfilePassword("");
    } catch (err: any) {
      setProfileError(err.message || "Failed to update profile details.");
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
      setBizError("Payment screenshot is required for verification.");
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
      setBizSuccess("Business profile submitted successfully!");
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
          <p className="text-sm font-medium text-muted">Loading your profile...</p>
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
                {user.role === "admin" ? "Administrator" : "Member"}
              </span>
              {avatarFile && (
                <p className="mt-2 text-[10px] font-semibold text-amber-600">New photo selected — save to apply</p>
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
                <span className="flex-1 text-left">My Profile</span>
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
                <span className="flex-1 text-left">Business Profile</span>
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

              <div className="my-1 border-t border-border" />

              <button
                type="button"
                onClick={() => { logout(); router.replace("/"); }}
                className="w-full flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-red-600 transition-colors hover:bg-red-50 cursor-pointer"
              >
                <LogOut size={17} />
                <span className="flex-1 text-left">Logout</span>
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
                <h2 className="text-lg font-bold text-foreground leading-tight">Profile Settings</h2>
                <p className="text-[11px] text-muted font-medium">Manage your personal details and account photo</p>
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
                <label className={labelClass}>Full name</label>
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
                <label className={labelClass}>Email address</label>
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
                <label className={labelClass}>Phone Number</label>
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
                <label className={labelClass}>City / Location</label>
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
                  <label className={labelClass}>Designation</label>
                  <input
                    type="text"
                    value={profileDesignation}
                    onChange={(e) => setProfileDesignation(e.target.value)}
                    placeholder="e.g. Co-Founder"
                    className={inputClass}
                  />
                </div>
                <div>
                  <label className={labelClass}>Company</label>
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
                <label className={labelClass}>Short Bio</label>
                <textarea
                  rows={3}
                  value={profileBio}
                  onChange={(e) => setProfileBio(e.target.value)}
                  placeholder="Tell us about your professional background..."
                  className={`${inputClass} resize-none`}
                />
              </div>

              <div>
                <label className={labelClass}>New Password (keep blank to skip)</label>
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
                {profileLoading ? "Updating..." : "Save Profile Details"}
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
                  <Briefcase size={18} className="text-primary" /> Business Listing
                </h2>

                {/* Show status badge */}
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
                  <p className="mt-2 text-xs text-muted">Checking business details...</p>
                </div>
              ) : (
                <>
                  {/* Status Banner Messages */}
                  {business && business.status === "pending" && (
                    <div className="rounded-xl bg-amber-50 border border-amber-100 p-4 text-sm font-semibold text-amber-800 flex items-start gap-3">
                      <AlertCircle className="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                      <div>
                        <p className="font-bold">Your Business profile is in progress</p>
                        <p className="font-medium text-xs mt-1 text-amber-700">Please wait, we will update and approve your listing shortly once the payment is verified.</p>
                      </div>
                    </div>
                  )}

                  {business && business.status === "rejected" && (
                    <div className="rounded-xl bg-red-50 border border-red-100 p-4 text-sm font-semibold text-red-800 flex items-start gap-3">
                      <XCircle className="h-5 w-5 text-red-600 shrink-0 mt-0.5" />
                      <div>
                        <p className="font-bold">Your Business Profile was Rejected</p>
                        <p className="font-bold text-xs mt-1 text-red-700">Reason: <span className="underline">{business.rejection_reason || "Invalid payment details / payment screenshot not verified."}</span></p>
                        <p className="font-medium text-xs mt-2 text-red-700">You can edit the details and upload a new payment screenshot below to resubmit.</p>
                      </div>
                    </div>
                  )}

                  {business && business.status === "approved" && (
                    <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-sm font-semibold text-emerald-800 flex items-start gap-3">
                      <CheckCircle2 className="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" />
                      <div>
                        <p className="font-bold">Your Business profile is active</p>
                        <p className="font-medium text-xs mt-1 text-emerald-700">Your business has been successfully verified and is public on the Sabha Directory.</p>
                      </div>
                    </div>
                  )}

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

                  {/* Render form if no business exists, or if isEditingBusiness is true (only possible if not pending) */}
                  {!business || isEditingBusiness ? (
                    <form onSubmit={handleBusinessSubmit} className="space-y-6">
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                          <label className={labelClass}>Business name</label>
                          <input
                            type="text"
                            required
                            value={bizName}
                            onChange={(e) => setBizName(e.target.value)}
                            placeholder="E.g. Vertex Solutions"
                            className={inputClass}
                          />
                        </div>

                        <div>
                          <label className={labelClass}>Category</label>
                          <select
                            value={bizCategory}
                            onChange={(e) => setBizCategory(e.target.value)}
                            className={inputClass}
                          >
                            {categories.map((cat) => (
                              <option key={cat} value={cat}>{cat}</option>
                            ))}
                          </select>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                          <label className={labelClass}>Tagline</label>
                          <input
                            type="text"
                            value={bizTagline}
                            onChange={(e) => setBizTagline(e.target.value)}
                            placeholder="E.g. Enterprise Cloud Architecture & Digital Solutions"
                            className={inputClass}
                          />
                        </div>

                        <div>
                          <label className={labelClass}>Location (City / State)</label>
                          <input
                            type="text"
                            value={bizLocation}
                            onChange={(e) => setBizLocation(e.target.value)}
                            placeholder="E.g. Mumbai, Maharashtra"
                            className={inputClass}
                          />
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                          <label className={labelClass}>Website URL</label>
                          <div className="relative">
                            <Globe className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input
                              type="url"
                              value={bizWebsite}
                              onChange={(e) => setBizWebsite(e.target.value)}
                              placeholder="e.g. https://vertex.solutions"
                              className={`${inputClass} pl-10`}
                            />
                          </div>
                        </div>

                        <div>
                          <label className={labelClass}>Operating Hours (Time)</label>
                          <div className="relative">
                            <Clock className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input
                              type="text"
                              value={bizHours}
                              onChange={(e) => setBizHours(e.target.value)}
                              placeholder="e.g. 9:00 AM - 6:00 PM (Mon - Fri)"
                              className={`${inputClass} pl-10`}
                            />
                          </div>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                          <label className={labelClass}>Founded Year</label>
                          <input
                            type="text"
                            value={bizFounded}
                            onChange={(e) => setBizFounded(e.target.value)}
                            placeholder="e.g. 2018"
                            className={inputClass}
                          />
                        </div>
                        <div>
                          <label className={labelClass}>Team Size</label>
                          <input
                            type="text"
                            value={bizTeamSize}
                            onChange={(e) => setBizTeamSize(e.target.value)}
                            placeholder="e.g. 45+ engineers"
                            className={inputClass}
                          />
                        </div>
                        <div className="md:col-span-2">
                          <label className={labelClass}>Projects / Volume</label>
                          <input
                            type="text"
                            value={bizProjects}
                            onChange={(e) => setBizProjects(e.target.value)}
                            placeholder="e.g. 120+ projects completed"
                            className={inputClass}
                          />
                        </div>
                      </div>

                      <div className="space-y-1.5">
                        <label className={labelClass}>About The Company (Long Description)</label>
                        <textarea
                          rows={4}
                          required
                          value={bizDescription}
                          onChange={(e) => setBizDescription(e.target.value)}
                          placeholder="Provide a comprehensive summary of your services, goals, and history..."
                          className={`${inputClass} resize-none`}
                        />
                      </div>

                      <div className="space-y-1.5">
                        <label className={labelClass}>Core Services (comma-separated list)</label>
                        <div className="relative">
                          <Layers className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                          <input
                            type="text"
                            value={bizServices}
                            onChange={(e) => setBizServices(e.target.value)}
                            placeholder="e.g. Cloud Migration, Custom ERP Solutions, AI Integrations"
                            className={`${inputClass} pl-10`}
                          />
                        </div>
                        <p className="text-[10px] text-muted-foreground font-semibold mt-1">Separate each service with a comma. They will be formatted as search filters on your listing details page.</p>
                      </div>

                      {/* Contact Channels + Social Media */}
                      <div className="space-y-4 border-t border-border pt-4">
                        <h4 className="text-sm font-bold text-foreground">Contact Channels & Social Media</h4>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div>
                            <label className={labelClass}>Business Email</label>
                            <input
                              type="email"
                              value={bizEmail}
                              onChange={(e) => setBizEmail(e.target.value)}
                              placeholder="e.g. contact@vertex.solutions"
                              className={inputClass}
                            />
                          </div>
                          <div>
                            <label className={labelClass}>Business Phone</label>
                            <input
                              type="text"
                              value={bizPhone}
                              onChange={(e) => setBizPhone(e.target.value)}
                              placeholder="e.g. +91 22 5550 1928"
                              className={inputClass}
                            />
                          </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                          <div>
                            <label className={labelClass}>Instagram URL</label>
                            <div className="relative">
                              <InstagramIcon size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" />
                              <input
                                type="url"
                                value={bizInstagram}
                                onChange={(e) => setBizInstagram(e.target.value)}
                                placeholder="https://instagram.com/handle"
                                className={`${inputClass} pl-10`}
                              />
                            </div>
                          </div>
                          <div>
                            <label className={labelClass}>Youtube Channel URL</label>
                            <div className="relative">
                              <YoutubeIcon size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" />
                              <input
                                type="url"
                                value={bizYoutube}
                                onChange={(e) => setBizYoutube(e.target.value)}
                                placeholder="https://youtube.com/c/channel"
                                className={`${inputClass} pl-10`}
                              />
                            </div>
                          </div>
                          <div>
                            <label className={labelClass}>Twitter / X URL</label>
                            <div className="relative">
                              <TwitterIcon size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" />
                              <input
                                type="url"
                                value={bizTwitter}
                                onChange={(e) => setBizTwitter(e.target.value)}
                                placeholder="https://twitter.com/handle"
                                className={`${inputClass} pl-10`}
                              />
                            </div>
                          </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div>
                            <label className={labelClass}>LinkedIn Company / Profile URL</label>
                            <div className="relative">
                              <LinkedinIcon size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" />
                              <input
                                type="url"
                                value={bizLinkedin}
                                onChange={(e) => setBizLinkedin(e.target.value)}
                                placeholder="https://linkedin.com/company/name"
                                className={`${inputClass} pl-10`}
                              />
                            </div>
                          </div>
                          <div>
                            <label className={labelClass}>Direct WhatsApp Number</label>
                            <div className="relative">
                              <MessageCircle className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                              <input
                                type="text"
                                value={bizWhatsapp}
                                onChange={(e) => setBizWhatsapp(e.target.value)}
                                placeholder="e.g. +919820012345 (no spaces/dashes)"
                                className={`${inputClass} pl-10`}
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      {/* Photo Uploads */}
                      <div className="space-y-4 border-t border-border pt-4">
                        <h4 className="text-sm font-bold text-foreground">Business Branding Photos</h4>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                          <div>
                            <label className={labelClass}>Profile Photo / Logo</label>
                            <div className="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative">
                              <input
                                type="file"
                                accept="image/*"
                                onChange={(e) => setLogoFile(e.target.files?.[0] || null)}
                                className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                              />
                              <Upload className="h-6 w-6 text-primary mb-2" />
                              <span className="text-[11px] font-semibold text-foreground text-center">
                                {logoFile ? logoFile.name : "Choose Profile / Logo"}
                              </span>
                              <span className="text-[9px] text-muted-foreground mt-1">Square ratio recommended (PNG, JPG)</span>
                            </div>
                          </div>

                          <div>
                            <label className={labelClass}>Cover Photo / Banner</label>
                            <div className="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative">
                              <input
                                type="file"
                                accept="image/*"
                                onChange={(e) => setCoverFile(e.target.files?.[0] || null)}
                                className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                              />
                              <Upload className="h-6 w-6 text-primary mb-2" />
                              <span className="text-[11px] font-semibold text-foreground text-center">
                                {coverFile ? coverFile.name : "Choose Cover Banner"}
                              </span>
                              <span className="text-[9px] text-muted-foreground mt-1">Landscape ratio (16:9 or 3:1, up to 10MB)</span>
                            </div>
                          </div>
                        </div>
                      </div>

                      {/* Payment Screenshot Upload */}
                      <div className="space-y-2 border-t border-border pt-4">
                        <h4 className="text-sm font-bold text-foreground">Payment Verification</h4>
                        <p className="text-xs text-muted font-medium">To join, make the community transfer fee and upload the payment receipt/screenshot below.</p>
                        
                        <div className="mt-3 flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative">
                          <input
                            type="file"
                            accept="image/*"
                            required={!business}
                            onChange={(e) => setPaymentFile(e.target.files?.[0] || null)}
                            className="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                          />
                          <Upload className="h-8 w-8 text-primary mb-2" />
                          <span className="text-xs font-semibold text-foreground">
                            {paymentFile ? paymentFile.name : "Click to upload payment receipt / screenshot"}
                          </span>
                          <span className="text-[10px] text-muted-foreground mt-1">PNG, JPG or WEBP up to 10MB</span>
                        </div>
                      </div>

                      <div className="flex gap-4 pt-4 border-t border-border">
                        {business && (
                          <button
                            type="button"
                            onClick={() => setIsEditingBusiness(false)}
                            className="flex-1 rounded-xl border border-border bg-white px-4 py-3 text-sm font-semibold text-foreground transition-colors hover:bg-surface cursor-pointer text-center font-outfit"
                          >
                            Cancel
                          </button>
                        )}
                        <button
                          type="submit"
                          disabled={bizSubmitting}
                          className="flex-[2] inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer font-outfit"
                        >
                          {bizSubmitting ? "Submitting..." : business ? "Resubmit Business Details" : "Submit Business & Payment"}
                        </button>
                      </div>
                    </form>
                  ) : (
                    /* Display Business Information mode */
                    <div className="space-y-8">
                      {/* Cover Image Banner */}
                      <div className="relative rounded-2xl overflow-hidden border border-border h-48 sm:h-60 bg-slate-900 shadow-sm">
                        <img
                          src={business.cover_image ? assetUrl(business.cover_image) : "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1200&auto=format&fit=crop"}
                          alt="Business Cover"
                          className="h-full w-full object-cover opacity-85"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />
                        
                        {/* Profile Logo overlay */}
                        <div className="absolute bottom-4 left-6 flex items-end gap-4">
                          <div className="h-16 w-16 sm:h-20 sm:w-20 rounded-xl bg-white border border-border p-1.5 shadow-md flex items-center justify-center overflow-hidden shrink-0">
                            {business.logo ? (
                              <img
                                src={assetUrl(business.logo)}
                                alt="Logo"
                                className="h-full w-full object-contain"
                              />
                            ) : (
                              <span className="text-2xl font-bold text-primary">{business.name?.[0] ?? "?"}</span>
                            )}
                          </div>
                          <div className="text-white pb-1">
                            <h3 className="text-lg sm:text-xl font-bold">{business.name}</h3>
                            <p className="text-xs font-semibold text-white/80">{business.tagline || "Sabha Vetted Member"}</p>
                          </div>
                        </div>
                      </div>

                      {/* Info grid */}
                      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="glass-card p-4 flex flex-col gap-1 border border-border/60">
                          <span className="text-[10px] uppercase font-bold text-muted-foreground flex items-center gap-1">
                            <Briefcase size={12} className="text-primary" /> Category
                          </span>
                          <p className="text-xs font-extrabold text-foreground">{business.category}</p>
                        </div>
                        
                        <div className="glass-card p-4 flex flex-col gap-1 border border-border/60">
                          <span className="text-[10px] uppercase font-bold text-muted-foreground flex items-center gap-1">
                            <MapPin size={12} className="text-primary" /> Location
                          </span>
                          <p className="text-xs font-extrabold text-foreground">{business.location || "Not defined"}</p>
                        </div>

                        <div className="glass-card p-4 flex flex-col gap-1 border border-border/60">
                          <span className="text-[10px] uppercase font-bold text-muted-foreground flex items-center gap-1">
                            <Clock size={12} className="text-primary" /> Operating Hours
                          </span>
                          <p className="text-xs font-extrabold text-foreground">{business.hours || "Not defined"}</p>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                        <div className="bg-surface/50 rounded-xl p-4.5 border border-border/30">
                          <p className="text-lg font-extrabold text-slate-800">{business.founded || "-"}</p>
                          <p className="text-[10px] text-muted-foreground font-bold uppercase mt-0.5">Founded Year</p>
                        </div>
                        <div className="bg-surface/50 rounded-xl p-4.5 border border-border/30">
                          <p className="text-lg font-extrabold text-slate-800">{business.team_size || "-"}</p>
                          <p className="text-[10px] text-muted-foreground font-bold uppercase mt-0.5">Team Size</p>
                        </div>
                        <div className="bg-surface/50 rounded-xl p-4.5 border border-border/30">
                          <p className="text-lg font-extrabold text-slate-800">{business.projects || "-"}</p>
                          <p className="text-[10px] text-muted-foreground font-bold uppercase mt-0.5">Projects / Volume</p>
                        </div>
                      </div>

                      {/* Services */}
                      {business.services && business.services.length > 0 && (
                        <div>
                          <span className="text-[10px] uppercase font-bold text-muted block mb-2">Core Services</span>
                          <div className="flex flex-wrap gap-2">
                            {(Array.isArray(business.services) ? business.services : [business.services]).map((s: any, idx: number) => (
                              <span key={idx} className="rounded-full bg-primary-soft border border-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                                {s}
                              </span>
                            ))}
                          </div>
                        </div>
                      )}

                      {/* Description */}
                      <div>
                        <span className="text-[10px] uppercase font-bold text-muted">About The Company</span>
                        <p className="text-xs leading-relaxed text-muted mt-1 font-medium bg-surface/30 p-4 rounded-xl border border-border/60">
                          {business.description || "No description provided."}
                        </p>
                      </div>

                      {/* Contact & Social Channels */}
                      <div className="space-y-3 pt-4 border-t border-border">
                        <span className="text-[10px] uppercase font-bold text-muted block">Contact Channels & Social Media</span>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                          {business.email && (
                            <div className="flex items-center gap-2.5 text-xs text-muted-foreground font-semibold">
                              <span className="flex h-8 w-8 items-center justify-center bg-surface rounded-lg border border-border text-primary shrink-0"><Mail size={14} /></span>
                              <span>Email: {business.email}</span>
                            </div>
                          )}
                          {business.phone && (
                            <div className="flex items-center gap-2.5 text-xs text-muted-foreground font-semibold">
                              <span className="flex h-8 w-8 items-center justify-center bg-surface rounded-lg border border-border text-primary shrink-0"><Phone size={14} /></span>
                              <span>Phone: {business.phone}</span>
                            </div>
                          )}
                          {business.website && (
                            <div className="flex items-center gap-2.5 text-xs text-muted-foreground font-semibold">
                              <span className="flex h-8 w-8 items-center justify-center bg-surface rounded-lg border border-border text-primary shrink-0"><Globe size={14} /></span>
                              <a href={business.website} target="_blank" rel="noreferrer" className="hover:underline text-primary">Website: {business.website}</a>
                            </div>
                          )}
                          {business.whatsapp && (
                            <div className="flex items-center gap-2.5 text-xs text-muted-foreground font-semibold">
                              <span className="flex h-8 w-8 items-center justify-center bg-surface rounded-lg border border-border text-emerald-600 shrink-0"><MessageCircle size={14} /></span>
                              <a href={`https://wa.me/${business.whatsapp.replace(/[^0-9]/g, "")}`} target="_blank" rel="noreferrer" className="hover:underline text-emerald-600">WhatsApp: Connect Direct</a>
                            </div>
                          )}
                        </div>

                        {/* Social Icons row */}
                        <div className="flex items-center gap-2.5 mt-3">
                          {business.instagram && (
                            <a href={business.instagram} target="_blank" rel="noreferrer" className="h-9 w-9 rounded-xl bg-gradient-to-br from-pink-500 via-red-500 to-yellow-500 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Instagram">
                              <InstagramIcon size={15} />
                            </a>
                          )}
                          {business.youtube && (
                            <a href={business.youtube} target="_blank" rel="noreferrer" className="h-9 w-9 rounded-xl bg-red-600 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Youtube">
                              <YoutubeIcon size={15} />
                            </a>
                          )}
                          {business.twitter && (
                            <a href={business.twitter} target="_blank" rel="noreferrer" className="h-9 w-9 rounded-xl bg-black text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="Twitter / X">
                              <TwitterIcon size={15} />
                            </a>
                          )}
                          {business.linkedin && (
                            <a href={business.linkedin} target="_blank" rel="noreferrer" className="h-9 w-9 rounded-xl bg-[#0A66C2] text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="LinkedIn">
                              <LinkedinIcon size={15} />
                            </a>
                          )}
                          {business.whatsapp && (
                            <a href={`https://wa.me/${business.whatsapp.replace(/[^0-9]/g, '')}`} target="_blank" rel="noreferrer" className="h-9 w-9 rounded-xl bg-[#25D366] text-white flex items-center justify-center transition-transform hover:scale-110 shadow-sm" title="WhatsApp">
                              <WhatsappIcon size={15} />
                            </a>
                          )}
                        </div>
                      </div>

                      {business.payment_screenshot && (
                        <div className="border-t border-border pt-4">
                          <span className="text-[10px] uppercase font-bold text-muted block mb-2">Community Fee Verification Screenshot</span>
                          <div className="relative rounded-xl border border-border overflow-hidden h-32 w-52 bg-slate-900 group">
                            <img
                              src={assetUrl(business.payment_screenshot)}
                              alt="Payment Screenshot"
                              className="h-full w-full object-cover group-hover:scale-105 transition-transform"
                            />
                            <a
                              href={assetUrl(business.payment_screenshot)}
                              target="_blank"
                              rel="noreferrer"
                              className="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-bold gap-1"
                            >
                              <Eye size={14} /> View Full Receipt
                            </a>
                          </div>
                        </div>
                      )}

                      {/* Disable editing if pending */}
                      {business.status !== "pending" && (
                        <div className="pt-4 border-t border-border">
                          <button
                            type="button"
                            onClick={() => setIsEditingBusiness(true)}
                            className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
                          >
                            Edit Business Details
                          </button>
                        </div>
                      )}
                    </div>
                  )}
                </>
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

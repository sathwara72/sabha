"use client";

import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import {
  Mail,
  Phone,
  MapPin,
  Send,
  Globe,
  MessageCircle,
  Link as LinkIcon,
  CheckCircle2,
  Clock,
  ShieldCheck
} from "lucide-react";
import PageHeader from "@/components/shared/PageHeader";
import { useLanguage } from "@/lib/language";
import { fetchSettings, submitContactInquiry } from "@/lib/api";

interface Coordinator {
  city: string;
  contact: string;
  phone: string;
  email: string;
  bg?: string;
  border?: string;
}

export default function ContactPage() {
  const { t } = useLanguage();
  const [inquiryType, setInquiryType] = useState("Membership");
  const [formSubmitted, setFormSubmitted] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState("");
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    subject: "",
    message: ""
  });

  const [contactEmail, setContactEmail] = useState("hello@sabha.global");
  const [responseTime, setResponseTime] = useState("Within 1 Business Day");
  const [coordinators, setCoordinators] = useState<Coordinator[]>([
    {
      city: "Mumbai Coordinator",
      contact: "Ravi Sharma",
      phone: "+91 98200 12345",
      email: "mumbai@sabha.global",
      bg: "bg-blue-50/50",
      border: "border-blue-100"
    },
    {
      city: "Pune Coordinator",
      contact: "Pooja Verma",
      phone: "+91 96110 54321",
      email: "pune@sabha.global",
      bg: "bg-emerald-50/50",
      border: "border-emerald-100"
    },
    {
      city: "Ahmedabad Coordinator",
      contact: "Dev Patel",
      phone: "+91 94260 98765",
      email: "ahmedabad@sabha.global",
      bg: "bg-amber-50/50",
      border: "border-amber-100"
    }
  ]);

  useEffect(() => {
    async function loadSettings() {
      try {
        const data = await fetchSettings();
        if (data.contact_email) setContactEmail(data.contact_email);
        if (data.response_time) setResponseTime(data.response_time);
        if (data.coordinators) {
          const coords = typeof data.coordinators === "string" 
            ? JSON.parse(data.coordinators) 
            : data.coordinators;
          
          const styledCoords = coords.map((c: any, idx: number) => {
            const styles = [
              { bg: "bg-blue-50/50", border: "border-blue-100" },
              { bg: "bg-emerald-50/50", border: "border-emerald-100" },
              { bg: "bg-amber-50/50", border: "border-amber-100" },
            ];
            return {
              ...c,
              ...(styles[idx % styles.length])
            };
          });
          setCoordinators(styledCoords);
        }
      } catch (err) {
        console.error("Failed to load settings:", err);
      }
    }
    loadSettings();
  }, []);



  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.message) return;

    setIsSubmitting(true);
    setSubmitError("");
    setFormSubmitted(false);

    try {
      await submitContactInquiry({
        name: formData.name,
        email: formData.email,
        subject: formData.subject || "Contact Page Inquiry",
        message: formData.message,
        category: inquiryType,
      });

      setFormSubmitted(true);
      setFormData({ name: "", email: "", subject: "", message: "" });
      
      // Reset success state after 7 seconds
      setTimeout(() => {
        setFormSubmitted(false);
      }, 7000);
    } catch (err: any) {
      console.error("Failed to send inquiry:", err);
      setSubmitError(err.message || "Failed to send message. Please try again.");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="bg-background">
      {/* Hero */}
      <PageHeader
        kicker={t("contact.kicker")}
        title={t("contact.title")}
        subtitle={t("contact.subtitle")}
      />

      <div className="mx-auto max-w-7xl px-6 py-5 lg:py-4">
        <div className="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-5">
          
          {/* Contact details & Chapters info */}
          <div className="lg:col-span-5 space-y-6">
            <div>
              <span className="text-xs font-bold uppercase tracking-wider text-primary">{t("contact.get_in_touch")}</span>
              <h2 className="mt-1.5 text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">
                {t("contact.reach_title")}
              </h2>
              <p className="mt-2 text-xs leading-relaxed text-muted font-medium">
                {t("contact.reach_subtitle")}
              </p>
            </div>

            {/* Direct Contacts */}
            <div className="space-y-3">
              <div className="glass-card flex items-center gap-3 p-3.5">
                <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                  <Mail size={16} />
                </div>
                <div>
                  <h3 className="text-[10px] font-bold text-muted uppercase">{t("contact.general_email")}</h3>
                  <p className="text-xs font-extrabold text-slate-900">{contactEmail}</p>
                </div>
              </div>
              <div className="glass-card flex items-center gap-3 p-3.5">
                <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                  <Clock size={16} />
                </div>
                <div>
                  <h3 className="text-[10px] font-bold text-muted uppercase">{t("contact.response_time")}</h3>
                  <p className="text-xs font-extrabold text-slate-900">{responseTime}</p>
                </div>
              </div>
            </div>

            {/* Chapter Coordinators */}
            <div className="space-y-3 pt-4 border-t border-border/80">
              <h3 className="text-xs font-bold uppercase tracking-wider text-foreground">{t("contact.regional_contacts")}</h3>
              <div className="grid grid-cols-1 gap-2.5">
                {coordinators.map((ch, idx) => (
                  <div key={idx} className={`rounded-xl border ${ch.border || "border-border"} ${ch.bg || "bg-white"} p-3 space-y-1.5`}>
                    <div className="flex justify-between items-center">
                      <span className="text-xs font-extrabold text-foreground">
                        {(() => {
                          const translationKey = `contact.${ch.city.toLowerCase().replace(/ /g, "_")}`;
                          const translated = t(translationKey);
                          return translated === translationKey ? ch.city : translated;
                        })()}
                      </span>
                      <span className="inline-flex items-center gap-1 text-[8px] font-bold bg-white text-primary border border-border px-2 py-0.5 rounded-full">
                        {t("contact.coordinator")}
                      </span>
                    </div>
                    <div className="space-y-0.5">
                      <p className="text-xs font-extrabold text-slate-900">{ch.contact}</p>
                      <div className="flex flex-wrap gap-x-3 text-[11px] text-muted font-medium">
                        <span className="flex items-center gap-1"><Phone size={11} className="text-primary" /> {ch.phone}</span>
                        <span className="flex items-center gap-1"><Mail size={11} className="text-primary" /> {ch.email}</span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Contact form */}
          <div className="lg:col-span-7">
            <div className="glass-card p-5 border border-border/85">
              <h3 className="text-sm font-bold text-foreground">{t("contact.send_message")}</h3>
              <p className="text-xs text-muted font-medium mt-0.5">{t("contact.send_subtitle")}</p>
              
              <form onSubmit={handleSubmit} className="mt-4 space-y-3">
                
                {/* Inquiry Type Radio/Tabs */}
                <div className="space-y-1.5">
                  <label className="text-[10px] font-bold uppercase tracking-wider text-muted">{t("contact.inquiry_category")}</label>
                  <div className="flex flex-wrap gap-1.5">
                    {[
                      { value: "Membership", tKey: "contact.inquiry_membership" },
                      { value: "Sponsorship", tKey: "contact.inquiry_sponsorship" },
                      { value: "Event hosting", tKey: "contact.inquiry_event" },
                      { value: "Technical Support", tKey: "contact.inquiry_support" },
                    ].map(({ value, tKey }) => (
                      <button
                        key={value}
                        type="button"
                        onClick={() => setInquiryType(value)}
                        className={`rounded-lg border px-3 py-1.5 text-xs font-bold transition-all ${
                          inquiryType === value
                            ? "border-primary bg-primary text-white shadow-sm"
                            : "border-border bg-white text-muted hover:bg-surface hover:text-foreground"
                        }`}
                      >
                        {t(tKey)}
                      </button>
                    ))}
                  </div>
                </div>

                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                  <div className="space-y-1">
                    <label className="text-[10px] font-bold text-foreground uppercase tracking-wide">{t("contact.full_name")}</label>
                    <input
                      type="text"
                      required
                      placeholder={t("contact.ph_name")}
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      className="w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground"
                    />
                  </div>
                  <div className="space-y-1">
                    <label className="text-[10px] font-bold text-foreground uppercase tracking-wide">{t("contact.email_address")}</label>
                    <input
                      type="email"
                      required
                      placeholder={t("contact.ph_email")}
                      value={formData.email}
                      onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                      className="w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground"
                    />
                  </div>
                </div>

                <div className="space-y-1">
                  <label className="text-[10px] font-bold text-foreground uppercase tracking-wide">{t("contact.subject")}</label>
                  <input
                    type="text"
                    required
                    placeholder={t("contact.ph_subject")}
                    value={formData.subject}
                    onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                    className="w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground"
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[10px] font-bold text-foreground uppercase tracking-wide">{t("contact.your_message")}</label>
                  <textarea
                    required
                    rows={4}
                    placeholder={t("contact.ph_message")}
                    value={formData.message}
                    onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                    className="w-full resize-none rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground"
                  />
                </div>

                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {isSubmitting ? "Sending..." : t("contact.send_inquiry")}
                  {!isSubmitting && <Send className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />}
                </button>

                <AnimatePresence>
                  {submitError && (
                    <motion.div
                      initial={{ opacity: 0, y: 8 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0 }}
                      className="flex items-center gap-2.5 rounded-xl border border-red-200 bg-red-50 p-4 text-xs font-bold text-red-700"
                    >
                      <div className="h-5 w-5 rounded-full border-2 border-red-600 flex items-center justify-center text-[10px] font-extrabold shrink-0">✕</div>
                      <p>{submitError}</p>
                    </motion.div>
                  )}

                  {formSubmitted && (
                    <motion.div
                      initial={{ opacity: 0, y: 8 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0 }}
                      className="flex items-center gap-2.5 rounded-xl border border-green-200 bg-green-50 p-4 text-xs font-bold text-green-700"
                    >
                      <CheckCircle2 className="h-5 w-5 text-green-600 shrink-0" />
                      <div>
                        <p>{t("contact.success_msg")}</p>
                        <p className="text-[10px] text-green-600 font-semibold mt-0.5">{t("contact.success_note")}</p>
                      </div>
                    </motion.div>
                  )}
                </AnimatePresence>

              </form>
            </div>
          </div>

        </div>
      </div>

      {/* Trust verification banner */}
      <section className="border-t border-border bg-surface">
        <div className="mx-auto max-w-4xl px-4 py-5 text-center space-y-3">
          <div className="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-primary-soft text-primary">
            <ShieldCheck className="h-4 w-4" />
          </div>
          <h2 className="text-xl font-extrabold tracking-tight text-foreground sm:text-2xl">
            {t("contact.integrity_title")}
          </h2>
          <p className="mx-auto max-w-xl text-xs leading-relaxed text-muted font-semibold">
            {t("contact.integrity_desc")}
          </p>
        </div>
      </section>
    </div>
  );
}

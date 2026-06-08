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
import { fetchSettings } from "@/lib/api";

interface Coordinator {
  city: string;
  contact: string;
  phone: string;
  email: string;
  bg?: string;
  border?: string;
}

export default function ContactPage() {
  const [inquiryType, setInquiryType] = useState("Membership");
  const [formSubmitted, setFormSubmitted] = useState(false);
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
          const parsed = typeof data.coordinators === "string" 
            ? JSON.parse(data.coordinators) 
            : data.coordinators;
          if (Array.isArray(parsed) && parsed.length > 0) {
            const styles = [
              { bg: "bg-blue-50/50", border: "border-blue-100" },
              { bg: "bg-emerald-50/50", border: "border-emerald-100" },
              { bg: "bg-amber-50/50", border: "border-amber-100" }
            ];
            const styledCoords = parsed.map((item: any, idx: number) => ({
              ...item,
              bg: styles[idx % styles.length].bg,
              border: styles[idx % styles.length].border
            }));
            setCoordinators(styledCoords);
          }
        }
      } catch (err) {
        console.error("Error loading contact settings:", err);
      }
    }
    loadSettings();
  }, []);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.message) return;
    setFormSubmitted(true);
    // Reset state after 5 seconds
    setTimeout(() => {
      setFormSubmitted(false);
      setFormData({ name: "", email: "", subject: "", message: "" });
    }, 5000);
  };

  return (
    <div className="bg-background">
      {/* Hero */}
      <PageHeader
        kicker="Sathwara Association of Business, Harmony & Advancement"
        title="Contact SABHA"
        subtitle="Have questions about listing your business, joining upcoming events, or general partnerships? Get in touch with our team."
      />

      <div className="mx-auto max-w-7xl px-6 py-20 lg:py-24">
        <div className="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-16">
          
          {/* Contact details & Chapters info */}
          <div className="lg:col-span-5 space-y-10">
            <div>
              <span className="text-xs font-bold uppercase tracking-wider text-primary">Get in Touch</span>
              <h2 className="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
                Reach Our Committee
              </h2>
              <p className="mt-3 text-sm leading-relaxed text-muted font-medium">
                Pick the coordinator closest to your area or complete the direct inquiry form.
              </p>
            </div>

            {/* Direct Contacts */}
            <div className="space-y-4">
              <div className="glass-card flex items-start gap-4 p-5">
                <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                  <Mail size={18} />
                </div>
                <div>
                  <h3 className="text-[10px] font-bold text-muted uppercase">General Email</h3>
                  <p className="mt-1 text-sm font-extrabold text-slate-900">{contactEmail}</p>
                </div>
              </div>
              <div className="glass-card flex items-start gap-4 p-5">
                <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                  <Clock size={18} />
                </div>
                <div>
                  <h3 className="text-[10px] font-bold text-muted uppercase">Response Time</h3>
                  <p className="mt-1 text-sm font-extrabold text-slate-900">{responseTime}</p>
                </div>
              </div>
            </div>

            {/* Chapter Coordinators */}
            <div className="space-y-4 pt-6 border-t border-border/80">
              <h3 className="text-xs font-bold uppercase tracking-wider text-foreground">Regional Contacts</h3>
              <div className="grid grid-cols-1 gap-4">
                {coordinators.map((ch, idx) => (
                  <div key={idx} className={`rounded-2xl border ${ch.border || "border-border"} ${ch.bg || "bg-white"} p-4.5 space-y-2.5`}>
                    <div className="flex justify-between items-center">
                      <span className="text-xs font-extrabold text-foreground">{ch.city}</span>
                      <span className="inline-flex items-center gap-1 text-[8px] font-bold bg-white text-primary border border-border px-2 py-0.5 rounded-full">
                        Coordinator
                      </span>
                    </div>
                    <div className="space-y-1">
                      <p className="text-sm font-extrabold text-slate-900">{ch.contact}</p>
                      <div className="flex flex-wrap gap-x-4 text-[11px] text-muted font-medium">
                        <span className="flex items-center gap-1"><Phone size={12} className="text-primary" /> {ch.phone}</span>
                        <span className="flex items-center gap-1"><Mail size={12} className="text-primary" /> {ch.email}</span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Contact form */}
          <div className="lg:col-span-7">
            <div className="glass-card p-7 sm:p-9 border border-border/85">
              <h3 className="text-lg font-bold text-foreground">Send a Message</h3>
              <p className="text-xs text-muted font-medium mt-1">Fill out the details below and we will route it to the appropriate coordinator.</p>
              
              <form onSubmit={handleSubmit} className="mt-8 space-y-6">
                
                {/* Inquiry Type Radio/Tabs */}
                <div className="space-y-2">
                  <label className="text-[11px] font-bold uppercase tracking-wider text-muted">Inquiry Category</label>
                  <div className="flex flex-wrap gap-2">
                    {["Membership", "Sponsorship", "Event hosting", "Technical Support"].map((type) => (
                      <button
                        key={type}
                        type="button"
                        onClick={() => setInquiryType(type)}
                        className={`rounded-xl border px-3.5 py-2 text-xs font-bold transition-all ${
                          inquiryType === type
                            ? "border-primary bg-primary text-white shadow-sm"
                            : "border-border bg-white text-muted hover:bg-surface hover:text-foreground"
                        }`}
                      >
                        {type}
                      </button>
                    ))}
                  </div>
                </div>

                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                  <div className="space-y-1.5">
                    <label className="text-xs font-bold text-foreground">Full Name</label>
                    <input
                      type="text"
                      required
                      placeholder="e.g. John Doe"
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      className="w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground"
                    />
                  </div>
                  <div className="space-y-1.5">
                    <label className="text-xs font-bold text-foreground">Email Address</label>
                    <input
                      type="email"
                      required
                      placeholder="e.g. hello@domain.com"
                      value={formData.email}
                      onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                      className="w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground"
                    />
                  </div>
                </div>

                <div className="space-y-1.5">
                  <label className="text-xs font-bold text-foreground">Subject</label>
                  <input
                    type="text"
                    required
                    placeholder="e.g. Partnering with Mumbai chapter"
                    value={formData.subject}
                    onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                    className="w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground"
                  />
                </div>

                <div className="space-y-1.5">
                  <label className="text-xs font-bold text-foreground">Your Message</label>
                  <textarea
                    required
                    rows={5}
                    placeholder="Write your details here..."
                    value={formData.message}
                    onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                    className="w-full resize-none rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground"
                  />
                </div>

                <button
                  type="submit"
                  className="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]"
                >
                  Send Inquiry
                  <Send className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                </button>

                <AnimatePresence>
                  {formSubmitted && (
                    <motion.div
                      initial={{ opacity: 0, y: 8 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0 }}
                      className="flex items-center gap-2.5 rounded-xl border border-green-200 bg-green-50 p-4 text-xs font-bold text-green-700"
                    >
                      <CheckCircle2 className="h-5 w-5 text-green-600 shrink-0" />
                      <div>
                        <p>Inquiry sent successfully!</p>
                        <p className="text-[10px] text-green-600 font-semibold mt-0.5">We will route this to the regional coordinator.</p>
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
        <div className="mx-auto max-w-4xl px-6 py-16 text-center space-y-4">
          <div className="mx-auto flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-soft text-primary">
            <ShieldCheck className="h-5 w-5" />
          </div>
          <h2 className="text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">
            SABHA Integrity System
          </h2>
          <p className="mx-auto mt-2 max-w-xl text-xs leading-relaxed text-muted font-semibold">
            We review and moderate all listings and member requests to ensure a safe, collaborative, and professional environment. Your inquiries are treated with confidentiality.
          </p>
        </div>
      </section>
    </div>
  );
}

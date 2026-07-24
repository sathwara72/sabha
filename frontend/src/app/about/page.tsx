"use client";

import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import {
  Users,
  Target,
  ShieldCheck,
  Heart,
  Award,
  ArrowRight,
  Zap,
  TrendingUp,
  Briefcase,
  MapPin,
  Calendar
} from "lucide-react";
import Link from "next/link";
import PageHeader from "@/components/shared/PageHeader";
import { fetchStatistics, fetchSettings } from "@/lib/api";
import { useLanguage } from "@/lib/language";
import { useAuth } from "@/lib/auth";
import { API_ORIGIN, assetUrl } from "@/lib/config";

const values = [
  {
    tKey: "value_1",
    icon: ShieldCheck,
    color: "text-blue-600",
    bg: "bg-blue-50"
  },
  {
    tKey: "value_2",
    icon: Users,
    color: "text-emerald-600",
    bg: "bg-emerald-50"
  },
  {
    tKey: "value_3",
    icon: Heart,
    color: "text-rose-600",
    bg: "bg-rose-50"
  },
  {
    tKey: "value_4",
    icon: TrendingUp,
    color: "text-amber-600",
    bg: "bg-amber-50"
  },
];

const fallbackTeam = [
  {
    tKey: "team_member_1",
    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150&auto=format&fit=crop"
  },
  {
    tKey: "team_member_2",
    avatar: "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=150&auto=format&fit=crop"
  },
  {
    tKey: "team_member_3",
    avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=150&auto=format&fit=crop"
  },
  {
    tKey: "team_member_4",
    avatar: "https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=150&auto=format&fit=crop"
  }
];

const milestones = [
  {
    year: "2024",
    tKey: "milestone_1"
  },
  {
    year: "2025",
    tKey: "milestone_2"
  },
  {
    year: "2026",
    tKey: "milestone_3"
  }
];

export default function AboutPage() {
  const { t } = useLanguage();
  const { isAuthenticated, openRegister } = useAuth();
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState({
    members: "500+",
    businessExchanged: "₹10Cr+",
    monthlyMixers: "50+"
  });
  const [team, setTeam] = useState<any[]>([]);

  useEffect(() => {
    async function loadStats() {
      try {
        setLoading(true);
        const [statData, settingsData] = await Promise.all([
          fetchStatistics().catch(() => []),
          fetchSettings().catch(() => null)
        ]);

        const foundMembers = statData.find((s: any) => s.label.toLowerCase().includes("member") || s.label.toLowerCase().includes("professional"));
        const foundBusiness = statData.find((s: any) => s.label.toLowerCase().includes("exchange"));
        const foundMixers = statData.find((s: any) => s.label.toLowerCase().includes("mixer"));

        setStats({
          members: foundMembers ? foundMembers.value : "500+",
          businessExchanged: foundBusiness ? foundBusiness.value : "₹10Cr+",
          monthlyMixers: foundMixers ? foundMixers.value : "50+"
        });

        let loadedTeam = [];
        if (settingsData && settingsData.trustees) {
          loadedTeam = typeof settingsData.trustees === "string"
            ? JSON.parse(settingsData.trustees)
            : settingsData.trustees;
        }
        if (loadedTeam && loadedTeam.length > 0) {
          setTeam(loadedTeam);
        } else {
          setTeam(fallbackTeam);
        }
      } catch (e) {
        console.error("Failed to load statistics for about page:", e);
        setTeam(fallbackTeam);
      } finally {
        setLoading(false);
      }
    }
    loadStats();
  }, []);

  if (loading) {
    return (
      <div className="flex h-[75vh] items-center justify-center font-outfit">
        <div className="text-center space-y-3">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="text-sm font-medium text-muted">{t("loading") || "Loading..."}</p>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-background">
      {/* Hero */}
      <PageHeader
        kicker={t("about.kicker")}
        title={t("about.title")}
        subtitle={t("about.subtitle")}
      />

      {/* Mission & Impact */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-5">
        <div className="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-8">
          <motion.div
            initial={{ opacity: 0, scale: 0.97 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            className="group relative aspect-[4/3] overflow-hidden rounded-2xl border border-border shadow-lg"
          >
            <img
              src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1000"
              alt="SABHA Community Mixer"
              className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-103"
            />
            <div className="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-t from-slate-900/80 via-slate-900/35 to-transparent p-10 text-center">
              <div className="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 text-white backdrop-blur-md">
                <Users className="h-6 w-6" />
              </div>
              <p className="text-base font-bold leading-relaxed text-white">
                {t("about.mixers_panels")}
              </p>
            </div>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            className="space-y-6"
          >
            <span className="text-xs font-bold uppercase tracking-wider text-primary">{t("about.mission_label")}</span>
            <h2 className="text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
              {t("about.mission_title")}
            </h2>
            <p className="text-sm leading-relaxed text-muted font-medium">
              {t("about.mission_desc")}
            </p>

            <div className="grid grid-cols-3 gap-6 pt-4 border-t border-border/80">
              <div className="space-y-1">
                <p className="text-3xl font-extrabold text-foreground sm:text-4xl">{stats.members}</p>
                <p className="text-[11px] font-bold text-muted uppercase">{t("about.verified_members")}</p>
              </div>
              <div className="space-y-1">
                <p className="text-3xl font-extrabold text-foreground sm:text-4xl">{stats.businessExchanged}</p>
                <p className="text-[11px] font-bold text-muted uppercase">{t("about.business_exchanged")}</p>
              </div>
              <div className="space-y-1">
                <p className="text-3xl font-extrabold text-foreground sm:text-4xl">{stats.monthlyMixers}</p>
                <p className="text-[11px] font-bold text-muted uppercase">{t("about.monthly_mixers")}</p>
              </div>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Values Grid */}
      <section className="border-y border-border bg-surface">
        <div className="mx-auto max-w-7xl px-3 py-20 lg:py-5">
          <div className="mb-3 text-center max-w-2xl mx-auto">
            <span className="text-xs font-bold uppercase tracking-wider text-primary">{t("about.values_label")}</span>
            <h2 className="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
              {t("about.values_title")}
            </h2>
          </div>

          <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            {values.map((v, i) => (
              <motion.div
                key={v.tKey}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.05 }}
                className="glass-card hover-card p-7 flex flex-col items-start"
              >
                <div className={`mb-5 flex h-12 w-12 items-center justify-center rounded-xl ${v.bg} ${v.color}`}>
                  <v.icon className="h-5.5 w-5.5" />
                </div>
                <h3 className="text-base font-bold text-foreground">{t(`about.${v.tKey}_title`)}</h3>
                <p className="mt-2.5 text-xs leading-relaxed text-muted flex-1">
                  {t(`about.${v.tKey}_desc`)}
                </p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Evolution Timeline */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-5 border-b border-border">
        <div className="mb-3 text-center max-w-2xl mx-auto">
          <span className="text-xs font-bold uppercase tracking-wider text-primary">{t("about.timeline_label")}</span>
          <h2 className="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
            {t("about.timeline_title")}
          </h2>
        </div>

        <div className="relative border-l border-primary/25 ml-4 md:ml-32 space-y-12">
          {milestones.map((m, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, x: -20 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.1 }}
              className="relative pl-8 md:pl-10"
            >
              {/* Year badge left-aligned on desktop */}
              <div className="hidden md:flex absolute right-full mr-10 top-0.5 text-right flex-col">
                <span className="text-2xl font-extrabold text-primary">{m.year}</span>
                <span className="text-[10px] font-bold text-muted uppercase">{t("about.milestone")}</span>
              </div>

              {/* Dot indicator */}
              <div className="absolute -left-1.5 top-2.5 h-3.5 w-3.5 rounded-full border-2 border-primary bg-white z-10" />

              <div>
                <span className="inline-block md:hidden text-lg font-extrabold text-primary mb-1">{m.year}</span>
                <h3 className="text-base font-bold text-foreground">{t(`about.${m.tKey}_title`)}</h3>
                <p className="mt-2 text-xs leading-relaxed text-muted font-medium max-w-3xl">{t(`about.${m.tKey}_desc`)}</p>
              </div>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Leadership Board */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-5">
        <div className="mb-3 text-center max-w-2xl mx-auto">
          <span className="text-xs font-bold uppercase tracking-wider text-primary">{t("about.leadership_label")}</span>
          <h2 className="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
            {t("about.leadership_title")}
          </h2>
          <p className="mt-4 text-xs text-muted font-medium">
            {t("about.leadership_subtitle")}
          </p>
        </div>

        <div className="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4">
          {team.map((member, idx) => {
            const name = member.tKey ? t(`about.${member.tKey}_name`) : member.name;
            const role = member.tKey ? t(`about.${member.tKey}_role`) : member.role;
            const company = member.tKey ? t(`about.${member.tKey}_org`) : (member.company || member.org);

            return (
              <motion.div
                key={idx}
                initial={{ opacity: 0, y: 15 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: idx * 0.05 }}
                className="glass-card hover-card p-5 text-center flex flex-col items-center border border-border"
              >
                <img
                  src={assetUrl(member.avatar)}
                  alt={name}
                  className="h-20 w-20 rounded-full object-cover border-2 border-primary-soft shadow-sm mb-4"
                />
                {/* <span className="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full mb-2 border border-emerald-100">
                  {t("about.verified_trustee")}
                </span> */}
                <h3 className="text-sm font-extrabold text-foreground">{name}</h3>
                <p className="text-[11px] font-bold text-primary mt-0.5">{role}</p>
                <p className="text-[10px] text-muted font-semibold mt-1 truncate max-w-full">{company}</p>
              </motion.div>
            );
          })}
        </div>
      </section>

      {/* CTA */}
      <section className="mx-auto max-w-7xl px-6 pb-5">
        <div className="rounded-2xl border border-border bg-primary px-8 py-14 text-center text-white lg:px-16 shadow-lg">
          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
            {t("about.cta_title")}
          </h2>
          <p className="mx-auto mt-3 max-w-xl text-sm text-white/80 leading-relaxed">
            {t("about.cta_subtitle")}
          </p>
          {isAuthenticated ? (
            <Link
              href="/profile"
              className="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-white px-7 py-3.5 text-sm font-semibold text-primary shadow-md transition-all hover:opacity-90 active:scale-[0.98]"
            >
              {t("nav.profile") || "Go to Profile"} <ArrowRight size={16} />
            </Link>
          ) : (
            <button
              onClick={openRegister}
              className="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-white px-7 py-3.5 text-sm font-semibold text-primary shadow-md transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
            >
              {t("about.cta_btn")} <ArrowRight size={16} />
            </button>
          )}
        </div>
      </section>
    </div>
  );
}

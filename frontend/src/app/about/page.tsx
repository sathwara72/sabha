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
import { fetchStatistics } from "@/lib/api";

const values = [
  {
    name: "Trust First",
    description: "Every business and member in SABHA undergoes rigorous verification to maintain professional integrity and authenticity.",
    icon: ShieldCheck,
    color: "text-blue-600",
    bg: "bg-blue-50"
  },
  {
    name: "Collaborative Growth",
    description: "We align community strength with business strategy. Collaboration is our primary driver for scaling operations.",
    icon: Users,
    color: "text-emerald-600",
    bg: "bg-emerald-50"
  },
  {
    name: "Harmony & Connection",
    description: "Fostering respect, mutual alignment, and community synergy to build relationships that extend beyond business.",
    icon: Heart,
    color: "text-rose-600",
    bg: "bg-rose-50"
  },
  {
    name: "Advancement",
    description: "Equipping young entrepreneurs and established enterprises with modern tools, workshops, and strategic mentorship.",
    icon: TrendingUp,
    color: "text-amber-600",
    bg: "bg-amber-50"
  },
];

const team = [
  {
    name: "Ravi Sharma",
    role: "President & Trustee",
    org: "Founder, Vertex Solutions",
    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150&auto=format&fit=crop"
  },
  {
    name: "Pooja Verma",
    role: "Vice President",
    org: "Chief Architect & CEO, Prime Builders",
    avatar: "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=150&auto=format&fit=crop"
  },
  {
    name: "Amit Shah",
    role: "Treasurer & Growth Lead",
    org: "Director of Operations, Global Logistics",
    avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=150&auto=format&fit=crop"
  },
  {
    name: "Sara Khan",
    role: "General Secretary",
    org: "Senior Partner, Summit Consulting",
    avatar: "https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=150&auto=format&fit=crop"
  }
];

const milestones = [
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

export default function AboutPage() {
  const [stats, setStats] = useState({
    members: "500+",
    businessExchanged: "₹10Cr+",
    monthlyMixers: "50+"
  });

  useEffect(() => {
    async function loadStats() {
      try {
        const statData = await fetchStatistics();
        const foundMembers = statData.find((s: any) => s.label.toLowerCase().includes("member") || s.label.toLowerCase().includes("professional"));
        const foundBusiness = statData.find((s: any) => s.label.toLowerCase().includes("exchange") || s.label.toLowerCase().includes("success"));
        const foundMixers = statData.find((s: any) => s.label.toLowerCase().includes("mixer") || s.label.toLowerCase().includes("event"));

        setStats({
          members: foundMembers ? foundMembers.value : "500+",
          businessExchanged: foundBusiness ? foundBusiness.value : "₹10Cr+",
          monthlyMixers: foundMixers ? foundMixers.value : "50+"
        });
      } catch (e) {
        console.error("Failed to load statistics for about page:", e);
      }
    }
    loadStats();
  }, []);

  return (
    <div className="bg-background">
      {/* Hero */}
      <PageHeader
        kicker="Sathwara Association of Business, Harmony & Advancement"
        title="About SABHA"
        subtitle="SABHA unites entrepreneurs, service providers, and professionals. We believe in building trust, driving growth, and advancing our community together."
      />

      {/* Mission & Impact */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-24">
        <div className="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-16">
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
                Monthly Mixers • Technical Panels • Business Referrals
              </p>
            </div>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            className="space-y-6"
          >
            <span className="text-xs font-bold uppercase tracking-wider text-primary">Our Mission & Vision</span>
            <h2 className="text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
              Empowering Community-Led Global Businesses
            </h2>
            <p className="text-sm leading-relaxed text-muted font-medium">
              SABHA provides the digital tools, strategic networks, and interactive platforms necessary to help Sathwara entrepreneurs showcase their capabilities, exchange vetted business leads, and achieve mutual growth.
            </p>

            <div className="grid grid-cols-3 gap-6 pt-4 border-t border-border/80">
              <div className="space-y-1">
                <p className="text-3xl font-extrabold text-foreground sm:text-4xl">{stats.members}</p>
                <p className="text-[11px] font-bold text-muted uppercase">Verified Members</p>
              </div>
              <div className="space-y-1">
                <p className="text-3xl font-extrabold text-foreground sm:text-4xl">{stats.businessExchanged}</p>
                <p className="text-[11px] font-bold text-muted uppercase">Business Exchanged</p>
              </div>
              <div className="space-y-1">
                <p className="text-3xl font-extrabold text-foreground sm:text-4xl">{stats.monthlyMixers}</p>
                <p className="text-[11px] font-bold text-muted uppercase">Monthly Mixers</p>
              </div>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Values Grid */}
      <section className="border-y border-border bg-surface">
        <div className="mx-auto max-w-7xl px-6 py-20 lg:py-24">
          <div className="mb-14 text-center max-w-2xl mx-auto">
            <span className="text-xs font-bold uppercase tracking-wider text-primary">Core Principles</span>
            <h2 className="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
              What We Stand For
            </h2>
          </div>

          <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {values.map((v, i) => (
              <motion.div
                key={v.name}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.05 }}
                className="glass-card hover-card p-7 flex flex-col items-start"
              >
                <div className={`mb-5 flex h-12 w-12 items-center justify-center rounded-xl ${v.bg} ${v.color}`}>
                  <v.icon className="h-5.5 w-5.5" />
                </div>
                <h3 className="text-base font-bold text-foreground">{v.name}</h3>
                <p className="mt-2.5 text-xs leading-relaxed text-muted flex-1">
                  {v.description}
                </p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Evolution Timeline */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-24 border-b border-border">
        <div className="mb-14 text-center max-w-2xl mx-auto">
          <span className="text-xs font-bold uppercase tracking-wider text-primary">Our Timeline</span>
          <h2 className="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
            SABHA's Journey & Evolution
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
                <span className="text-[10px] font-bold text-muted uppercase">Milestone</span>
              </div>

              {/* Dot indicator */}
              <div className="absolute -left-1.5 top-2.5 h-3.5 w-3.5 rounded-full border-2 border-primary bg-white z-10" />

              <div>
                <span className="inline-block md:hidden text-lg font-extrabold text-primary mb-1">{m.year}</span>
                <h3 className="text-base font-bold text-foreground">{m.title}</h3>
                <p className="mt-2 text-xs leading-relaxed text-muted font-medium max-w-3xl">{m.description}</p>
              </div>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Leadership Board */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-24">
        <div className="mb-14 text-center max-w-2xl mx-auto">
          <span className="text-xs font-bold uppercase tracking-wider text-primary">Trustees & Leadership</span>
          <h2 className="mt-2 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl">
            Meet Our Core Committee
          </h2>
          <p className="mt-4 text-xs text-muted font-medium">
            The visionary leaders steering SABHA towards community empowerment, business alignment, and global opportunities.
          </p>
        </div>

        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {team.map((t, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, y: 15 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.05 }}
              className="glass-card hover-card p-5 text-center flex flex-col items-center border border-border"
            >
              <img
                src={t.avatar}
                alt={t.name}
                className="h-20 w-20 rounded-full object-cover border-2 border-primary-soft shadow-sm mb-4"
              />
              <span className="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full mb-2 border border-emerald-100">
                Verified Trustee
              </span>
              <h3 className="text-sm font-extrabold text-foreground">{t.name}</h3>
              <p className="text-[11px] font-bold text-primary mt-0.5">{t.role}</p>
              <p className="text-[10px] text-muted font-semibold mt-1 truncate max-w-full">{t.org}</p>
            </motion.div>
          ))}
        </div>
      </section>

      {/* CTA */}
      <section className="mx-auto max-w-7xl px-6 pb-24">
        <div className="rounded-2xl border border-border bg-primary px-8 py-14 text-center text-white lg:px-16 shadow-lg">
          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
            Grow Your Business & Network Today
          </h2>
          <p className="mx-auto mt-3 max-w-xl text-sm text-white/80 leading-relaxed">
            Join 500+ community owners, list your services, receive qualified referrals, and advance your startup.
          </p>
          <Link
            href="/register"
            className="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-white px-7 py-3.5 text-sm font-semibold text-primary shadow-md transition-all hover:opacity-90 active:scale-[0.98]"
          >
            Join SABHA Community <ArrowRight size={16} />
          </Link>
        </div>
      </section>
    </div>
  );
}

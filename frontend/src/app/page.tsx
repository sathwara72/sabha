"use client";

import { useEffect, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import {
  ArrowRight,
  Briefcase,
  ChevronRight,
  Calendar,
  MapPin,
  Zap,
  Users,
} from "lucide-react";
import Link from "next/link";
import { fetchBusinesses, fetchEvents, fetchStatistics } from "@/lib/api";

interface Business {
  id: number;
  name: string;
  category: string;
  description: string;
  website: string;
  logo?: string;
}

interface Event {
  id: number;
  title: string;
  description: string;
  date: string;
  location: string;
  type: string;
  image?: string;
}

interface Stat {
  label: string;
  value: string;
}

const heroImages = [
  "https://scontent.famd4-1.fna.fbcdn.net/v/t39.30808-6/687022534_122132617917108566_6525249059325229568_n.jpg?stp=dst-jpg_tt6&cstp=mx3022x1387&ctp=s3022x1387&_nc_cat=101&ccb=1-7&_nc_sid=127cfc&_nc_ohc=Y-B4AH3GShsQ7kNvwEwxjhp&_nc_oc=Adpp1NGL_DAjDIF0gcqSmkvRfuxfgmqJ_3RHuNWFima1S0l-Ea4cbhNXeE_NyGM_btC-uH0QU9k11gp1agImO5ed&_nc_zt=23&_nc_ht=scontent.famd4-1.fna&_nc_gid=rLvz5pGt_ufDa7OO0zQm1A&_nc_ss=7b289&oh=00_Af8MUYwNmFoYFO2racbrjO49JH6zNWyDE7jFRm5TviICdQ&oe=6A29E627",
  "https://scontent.famd4-1.fna.fbcdn.net/v/t39.30808-6/688438297_122133740973108566_5769010417200152383_n.jpg?stp=cp6_dst-jpegr_tt6&cstp=mx2048x940&ctp=s2048x940&_nc_cat=105&ccb=1-7&_nc_sid=833d8c&_nc_ohc=JGlUBtIsFXwQ7kNvwEcTlKy&_nc_oc=Adp3OuucPuXA1EuXSSN4GSdPn5rAJA1eWEpDGDYzyKMsjaH0BpXheLrcnc5l6ESaz7vG_biyL9wyWtONsQrYM_ua&_nc_zt=23&se=-1&_nc_ht=scontent.famd4-1.fna&_nc_gid=Qx4wNq1Xv5y7MI5ie-Ctew&_nc_ss=7b289&oh=00_Af9F3GbKDxAuY6EscVzH-NSKszdaWRDzMUTkfpvt3XFDzw&oe=6A29F952",
  "https://scontent.famd4-1.fna.fbcdn.net/v/t39.30808-6/690647620_122133741063108566_6947694810305175354_n.jpg?stp=cp6_dst-jpegr_tt6&cstp=mx940x2048&ctp=s940x2048&_nc_cat=100&ccb=1-7&_nc_sid=833d8c&_nc_ohc=r76OQiRYXgUQ7kNvwGY6gLM&_nc_oc=AdoZsnubFV8TS7WwP3J_b249-dKYxrKSTTFTGYMAo5p8I7Z4KYEGdeZwUPGRjPtlI_DrtRlFEfHaZf1efQybirJG&_nc_zt=23&se=-1&_nc_ht=scontent.famd4-1.fna&_nc_gid=aut7rGdM3RcPlDs0-Qgs9g&_nc_ss=7b289&oh=00_Af8cr0Jxn3sk3l0tcfr7xC-WOISvn70K2Q_1JG46FVgKAQ&oe=6A29F2B0",
  "https://scontent.famd4-1.fna.fbcdn.net/v/t39.30808-6/682921685_122132617701108566_7947770818879288591_n.jpg?stp=dst-jpg_tt6&cstp=mx587x1280&ctp=s587x1280&_nc_cat=105&ccb=1-7&_nc_sid=127cfc&_nc_ohc=iEZxOiJdIpIQ7kNvwGiOdXE&_nc_oc=Adre9GuzakuZt6GWSjHRUnZuOi0vM7Odm3a5vvX2VArf_-ugV3dMckNpCPZOvA0GEmGZXiOjFfYwdXu-_DoyQhWi&_nc_zt=23&_nc_ht=scontent.famd4-1.fna&_nc_gid=boxAlSMRU3wBGD9IQXN5Mg&_nc_ss=7b289&oh=00_Af-CJ7TG6q7UqKqu8oPNZCw4efIVgMY8-QzCs5i3M7PFyg&oe=6A29E065"
];

export default function Home() {
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [events, setEvents] = useState<Event[]>([]);
  const [stats, setStats] = useState<Stat[]>([
    { label: "Active members", value: "500+" },
    { label: "Events hosted", value: "120+" },
    { label: "Connections made", value: "2,500+" },
  ]);
  const [loading, setLoading] = useState(true);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const [activeFaq, setActiveFaq] = useState<number | null>(null);

  useEffect(() => {
    async function loadData() {
      try {
        const [bizData, eventData, statData] = await Promise.all([
          fetchBusinesses(),
          fetchEvents(),
          fetchStatistics(),
        ]);
        
        // Defensive uniqueness filters
        const uniqueBiz = (bizData || []).filter(
          (v: any, i: number, a: any[]) => a.findIndex((t: any) => t.id === v.id) === i
        );
        const uniqueEvents = (eventData || []).filter(
          (v: any, i: number, a: any[]) => a.findIndex((t: any) => t.id === v.id) === i
        );
        const uniqueStats = (statData || []).filter(
          (v: any, i: number, a: any[]) => a.findIndex((t: any) => t.label === v.label) === i
        );

        setBusinesses(uniqueBiz);
        setEvents(uniqueEvents);
        if (uniqueStats && uniqueStats.length > 0) setStats(uniqueStats);
      } catch (error) {
        console.error("Error loading live data:", error);
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, []);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentImageIndex((prev) => (prev + 1) % heroImages.length);
    }, 5000);
    return () => clearInterval(timer);
  }, []);

  return (
    <div className="bg-background">
      {/* Hero */}
      <section className="hero-surface relative overflow-hidden border-b border-border">
        <div className="mx-auto max-w-7xl px-6 py-12 lg:px-8 lg:py-16 grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
          <motion.div
            initial={{ opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            className="flex flex-col items-start text-left space-y-6"
          >
            <span className="inline-flex items-center gap-2 rounded-full bg-primary-soft px-4 py-1.5 text-xs sm:text-sm font-semibold text-primary">
                <span className="h-1.5 w-1.5 rounded-full bg-primary" />
                Sathwara Association of Business, Harmony & Advancement
              </span>

            <h1 className="text-4xl font-bold tracking-tight text-foreground sm:text-5xl lg:text-6xl">
              SABHA: Connect, list your business, and grow <span className="text-primary">together</span>.
            </h1>

            <p className="text-base leading-relaxed text-muted max-w-2xl">
              SABHA (Sathwara Association of Business, Harmony & Advancement) brings entrepreneurs and service providers together. Create your
              profile, showcase what you do, and meet the people who can help you scale.
            </p>

            <div className="flex flex-col items-center gap-3 sm:flex-row w-full sm:w-auto">
              <Link
                href="/register"
                className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-7 py-3.5 text-base font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] sm:w-auto"
              >
                Get started <ArrowRight size={18} />
              </Link>
              <Link
                href="/events"
                className="inline-flex w-full items-center justify-center rounded-xl border border-border bg-white px-7 py-3.5 text-base font-semibold text-foreground transition-colors hover:bg-surface sm:w-auto"
              >
                Browse events
              </Link>
            </div>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, scale: 0.98 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ delay: 0.1, duration: 0.4 }}
            className="relative h-[280px] w-full overflow-hidden rounded-2xl border border-border bg-muted sm:h-[360px] lg:h-[420px] shadow-lg"
          >
            <AnimatePresence mode="wait">
              <motion.img
                key={currentImageIndex}
                src={heroImages[currentImageIndex]}
                alt="Sabha Event"
                initial={{ opacity: 0, scale: 1.05 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.95 }}
                transition={{ duration: 0.6 }}
                className="absolute inset-0 h-full w-full object-cover"
              />
            </AnimatePresence>

            {/* Navigation overlays */}
            <div className="absolute inset-x-0 bottom-4 flex justify-center gap-2 z-10">
              {heroImages.map((_, idx) => (
                <button
                  key={idx}
                  onClick={() => setCurrentImageIndex(idx)}
                  className={`h-2 rounded-full transition-all duration-300 ${
                    currentImageIndex === idx ? "bg-white w-6" : "bg-white/50 w-2"
                  }`}
                  aria-label={`Go to slide ${idx + 1}`}
                />
              ))}
            </div>
          </motion.div>
        </div>
      </section>

      {/* Stats */}
      <section className="border-b border-border bg-surface">
        <div className="mx-auto grid max-w-5xl grid-cols-1 gap-8 px-6 py-14 text-center sm:grid-cols-3">
          {stats.map((stat, i) => (
            <motion.div
              key={i}
              initial={{ opacity: 0, y: 12 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.05 }}
            >
              <p className="text-4xl font-bold text-foreground sm:text-5xl">{stat.value}</p>
              <p className="mt-2 text-sm font-medium text-muted">{stat.label}</p>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Core Pillars */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-24 border-b border-border">
        <div className="text-center max-w-3xl mx-auto mb-16">
          <p className="text-sm font-semibold text-primary uppercase tracking-wider">Our Foundation</p>
          <h2 className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
            The Three Pillars of SABHA
          </h2>
          <p className="mt-4 text-sm text-muted leading-relaxed">
            Sathwara Association of Business, Harmony & Advancement is built on core objectives designed to elevate every member's professional journey.
          </p>
        </div>

        <div className="grid grid-cols-1 gap-8 md:grid-cols-3">
          {[
            {
              title: "Business Growth",
              description: "Empowering members with business directory listings, digital exposure, and direct customer leads to accelerate revenue and expansion.",
              icon: Briefcase,
              color: "from-blue-500 to-indigo-600",
            },
            {
              title: "Harmony & Connection",
              description: "Cultivating a trust-based professional family. Fostering long-term collaborations, ethical standards, and community alignment.",
              icon: Users,
              color: "from-emerald-500 to-teal-600",
            },
            {
              title: "Advancement & Mentoring",
              description: "Hosting tech workshops, strategic marketing panels, and leadership training programs to equip the next generation of community founders.",
              icon: Zap,
              color: "from-orange-500 to-amber-600",
            }
          ].map((pillar, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.1, duration: 0.5 }}
              className="glass-card flex flex-col p-8 transition-all hover:border-primary/20 group hover:shadow-md"
            >
              <div className={`flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br ${pillar.color} text-white shadow-md mb-6 transition-transform group-hover:scale-105`}>
                <pillar.icon size={26} />
              </div>
              <h3 className="text-lg font-bold text-foreground group-hover:text-primary transition-colors">{pillar.title}</h3>
              <p className="mt-3.5 text-xs leading-relaxed text-muted flex-1">{pillar.description}</p>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Featured businesses */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-24">
        <div className="mb-12 max-w-2xl">
          <p className="text-sm font-semibold text-primary">Member businesses</p>
          <h2 className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
            Discover what the community offers
          </h2>
          <p className="mt-3 text-base text-muted">
            Browse businesses listed by members across the network.
          </p>
        </div>

        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          {businesses.length > 0 ? (
            businesses.map((biz, i) => (
              <motion.div
                key={biz.id}
                initial={{ opacity: 0, y: 16 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.05 }}
                className="glass-card flex flex-col p-7"
              >
                <div className="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft text-primary">
                  <Briefcase size={22} />
                </div>
                <h3 className="text-lg font-semibold text-foreground">{biz.name}</h3>
                <p className="mt-1 text-sm font-medium text-primary">{biz.category}</p>
                <p className="mt-3 flex-1 text-sm leading-relaxed text-muted">
                  {biz.description}
                </p>
                <a
                  href={biz.website}
                  target="_blank"
                  rel="noreferrer"
                  className="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-primary transition-colors hover:opacity-80"
                >
                  Visit site <ChevronRight size={15} />
                </a>
              </motion.div>
            ))
          ) : (
            <div className="col-span-full rounded-xl border border-dashed border-border py-16 text-center text-muted">
              {loading ? "Loading businesses…" : "No businesses listed yet."}
            </div>
          )}
        </div>
      </section>

      {/* Events */}
      <section className="border-y border-border bg-surface">
        <div className="mx-auto max-w-7xl px-6 py-20 lg:py-24">
          <div className="mb-12 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div className="max-w-2xl">
              <p className="text-sm font-semibold text-primary">Upcoming events</p>
              <h2 className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                The community in action
              </h2>
            </div>
            <Link
              href="/gallery"
              className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary transition-colors hover:opacity-80"
            >
              View gallery <ArrowRight size={16} />
            </Link>
          </div>

          <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            {events.slice(0, 3).map((event, i) => (
              <motion.div
                key={event.id}
                initial={{ opacity: 0, y: 16 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.05 }}
                className="glass-card group overflow-hidden p-0"
              >
                <div className="aspect-[16/10] w-full overflow-hidden">
                  <img
                    src={
                      event.image ||
                      "https://images.unsplash.com/photo-1540575861501-7ad0582373f3?q=80&w=800&auto=format&fit=crop"
                    }
                    alt={event.title}
                    className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                  />
                </div>
                <div className="p-6">
                  <h4 className="text-base font-semibold text-foreground">{event.title}</h4>
                  <div className="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted">
                    <span className="inline-flex items-center gap-1.5">
                      <Calendar size={14} className="text-primary" />
                      {new Date(event.date).toLocaleDateString()}
                    </span>
                    <span className="inline-flex items-center gap-1.5">
                      <MapPin size={14} className="text-primary" />
                      {event.location}
                    </span>
                  </div>
                </div>
              </motion.div>
            ))}

            {events.length === 0 && !loading && (
              <div className="rounded-xl border border-dashed border-border py-16 text-center text-muted md:col-span-2">
                No upcoming events scheduled.
              </div>
            )}

            {/* CTA card */}
            <div className="glass-card flex flex-col justify-between p-7">
              <div>
                <h3 className="text-xl font-bold text-foreground">Join the next event</h3>
                <p className="mt-3 text-sm leading-relaxed text-muted">
                  Meet potential partners and customers in a friendly environment built
                  for connecting and growing.
                </p>
              </div>
              <Link
                href="/events"
                className="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white transition-all hover:opacity-90 active:scale-[0.98]"
              >
                Browse schedule <ArrowRight size={16} />
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* How it works */}
      <section className="mx-auto max-w-7xl px-6 py-20 lg:py-24">
        <div className="mb-14 max-w-2xl">
          <p className="text-sm font-semibold text-primary">How it works</p>
          <h2 className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
            Get started in three simple steps
          </h2>
        </div>

        <div className="grid grid-cols-1 gap-8 md:grid-cols-3">
          {[
            {
              step: "1",
              title: "Create your account",
              detail: "Sign up and complete your profile with your business details.",
            },
            {
              step: "2",
              title: "List your business",
              detail: "Showcase your services and get discovered by the community.",
            },
            {
              step: "3",
              title: "Connect and grow",
              detail: "Join events, meet members, and find new opportunities.",
            },
          ].map((item, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, y: 16 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: idx * 0.05 }}
              className="glass-card p-7"
            >
              <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white">
                {item.step}
              </div>
              <h3 className="mt-5 text-lg font-semibold text-foreground">{item.title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-muted">{item.detail}</p>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Interactive FAQ Section */}
      <section className="mx-auto max-w-4xl px-6 py-20 border-t border-border">
        <div className="text-center mb-12">
          <p className="text-sm font-semibold text-primary uppercase tracking-wider">Got Questions?</p>
          <h2 className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
            Frequently Asked Questions
          </h2>
        </div>

        <div className="space-y-4">
          {[
            {
              q: "What is SABHA?",
              a: "SABHA stands for Sathwara Association of Business, Harmony & Advancement. It is a premium community platform built to connect entrepreneurs, service providers, and professionals to share business opportunities, build trust, and advance together."
            },
            {
              q: "Who is eligible to join SABHA?",
              a: "SABHA is open to all community entrepreneurs, corporate professionals, local service providers, freelancers, and aspiring business owners who want to grow their professional network and collaborate with other vetted businesses."
            },
            {
              q: "How can I register my business in the Directory?",
              a: "Once you create your member account by clicking 'Join' and filling out the registration form, you can submit your business details, logo, images, services, and contact information through the member dashboard."
            },
            {
              q: "Are the community events physical or virtual?",
              a: "We host both physical networking mixers (like Mixer sessions, corporate meets) and virtual webinars (such as Next.js/Laravel scaling workshops). Our events calendar is updated weekly with detailed location/joining credentials."
            }
          ].map((item, idx) => {
            const isOpen = activeFaq === idx;
            return (
              <div
                key={idx}
                className="glass-card overflow-hidden border border-border/80 transition-colors"
              >
                <button
                  onClick={() => setActiveFaq(isOpen ? null : idx)}
                  className="w-full flex items-center justify-between p-5 text-left font-bold text-base text-foreground outline-none"
                >
                  <span>{item.q}</span>
                  <span className={`text-primary font-bold text-xl transition-transform duration-200 ${isOpen ? "rotate-45" : ""}`}>
                    +
                  </span>
                </button>
                <AnimatePresence initial={false}>
                  {isOpen && (
                    <motion.div
                      initial={{ height: 0, opacity: 0 }}
                      animate={{ height: "auto", opacity: 1 }}
                      exit={{ height: 0, opacity: 0 }}
                      transition={{ duration: 0.25, ease: "easeInOut" }}
                    >
                      <div className="p-5 pt-0 border-t border-border/10 text-sm leading-relaxed text-muted font-medium bg-surface/30">
                        {item.a}
                      </div>
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>
            );
          })}
        </div>
      </section>

      {/* Newsletter CTA */}
      <section className="mx-auto max-w-7xl px-6 pb-24">
        <div className="rounded-2xl border border-border bg-primary px-8 py-14 text-center text-white lg:px-16">
          <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
            Stay in the loop
          </h2>
          <p className="mx-auto mt-3 max-w-xl text-base text-white/80">
            Get insights on growing your business, networking tips, and early access to
            community events.
          </p>
          <form
            className="mx-auto mt-8 flex max-w-md flex-col gap-3 sm:flex-row"
            onSubmit={(e) => e.preventDefault()}
          >
            <input
              type="email"
              placeholder="Enter your email"
              className="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white outline-none transition-colors placeholder:text-white/60 focus:border-white/50"
            />
            <button className="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-primary transition-all hover:opacity-90 active:scale-[0.98]">
              Subscribe
            </button>
          </form>
          <p className="mt-6 text-sm text-white/60">Trusted by 2,500+ members nationwide.</p>
        </div>
      </section>
    </div>
  );
}

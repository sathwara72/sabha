"use client";

import Link from "next/link";
import { motion } from "framer-motion";
import { ArrowRight, Users, Calendar, ShieldCheck, Zap, Globe } from "lucide-react";
import { cn } from "@/lib/utils";

const features = [
  {
    name: "Verified Business Network",
    description: "Join a community of vetted professionals and businesses, ensuring trust and quality in every interaction.",
    icon: ShieldCheck,
  },
  {
    name: "Strategic Events",
    description: "Access workshops, networking mixers, and industry-leading events designed to accelerate your growth.",
    icon: Calendar,
  },
  {
    name: "Global Opportunities",
    description: "Connect with local and international partners seeking the specialized services your business offers.",
    icon: Globe,
  },
];

export default function Home() {
  return (
    <div className="relative isolate overflow-hidden">
      {/* Background Blobs */}
      <div className="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
        <div className="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-primary to-accent opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" />
      </div>

      {/* Hero Section */}
      <div className="py-24 sm:py-32 lg:pb-40">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="mx-auto max-w-2xl text-center">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5 }}
            >
              <h1 className="text-4xl font-bold tracking-tight text-foreground sm:text-6xl lg:text-7xl">
                Bridge. Build. <span className="text-gradient">Belong.</span>
              </h1>
              <p className="mt-6 text-lg leading-8 text-foreground/70">
                Sabha is the premium destination for visionary entrepreneurs and service providers to connect, collaborate, and conquer new markets together.
              </p>
              <div className="mt-10 flex items-center justify-center gap-x-6">
                <Link
                  href="/register"
                  className="rounded-full bg-primary px-8 py-4 text-sm font-semibold text-primary-foreground shadow-lg hover:bg-primary/90 transition-all hover:scale-105 flex items-center gap-2 group"
                >
                  Start Your Journey
                  <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </Link>
                <Link href="/events" className="text-sm font-semibold leading-6 text-foreground hover:text-primary transition-colors">
                  Browse Events <span aria-hidden="true">→</span>
                </Link>
              </div>
            </motion.div>
          </div>

          {/* Feature Grid */}
          <div className="mx-auto mt-32 max-w-7xl px-6 lg:px-8">
            <div className="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
              {features.map((feature) => (
                <motion.div
                  key={feature.name}
                  whileHover={{ y: -5 }}
                  className="relative pl-16 group"
                >
                  <dt className="text-base font-semibold leading-7 text-foreground">
                    <div className="absolute left-0 top-0 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 group-hover:bg-primary transition-colors duration-300">
                      <feature.icon className="h-6 w-6 text-primary group-hover:text-primary-foreground transition-colors duration-300" aria-hidden="true" />
                    </div>
                    {feature.name}
                  </dt>
                  <dd className="mt-2 text-base leading-7 text-foreground/60">{feature.description}</dd>
                </motion.div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Feature Bento Grid */}
      <div className="py-24 sm:py-32">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="mx-auto max-w-2xl lg:text-center mb-16">
            <h2 className="text-base font-semibold leading-7 text-primary uppercase tracking-widest">Growth Ecosystem</h2>
            <p className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-5xl">
              Everything you need to <span className="text-gradient">succeed.</span>
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {features.map((feature, idx) => (
              <motion.div
                key={feature.name}
                initial={{ opacity: 0, scale: 0.95 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                transition={{ delay: idx * 0.1 }}
                className="glass relative p-8 rounded-3xl border hover:border-primary/50 transition-all group overflow-hidden"
              >
                <div className="absolute -right-4 -bottom-4 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/20 transition-all" />
                <div className="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                  <feature.icon className="h-6 w-6 text-primary group-hover:text-primary-foreground transition-colors" />
                </div>
                <h3 className="text-xl font-bold mb-3">{feature.name}</h3>
                <p className="text-sm text-foreground/60 leading-relaxed font-medium">
                  {feature.description}
                </p>
              </motion.div>
            ))}
          </div>
        </div>
      </div>

      {/* Community Gallery Section */}
      <div className="py-24 bg-white relative overflow-hidden border-y border-foreground/5">
        <div className="absolute inset-0 bg-gradient-to-b from-primary/5 to-transparent" />
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <div className="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
            <div className="max-w-2xl">
              <h2 className="text-base font-semibold leading-7 text-primary uppercase tracking-widest">Live Sabha</h2>
              <p className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-5xl">
                Community in Action
              </p>
            </div>
            <Link href="/gallery" className="text-primary font-bold hover:underline flex items-center gap-2 mb-2 group">
              View Full Gallery
              <Zap className="w-4 h-4 group-hover:animate-pulse" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div className="group relative overflow-hidden rounded-3xl aspect-[4/5] shadow-xl shadow-foreground/5">
              <img src="/_next/image?url=%2FUsers%2Fbhaveshsathwara%2F.gemini%2Fantigravity%2Fbrain%2Ff1d73360-eb6c-4af2-b654-b886652608bf%2Fsabha_networking_mixer_1775039898287.png&w=1080&q=75" alt="Networking Mixer" className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
              <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex items-end p-8">
                <div>
                  <h4 className="text-white font-bold text-lg">Industry Mixers</h4>
                  <p className="text-white/60 text-sm italic">"Connect beyond boundaries."</p>
                </div>
              </div>
            </div>
            <div className="space-y-6">
              <div className="group relative overflow-hidden rounded-3xl aspect-square shadow-xl shadow-foreground/5">
                <img src="/_next/image?url=%2FUsers%2Fbhaveshsathwara%2F.gemini%2Fantigravity%2Fbrain%2Ff1d73360-eb6c-4af2-b654-b886652608bf%2Fsabha_business_workshop_1775039921602.png&w=1080&q=75" alt="Workshop" className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex items-end p-6">
                  <h4 className="text-white font-bold">Innovation Seminars</h4>
                </div>
              </div>
              <div className="group relative overflow-hidden rounded-3xl aspect-square shadow-xl shadow-foreground/5">
                <img src="/_next/image?url=%2FUsers%2Fbhaveshsathwara%2F.gemini%2Fantigravity%2Fbrain%2Ff1d73360-eb6c-4af2-b654-b886652608bf%2Fsabha_community_growth_1775039940221.png&w=1080&q=75" alt="Community" className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex items-end p-6">
                  <h4 className="text-white font-bold">Peer Collaboration</h4>
                </div>
              </div>
            </div>
            <div className="glass group rounded-3xl p-10 flex flex-col justify-between border shadow-xl shadow-foreground/5 h-full relative overflow-hidden">
              <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl" />
              <div className="space-y-6 relative z-10">
                <h3 className="text-2xl font-bold text-foreground">Join the Next Event</h3>
                <p className="text-foreground/50 leading-relaxed italic">"The Sabha environment is electric. I've met three of my current business partners at a single mixer."</p>
                <div className="flex items-center gap-4">
                  <div className="w-12 h-12 rounded-full border-2 border-primary overflow-hidden bg-muted" />
                  <div>
                    <p className="text-foreground font-bold italic">Ravi Sharma</p>
                    <p className="text-primary text-[10px] font-bold uppercase tracking-widest">Founder, TechWave</p>
                  </div>
                </div>
              </div>
              <Link href="/events" className="mt-8 py-4 rounded-2xl bg-primary text-white text-center font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 relative z-10">
                Browse Schedule
              </Link>
            </div>
          </div>
        </div>
      </div>

      {/* How It Works Section */}
      <div className="py-24 sm:py-32 bg-slate-50">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 text-center mb-24">
          <h2 className="text-base font-semibold leading-7 text-primary uppercase tracking-widest">User Journey</h2>
          <p className="mt-2 text-3xl font-bold tracking-tight text-foreground sm:text-5xl italic">Simplified <span className="text-gradient">Progress.</span></p>
        </div>
        
        <div className="mx-auto max-w-7xl px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
          {[
            { step: "01", title: "Apply for Entry", detail: "Complete your professional bio and business details for verification." },
            { step: "02", title: "Connect Strategically", detail: "Get matched with peer businesses and relevant community workshops." },
            { step: "03", title: "Unlock Scale", detail: "Leverage the network to find service providers and scaling opportunities." },
          ].map((item, idx) => (
            <div key={idx} className="relative group p-8 rounded-3xl hover:bg-white hover:shadow-xl transition-all">
              <div className="text-8xl font-black text-foreground/5 absolute top-0 left-1/2 -translate-x-1/2 group-hover:text-primary/10 transition-colors pointer-events-none">{item.step}</div>
              <h3 className="text-2xl font-bold mb-4 relative z-10">{item.title}</h3>
              <p className="text-foreground/60 leading-relaxed max-w-[280px] mx-auto relative z-10 font-medium italic">"{item.detail}"</p>
            </div>
          ))}
        </div>
      </div>

      {/* Impact Numbers */}
      <div className="bg-primary py-24 sm:py-32 relative overflow-hidden">
        <div className="absolute inset-0 bg-white/5 backdrop-blur-[2px]" />
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10 text-center">
          <dl className="grid grid-cols-1 gap-x-8 gap-y-16 lg:grid-cols-3">
            {[
              { label: "Active Professionals", val: "500+" },
              { label: "Strategic Events", val: "120+" },
              { label: "Business Successes", val: "2500+" },
            ].map((stat) => (
              <div key={stat.label} className="flex flex-col gap-y-4">
                <dt className="text-sm font-bold leading-7 text-primary-foreground/70 uppercase tracking-widest">{stat.label}</dt>
                <dd className="text-5xl font-bold tracking-tight text-white lg:text-8xl">{stat.val}</dd>
              </div>
            ))}
          </dl>
        </div>
      </div>

      {/* Final Newsletter & CTA */}
      <div className="py-24 sm:py-40 relative isolate overflow-hidden bg-white">
        <div className="mx-auto max-w-7xl px-6 lg:px-8">
          <div className="relative isolate overflow-hidden bg-slate-50 py-24 px-6 rounded-[3rem] border shadow-2xl shadow-foreground/5 sm:px-24">
            <div className="mx-auto max-w-2xl text-center">
              <h2 className="text-4xl font-bold tracking-tight text-foreground italic">Join the Inner <span className="text-gradient">Circle.</span></h2>
              <p className="mx-auto mt-6 text-lg leading-8 text-foreground/60 font-medium">
                Weekly insights on business scaling, networking hacks, and early-bird event invites.
              </p>
              <div className="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                <input
                  type="email"
                  placeholder="Enter your business email"
                  className="w-full sm:w-80 bg-white border border-foreground/10 rounded-2xl px-6 py-4 text-foreground outline-none focus:border-primary transition-all shadow-inner font-bold"
                />
                <button
                  className="w-full sm:w-auto rounded-2xl bg-primary px-10 py-4 text-sm font-bold text-white shadow-lg hover:bg-primary/90 transition-all hover:scale-105 active:scale-95"
                >
                  Join the List
                </button>
              </div>
              <p className="mt-6 text-xs text-foreground/40 font-bold uppercase tracking-widest">Already a member? <Link href="/login" className="text-primary hover:underline">Sign In</Link></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

"use client";

import { useState, useMemo } from "react";
import { Calendar, MapPin, Search, Tag, ArrowRight, Info, Filter, Zap, ChevronLeft, ChevronRight } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import Link from "next/link";

const mockEvents = [
  { id: 1, title: "Modern Business Networking Mixer", date: "Oct 12, 2026", time: "6:30 PM", type: "physical", status: "upcoming", category: "Networking", description: "Connect with over 50 local entrepreneurs and service providers in a structured networking environment.", attendees: "120+", price: "₹2,499", image: "https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=800&auto=format&fit=crop" },
  { id: 2, title: "Next.js & Laravel: Scaling to Millions", date: "Oct 15, 2026", time: "2:00 PM", type: "virtual", status: "upcoming", category: "Workshop", description: "Deep dive into full-stack architecture with industry experts. Learn how to scale your community platform.", attendees: "500+", price: "Free", image: "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=800&auto=format&fit=crop" },
  { id: 3, title: "Marketing Strategy for Startups", date: "Oct 01, 2026", time: "10:30 AM", type: "hybrid", status: "current", category: "Seminar", description: "A high-impact seminar on digital marketing trends and organic growth strategies for new businesses.", attendees: "80+", price: "₹1,499", image: "https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=800&auto=format&fit=crop" },
  { id: 4, title: "Founders Roundtable: Q3 Review", date: "Sep 20, 2026", time: "5:00 PM", type: "physical", status: "past", category: "Networking", description: "An exclusive invite-only session for business owners to discuss market trends and quarterly results.", attendees: "30", price: "Invite Only", image: "https://images.unsplash.com/photo-1556761175-b413da4baf72?q=80&w=800&auto=format&fit=crop" },
  { id: 5, title: "Introduction to Community Building", date: "Aug 15, 2026", time: "11:00 AM", type: "virtual", status: "past", category: "Workshop", description: "Learning the basics of fostering engaged digital and physical communities in 2026.", attendees: "250+", price: "Free", image: "https://images.unsplash.com/photo-1540317580384-e5d43616b9aa?q=80&w=800&auto=format&fit=crop" },
  { id: 6, title: "AI in Global Supply Chains", date: "Nov 05, 2026", time: "4:00 PM", type: "virtual", status: "upcoming", category: "Seminar", description: "Exploring how artificial intelligence is transforming logistics and procurement for large-scale operations.", attendees: "300+", price: "₹999", image: "https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=800&auto=format&fit=crop" },
  { id: 7, title: "Design Thinking for Leaders", date: "Nov 12, 2026", time: "9:00 AM", type: "physical", status: "upcoming", category: "Workshop", description: "A hands-on workshop on applying creative problem-solving frameworks to business management.", attendees: "45", price: "₹4,999", image: "https://images.unsplash.com/photo-1513128034602-7814ccaddd4e?q=80&w=800&auto=format&fit=crop" },
  { id: 8, title: "Quarterly Tech Summit 2026", date: "Dec 01, 2026", time: "10:00 AM", type: "hybrid", status: "upcoming", category: "Summit", description: "The premier gathering of developers and tech founders to discuss the landscape of the Indian IT sector.", attendees: "1000+", price: "₹5,499", image: "https://images.unsplash.com/photo-1475721027785-f74eccf877e2?q=80&w=800&auto=format&fit=crop" },
  { id: 9, title: "Social Impact Venture Day", date: "Oct 28, 2026", time: "11:30 AM", type: "physical", status: "upcoming", category: "Networking", description: "Connecting impact investors with sustainable business founders for a better tomorrow.", attendees: "150+", price: "₹2,999", image: "https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=800&auto=format&fit=crop" },
  { id: 10, title: "Cybersecurity for Small Biz", date: "Oct 25, 2026", time: "3:00 PM", type: "virtual", status: "upcoming", category: "Workshop", description: "Practical steps to secure your business digital assets against emerging threats in the 2026 landscape.", attendees: "180", price: "Free", image: "https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=800&auto=format&fit=crop" },
];

export default function EventsPage() {
  const [filter, setFilter] = useState("upcoming");
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 6;

  const filteredEvents = useMemo(() => {
    return mockEvents.filter(event => {
      const matchesFilter = filter === "all" || event.status === filter;
      const matchesSearch = event.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
                            event.category.toLowerCase().includes(searchQuery.toLowerCase());
      return matchesFilter && matchesSearch;
    });
  }, [filter, searchQuery]);

  const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
  const paginatedEvents = filteredEvents.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  const handleFilterChange = (f: string) => {
    setFilter(f);
    setCurrentPage(1);
  };

  const handleSearchChange = (query: string) => {
    setSearchQuery(query);
    setCurrentPage(1);
  };

  return (
    <div className="relative isolate min-h-screen pt-20 overflow-hidden bg-background">
      {/* Background Blobs */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
        <div className="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-primary/10 rounded-full blur-[120px] animate-pulse" />
        <div className="absolute bottom-[10%] right-[-10%] w-[600px] h-[600px] bg-accent/10 rounded-full blur-[150px] animate-pulse" />
      </div>

      {/* Hero Header */}
      <section className="relative py-24 lg:py-32 overflow-hidden border-b border-white/5">
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <motion.div 
            initial={{ opacity: 0, y: 30 }} 
            animate={{ opacity: 1, y: 0 }}
          >
             <div className="inline-block px-4 py-2 rounded-full glass border-white/5 mb-8">
              <span className="text-[10px] font-black uppercase tracking-[0.2em] text-white/50">
                Curated Professional Gatherings
              </span>
            </div>
            <h1 className="text-5xl md:text-8xl font-black mb-8 leading-none tracking-tighter uppercase">
              Events <span className="text-gradient">Radar.</span>
            </h1>
            <p className="max-w-2xl text-xl md:text-2xl text-white/50 font-bold leading-relaxed mb-12">
              From mastermind sessions to networking mixers. Curated experiences designed for professional breakthroughs.
            </p>
          </motion.div>
        </div>
      </section>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-12 relative z-10">
        {/* Search & Filter Bar */}
        <div className="flex flex-col gap-12 mb-20">
          <div className="relative max-w-3xl">
            <motion.div 
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="relative"
            >
              <Search className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-white/30" />
              <input
                type="text"
                placeholder="PRO SEARCH: e.g. 'Networking' or 'Workshop'"
                className="w-full glass border-white/10 rounded-[2rem] py-6 pl-16 pr-8 focus:border-primary outline-none text-white font-black text-sm tracking-widest uppercase transition-all"
                value={searchQuery}
                onChange={(e) => handleSearchChange(e.target.value)}
              />
            </motion.div>
          </div>

          <div className="flex flex-col md:flex-row items-start md:items-center gap-12">
            <div className="flex items-center gap-4">
              <Filter className="w-5 h-5 text-primary" />
              <h2 className="font-black text-xs uppercase tracking-widest text-white/40">Status:</h2>
              <div className="flex flex-wrap gap-3">
                {["upcoming", "current", "past"].map((f) => (
                  <button
                    key={f}
                    onClick={() => handleFilterChange(f)}
                    className={cn(
                      "px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all border",
                      filter === f 
                        ? "bg-primary text-white border-primary shadow-lg shadow-primary/20" 
                        : "glass border-white/5 text-white/40 hover:text-white"
                    )}
                  >
                    {f}
                  </button>
                ))}
              </div>
            </div>

            <motion.div 
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="glass px-8 py-4 rounded-3xl border-white/5 flex items-center gap-4 ml-auto"
            >
              <Info className="w-4 h-4 text-primary" />
              <p className="text-[10px] text-white/40 font-black uppercase tracking-widest">
                Members get 40% priority discount.
              </p>
            </motion.div>
          </div>
        </div>

        {/* Results Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
          <AnimatePresence mode="popLayout">
            {paginatedEvents.map((event, idx) => (
              <motion.div
                key={event.id}
                layout
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                whileHover={{ y: -10 }}
                className="glass-card flex flex-col h-full rounded-[3rem] overflow-hidden border-none relative group"
              >
                <div className="h-56 w-full relative overflow-hidden">
                  <img 
                    src={event.image} 
                    alt={event.title}
                    className="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 group-hover:scale-105"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80" />
                  <div className="absolute top-6 left-6">
                    <div className="glass text-white/80 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border-white/10">
                      {event.category}
                    </div>
                  </div>
                </div>

                <div className="p-10 flex flex-col flex-1">
                  <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center gap-2 text-[10px] font-black text-primary uppercase tracking-widest">
                      <Tag size={12} /> {event.type}
                    </div>
                    {event.status === "upcoming" && (
                      <div className="w-2 h-2 rounded-full bg-primary animate-ping" />
                    )}
                  </div>

                  <div className="flex-1">
                    <div className="flex items-center gap-2 mb-4 text-white/30 font-black text-[10px] uppercase tracking-widest">
                      <Calendar size={14} /> {event.date}
                    </div>
                    <h3 className="text-2xl font-black text-white mb-4 uppercase tracking-tight group-hover:text-primary transition-colors">
                      {event.title}
                    </h3>
                    <p className="text-lg text-white/40 font-bold line-clamp-2 mb-8 leading-relaxed">
                      {event.description}
                    </p>
                  </div>

                  <div className="pt-8 border-t border-white/5 mt-auto">
                    <div className="flex items-center justify-between mb-8">
                       <div className="flex items-center gap-2 text-[10px] font-black text-white/30 uppercase tracking-widest">
                        <MapPin size={12} className="text-primary" /> Location Vetted
                      </div>
                      <span className="text-white font-black text-xl">{event.price}</span>
                    </div>
                    <Link 
                      href={`/events/${event.id}`}
                      className="btn-premium w-full py-5 text-sm uppercase tracking-widest"
                    >
                      Book Ticket
                    </Link>
                  </div>
                </div>
              </motion.div>
            ))}
          </AnimatePresence>
        </div>

        {filteredEvents.length === 0 && (
          <div className="text-center py-40 glass rounded-[4rem] border-white/5">
            <h3 className="text-2xl font-black text-white/20 uppercase tracking-widest mb-4">No Signals Detected</h3>
            <p className="text-white/10 font-bold text-lg max-w-xs mx-auto">Check back shortly. New sessions are being vetted.</p>
          </div>
        )}

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="mt-24 flex items-center justify-center gap-4">
            <button
              onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
              className="w-14 h-14 rounded-2xl glass border-white/10 flex items-center justify-center text-white hover:border-primary transition-all"
            >
              <ChevronLeft size={24} />
            </button>
            <div className="flex gap-2">
              {[...Array(totalPages)].map((_, i) => (
                <button
                  key={i}
                  onClick={() => setCurrentPage(i + 1)}
                  className={cn(
                    "w-14 h-14 rounded-2xl text-sm font-black transition-all",
                    currentPage === i + 1 
                      ? "bg-primary text-white" 
                      : "glass border-white/5 text-white/30 hover:text-white"
                  )}
                >
                  {i + 1}
                </button>
              ))}
            </div>
            <button
              onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
              className="w-14 h-14 rounded-2xl glass border-white/10 flex items-center justify-center text-white hover:border-primary transition-all"
            >
              <ChevronRight size={24} />
            </button>
          </div>
        )}
      </div>
    </div>
  );
}

const cn = (...classes: any[]) => classes.filter(Boolean).join(" ");

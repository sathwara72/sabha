"use client";

import { useState, useMemo } from "react";
import { Calendar, MapPin, Search, Tag, ArrowRight, Info, Filter, Zap, ChevronLeft, ChevronRight } from "lucide-react";
import { cn } from "@/lib/utils";
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
  { id: 11, title: "The Art of Negotiation", date: "Sep 15, 2026", time: "2:00 PM", type: "physical", status: "past", category: "Seminar", description: "Learning advanced psychological and strategic techniques for high-stakes business deals.", attendees: "60", price: "₹3,499", image: "https://images.unsplash.com/photo-1507679799987-c7377bd5871f?q=80&w=800&auto=format&fit=crop" },
  { id: 12, title: "FinTech 2026: The Future", date: "Jul 10, 2026", time: "5:00 PM", type: "virtual", status: "past", category: "Workshop", description: "A deep dive into distributed ledgers and the next generation of financial infrastructure.", attendees: "400", price: "Free", image: "https://images.unsplash.com/photo-1551288049-bbbda536339a?q=80&w=800&auto=format&fit=crop" },
  { id: 13, title: "Wellness for Entrepreneurs", date: "Jun 05, 2026", time: "7:00 AM", type: "hybrid", status: "past", category: "Networking", description: "Focusing on mental and physical health as the core engine for sustainable business growth.", attendees: "120", price: "₹1,499", image: "https://images.unsplash.com/photo-1526676037777-05a232534f57?q=80&w=800&auto=format&fit=crop" },
  { id: 14, title: "Global Franchising Expo", date: "May 22, 2026", time: "10:00 AM", type: "physical", status: "past", category: "Seminar", description: "Connecting franchise brands with potential partners across the Asian subcontinent.", attendees: "2500", price: "₹1,999", image: "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800&auto=format&fit=crop" },
  { id: 15, title: "SaaS Growth Masterclass", date: "Apr 18, 2026", time: "11:00 AM", type: "virtual", status: "past", category: "Workshop", description: "Scaling a software subscription business from zero to 1M ARR in record time.", attendees: "800", price: "₹9,999", image: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=800&auto=format&fit=crop" },
  { id: 16, title: "Pitch Perfect: VC Demo Day", date: "Nov 30, 2026", time: "2:00 PM", type: "physical", status: "upcoming", category: "Networking", description: "The final demo event for our current incubator cohort in front of India top seed investors.", attendees: "200+", price: "Invite Only", image: "https://images.unsplash.com/photo-1475721027785-f74eccf877e2?q=80&w=800&auto=format&fit=crop" },
  { id: 17, title: "Blockchain Decoded", date: "Dec 12, 2026", time: "6:00 PM", type: "virtual", status: "upcoming", category: "Workshop", description: "Moving beyond crypto: how blockchain is securing enterprise-level data in 2026.", attendees: "350+", price: "Free", image: "https://images.unsplash.com/photo-1518544801976-3e159e50e5bb?q=80&w=800&auto=format&fit=crop" },
  { id: 18, title: "Sustainability Summit", date: "Dec 20, 2026", time: "10:00 AM", type: "hybrid", status: "upcoming", category: "Summit", description: "Aligning corporate governance with ESG goals for a greener global economy.", attendees: "600+", price: "₹3,999", image: "https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=800&auto=format&fit=crop" },
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
    <div className="bg-slate-50 min-h-screen relative overflow-hidden">
      {/* Dynamic Background Orbs */}
      <div className="absolute inset-0 pointer-events-none">
        <motion.div 
          animate={{ 
            x: [0, -120, 0], 
            y: [0, 80, 0],
            scale: [1, 1.1, 1]
          }}
          transition={{ duration: 22, repeat: Infinity, ease: "easeInOut" }}
          className="absolute top-[10%] right-[-5%] w-[600px] h-[600px] bg-primary/5 rounded-full blur-[140px]" 
        />
        <motion.div 
          animate={{ 
            x: [0, 60, 0], 
            y: [0, -40, 0],
            scale: [1, 1.2, 1]
          }}
          transition={{ duration: 18, repeat: Infinity, ease: "easeInOut", delay: 1 }}
          className="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-accent/5 rounded-full blur-[120px]" 
        />
      </div>

      {/* Dynamic Header */}
      <div className="bg-white py-24 relative overflow-hidden border-b z-10">
        <div className="absolute inset-0 bg-gradient-to-r from-primary/5 to-accent/5 opacity-50" />
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <motion.div 
            initial={{ opacity: 0, y: 20 }} 
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, ease: "easeOut" }}
          >
            <h1 className="text-4xl font-black tracking-tight text-foreground sm:text-6xl md:text-7xl italic">
              Events <span className="text-gradient">Radar.</span>
            </h1>
            <p className="mt-6 text-lg text-foreground/50 max-w-2xl font-medium leading-relaxed italic">
              From mastermind sessions to networking mixers. Curated experiences designed for professional breakthroughs and strategic connections.
            </p>
          </motion.div>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-6 lg:px-8 py-12 relative z-10">
        {/* Horizontal Top Filter Bar */}
        <div className="flex flex-col gap-8 mb-12">
          {/* Search Bar Section */}
          <div className="relative max-w-3xl">
            <div className="absolute inset-0 bg-primary/10 blur-3xl opacity-30" />
            <motion.div 
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="relative"
            >
              <Search className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-foreground/30" />
              <input
                type="text"
                placeholder="Scan the radar: e.g. 'Networking Mixers' or 'Scale Workshops'"
                className="w-full bg-white border rounded-[2rem] py-6 pl-16 pr-8 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all shadow-2xl shadow-foreground/5 text-lg font-bold italic"
                value={searchQuery}
                onChange={(e) => handleSearchChange(e.target.value)}
              />
            </motion.div>
          </div>

          {/* Status Filter Section */}
          <div className="flex flex-col md:flex-row items-start md:items-center gap-6">
            <div className="flex items-center gap-3 shrink-0">
              <div className="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                <Filter className="w-5 h-5 text-primary" />
              </div>
              <h2 className="font-black text-lg italic tracking-tight uppercase text-foreground/40">Status:</h2>
            </div>
            
            <div className="flex flex-wrap gap-2">
              {["upcoming", "current", "past"].map((f, idx) => (
                <motion.button
                  key={f}
                  initial={{ opacity: 0, x: -10 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: idx * 0.05 }}
                  onClick={() => handleFilterChange(f)}
                  className={cn(
                    "px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all border shrink-0 flex items-center gap-2",
                    filter === f 
                      ? "bg-primary text-white border-primary shadow-lg shadow-primary/20" 
                      : "bg-white border-foreground/5 text-foreground/40 hover:border-primary/30 hover:text-primary italic"
                  )}
                >
                  {f}
                  {filter === f && <Zap className="w-3 h-3 fill-current" />}
                </motion.button>
              ))}
            </div>

            <div className="hidden md:block ml-auto">
              <motion.div 
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                className="bg-primary/5 rounded-2xl px-6 py-3 border border-primary/10 flex items-center gap-3"
              >
                <Info className="w-4 h-4 text-primary" />
                <p className="text-[10px] text-foreground/40 leading-relaxed font-bold italic">
                  "Members get 40% priority discount on all events."
                </p>
              </motion.div>
            </div>
          </div>
        </div>

        {/* Results Grid */}
        <div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <AnimatePresence mode="popLayout">
              {paginatedEvents.map((event, idx) => (
                <motion.div
                  key={event.id}
                  layout
                  initial={{ opacity: 0, y: 30 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, scale: 0.95 }}
                  whileHover={{ 
                    rotateX: 3, 
                    rotateY: -3, 
                    y: -12,
                    scale: 1.01,
                    zIndex: 20
                  }}
                  transition={{ 
                    type: "spring", 
                    stiffness: 100, 
                    damping: 20,
                    layout: { duration: 0.3 }
                  }}
                  style={{ perspective: 1200 }}
                  className="group bg-white rounded-[2.5rem] border hover:border-primary/50 transition-all flex flex-col h-full shadow-xl shadow-foreground/5 relative overflow-hidden"
                >
                  <div className="absolute inset-0 bg-gradient-to-br from-primary/[0.03] to-accent/[0.03] pointer-events-none" />
                  
                  {/* Featured Image */}
                  <div className="h-48 w-full overflow-hidden relative">
                    <div className="absolute inset-0 bg-primary/20 mix-blend-multiply opacity-0 group-hover:opacity-10 transition-opacity z-10" />
                    <motion.img 
                      src={event.image} 
                      alt={event.title}
                      whileHover={{ scale: 1.15 }}
                      transition={{ duration: 0.8, ease: "easeOut" }}
                      className="w-full h-full object-cover"
                    />
                    <div className="absolute top-4 left-4 z-20">
                      <div className="bg-white/90 backdrop-blur-md text-primary px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] border border-white/20 shadow-lg italic">
                        {event.category}
                      </div>
                    </div>
                  </div>

                  <div className="p-8 flex flex-col flex-1 relative">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors pointer-events-none" />
                    
                    <div className="flex items-center justify-between mb-6 relative z-10">
                      <div className="flex items-center gap-1.5 text-[10px] font-black text-foreground/30 uppercase tracking-[0.2em]">
                        <Tag className="w-3.5 h-3.5" />
                        {event.type}
                      </div>
                      {event.status === "upcoming" && (
                        <motion.div 
                          animate={{ scale: [1, 1.4, 1], opacity: [1, 0.6, 1] }}
                          transition={{ duration: 2, repeat: Infinity }}
                          className="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_15px_rgba(34,197,94,0.4)]" 
                        />
                      )}
                    </div>

                    <div className="flex-1 relative z-10 text-left">
                      <div className="flex items-center gap-2 mb-3">
                        <Calendar className="w-4 h-4 text-primary" />
                        <span className="text-[10px] font-black text-foreground/40 uppercase tracking-widest">{event.date} • {event.time}</span>
                      </div>
                      <h3 className="text-2xl font-black mb-4 group-hover:text-primary transition-all leading-tight italic tracking-tight min-h-[3.5rem]">
                        "{event.title}"
                      </h3>
                      <p className="text-sm text-foreground/50 mb-8 line-clamp-2 leading-relaxed font-medium italic">
                        {event.description}
                      </p>
                    </div>

                    <div className="space-y-6 pt-6 border-t border-dashed relative z-10 mt-auto">
                      <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.15em] text-foreground/40">
                          <MapPin className="w-3.5 h-3.5 text-primary" />
                          Location Vetted
                        </div>
                        <span className="text-primary font-black text-lg tracking-tight">{event.price}</span>
                      </div>
                      
                      <Link 
                        href={`/events/${event.id}`}
                        className="w-full py-5 rounded-2xl bg-primary text-white text-xs font-black uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-primary/95 transition-all shadow-lg shadow-primary/30 group/btn hover:scale-[1.03] active:scale-[0.98]"
                      >
                        Elite Registration
                        <ArrowRight className="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
                      </Link>
                    </div>
                  </div>
                </motion.div>
              ))}
            </AnimatePresence>
          </div>

          {filteredEvents.length === 0 && (
            <motion.div 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-32 bg-white rounded-[3.5rem] border-2 border-dashed border-foreground/10 shadow-xl shadow-foreground/5 relative z-10"
            >
              <div className="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <Calendar className="w-8 h-8 text-foreground/10" />
              </div>
              <h3 className="text-2xl font-black text-foreground/30 italic tracking-tight">No Events in this Signal</h3>
              <p className="text-foreground/20 max-w-xs mx-auto italic text-sm font-medium">"Check back shortly. New mastermind sessions and mixers are currently being vetted."</p>
            </motion.div>
          )}

          {/* Pagination Controls */}
          {totalPages > 1 && (
            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              className="mt-20 flex flex-col items-center gap-8 relative z-10"
            >
              <div className="flex items-center gap-3">
                <button
                  onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
                  disabled={currentPage === 1}
                  className="w-12 h-12 rounded-2xl border flex items-center justify-center text-foreground disabled:opacity-20 hover:bg-primary hover:text-white hover:border-primary transition-all group shadow-sm bg-white"
                >
                  <ChevronLeft className="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" />
                </button>
                
                <div className="flex items-center gap-2">
                  {[...Array(totalPages)].map((_, i) => (
                    <button
                      key={i + 1}
                      onClick={() => setCurrentPage(i + 1)}
                      className={cn(
                        "w-12 h-12 rounded-2xl text-xs font-black transition-all shadow-sm",
                        currentPage === i + 1 
                          ? "bg-primary text-white scale-110 shadow-lg shadow-primary/30" 
                          : "bg-white border hover:border-primary text-foreground/40 hover:text-primary italic"
                      )}
                    >
                      {i + 1}
                    </button>
                  ))}
                </div>

                <button
                  onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
                  disabled={currentPage === totalPages}
                  className="w-12 h-12 rounded-2xl border flex items-center justify-center text-foreground disabled:opacity-20 hover:bg-primary hover:text-white hover:border-primary transition-all group shadow-sm bg-white"
                >
                  <ChevronRight className="w-5 h-5 group-hover:translate-x-0.5 transition-transform" />
                </button>
              </div>
              
              <p className="text-[10px] font-black text-foreground/30 uppercase tracking-[0.3em] bg-white px-8 py-3 rounded-full border shadow-sm italic">
                Showing {paginatedEvents.length} of {filteredEvents.length} Verified Events
              </p>
            </motion.div>
          )}
        </div>
      </div>
    </div>
  );
}

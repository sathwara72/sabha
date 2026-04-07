"use client";

import { useState, useMemo } from "react";
import { Search, Filter, MapPin, Briefcase, ChevronRight, Star, ShieldCheck, Zap, ChevronLeft } from "lucide-react";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";

const mockBusinesses = [
  { id: 1, name: "Vertex Solutions", category: "Software Development", location: "Mumbai", rating: 4.8, reviews: 24, verified: true, image: "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 2, name: "Global Logistics", category: "Supply Chain", location: "Delhi", rating: 4.5, reviews: 18, verified: true, image: "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 3, name: "Nexus Marketing", category: "Digital Marketing", location: "Bangalore", rating: 4.2, reviews: 12, verified: false, image: "https://images.unsplash.com/photo-1551836022-d5d88e9218df?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 4, name: "Prime Builders", category: "Construction", location: "Pune", rating: 4.9, reviews: 31, verified: true, image: "https://images.unsplash.com/photo-1503387762-592dea58ef21?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 5, name: "Zenith Finance", category: "Financial Services", location: "Mumbai", rating: 4.6, reviews: 15, verified: false, image: "https://images.unsplash.com/photo-1454165833762-02ad50e8958?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 6, name: "Eco Energy", category: "Renewables", location: "Hyderabad", rating: 4.7, reviews: 20, verified: true, image: "https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 7, name: "Summit Consulting", category: "Business Strategy", location: "Mumbai", rating: 4.4, reviews: 10, verified: true, image: "https://images.unsplash.com/photo-1557804506-669a67965ba0?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 8, name: "Alpha Tech Lab", category: "Software Development", location: "Chennai", rating: 4.9, reviews: 45, verified: true, image: "https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=200&h=200&auto=format&fit=crop" },
];

export default function BusinessDirectory() {
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 6;

  const categories = ["All", "Software Development", "Supply Chain", "Digital Marketing", "Construction", "Financial Services", "Renewables"];

  const filteredBusinesses = useMemo(() => {
    return mockBusinesses.filter(b => {
      const matchesSearch = b.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                            b.category.toLowerCase().includes(searchQuery.toLowerCase());
      const matchesCategory = selectedCategory === "All" || b.category === selectedCategory;
      return matchesSearch && matchesCategory;
    });
  }, [searchQuery, selectedCategory]);

  const totalPages = Math.ceil(filteredBusinesses.length / itemsPerPage);
  const paginatedBusinesses = filteredBusinesses.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  const handleCategoryChange = (cat: string) => {
    setSelectedCategory(cat);
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
        <div className="absolute top-[-5%] right-[-5%] w-[600px] h-[600px] bg-primary/10 rounded-full blur-[140px] animate-pulse" />
        <div className="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-accent/10 rounded-full blur-[120px] animate-pulse" />
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
                Vetted Professional Ecosystem
              </span>
            </div>
            <h1 className="text-5xl md:text-8xl font-black mb-8 leading-none tracking-tighter uppercase">
              Elite <span className="text-gradient">Directory.</span>
            </h1>
            <p className="max-w-2xl text-xl md:text-2xl text-white/50 font-bold leading-relaxed mb-12">
              Connect with vetted industry leaders. Find your next strategic partner in the Sabha ecosystem.
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
                placeholder="PRO DIRECTORY SEARCH: e.g. 'Software' or 'Mumbai'"
                className="w-full glass border-white/10 rounded-[2rem] py-6 pl-16 pr-8 focus:border-primary outline-none text-white font-black text-sm tracking-widest uppercase transition-all"
                value={searchQuery}
                onChange={(e) => handleSearchChange(e.target.value)}
              />
            </motion.div>
          </div>

          <div className="flex flex-col md:flex-row items-start md:items-center gap-8">
            <div className="flex items-center gap-4">
              <Filter className="w-5 h-5 text-primary" />
              <h2 className="font-black text-xs uppercase tracking-widest text-white/40">Filters:</h2>
              <div className="flex flex-wrap gap-2">
                {categories.map((cat) => (
                  <button
                    key={cat}
                    onClick={() => handleCategoryChange(cat)}
                    className={cn(
                      "px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all border",
                      selectedCategory === cat 
                        ? "bg-primary text-white border-primary shadow-lg shadow-primary/20" 
                        : "glass border-white/5 text-white/40 hover:text-white"
                    )}
                  >
                    {cat}
                  </button>
                ))}
              </div>
            </div>
          </div>
        </div>

        {/* Results Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
          <AnimatePresence mode="popLayout">
            {paginatedBusinesses.map((business, idx) => (
              <motion.div
                key={business.id}
                layout
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                whileHover={{ y: -10 }}
                className="glass-card flex flex-col h-full rounded-[3rem] p-10 relative group"
              >
                <div className="flex items-start justify-between mb-10">
                  <div className="h-24 w-24 rounded-3xl glass border-white/10 p-2 overflow-hidden group-hover:border-primary/50 transition-colors">
                    <img 
                      src={business.image} 
                      alt={business.name}
                      className="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 rounded-2xl"
                    />
                  </div>
                  <div className="flex flex-col items-end gap-3">
                    {business.verified && (
                      <div className="glass text-primary px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2 border-primary/20 shadow-lg shadow-primary/10">
                        <ShieldCheck size={12} />
                        Verified
                      </div>
                    )}
                    <div className="flex items-center gap-1.5 text-amber-500 font-black text-xs bg-amber-500/5 px-4 py-1.5 rounded-full border border-amber-500/10">
                      <Star size={12} className="fill-current" />
                      {business.rating}
                    </div>
                  </div>
                </div>

                <div className="flex-1">
                  <h3 className="text-3xl font-black text-white mb-4 uppercase tracking-tighter group-hover:text-primary transition-colors">
                    {business.name}
                  </h3>
                  <div className="flex items-center gap-2 text-[10px] font-black text-white/30 uppercase tracking-widest mb-8">
                    <MapPin size={12} className="text-primary" />
                    {business.location} • {business.category}
                  </div>
                  
                  <div className="grid grid-cols-2 gap-6 mb-10 pt-8 border-t border-white/5">
                    <div className="flex flex-col">
                      <span className="text-[10px] text-white/20 font-black uppercase tracking-[0.2em] mb-1">Reviews</span>
                      <span className="font-bold text-white text-sm">{business.reviews} Members</span>
                    </div>
                    <div className="flex flex-col">
                      <span className="text-[10px] text-white/20 font-black uppercase tracking-[0.2em] mb-1">Status</span>
                      <span className="text-xs font-black text-green-500 flex items-center gap-1.5 tracking-widest uppercase">
                        <span className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse" />
                        Active
                      </span>
                    </div>
                  </div>
                </div>

                <Link 
                  href={`/businesses/${business.id}`}
                  className="btn-premium w-full py-5 text-sm uppercase tracking-widest"
                >
                  View Profile
                </Link>
              </motion.div>
            ))}
          </AnimatePresence>
        </div>

        {filteredBusinesses.length === 0 && (
          <div className="text-center py-40 glass rounded-[4rem] border-white/5">
            <h3 className="text-2xl font-black text-white/20 uppercase tracking-widest mb-4">No Entities Found</h3>
            <p className="text-white/10 font-bold text-lg max-w-xs mx-auto">Try broadening your industry focus.</p>
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

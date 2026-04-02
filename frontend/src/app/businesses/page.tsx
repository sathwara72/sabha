"use client";

import { useState, useMemo } from "react";
import { Search, Filter, MapPin, Briefcase, ChevronRight, Star, ShieldCheck, Zap, ChevronLeft } from "lucide-react";
import { cn } from "@/lib/utils";
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
  { id: 9, name: "Horizon Architects", category: "Construction", location: "Ahmedabad", rating: 4.3, reviews: 22, verified: false, image: "https://images.unsplash.com/photo-1487958449943-2429e8be8625?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 10, name: "Stellar PR", category: "Digital Marketing", location: "Mumbai", rating: 4.6, reviews: 28, verified: true, image: "https://images.unsplash.com/photo-1542744173-8e7e53415bb0?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 11, name: "Velocity Ventures", category: "Financial Services", location: "Gurgaon", rating: 4.7, reviews: 35, verified: true, image: "https://images.unsplash.com/photo-1559526324-4b87b5e36e44?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 12, name: "Clean Stream", category: "Renewables", location: "Kochi", rating: 4.5, reviews: 14, verified: false, image: "https://images.unsplash.com/photo-1466611653911-954ff21b6748?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 13, name: "Titan Logistics", category: "Supply Chain", location: "Surat", rating: 4.2, reviews: 19, verified: true, image: "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 14, name: "Blue Sky Apps", category: "Software Development", location: "Bangalore", rating: 4.8, reviews: 52, verified: true, image: "https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 15, name: "Empire Real Estate", category: "Construction", location: "Lucknow", rating: 4.1, reviews: 9, verified: false, image: "https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 16, name: "Vision Media", category: "Digital Marketing", location: "Jaipur", rating: 4.4, reviews: 16, verified: true, image: "https://images.unsplash.com/photo-1551288049-bbbda536339a?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 17, name: "Fortitude Wealth", category: "Financial Services", location: "Pune", rating: 4.9, reviews: 41, verified: true, image: "https://images.unsplash.com/photo-1553729459-014266329eee?q=80&w=200&h=200&auto=format&fit=crop" },
  { id: 18, name: "Power Grid Solutions", category: "Renewables", location: "Nagpur", rating: 4.6, reviews: 27, verified: true, image: "https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?q=80&w=200&h=200&auto=format&fit=crop" },
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
    <div className="min-h-screen bg-slate-50 relative overflow-hidden">
      {/* Dynamic Background Orbs */}
      <div className="absolute inset-0 pointer-events-none">
        <motion.div 
          animate={{ 
            x: [0, 100, 0], 
            y: [0, 50, 0],
            scale: [1, 1.2, 1]
          }}
          transition={{ duration: 20, repeat: Infinity, ease: "easeInOut" }}
          className="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-primary/5 rounded-full blur-[120px]" 
        />
        <motion.div 
          animate={{ 
            x: [0, -80, 0], 
            y: [0, 120, 0],
            scale: [1, 1.1, 1]
          }}
          transition={{ duration: 25, repeat: Infinity, ease: "easeInOut", delay: 2 }}
          className="absolute bottom-[20%] right-[-5%] w-[400px] h-[400px] bg-accent/5 rounded-full blur-[100px]" 
        />
        <motion.div 
          animate={{ 
            x: [0, 50, 0], 
            y: [0, -100, 0],
            scale: [1, 1.3, 1]
          }}
          transition={{ duration: 18, repeat: Infinity, ease: "easeInOut", delay: 5 }}
          className="absolute top-[40%] left-[20%] w-[300px] h-[300px] bg-primary/5 rounded-full blur-[80px]" 
        />
      </div>

      {/* Premium Header */}
      <div className="bg-white py-24 relative overflow-hidden border-b z-10">
        <div className="absolute inset-0 bg-gradient-to-r from-primary/5 to-accent/5 opacity-50" />
        <div className="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, ease: "easeOut" }}
          >
            <h1 className="text-4xl font-black tracking-tight text-foreground sm:text-6xl md:text-7xl italic">
              Elite <span className="text-gradient">Directory.</span>
            </h1>
            <p className="mt-6 text-lg text-foreground/50 max-w-2xl font-medium leading-relaxed italic">
              Connect with vetted industry leaders. From startups to conglomerates, find your next strategic partner in the Sabha ecosystem.
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
                placeholder="Search the elite directory: e.g. 'Software Developers' or 'Mumbai'"
                className="w-full bg-white border rounded-[2rem] py-6 pl-16 pr-8 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all shadow-2xl shadow-foreground/5 text-lg font-bold italic"
                value={searchQuery}
                onChange={(e) => handleSearchChange(e.target.value)}
              />
            </motion.div>
          </div>

          {/* Category Filter Section */}
          <div className="flex flex-col md:flex-row items-start md:items-center gap-6">
            <div className="flex items-center gap-3 shrink-0">
              <div className="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                <Filter className="w-5 h-5 text-primary" />
              </div>
              <h2 className="font-black text-lg italic tracking-tight uppercase text-foreground/40">Filters:</h2>
            </div>
            
            <div className="flex flex-wrap gap-2">
              {categories.map((cat, idx) => (
                <motion.button
                  key={cat}
                  initial={{ opacity: 0, x: -10 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: idx * 0.05 }}
                  onClick={() => handleCategoryChange(cat)}
                  className={cn(
                    "px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all border shrink-0",
                    selectedCategory === cat 
                      ? "bg-primary text-white border-primary shadow-lg shadow-primary/20" 
                      : "bg-white border-foreground/5 text-foreground/40 hover:border-primary/30 hover:text-primary italic"
                  )}
                >
                  {cat}
                </motion.button>
              ))}
            </div>
          </div>
        </div>

        {/* Results Grid */}
        <div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <AnimatePresence mode="popLayout">
              {paginatedBusinesses.map((business, idx) => (
                <motion.div
                  key={business.id}
                  layout
                  initial={{ opacity: 0, y: 30 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, scale: 0.95 }}
                  whileHover={{ 
                    rotateX: 2, 
                    rotateY: -2, 
                    y: -10,
                    scale: 1.02,
                    zIndex: 20
                  }}
                  transition={{ 
                    type: "spring", 
                    stiffness: 100, 
                    damping: 20,
                    layout: { duration: 0.3 }
                  }}
                  style={{ perspective: 1000 }}
                  className="group bg-white rounded-[2.5rem] p-8 border hover:border-primary/50 transition-all flex flex-col h-full shadow-xl shadow-foreground/5 relative overflow-hidden"
                >
                  <div className="absolute inset-0 bg-gradient-to-br from-primary/[0.02] to-accent/[0.02] pointer-events-none" />
                  <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/20 transition-colors pointer-events-none" />
                  
                  <div className="flex items-start justify-between mb-8 relative z-10">
                    <div className="h-20 w-20 rounded-[1.5rem] bg-slate-50 overflow-hidden border border-foreground/5 flex items-center justify-center transition-all shadow-inner group-hover:shadow-primary/20 group-hover:border-primary/20">
                      <img 
                        src={business.image} 
                        alt={business.name}
                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                      />
                    </div>
                    <div className="flex flex-col items-end gap-2">
                      {business.verified && (
                        <div className="bg-primary/10 text-primary px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 border border-primary/20 shadow-sm">
                          <ShieldCheck className="w-3 h-3" />
                          Verified
                        </div>
                      )}
                      <div className="flex items-center gap-1 text-amber-500 font-bold text-xs bg-amber-500/5 px-3 py-1 rounded-full border border-amber-500/10">
                        <Star className="w-3 h-3 fill-current" />
                        {business.rating}
                      </div>
                    </div>
                  </div>
                  
                  <div className="flex-1 relative z-10 text-left">
                    <h3 className="text-2xl font-black mb-2 group-hover:text-primary transition-colors italic tracking-tight leading-tight">{business.name}</h3>
                    <div className="flex items-center gap-2 text-[10px] text-foreground/40 mb-6 font-black uppercase tracking-widest">
                      <MapPin className="w-3.5 h-3.5 text-primary" />
                      {business.location} • {business.category}
                    </div>
                    
                    <div className="flex gap-4 mb-8">
                      <div className="flex flex-col">
                        <span className="text-[10px] text-foreground/30 font-black uppercase tracking-widest">Reviews</span>
                        <span className="font-bold text-sm tracking-tight">{business.reviews} Professionals</span>
                      </div>
                      <div className="w-px h-8 bg-foreground/5" />
                      <div className="flex flex-col">
                        <span className="text-[10px] text-foreground/30 font-black uppercase tracking-widest">Status</span>
                        <span className="text-xs font-bold text-green-500 flex items-center gap-1.5 uppercase tracking-widest">
                          <motion.div 
                            animate={{ scale: [1, 1.5, 1], opacity: [1, 0.5, 1] }}
                            transition={{ duration: 2, repeat: Infinity }}
                            className="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]" 
                          />
                          Active
                        </span>
                      </div>
                    </div>
                  </div>

                  <Link 
                    href={`/businesses/${business.id}`}
                    className="w-full py-5 rounded-2xl bg-primary text-white text-xs font-black uppercase tracking-widest flex items-center justify-center gap-3 hover:bg-primary/95 transition-all group/btn shadow-lg shadow-primary/20 relative z-10 hover:scale-[1.03] active:scale-[0.98]"
                  >
                    View Elite Profile
                    <ChevronRight className="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
                  </Link>
                </motion.div>
              ))}
            </AnimatePresence>
          </div>

          {filteredBusinesses.length === 0 && (
            <motion.div 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-32 bg-white rounded-[3rem] border-2 border-dashed border-foreground/10 relative z-10"
            >
              <div className="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <Briefcase className="w-10 h-10 text-foreground/10" />
              </div>
              <h3 className="text-2xl font-black text-foreground/30 mb-2 italic tracking-tight">Requirement Unmatched</h3>
              <p className="text-foreground/20 max-w-xs mx-auto italic text-sm font-medium">"We couldn't find a business matching those elite criteria. Try broadening your industry focus."</p>
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
                          ? "bg-primary text-white scale-110 shadow-lg shadow-primary/20" 
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
                Showing {paginatedBusinesses.length} of {filteredBusinesses.length} Elite Entities
              </p>
            </motion.div>
          )}
        </div>
      </div>
    </div>
  );
}

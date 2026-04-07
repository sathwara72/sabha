"use client";

import { useState } from "react";
import { createEventAdmin } from "@/lib/api";
import { 
  Zap, Calendar, MapPin, 
  Tag, Info, PlusCircle, CheckCircle2 
} from "lucide-react";
import { motion } from "framer-motion";

export default function AdminEventsPage() {
  const [formData, setFormData] = useState({
    title: "",
    description: "",
    date: "",
    location: "",
    type: "Mixer",
  });
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      await createEventAdmin(formData);
      setSuccess(true);
      setFormData({ title: "", description: "", date: "", location: "", type: "Mixer" });
      setTimeout(() => setSuccess(false), 3000);
    } catch (error) {
      alert("Failed to curate event signal.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-16 max-w-4xl">
      <div className="flex flex-col gap-6">
        <h1 className="text-4xl md:text-6xl font-black text-white leading-none tracking-tighter uppercase whitespace-nowrap">
           Curation <span className="text-gradient underline underline-offset-8 decoration-accent">Engine.</span>
        </h1>
        <p className="text-[11px] font-black uppercase tracking-[0.4em] text-white/30 px-2 italic-none tracking-tighter">Broadcasting elite community gatherings</p>
      </div>

       <div className="glass p-12 rounded-[4rem] border-white/5 relative overflow-hidden">
          <div className="absolute top-0 right-0 w-64 h-64 bg-accent/5 rounded-full blur-[100px]" />
          
          <form className="space-y-10 relative z-10" onSubmit={handleSubmit}>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
                 <div className="space-y-4">
                    <label className="text-[10px] font-black uppercase tracking-[0.3em] text-primary ml-4">Signal Title</label>
                    <input 
                      required
                      type="text" 
                      placeholder="e.g. 'SABHER NETWORKING'"
                      className="w-full glass border-white/10 rounded-[1.5rem] p-6 text-white outline-none focus:border-primary transition-all font-black text-xs uppercase tracking-widest"
                      value={formData.title}
                      onChange={(e) => setFormData({...formData, title: e.target.value})}
                    />
                 </div>
                 <div className="space-y-4">
                    <label className="text-[10px] font-black uppercase tracking-[0.3em] text-accent ml-4">Deployment Node (Location)</label>
                    <input 
                      required
                      type="text" 
                      placeholder="e.g. 'Aloft Hotel, Ahmedabad'"
                      className="w-full glass border-white/10 rounded-[1.5rem] p-6 text-white outline-none focus:border-accent transition-all font-black text-xs uppercase tracking-widest"
                      value={formData.location}
                      onChange={(e) => setFormData({...formData, location: e.target.value})}
                    />
                 </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div className="space-y-4">
                    <label className="text-[10px] font-black uppercase tracking-[0.3em] text-white/40 ml-4">Temporal Coordinates (Date)</label>
                    <input 
                      required
                      type="date" 
                      className="w-full glass border-white/10 rounded-[1.5rem] p-6 text-white outline-none focus:border-primary transition-all font-black text-xs uppercase tracking-widest"
                      value={formData.date}
                      onChange={(e) => setFormData({...formData, date: e.target.value})}
                    />
                 </div>
                 <div className="space-y-4">
                    <label className="text-[10px] font-black uppercase tracking-[0.3em] text-white/40 ml-4">Category Signal</label>
                    <select 
                      className="w-full glass border-white/10 rounded-[1.5rem] p-6 text-white outline-none focus:border-primary transition-all font-black text-xs uppercase tracking-widest"
                      value={formData.type}
                      onChange={(e) => setFormData({...formData, type: e.target.value})}
                    >
                       <option value="Mixer">Mixer</option>
                       <option value="Workshop">Workshop</option>
                       <option value="Summit">Summit</option>
                    </select>
                 </div>
              </div>

              <div className="space-y-4">
                 <label className="text-[10px] font-black uppercase tracking-[0.3em] text-white/40 ml-4">Mission Description</label>
                 <textarea 
                   required
                   rows={4}
                   placeholder="Detailed briefing for the gathering..."
                   className="w-full glass border-white/10 rounded-[1.5rem] p-6 text-white outline-none focus:border-primary transition-all font-black text-xs uppercase tracking-widest"
                   value={formData.description}
                   onChange={(e) => setFormData({...formData, description: e.target.value})}
                 />
              </div>

              <div className="pt-8 flex flex-col md:flex-row items-center justify-between gap-10 border-t border-white/5">
                 <div className="flex items-center gap-4 text-white/20 italic-none">
                    <Info size={16} className="text-primary" />
                    <p className="text-[10px] font-black uppercase tracking-widest">Public nodes will update instantly.</p>
                 </div>
                 
                 <button 
                   disabled={loading}
                   className="w-full md:w-auto btn-premium px-16 py-6 text-sm uppercase tracking-widest relative group overflow-hidden"
                 >
                   <span className="relative z-10 flex items-center gap-4">
                      {success ? (
                        <motion.div initial={{ scale: 0.5 }} animate={{ scale: 1 }} className="flex items-center gap-2">
                           Curated <CheckCircle2 size={18} />
                        </motion.div>
                      ) : (
                        loading ? "Processing..." : <>Deploy Signal <PlusCircle size={20} /></>
                      )}
                   </span>
                 </button>
              </div>
          </form>
       </div>

       <div className="mt-16 glass border-white/5 p-12 rounded-[3.5rem] flex items-start gap-10">
          <div className="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center shrink-0">
             <Calendar className="w-8 h-8 text-primary" />
          </div>
          <div>
            <h4 className="text-xl font-black text-white mb-2 uppercase tracking-tighter italic-none">High-Density Broadcast</h4>
            <p className="text-white/30 text-lg leading-relaxed font-bold italic-none uppercase tracking-tight">
               Every event signal is cross-referenced with your strategic partners for maximum visibility. Ensure temporal coordinates are verified before broadcast.
            </p>
          </div>
       </div>
    </div>
  );
}

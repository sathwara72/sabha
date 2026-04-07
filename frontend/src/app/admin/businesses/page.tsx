"use client";

import { useEffect, useState } from "react";
import { 
  fetchAllBusinessesAdmin, approveBusiness, rejectBusiness 
} from "@/lib/api";
import { 
  ShieldCheck, XCircle, CheckCircle2, 
  MapPin, Globe, Briefcase, Star, Info 
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";

interface Business {
  id: number;
  name: string;
  category: string;
  description: string;
  website: string;
  status: string;
  is_verified: boolean;
}

export default function AdminBusinessesPage() {
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadData();
  }, []);

  async function loadData() {
    try {
      const data = await fetchAllBusinessesAdmin();
      setBusinesses(data);
    } catch (error) {
      console.error("Error loading businesses:", error);
    } finally {
      setLoading(false);
    }
  }

  async function handleApprove(id: number) {
    try {
      await approveBusiness(id);
      loadData();
    } catch (error) {
      alert("Approval failed");
    }
  }

  async function handleReject(id: number) {
    try {
      await rejectBusiness(id);
      loadData();
    } catch (error) {
      alert("Rejection failed");
    }
  }

  return (
    <div className="space-y-16">
      <div className="flex flex-col gap-6">
        <h1 className="text-4xl md:text-6xl font-black text-white leading-none tracking-tighter uppercase">
           Entity <span className="text-gradient">Verification.</span>
        </h1>
        <p className="text-[11px] font-black uppercase tracking-[0.4em] text-white/30 px-2 italic-none">Vetting the elite professional network</p>
      </div>

      <div className="flex items-center gap-4 glass bg-primary/5 border-primary/20 p-6 rounded-[2rem] mb-12">
        <Info className="w-6 h-6 text-primary" />
        <p className="text-[10px] font-black uppercase text-white/50 tracking-widest leading-relaxed">
          "Every entity must undergo a multi-layered verification process. Ensure professional integrity before granting approved status."
        </p>
      </div>

      <div className="grid grid-cols-1 gap-8">
        <AnimatePresence>
          {businesses.map((biz) => (
            <motion.div
              key={biz.id}
              layout
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="glass p-12 rounded-[3.5rem] border-white/5 relative overflow-hidden group hover:border-primary/20 transition-all"
            >
              <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-[40px] group-hover:bg-primary/10 transition-colors" />
              
              <div className="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-12 relative z-10">
                <div className="flex flex-col md:flex-row items-center gap-10">
                  <div className="w-24 h-24 rounded-3xl glass border-white/10 flex items-center justify-center text-4xl font-black text-primary shadow-2xl">
                    {biz.name[0]}
                  </div>
                  <div>
                    <div className="flex items-center gap-4 mb-3">
                      <h3 className="text-3xl font-black text-white uppercase tracking-tighter">{biz.name}</h3>
                      <div className={`px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border ${
                        biz.status === "approved" 
                          ? "bg-primary/10 text-primary border-primary/20" 
                          : "bg-white/5 text-white/30 border-white/5"
                      }`}>
                        {biz.status}
                      </div>
                    </div>
                    <div className="flex flex-wrap gap-6 text-[10px] font-black uppercase tracking-widest text-white/30">
                      <span className="flex items-center gap-2"><Briefcase size={14} className="text-primary" /> {biz.category}</span>
                      <span className="flex items-center gap-2"><Globe size={14} className="text-primary" /> {biz.website}</span>
                    </div>
                  </div>
                </div>

                <div className="flex items-center gap-6 w-full lg:w-auto">
                  {biz.status === "pending" ? (
                    <>
                      <button 
                        onClick={() => handleApprove(biz.id)}
                        className="flex-1 lg:flex-none flex items-center gap-3 bg-primary text-white font-black text-xs uppercase tracking-widest px-10 py-5 rounded-2xl hover:opacity-90 active:scale-95 transition-all shadow-xl shadow-primary/20"
                      >
                        <CheckCircle2 size={18} />
                        Approve
                      </button>
                      <button 
                        onClick={() => handleReject(biz.id)}
                        className="flex-1 lg:flex-none flex items-center gap-3 bg-white/5 text-white/40 font-black text-xs uppercase tracking-widest px-10 py-5 rounded-2xl hover:bg-white/10 active:scale-95 transition-all"
                      >
                        <XCircle size={18} />
                        Reject
                      </button>
                    </>
                  ) : (
                    <div className="flex items-center gap-4 text-white/20 font-black text-[10px] uppercase tracking-widest border border-white/5 px-8 py-4 rounded-full">
                       <ShieldCheck size={16} className={biz.status === "approved" ? "text-primary" : "text-white/20"} />
                       Entity {biz.status}
                    </div>
                  )}
                </div>
              </div>

              <div className="mt-10 pt-10 border-t border-white/5 relative z-10">
                 <p className="text-lg text-white/40 font-bold leading-relaxed max-w-4xl italic-none uppercase tracking-tight">
                    "{biz.description || "No professional overview available for this entity. Pending manual audit."}"
                 </p>
              </div>
            </motion.div>
          ))}
        </AnimatePresence>

        {businesses.length === 0 && !loading && (
           <div className="text-center py-40 glass rounded-[4rem] border-white/5">
              <p className="text-3xl font-black text-white/10 uppercase tracking-widest">No Signals Detected.</p>
           </div>
        )}
      </div>
    </div>
  );
}

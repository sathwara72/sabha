"use client";

import { motion } from "framer-motion";
import { 
  Users, Briefcase, Calendar, Star, 
  ArrowUpRight, Clock, ShieldCheck, Zap 
} from "lucide-react";
import { useEffect, useState } from "react";
import { fetchAllBusinessesAdmin, fetchStatistics, fetchUsersAdmin } from "@/lib/api";

export default function AdminDashboard() {
  const [stats, setStats] = useState<any[]>([]);
  const [userCount, setUserCount] = useState(0);
  const [businessCount, setBusinessCount] = useState(0);

  useEffect(() => {
    async function loadData() {
      const [statData, userData, bizData] = await Promise.all([
        fetchStatistics(),
        fetchUsersAdmin(),
        fetchAllBusinessesAdmin()
      ]);
      setStats(statData);
      setUserCount(userData.length);
      setBusinessCount(bizData.length);
    }
    loadData();
  }, []);

  return (
    <div className="space-y-16">
      <div className="flex flex-col gap-6">
        <h1 className="text-4xl md:text-6xl font-black text-white leading-none tracking-tighter uppercase whitespace-nowrap">
           Operations Radar <span className="text-gradient">Control.</span>
        </h1>
        <p className="text-[11px] font-black uppercase tracking-[0.4em] text-white/30 px-2">Real-time status of your elite business ecosystem</p>
      </div>

      {/* Highlights Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div className="glass p-10 rounded-[3rem] border-white/10 group relative transition-all overflow-hidden flex flex-col justify-between">
           <div className="flex items-center justify-between mb-12">
             <div className="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20">
               <Briefcase size={28} />
             </div>
             <span className="text-white/20 font-black text-xs uppercase tracking-widest">+12%</span>
           </div>
           <div>
             <p className="text-5xl font-black text-white mb-2">{businessCount}</p>
             <p className="text-[10px] font-black text-white/40 uppercase tracking-widest uppercase">Registered Entities</p>
           </div>
        </div>

        <div className="glass p-10 rounded-[3rem] border-white/10 group relative transition-all overflow-hidden flex flex-col justify-between">
           <div className="flex items-center justify-between mb-12">
             <div className="w-16 h-16 rounded-2xl bg-accent/10 flex items-center justify-center text-accent border border-accent/20">
               <Users size={28} />
             </div>
             <span className="text-white/20 font-black text-xs uppercase tracking-widest">+8%</span>
           </div>
           <div>
             <p className="text-5xl font-black text-white mb-2">{userCount}</p>
             <p className="text-[10px] font-black text-white/40 uppercase tracking-widest uppercase">Active Professionals</p>
           </div>
        </div>

        <div className="glass p-10 rounded-[3rem] border-white/10 group relative transition-all overflow-hidden flex flex-col justify-between">
           <div className="flex items-center justify-between mb-12">
             <div className="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20">
               <Calendar size={28} />
             </div>
           </div>
           <div>
             <p className="text-5xl font-black text-white mb-2">12</p>
             <p className="text-[10px] font-black text-white/40 uppercase tracking-widest uppercase">Approvals Pending</p>
           </div>
        </div>

         <div className="glass p-10 rounded-[3rem] border-white/10 group relative transition-all overflow-hidden flex flex-col justify-between">
           <div className="flex items-center justify-between mb-12">
             <div className="w-16 h-16 rounded-2xl bg-accent/10 flex items-center justify-center text-accent border border-accent/20">
               <Zap size={28} />
             </div>
           </div>
           <div>
             <p className="text-5xl font-black text-white mb-2">98.2%</p>
             <p className="text-[10px] font-black text-white/40 uppercase tracking-widest uppercase">System Uptime</p>
           </div>
        </div>
      </div>

       <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div className="glass rounded-[4rem] p-12 border-white/5 relative overflow-hidden">
             <div className="absolute top-0 right-0 w-48 h-48 bg-primary/5 rounded-full blur-[80px]" />
             <h3 className="text-2xl font-black mb-10 text-white uppercase tracking-tighter flex items-center gap-4">
                <Star size={24} className="text-accent fill-accent" />
                Strategic Alerts
             </h3>
             <div className="space-y-6">
                {[
                  "New high-revenue entity 'Acme Corp' is awaiting verification.",
                  "System detected 5 spike in user signals on the networking page.",
                  "Backup completed. Operational integrity verified for all 500+ nodes."
                ].map((msg, i) => (
                  <div key={i} className="flex gap-6 p-8 glass border-white/5 rounded-[2.5rem] hover:bg-white/5 transition-all group">
                     <div className="w-12 h-12 bg-white/5 flex items-center justify-center rounded-2xl text-white/30 group-hover:text-primary transition-colors">
                       <ShieldCheck size={20} />
                     </div>
                     <p className="text-sm font-bold text-white/40 group-hover:text-white transition-colors">{msg}</p>
                  </div>
                ))}
             </div>
          </div>

          <div className="glass rounded-[4rem] p-12 border-white/5 relative overflow-hidden">
             <div className="absolute top-0 right-0 w-48 h-48 bg-accent/5 rounded-full blur-[80px]" />
             <h3 className="text-2xl font-black mb-10 text-white uppercase tracking-tighter flex items-center gap-4">
                <Clock size={24} className="text-primary" />
                System Activity
             </h3>
             <div className="space-y-6">
                {[
                  { user: "Ravi S.", action: "Posted New Event", time: "2 min ago" },
                  { user: "Admin", action: "Approved 'DesignFlow'", time: "15 min ago" },
                  { user: "Pooja V.", action: "Updated Profile Signal", time: "1 hour ago" }
                ].map((log, i) => (
                   <div key={i} className="flex items-center justify-between p-8 glass border-white/5 rounded-[2.5rem] hover:bg-white/5 transition-all">
                      <div className="flex flex-col gap-1">
                        <p className="text-sm font-black text-white uppercase tracking-widest">{log.user}</p>
                        <p className="text-[10px] font-black text-white/20 uppercase tracking-widest">{log.action}</p>
                      </div>
                      <p className="text-[10px] font-black text-white/10 uppercase tracking-widest">{log.time}</p>
                   </div>
                ))}
             </div>
          </div>
       </div>
    </div>
  );
}

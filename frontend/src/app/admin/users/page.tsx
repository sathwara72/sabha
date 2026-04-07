"use client";

import { useEffect, useState } from "react";
import { fetchUsersAdmin } from "@/lib/api";
import { 
  Users, Mail, ShieldCheck, 
  Clock, ArrowUpRight, Search, Zap 
} from "lucide-react";
import { motion } from "framer-motion";

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  created_at: string;
}

export default function AdminUsersPage() {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");

  useEffect(() => {
    loadData();
  }, []);

  async function loadData() {
    try {
      const data = await fetchUsersAdmin();
      setUsers(data);
    } catch (error) {
      console.error("Error loading users:", error);
    } finally {
      setLoading(false);
    }
  }

  const filteredUsers = users.filter(u => 
    u.name.toLowerCase().includes(searchTerm.toLowerCase()) || 
    u.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-16">
      <div className="flex flex-col md:flex-row items-end justify-between gap-12">
        <div className="flex flex-col gap-6">
          <h1 className="text-4xl md:text-6xl font-black text-white leading-none tracking-tighter uppercase whitespace-nowrap">
             Community <span className="text-gradient">Nodes.</span>
          </h1>
          <p className="text-[11px] font-black uppercase tracking-[0.4em] text-white/30 px-2 italic-none tracking-tighter">Managing the elite professional collective</p>
        </div>

        <div className="relative w-full md:w-96">
           <Search className="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-white/30" />
           <input 
             type="text" 
             placeholder="Search Node..."
             className="w-full glass border-white/10 rounded-[1.5rem] py-5 pl-16 pr-8 text-white outline-none focus:border-primary transition-all font-black text-[10px] uppercase tracking-widest"
             value={searchTerm}
             onChange={(e) => setSearchTerm(e.target.value)}
           />
        </div>
      </div>

      <div className="glass rounded-[4rem] border-white/5 overflow-hidden">
         <table className="w-full text-left border-collapse">
            <thead>
               <tr className="bg-white/5 border-b border-white/5">
                  <th className="px-12 py-10 font-black text-[10px] uppercase tracking-[0.3em] text-white/40">Node Detail</th>
                  <th className="px-12 py-10 font-black text-[10px] uppercase tracking-[0.3em] text-white/40">Temporal Access (Join Date)</th>
                  <th className="px-12 py-10 font-black text-[10px] uppercase tracking-[0.3em] text-white/40">Classification (Role)</th>
                  <th className="px-12 py-10 font-black text-[10px] uppercase tracking-[0.3em] text-white/40">Action</th>
               </tr>
            </thead>
            <tbody className="divide-y divide-white/5">
               {filteredUsers.map((user) => (
                  <tr key={user.id} className="hover:bg-white/5 transition-colors group">
                     <td className="px-12 py-10">
                        <div className="flex items-center gap-6">
                           <div className="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center font-black text-primary border border-white/5 group-hover:border-primary/20 transition-all">
                              {user.name[0]}
                           </div>
                           <div>
                              <p className="text-lg font-black text-white uppercase tracking-tight italic-none mb-1">{user.name}</p>
                              <p className="text-[10px] font-black text-white/20 uppercase tracking-widest flex items-center gap-2"><Mail size={12} className="text-primary" /> {user.email}</p>
                           </div>
                        </div>
                     </td>
                     <td className="px-12 py-10">
                        <div className="flex flex-col gap-1">
                           <p className="text-sm font-black text-white/40 uppercase tracking-widest italic-none">
                              {new Date(user.created_at).toLocaleDateString()}
                           </p>
                           <p className="text-[10px] text-white/10 uppercase font-black tracking-widest flex items-center gap-2"><Clock size={12} /> {new Date(user.created_at).toLocaleTimeString()}</p>
                        </div>
                     </td>
                     <td className="px-12 py-10">
                        <div className={`inline-flex items-center gap-3 px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest border ${
                           user.role === "admin" 
                           ? "bg-primary/10 text-primary border-primary/20" 
                           : "bg-white/5 text-white/30 border-white/5"
                        }`}>
                           <ShieldCheck size={14} />
                           {user.role}
                        </div>
                     </td>
                     <td className="px-12 py-10">
                        <button className="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white/30 hover:text-primary hover:bg-white/10 transition-all border border-white/5">
                           <ArrowUpRight size={20} />
                        </button>
                     </td>
                  </tr>
               ))}
            </tbody>
         </table>
         {filteredUsers.length === 0 && !loading && (
            <div className="p-40 text-center">
               <p className="text-3xl font-black text-white/10 uppercase tracking-widest italic-none tracking-tighter">No Access Nodes Found.</p>
            </div>
         )}
      </div>

       <div className="mt-16 flex items-center justify-between glass border-white/5 p-12 rounded-[3.5rem] relative overflow-hidden group">
          <div className="absolute top-0 right-0 w-48 h-48 bg-primary/5 rounded-full blur-[80px]" />
          <div className="flex items-center gap-10">
             <div className="w-20 h-20 rounded-3xl bg-accent/20 flex items-center justify-center shrink-0">
                <Zap className="w-10 h-10 text-accent" />
             </div>
             <div>
                <h4 className="text-2xl font-black text-white mb-2 uppercase tracking-tighter italic-none">Collective Integrity</h4>
                <p className="text-white/30 text-lg leading-relaxed font-bold italic-none uppercase tracking-tight max-w-2xl">
                   Managing nodes requires high-level verification. Use the Node detail page to audit specific professional histories and engagement metrics.
                </p>
             </div>
          </div>
          <button className="btn-premium px-12 py-5 text-xs uppercase tracking-widest whitespace-nowrap">Node Audit Tool</button>
       </div>
    </div>
  );
}

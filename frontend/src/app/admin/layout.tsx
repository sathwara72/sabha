"use client";

import Link from "next/link";
import { 
  Users, LayoutDashboard, Briefcase, Calendar, 
  Settings, LogOut, ShieldCheck, Zap 
} from "lucide-react";
import { usePathname } from "next/navigation";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const pathname = usePathname();

  const menuItems = [
    { name: "Overview", icon: LayoutDashboard, href: "/admin" },
    { name: "Businesses", icon: Briefcase, href: "/admin/businesses" },
    { name: "Events", icon: Calendar, href: "/admin/events" },
    { name: "Users", icon: Users, href: "/admin/users" },
  ];

  return (
    <div className="min-h-screen bg-background font-outfit text-white flex">
      {/* Sidebar */}
      <aside className="w-80 border-r border-white/5 bg-[#050505] p-8 flex flex-col fixed h-full z-50">
        <div className="flex items-center gap-4 mb-16">
          <div className="w-12 h-12 bg-premium rounded-2xl flex items-center justify-center">
            <Zap className="w-6 h-6 text-white" />
          </div>
          <span className="text-2xl font-black tracking-tighter uppercase">Sabha Admin</span>
        </div>

        <nav className="space-y-4 flex-1">
          <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] mb-8 ml-4">Management Hub</p>
          {menuItems.map((item) => (
            <Link
              key={item.name}
              href={item.href}
              className={`flex items-center gap-4 px-6 py-5 rounded-[1.5rem] transition-all group ${
                pathname === item.href 
                  ? "bg-primary text-white shadow-lg shadow-primary/20" 
                  : "hover:bg-white/5 text-white/40 hover:text-white"
              }`}
            >
              <item.icon size={20} className={pathname === item.href ? "text-white" : "group-hover:text-primary transition-colors"} />
              <span className="font-black text-xs uppercase tracking-widest">{item.name}</span>
            </Link>
          ))}
        </nav>

        <div className="pt-10 border-t border-white/5">
           <button className="flex items-center gap-4 px-6 py-5 rounded-[1.5rem] hover:bg-white/5 text-white/40 hover:text-white w-full transition-all group">
             <LogOut size={20} className="group-hover:text-primary transition-colors" />
             <span className="font-black text-xs uppercase tracking-widest text-left">Logout Node</span>
           </button>
        </div>
      </aside>

      {/* Content Area */}
      <main className="flex-1 ml-80 p-12">
        <header className="flex items-center justify-between mb-16">
           <div className="flex items-center gap-4 glass border-white/5 px-6 py-3 rounded-full">
              <ShieldCheck size={16} className="text-primary" />
              <span className="text-[10px] font-black uppercase tracking-widest text-white/50 underline underline-offset-4 decoration-primary">Elite Access: Operational Ready</span>
           </div>
           
           <div className="flex items-center gap-6">
              <div className="text-right">
                 <p className="font-black text-xs uppercase tracking-widest">Admin Control</p>
                 <p className="text-[10px] text-white/20 uppercase font-black tracking-widest">admin@sabha.com</p>
              </div>
              <div className="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center font-black text-primary">A</div>
           </div>
        </header>
        
        {children}
      </main>
    </div>
  );
}

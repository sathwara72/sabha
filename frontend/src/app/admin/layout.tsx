"use client";

import Link from "next/link";
import {
  Users, LayoutDashboard, Briefcase, Calendar,
  LogOut, ShieldCheck, Zap, Image, BarChart3, Settings
} from "lucide-react";
import { usePathname, useRouter } from "next/navigation";
import { useAuth } from "@/lib/auth";
import { useEffect } from "react";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const pathname = usePathname();
  const router = useRouter();
  const { user, isReady, logout } = useAuth();

  useEffect(() => {
    if (isReady && (!user || user.role !== "admin")) {
      router.replace("/");
    }
  }, [isReady, user, router]);

  if (!isReady || !user || user.role !== "admin") {
    return (
      <div className="min-h-screen flex items-center justify-center bg-background font-outfit">
        <div className="text-center space-y-3">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="text-sm font-medium text-muted">Verifying authorization...</p>
        </div>
      </div>
    );
  }

  const menuItems = [
    { name: "Overview", icon: LayoutDashboard, href: "/admin" },
    { name: "Businesses", icon: Briefcase, href: "/admin/businesses" },
    { name: "Events", icon: Calendar, href: "/admin/events" },
    { name: "Users", icon: Users, href: "/admin/users" },
    { name: "Gallery", icon: Image, href: "/admin/gallery" },
    { name: "Statistics", icon: BarChart3, href: "/admin/statistics" },
    { name: "Site Settings", icon: Settings, href: "/admin/settings" },
  ];

  const handleLogout = () => {
    logout();
    router.replace("/");
  };

  return (
    <div className="min-h-screen bg-background font-outfit text-foreground flex">
      {/* Sidebar */}
      <aside className="w-72 border-r border-border bg-white p-6 flex flex-col fixed h-full z-50">
        <div className="flex items-center gap-3 mb-10">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white">
            <Zap className="h-5 w-5" />
          </div>
          <span className="text-xl font-bold tracking-tight text-foreground">Sabha Admin</span>
        </div>

        <nav className="space-y-1 flex-1">
          <p className="px-3 mb-3 text-xs font-semibold text-muted-foreground">Menu</p>
          {menuItems.map((item) => {
            const isActive = pathname === item.href;
            return (
              <Link
                key={item.name}
                href={item.href}
                className={`flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-colors ${
                  isActive
                    ? "bg-primary text-white"
                    : "text-muted hover:bg-surface hover:text-foreground"
                }`}
              >
                <item.icon size={18} />
                <span>{item.name}</span>
              </Link>
            );
          })}
        </nav>

        <div className="pt-4 border-t border-border">
          <button
            onClick={handleLogout}
            className="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-muted transition-colors hover:bg-surface hover:text-foreground"
          >
            <LogOut size={18} />
            <span>Log out</span>
          </button>
        </div>
      </aside>

      {/* Content Area */}
      <main className="flex-1 ml-72 p-8 lg:p-10">
        <header className="flex items-center justify-between mb-10">
          <div className="inline-flex items-center gap-2 rounded-full bg-primary-soft px-3.5 py-1.5 text-sm font-medium text-primary">
            <ShieldCheck size={15} />
            <span>Admin</span>
          </div>

          <div className="flex items-center gap-3">
            <div className="text-right">
              <p className="text-sm font-semibold text-foreground">{user.name}</p>
              <p className="text-xs text-muted">{user.email}</p>
            </div>
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-soft font-semibold text-primary">
              {user.name?.[0] ?? "A"}
            </div>
          </div>
        </header>

        {children}
      </main>
    </div>
  );
}

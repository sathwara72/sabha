"use client";

import Link from "next/link";
import {
  Users, LayoutDashboard, Briefcase, Calendar,
  LogOut, ShieldCheck, Zap, Image, Sliders, BarChart3, Settings, Menu, X, Tag
} from "lucide-react";
import { usePathname, useRouter } from "next/navigation";
import { useAuth } from "@/lib/auth";
import { useEffect, useState } from "react";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const pathname = usePathname();
  const router = useRouter();
  const { user, isReady, logout } = useAuth();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  useEffect(() => {
    if (isReady && (!user || user.role !== "admin")) {
      router.replace("/");
    }
  }, [isReady, user, router]);

  // Close sidebar on route change (mobile)
  useEffect(() => {
    setSidebarOpen(false);
  }, [pathname]);

  // Lock body scroll when mobile drawer is open
  useEffect(() => {
    if (sidebarOpen) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
    return () => { document.body.style.overflow = ""; };
  }, [sidebarOpen]);

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
    { name: "Bookings", icon: ShieldCheck, href: "/admin/bookings" },
    { name: "Users", icon: Users, href: "/admin/users" },
    { name: "Gallery", icon: Image, href: "/admin/gallery" },
    { name: "Hero Slider", icon: Sliders, href: "/admin/hero-slider" },
    { name: "Categories", icon: Tag, href: "/admin/categories" },
    { name: "Statistics", icon: BarChart3, href: "/admin/statistics" },
    { name: "Site Settings", icon: Settings, href: "/admin/settings" },
  ];

  const handleLogout = () => {
    logout();
    router.replace("/");
  };

  const SidebarContent = () => (
    <div className="flex flex-col h-full">
      {/* Logo */}
      <div className="flex items-center justify-between gap-3 mb-8 px-1">
        <div className="flex items-center gap-3">
          <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-white shrink-0">
            <Zap className="h-4 w-4" />
          </div>
          <span className="text-lg font-bold tracking-tight text-foreground">Sabha Admin</span>
        </div>
        {/* Close button — mobile only */}
        <button
          onClick={() => setSidebarOpen(false)}
          className="lg:hidden rounded-lg p-1.5 text-muted-foreground hover:bg-surface transition-colors"
        >
          <X size={18} />
        </button>
      </div>

      {/* Nav */}
      <nav className="space-y-0.5 flex-1">
        <p className="px-3 mb-2 text-[10px] font-bold text-muted-foreground uppercase tracking-wider">Menu</p>
        {menuItems.map((item) => {
          const isActive = pathname === item.href;
          return (
            <Link
              key={item.name}
              href={item.href}
              className={`flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-colors ${
                isActive
                  ? "bg-primary text-white"
                  : "text-muted hover:bg-surface hover:text-foreground"
              }`}
            >
              <item.icon size={16} />
              <span>{item.name}</span>
            </Link>
          );
        })}
      </nav>

      {/* Logout */}
      <div className="pt-3 border-t border-border">
        <button
          onClick={handleLogout}
          className="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-muted transition-colors hover:bg-surface hover:text-foreground"
        >
          <LogOut size={16} />
          <span>Log out</span>
        </button>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-background font-outfit text-foreground flex">

      {/* ── Desktop Sidebar ── */}
      <aside className="hidden lg:flex w-64 border-r border-border bg-white p-5 flex-col fixed h-full z-50">
        <SidebarContent />
      </aside>

      {/* ── Mobile Drawer Backdrop ── */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* ── Mobile Drawer ── */}
      <aside
        className={`fixed top-0 left-0 h-full w-72 bg-white border-r border-border p-5 z-50 flex flex-col transition-transform duration-300 lg:hidden ${
          sidebarOpen ? "translate-x-0" : "-translate-x-full"
        }`}
      >
        <SidebarContent />
      </aside>

      {/* ── Content Area ── */}
      <main className="flex-1 lg:ml-64 min-h-screen flex flex-col bg-background">
        {/* Sticky Header */}
        <header className="sticky top-0 z-40 flex items-center justify-between px-4 py-3 lg:px-6 border-b border-border bg-white/90 backdrop-blur-md">
          <div className="flex items-center gap-3">
            {/* Hamburger — mobile only */}
            <button
              onClick={() => setSidebarOpen(true)}
              className="lg:hidden inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-white text-muted-foreground hover:bg-surface transition-colors"
            >
              <Menu size={16} />
            </button>

            <div className="inline-flex items-center gap-1.5 rounded-full bg-primary-soft px-3 py-1 text-xs font-semibold text-primary">
              <ShieldCheck size={13} />
              <span>Admin</span>
            </div>
          </div>

          <div className="flex items-center gap-2.5">
            <div className="text-right hidden sm:block">
              <p className="text-xs font-semibold text-foreground leading-tight">{user.name}</p>
              <p className="text-[10px] text-muted">{user.email}</p>
            </div>
            <div className="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-soft text-xs font-bold text-primary">
              {user.name?.[0] ?? "A"}
            </div>
          </div>
        </header>

        {/* Page Content */}
        <div className="flex-1 p-4 lg:p-5">
          {children}
        </div>
      </main>
    </div>
  );
}

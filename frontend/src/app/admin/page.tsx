"use client";

import { motion } from "framer-motion";
import {
  Users, Briefcase, Calendar, Star,
  Clock, ShieldCheck, Zap
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

  const tiles = [
    { icon: Briefcase, value: businessCount, label: "Businesses", trend: "+12%", soft: "bg-primary-soft text-primary" },
    { icon: Users, value: userCount, label: "Members", trend: "+8%", soft: "bg-sky-50 text-accent" },
    { icon: Calendar, value: 12, label: "Pending approvals", trend: "", soft: "bg-primary-soft text-primary" },
    { icon: Zap, value: "98.2%", label: "Uptime", trend: "", soft: "bg-sky-50 text-accent" },
  ];

  return (
    <div className="space-y-10">
      <div className="flex flex-col gap-2">
        <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Dashboard</h1>
        <p className="text-sm text-muted">Overview of your community</p>
      </div>

      {/* Highlights Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {tiles.map((tile, i) => (
          <div key={i} className="glass-card p-6 flex flex-col gap-6">
            <div className="flex items-center justify-between">
              <div className={`flex h-12 w-12 items-center justify-center rounded-xl ${tile.soft}`}>
                <tile.icon size={22} />
              </div>
              {tile.trend && (
                <span className="text-sm font-medium text-muted">{tile.trend}</span>
              )}
            </div>
            <div>
              <p className="text-3xl font-bold text-foreground">{tile.value}</p>
              <p className="mt-1 text-sm font-medium text-muted">{tile.label}</p>
            </div>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="glass-card p-6">
          <h3 className="mb-6 flex items-center gap-2.5 text-lg font-semibold text-foreground">
            <Star size={20} className="text-accent" />
            Alerts
          </h3>
          <div className="space-y-3">
            {[
              "New business 'Acme Corp' is awaiting verification.",
              "Activity spiked on the networking page.",
              "Backup completed. All 500+ members verified.",
            ].map((msg, i) => (
              <div key={i} className="flex items-center gap-4 rounded-xl bg-surface p-4 transition-colors hover:bg-primary-soft">
                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                  <ShieldCheck size={18} />
                </div>
                <p className="text-sm font-medium text-foreground">{msg}</p>
              </div>
            ))}
          </div>
        </div>

        <div className="glass-card p-6">
          <h3 className="mb-6 flex items-center gap-2.5 text-lg font-semibold text-foreground">
            <Clock size={20} className="text-primary" />
            Recent activity
          </h3>
          <div className="space-y-3">
            {[
              { user: "Ravi S.", action: "Posted a new event", time: "2 min ago" },
              { user: "Admin", action: "Approved 'DesignFlow'", time: "15 min ago" },
              { user: "Pooja V.", action: "Updated profile", time: "1 hour ago" },
            ].map((log, i) => (
              <div key={i} className="flex items-center justify-between rounded-xl bg-surface p-4">
                <div className="flex flex-col gap-0.5">
                  <p className="text-sm font-semibold text-foreground">{log.user}</p>
                  <p className="text-sm text-muted">{log.action}</p>
                </div>
                <p className="text-sm text-muted-foreground">{log.time}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

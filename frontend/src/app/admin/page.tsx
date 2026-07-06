"use client";

import { motion } from "framer-motion";
import {
  Users, Briefcase, Calendar, Star,
  Clock, ShieldCheck, Zap
} from "lucide-react";
import { useEffect, useState } from "react";
import { fetchAllBusinessesAdmin, fetchStatistics, fetchUsersAdmin, getAllEventRegistrations } from "@/lib/api";

export default function AdminDashboard() {
  const [stats, setStats] = useState<any[]>([]);
  const [userCount, setUserCount] = useState(0);
  const [businessCount, setBusinessCount] = useState(0);
  const [pendingApprovals, setPendingApprovals] = useState(0);
  const [alerts, setAlerts] = useState<string[]>([]);
  const [recentActivities, setRecentActivities] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadData() {
      try {
        setLoading(true);
        const [statData, userData, bizData, regData] = await Promise.all([
          fetchStatistics(),
          fetchUsersAdmin(),
          fetchAllBusinessesAdmin(),
          getAllEventRegistrations()
        ]);
        setStats(statData);
        setUserCount(userData.length);
        setBusinessCount(bizData.length);
        
        const pendingBiz = bizData.filter((b: any) => b.status === "pending");
        const pendingReg = regData.filter((r: any) => r.status === "pending");
        setPendingApprovals(pendingBiz.length + pendingReg.length);
        
        // Build dynamic alerts
        const dynamicAlerts: string[] = [];
        pendingBiz.forEach((b: any) => {
          dynamicAlerts.push(`New business '${b.name}' is awaiting verification.`);
        });
        pendingReg.slice(0, 3).forEach((r: any) => {
          const uName = r.user?.name || "A member";
          const eTitle = r.event?.title || "an event";
          dynamicAlerts.push(`${uName} requested a seat for '${eTitle}'.`);
        });
        
        if (dynamicAlerts.length === 0) {
          dynamicAlerts.push("All business listings are verified.");
          dynamicAlerts.push("All event seat reservations are approved.");
          dynamicAlerts.push("No outstanding admin actions pending.");
        }
        setAlerts(dynamicAlerts);
        
        // Build recent activities list
        const activities: any[] = [];
        
        // Add businesses
        bizData.forEach((b: any) => {
          activities.push({
            user: b.user?.name || b.name,
            action: b.status === "approved" ? `Approved business '${b.name}'` : `Submitted business '${b.name}'`,
            time: new Date(b.updated_at || b.created_at)
          });
        });
        
        // Add registrations
        regData.forEach((r: any) => {
          activities.push({
            user: r.user?.name || "Member",
            action: r.status === "approved" ? `Approved booking for '${r.event?.title}'` : `Requested booking for '${r.event?.title}'`,
            time: new Date(r.updated_at || r.created_at)
          });
        });
        
        // Sort and select top 3
        activities.sort((a, b) => b.time.getTime() - a.time.getTime());
        
        const formattedActivities = activities.slice(0, 3).map((act: any) => {
          const diffMs = Date.now() - act.time.getTime();
          const diffMins = Math.floor(diffMs / 60000);
          let timeStr = "Just now";
          if (diffMins > 0 && diffMins < 60) {
            timeStr = `${diffMins} min ago`;
          } else if (diffMins >= 60) {
            const diffHours = Math.floor(diffMins / 60);
            timeStr = diffHours === 1 ? "1 hour ago" : `${diffHours} hours ago`;
          }
          return {
            user: act.user,
            action: act.action,
            time: timeStr
          };
        });
        
        if (formattedActivities.length === 0) {
          formattedActivities.push({ user: "System", action: "Dashboard initialized", time: "Just now" });
        }
        
        setRecentActivities(formattedActivities);
      } catch (error) {
        console.error("Error loading live dashboard metrics:", error);
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, []);

  const tiles = [
    { icon: Briefcase, value: businessCount, label: "Businesses", trend: "", soft: "bg-primary-soft text-primary" },
    { icon: Users, value: userCount, label: "Members", trend: "", soft: "bg-sky-50 text-accent" },
    { icon: Calendar, value: pendingApprovals, label: "Pending approvals", trend: "", soft: "bg-primary-soft text-primary" },
    { icon: Zap, value: "98.2%", label: "Uptime", trend: "", soft: "bg-sky-50 text-accent" },
  ];

  return (
    <div className="space-y-10">
      <div className="flex flex-col gap-2">
        <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Dashboard</h1>
        <p className="text-sm text-muted">Overview of your community</p>
      </div>

      {loading ? (
        <div className="py-20 text-center">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="mt-3 text-sm text-muted">Loading metrics...</p>
        </div>
      ) : (
        <>
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
                {alerts.map((msg, i) => (
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
                {recentActivities.map((log, i) => (
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
        </>
      )}
    </div>
  );
}

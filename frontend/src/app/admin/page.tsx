"use client";

import { motion } from "framer-motion";
import {
  Users, Briefcase, Calendar, Clock, TrendingUp, Filter, ChevronDown
} from "lucide-react";
import { useEffect, useState, useMemo } from "react";
import { fetchAllBusinessesAdmin, fetchEvents, fetchStatistics, fetchUsersAdmin, getAllEventRegistrations } from "@/lib/api";

export default function AdminDashboard() {
  const [stats, setStats] = useState<any[]>([]);
  const [userCount, setUserCount] = useState(0);
  const [businessCount, setBusinessCount] = useState(0);
  const [eventCount, setEventCount] = useState(0);
  const [recentActivities, setRecentActivities] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  // Registration chart data
  const [rawRegistrations, setRawRegistrations] = useState<{ date: Date; type: string }[]>([]);
  const [selectedYear, setSelectedYear] = useState<string>("2026");
  const [hoveredBarIndex, setHoveredBarIndex] = useState<number | null>(null);
  const [calendarOpen, setCalendarOpen] = useState(false);
  const [customStartDate, setCustomStartDate] = useState<string>("");
  const [customEndDate, setCustomEndDate] = useState<string>("");

  useEffect(() => {
    async function loadData() {
      try {
        setLoading(true);
        const [statData, userData, bizData, regData, eventData] = await Promise.all([
          fetchStatistics(),
          fetchUsersAdmin(),
          fetchAllBusinessesAdmin(),
          getAllEventRegistrations(),
          fetchEvents()
        ]);
        setStats(statData);
        setUserCount(userData.length);
        setBusinessCount(bizData.length);
        setEventCount(eventData.length);

        // Collect ONLY business registration events for graph analytics
        const allRegs: { date: Date; type: string }[] = [];
        bizData.forEach((b: any) => {
          if (b.created_at || b.updated_at) {
            allRegs.push({ date: new Date(b.created_at || b.updated_at), type: "business" });
          }
        });
        setRawRegistrations(allRegs);

        // Build recent activities list (only new business registrations and new event registrations)
        const activities: any[] = [];

        // 1. New business registrations
        bizData.forEach((b: any) => {
          activities.push({
            user: b.user?.name || b.name,
            action: `Registered business '${b.name}'`,
            time: new Date(b.created_at || b.updated_at || Date.now())
          });
        });

        // 2. New event registrations
        regData.forEach((r: any) => {
          activities.push({
            user: r.user?.name || "Member",
            action: `Registered for event '${r.event?.title || "Event"}'`,
            time: new Date(r.created_at || r.updated_at || Date.now())
          });
        });

        // Sort by newest first
        activities.sort((a, b) => b.time.getTime() - a.time.getTime());

        // Take top 2 items only
        const formattedActivities = activities.slice(0, 2).map((act: any) => {
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

  // Compute available years from dataset (2020 through current year + data years)
  const availableYears = useMemo(() => {
    const currentYr = new Date().getFullYear();
    const yearsSet = new Set<number>();
    for (let y = currentYr; y >= 2020; y--) {
      yearsSet.add(y);
    }
    rawRegistrations.forEach((r) => {
      const yr = r.date.getFullYear();
      if (!isNaN(yr) && yr >= 2015) {
        yearsSet.add(yr);
      }
    });
    return Array.from(yearsSet).sort((a, b) => b - a).map(String);
  }, [rawRegistrations]);

  // Compute monthly graph counts
  const chartData = useMemo(() => {
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    
    if (selectedYear === "custom" && customStartDate && customEndDate) {
      const start = new Date(customStartDate);
      const end = new Date(customEndDate);
      const result = [];
      const curr = new Date(start.getFullYear(), start.getMonth(), 1);
      const last = new Date(end.getFullYear(), end.getMonth(), 1);
      
      while (curr <= last) {
        const mIdx = curr.getMonth();
        const yr = curr.getFullYear();
        const mName = `${months[mIdx]} ${yr.toString().slice(-2)}`;
        
        const count = rawRegistrations.filter((r) => {
          return r.date >= start && r.date <= end && r.date.getMonth() === mIdx && r.date.getFullYear() === yr;
        }).length;
        
        result.push({ month: mName, count, fullDate: `${months[mIdx]} ${yr}` });
        curr.setMonth(curr.getMonth() + 1);
      }
      return result.length > 0 ? result : months.map((mName) => ({ month: mName, count: 0, fullDate: mName }));
    }

    if (selectedYear === "all") {
      return months.map((mName, mIdx) => {
        const count = rawRegistrations.filter((r) => {
          return r.date.getMonth() === mIdx;
        }).length;
        return { month: mName, count, fullDate: `${mName} (All Time)` };
      });
    }

    if (selectedYear === "last12") {
      const result = [];
      const now = new Date();
      for (let i = 11; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const mIdx = d.getMonth();
        const yr = d.getFullYear();
        const mName = `${months[mIdx]} ${yr.toString().slice(-2)}`;
        
        const count = rawRegistrations.filter((r) => {
          return r.date.getMonth() === mIdx && r.date.getFullYear() === yr;
        }).length;
        
        result.push({ month: mName, count, fullDate: `${months[mIdx]} ${yr}` });
      }
      return result;
    }

    const yrNum = parseInt(selectedYear) || new Date().getFullYear();
    return months.map((mName, mIdx) => {
      const count = rawRegistrations.filter((r) => {
        return r.date.getMonth() === mIdx && r.date.getFullYear() === yrNum;
      }).length;
      return { month: mName, count, fullDate: `${mName} ${yrNum}` };
    });
  }, [rawRegistrations, selectedYear, customStartDate, customEndDate]);

  const maxCount = useMemo(() => {
    const max = Math.max(...chartData.map((d) => d.count), 1);
    return Math.ceil(max * 1.2);
  }, [chartData]);

  const totalPeriodRegistrations = useMemo(() => {
    return chartData.reduce((acc, d) => acc + d.count, 0);
  }, [chartData]);

  const peakMonth = useMemo(() => {
    let peak = chartData[0] || { month: "N/A", count: 0 };
    chartData.forEach((d) => {
      if (d.count > peak.count) peak = d;
    });
    return peak;
  }, [chartData]);

  const tiles = [
    { icon: Briefcase, value: businessCount, label: "Businesses", trend: "", soft: "bg-primary-soft text-primary" },
    { icon: Users, value: userCount, label: "Members", trend: "", soft: "bg-sky-50 text-accent" },
    { icon: Calendar, value: eventCount, label: "Total Events", trend: "", soft: "bg-primary-soft text-primary" },
  ];

  return (
    <div className="space-y-4">
      

      {loading ? (
        <div className="py-20 text-center">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="mt-3 text-sm text-muted">Loading metrics...</p>
        </div>
      ) : (
        <>
          {/* Highlights Grid */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
            {tiles.map((tile, i) => (
              <div key={i} className="glass-card p-4 flex items-center gap-3">
                <div className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ${tile.soft}`}>
                  <tile.icon size={20} />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-xl font-bold text-foreground leading-none">{tile.value}</p>
                  <p className="mt-1 text-xs font-semibold text-muted truncate">{tile.label}</p>
                </div>
              </div>
            ))}
          </div>

          {/* Recent Activity (Single Line Format) */}
          {/* <div className="glass-card p-4 sm:p-5">
            <h3 className="mb-3 flex items-center gap-2 text-base font-bold text-foreground">
              <Clock size={18} className="text-primary" />
              Recent activity
            </h3>
            <div className="space-y-2">
              {recentActivities.map((log, i) => (
                <div key={i} className="flex items-center justify-between gap-3 rounded-xl bg-surface p-3 px-4 transition-colors hover:bg-primary-soft/40">
                  <div className="flex items-center gap-2 min-w-0 flex-1 truncate">
                    <span className="text-xs sm:text-sm font-bold text-foreground shrink-0">{log.user}</span>
                    <span className="text-xs text-muted font-semibold truncate">• {log.action}</span>
                  </div>
                  <span className="text-xs font-semibold text-muted-foreground shrink-0">{log.time}</span>
                </div>
              ))}
            </div>
          </div> */}

          {/* Registration Analytics Chart */}
          <div className="glass-card p-5 space-y-4">
            {/* Header with Title & Year Filter */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-border pb-3">
              <div className="flex items-center gap-2.5">
                <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-soft text-primary shrink-0">
                  <TrendingUp size={18} />
                </div>
                <div>
                  <h3 className="text-base font-bold text-foreground leading-tight">Business Registration Analytics</h3>
                  <p className="text-xs text-muted font-medium">Monthly business registrations over time</p>
                </div>
              </div>

              {/* Calendar Filter Popover */}
              <div className="relative self-start sm:self-auto">
                <button
                  type="button"
                  onClick={() => setCalendarOpen(!calendarOpen)}
                  className="flex items-center gap-2 rounded-xl border border-border bg-white py-2 px-3.5 text-xs font-bold text-foreground shadow-xs hover:border-primary focus:border-primary cursor-pointer transition-colors"
                >
                  <Calendar size={16} className="text-primary" />
                  <span>
                    {selectedYear === "last12"
                      ? "Last 12 Months"
                      : selectedYear === "all"
                      ? "All Time"
                      : selectedYear === "custom"
                      ? `${customStartDate || "Start"} to ${customEndDate || "End"}`
                      : `Year ${selectedYear}`}
                  </span>
                  <ChevronDown size={14} className="text-muted-foreground ml-1" />
                </button>

                {calendarOpen && (
                  <>
                    <div
                      className="fixed inset-0 z-20"
                      onClick={() => setCalendarOpen(false)}
                    />
                    <div className="absolute right-0 mt-2 z-30 w-72 sm:w-80 rounded-2xl border border-border bg-white p-4 shadow-xl space-y-3.5">
                      <div className="flex items-center justify-between border-b border-border pb-2">
                        <span className="text-xs font-bold text-foreground flex items-center gap-1.5">
                          <Calendar size={15} className="text-primary" /> Calendar & Date Filter
                        </span>
                        <button
                          type="button"
                          onClick={() => setCalendarOpen(false)}
                          className="text-xs font-bold text-muted-foreground hover:text-foreground cursor-pointer px-1"
                        >
                          ✕
                        </button>
                      </div>

                      {/* Presets */}
                      <div className="space-y-1">
                        <p className="text-[10px] font-bold uppercase tracking-wider text-muted-foreground mb-1">
                          Quick Presets
                        </p>
                        <div className="grid grid-cols-2 gap-1.5">
                          <button
                            type="button"
                            onClick={() => {
                              setSelectedYear("last12");
                              setCalendarOpen(false);
                            }}
                            className={`rounded-xl px-2.5 py-1.5 text-xs font-bold transition-colors cursor-pointer text-left ${
                              selectedYear === "last12"
                                ? "bg-primary text-white"
                                : "bg-surface hover:bg-primary-soft text-foreground"
                            }`}
                          >
                            🗓️ Last 12 Months
                          </button>
                          <button
                            type="button"
                            onClick={() => {
                              setSelectedYear("all");
                              setCalendarOpen(false);
                            }}
                            className={`rounded-xl px-2.5 py-1.5 text-xs font-bold transition-colors cursor-pointer text-left ${
                              selectedYear === "all"
                                ? "bg-primary text-white"
                                : "bg-surface hover:bg-primary-soft text-foreground"
                            }`}
                          >
                            🌐 All Time
                          </button>
                        </div>
                      </div>

                      {/* Select Year Grid (2020 through 2026+) */}
                      <div className="space-y-1">
                        <p className="text-[10px] font-bold uppercase tracking-wider text-muted-foreground mb-1">
                          Select Year
                        </p>
                        <div className="grid grid-cols-3 gap-1.5">
                          {availableYears.map((yr) => (
                            <button
                              key={yr}
                              type="button"
                              onClick={() => {
                                setSelectedYear(yr);
                                setCalendarOpen(false);
                              }}
                              className={`rounded-xl py-1.5 px-2 text-xs font-bold text-center transition-colors cursor-pointer ${
                                selectedYear === yr
                                  ? "bg-primary text-white"
                                  : "bg-surface hover:bg-primary-soft text-foreground"
                              }`}
                            >
                              Year {yr}
                            </button>
                          ))}
                        </div>
                      </div>

                      {/* Custom Calendar Date Inputs */}
                      <div className="space-y-1.5 pt-2 border-t border-border">
                        <p className="text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                          Custom Date Range
                        </p>
                        <div className="grid grid-cols-2 gap-2 text-xs">
                          <div>
                            <label className="block text-[9px] font-semibold text-muted mb-0.5">From Date</label>
                            <input
                              type="date"
                              value={customStartDate}
                              onChange={(e) => setCustomStartDate(e.target.value)}
                              className="w-full rounded-xl border border-border bg-white p-1.5 text-xs font-medium text-foreground outline-none focus:border-primary"
                            />
                          </div>
                          <div>
                            <label className="block text-[9px] font-semibold text-muted mb-0.5">To Date</label>
                            <input
                              type="date"
                              value={customEndDate}
                              onChange={(e) => setCustomEndDate(e.target.value)}
                              className="w-full rounded-xl border border-border bg-white p-1.5 text-xs font-medium text-foreground outline-none focus:border-primary"
                            />
                          </div>
                        </div>
                        {customStartDate && customEndDate && (
                          <button
                            type="button"
                            onClick={() => {
                              setSelectedYear("custom");
                              setCalendarOpen(false);
                            }}
                            className="w-full mt-1.5 rounded-xl bg-primary py-1.5 text-xs font-bold text-white shadow-xs hover:opacity-90 transition-opacity cursor-pointer"
                          >
                            Apply Custom Range
                          </button>
                        )}
                      </div>
                    </div>
                  </>
                )}
              </div>
            </div>

            {/* Quick Metrics Bar */}
            <div className="grid grid-cols-3 gap-2 bg-surface p-3 rounded-xl">
              <div>
                <p className="text-[10px] font-bold text-muted uppercase tracking-wider">Total Businesses</p>
                <p className="text-lg font-black text-primary leading-tight mt-0.5">{totalPeriodRegistrations}</p>
              </div>
              <div>
                <p className="text-[10px] font-bold text-muted uppercase tracking-wider">Peak Month</p>
                <p className="text-xs font-bold text-foreground leading-tight mt-1 truncate">
                  {peakMonth.month} ({peakMonth.count})
                </p>
              </div>
              <div>
                <p className="text-[10px] font-bold text-muted uppercase tracking-wider">Monthly Avg</p>
                <p className="text-xs font-bold text-foreground leading-tight mt-1">
                  {(totalPeriodRegistrations / (chartData.length || 1)).toFixed(1)}
                </p>
              </div>
            </div>

            {/* Visual Bar Chart */}
            <div className="pt-2">
              <div className="relative h-48 w-full flex items-end gap-1.5 sm:gap-3 pt-6 pb-6 px-2 border-b border-border">
                {/* Background Grid Lines */}
                <div className="absolute inset-x-0 top-0 bottom-6 flex flex-col justify-between pointer-events-none opacity-40">
                  <div className="border-b border-dashed border-border w-full flex justify-end pr-1 text-[9px] text-muted-foreground font-semibold">{maxCount}</div>
                  <div className="border-b border-dashed border-border w-full flex justify-end pr-1 text-[9px] text-muted-foreground font-semibold">{Math.round(maxCount / 2)}</div>
                  <div className="border-b border-dashed border-border w-full flex justify-end pr-1 text-[9px] text-muted-foreground font-semibold">0</div>
                </div>

                {/* Bars */}
                {chartData.map((d, idx) => {
                  const pct = Math.max((d.count / maxCount) * 100, 4);
                  const isHovered = hoveredBarIndex === idx;
                  return (
                    <div
                      key={idx}
                      onMouseEnter={() => setHoveredBarIndex(idx)}
                      onMouseLeave={() => setHoveredBarIndex(null)}
                      className="relative flex-1 flex flex-col items-center justify-end h-full group cursor-pointer z-10"
                    >
                      {/* Tooltip on Hover */}
                      {isHovered && (
                        <motion.div
                          initial={{ opacity: 0, y: 5 }}
                          animate={{ opacity: 1, y: 0 }}
                          className="absolute -top-10 z-30 rounded-lg bg-slate-900 px-2.5 py-1 text-[10px] font-bold text-white shadow-md whitespace-nowrap pointer-events-none"
                        >
                          {d.fullDate}: <span className="text-amber-400">{d.count}</span>
                        </motion.div>
                      )}

                      {/* Bar Value Count above bar */}
                      <span className={`text-[10px] font-bold transition-colors mb-1 ${d.count > 0 ? "text-primary" : "text-muted-foreground/50"}`}>
                        {d.count}
                      </span>

                      {/* Bar Container */}
                      <motion.div
                        initial={{ height: 0 }}
                        animate={{ height: `${pct}%` }}
                        transition={{ duration: 0.5, delay: idx * 0.03 }}
                        className={`w-full max-w-[28px] sm:max-w-[36px] rounded-t-lg transition-all ${
                          isHovered
                            ? "bg-gradient-to-t from-primary to-accent shadow-md scale-105"
                            : d.count > 0
                            ? "bg-gradient-to-t from-primary/80 to-primary"
                            : "bg-slate-200/60"
                        }`}
                      />
                    </div>
                  );
                })}
              </div>

              {/* Month X-Axis Labels */}
              <div className="flex items-center gap-1.5 sm:gap-3 px-2 pt-2">
                {chartData.map((d, idx) => (
                  <div key={idx} className="flex-1 text-center">
                    <span className="text-[10px] sm:text-xs font-bold text-muted truncate block">
                      {d.month}
                    </span>
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



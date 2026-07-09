"use client";

import { useEffect, useState } from "react";
import { fetchStatistics, updateStatistic } from "@/lib/api";
import {
  BarChart3, Save, CheckCircle2, AlertCircle,
  RefreshCw, Layers
} from "lucide-react";

interface StatisticItem {
  id: number;
  label: string;
  value: string;
}

export default function AdminStatisticsPage() {
  const [stats, setStats] = useState<StatisticItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [updatingId, setUpdatingId] = useState<number | null>(null);
  const [successMsg, setSuccessMsg] = useState("");
  const [errorMsg, setErrorMsg] = useState("");
  const [editValues, setEditValues] = useState<Record<number, { label: string; value: string }>>({});

  useEffect(() => { loadData(); }, []);

  async function loadData() {
    try {
      setLoading(true);
      const data = await fetchStatistics();
      setStats(data || []);
      const initialEdits: Record<number, { label: string; value: string }> = {};
      data.forEach((item: StatisticItem) => {
        initialEdits[item.id] = { label: item.label, value: item.value };
      });
      setEditValues(initialEdits);
    } catch (err) {
      console.error("Error loading statistics:", err);
    } finally {
      setLoading(false);
    }
  }

  const handleInputChange = (id: number, field: "label" | "value", text: string) => {
    setEditValues((prev) => ({ ...prev, [id]: { ...prev[id], [field]: text } }));
  };

  const handleUpdate = async (id: number) => {
    const dataToUpdate = editValues[id];
    if (!dataToUpdate?.label || !dataToUpdate?.value) {
      setErrorMsg("Label and value cannot be empty.");
      return;
    }
    setUpdatingId(id);
    setErrorMsg("");
    setSuccessMsg("");
    try {
      await updateStatistic(id, dataToUpdate);
      setSuccessMsg(`"${dataToUpdate.label}" updated successfully!`);
      await loadData();
      setTimeout(() => setSuccessMsg(""), 3000);
    } catch (err: any) {
      setErrorMsg(err.message || "Failed to update statistic.");
    } finally {
      setUpdatingId(null);
    }
  };

  const inputClass = "w-full rounded-lg border border-border bg-white px-3 py-1.5 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold";

  return (
    <div className="space-y-3">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div className="flex flex-col">
          <h1 className="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Website statistics</h1>
          <p className="text-xs text-muted">Manage numbers and labels displayed across the website</p>
        </div>
        <button
          onClick={loadData}
          className="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-border bg-white text-muted hover:bg-surface hover:text-foreground cursor-pointer transition-colors self-start sm:self-auto"
          title="Refresh Data"
        >
          <RefreshCw size={14} />
        </button>
      </div>

      {/* Alerts */}
      {successMsg && (
        <div className="rounded-xl bg-emerald-50 border border-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-800 flex items-center gap-2">
          <CheckCircle2 className="h-4 w-4 text-emerald-600 shrink-0" />
          <span>{successMsg}</span>
        </div>
      )}
      {errorMsg && (
        <div className="rounded-xl bg-red-50 border border-red-100 px-3 py-2 text-xs font-semibold text-red-800 flex items-center gap-2">
          <AlertCircle className="h-4 w-4 text-red-600 shrink-0" />
          <span>{errorMsg}</span>
        </div>
      )}

      {/* Content */}
      {loading ? (
        <div className="py-20 text-center">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="mt-3 text-sm text-muted">Loading website stats...</p>
        </div>
      ) : stats.length === 0 ? (
        <div className="glass-card py-20 text-center text-muted border border-dashed border-border rounded-xl text-xs">
          No statistics found. Run the database seeder to populate default values.
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
          {stats.map((stat) => {
            const currentEdit = editValues[stat.id] || { label: "", value: "" };
            const isUpdating = updatingId === stat.id;

            return (
              <div key={stat.id} className="glass-card p-4 flex flex-col gap-3">
                {/* Card header */}
                <div className="flex items-center justify-between">
                  <span className="flex items-center gap-1 text-[10px] font-bold text-primary bg-primary-soft px-2 py-0.5 rounded-md">
                    <Layers size={10} /> Stat #{stat.id}
                  </span>
                  <span className="text-[10px] text-muted-foreground font-semibold truncate max-w-[140px]">
                    Live: <span className="text-foreground">{stat.value}</span> — {stat.label}
                  </span>
                </div>

                {/* Inputs row */}
                <div className="grid grid-cols-2 gap-2">
                  <div className="space-y-0.5">
                    <label className="text-[10px] font-bold text-muted uppercase tracking-wider block">Label</label>
                    <input
                      type="text"
                      value={currentEdit.label}
                      onChange={(e) => handleInputChange(stat.id, "label", e.target.value)}
                      className={inputClass}
                      placeholder="e.g. Active Members"
                    />
                  </div>
                  <div className="space-y-0.5">
                    <label className="text-[10px] font-bold text-muted uppercase tracking-wider block">Value</label>
                    <input
                      type="text"
                      value={currentEdit.value}
                      onChange={(e) => handleInputChange(stat.id, "value", e.target.value)}
                      className={inputClass}
                      placeholder="e.g. 500+"
                    />
                  </div>
                </div>

                {/* Save */}
                <div className="flex justify-end">
                  <button
                    onClick={() => handleUpdate(stat.id)}
                    disabled={isUpdating}
                    className="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-[11px] font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                  >
                    <Save size={12} />
                    {isUpdating ? "Saving..." : "Save"}
                  </button>
                </div>
              </div>
            );
          })}
        </div>
      )}

      {/* Info card */}
      {/* <div className="glass-card p-4 flex items-center gap-3">
        <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
          <BarChart3 className="h-4 w-4" />
        </div>
        <div>
          <h4 className="text-xs font-bold text-foreground mb-0.5">About dynamic stats</h4>
          <p className="text-[11px] leading-relaxed text-muted-foreground">
            These values control the hero grids on the homepage, about us sections, and gallery. Keep labels punchy and values accurate.
          </p>
        </div>
      </div> */}
    </div>
  );
}

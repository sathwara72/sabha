"use client";

import { useEffect, useState } from "react";
import { fetchStatistics, updateStatistic } from "@/lib/api";
import {
  BarChart3, Save, CheckCircle2, AlertCircle,
  HelpCircle, RefreshCw, Layers
} from "lucide-react";
import { motion } from "framer-motion";

interface StatisticItem {
  id: number;
  label: string;
  value: string;
}

export default function AdminStatisticsPage() {
  const [stats, setStats] = useState<StatisticItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [updatingId, setUpdatingId] = useState<number | null>(null);
  
  // Feedback states
  const [successMsg, setSuccessMsg] = useState("");
  const [errorMsg, setErrorMsg] = useState("");

  // Editing values
  const [editValues, setEditValues] = useState<Record<number, { label: string; value: string }>>({});

  useEffect(() => {
    loadData();
  }, []);

  async function loadData() {
    try {
      setLoading(true);
      const data = await fetchStatistics();
      setStats(data || []);
      
      // Initialize edit values
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
    setEditValues((prev) => ({
      ...prev,
      [id]: {
        ...prev[id],
        [field]: text,
      },
    }));
  };

  const handleUpdate = async (id: number) => {
    const dataToUpdate = editValues[id];
    if (!dataToUpdate || !dataToUpdate.label || !dataToUpdate.value) {
      setErrorMsg("Label and value cannot be empty.");
      return;
    }

    setUpdatingId(id);
    setErrorMsg("");
    setSuccessMsg("");

    try {
      await updateStatistic(id, dataToUpdate);
      setSuccessMsg(`Statistic "${dataToUpdate.label}" updated successfully!`);
      await loadData();
      setTimeout(() => setSuccessMsg(""), 3000);
    } catch (err: any) {
      setErrorMsg(err.message || "Failed to update statistic.");
    } finally {
      setUpdatingId(null);
    }
  };

  const inputClass = "w-full rounded-xl border border-border bg-white px-4 py-3 text-sm text-foreground outline-none transition-colors focus:border-primary font-semibold";
  const labelClass = "text-xs font-semibold text-muted mb-1 block";

  return (
    <div className="space-y-10 max-w-4xl">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div className="flex flex-col gap-2">
          <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Website statistics</h1>
          <p className="text-sm text-muted">Dynamically manage numbers and labels displayed across the website</p>
        </div>
        <button
          onClick={loadData}
          className="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-border bg-white text-muted hover:bg-surface hover:text-foreground cursor-pointer transition-colors"
          title="Refresh Data"
        >
          <RefreshCw size={16} />
        </button>
      </div>

      {successMsg && (
        <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-sm font-semibold text-emerald-800 flex items-center gap-3">
          <CheckCircle2 className="h-5 w-5 text-emerald-600 shrink-0" />
          <span>{successMsg}</span>
        </div>
      )}

      {errorMsg && (
        <div className="rounded-xl bg-red-50 border border-red-100 p-4 text-sm font-semibold text-red-800 flex items-center gap-3">
          <AlertCircle className="h-5 w-5 text-red-600 shrink-0" />
          <span>{errorMsg}</span>
        </div>
      )}

      {loading ? (
        <div className="py-20 text-center">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="mt-3 text-sm text-muted">Loading website stats...</p>
        </div>
      ) : stats.length === 0 ? (
        <div className="glass-card py-20 text-center text-muted border border-dashed border-border rounded-xl">
          No statistics found in the database. Run database seeder to populate default values.
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {stats.map((stat) => {
            const currentEdit = editValues[stat.id] || { label: "", value: "" };
            const isUpdating = updatingId === stat.id;

            return (
              <div key={stat.id} className="glass-card p-6 flex flex-col gap-5 justify-between">
                <div>
                  <div className="flex items-center justify-between mb-4 border-b border-border pb-3">
                    <span className="flex items-center gap-1.5 text-xs font-bold text-primary bg-primary-soft px-2.5 py-1 rounded-md">
                      <Layers size={12} /> Stat #{stat.id}
                    </span>
                    <span className="text-[10px] text-muted-foreground font-semibold">
                      Live: {stat.value} ({stat.label})
                    </span>
                  </div>

                  <div className="space-y-4">
                    <div>
                      <label className={labelClass}>Statistic Label</label>
                      <input
                        type="text"
                        value={currentEdit.label}
                        onChange={(e) => handleInputChange(stat.id, "label", e.target.value)}
                        className={inputClass}
                        placeholder="e.g. Active Members"
                      />
                    </div>

                    <div>
                      <label className={labelClass}>Statistic Value</label>
                      <input
                        type="text"
                        value={currentEdit.value}
                        onChange={(e) => handleInputChange(stat.id, "value", e.target.value)}
                        className={inputClass}
                        placeholder="e.g. 500+"
                      />
                    </div>
                  </div>
                </div>

                <div className="pt-4 border-t border-border flex items-center justify-end">
                  <button
                    onClick={() => handleUpdate(stat.id)}
                    disabled={isUpdating}
                    className="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                  >
                    <Save size={14} />
                    {isUpdating ? "Saving..." : "Save Changes"}
                  </button>
                </div>
              </div>
            );
          })}
        </div>
      )}

      <div className="glass-card p-6 flex items-start gap-4">
        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
          <BarChart3 className="h-6 w-6" />
        </div>
        <div>
          <h4 className="text-lg font-semibold text-foreground mb-1">About dynamic stats</h4>
          <p className="text-sm leading-relaxed text-muted">
            The stats managed here directly control the values displayed in the hero grids on the website homepage, about us sections, and galleries. Make sure the labels and values are punchy and accurate!
          </p>
        </div>
      </div>
    </div>
  );
}

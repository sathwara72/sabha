"use client";

import { useEffect, useState } from "react";
import {
  fetchAllBusinessesAdmin, approveBusiness, rejectBusiness
} from "@/lib/api";
import {
  ShieldCheck, XCircle, CheckCircle2,
  Globe, Briefcase, Info
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";

interface Business {
  id: number;
  name: string;
  category: string;
  description: string;
  website: string;
  status: string;
  is_verified: boolean;
}

export default function AdminBusinessesPage() {
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadData();
  }, []);

  async function loadData() {
    try {
      const data = await fetchAllBusinessesAdmin();
      setBusinesses(data);
    } catch (error) {
      console.error("Error loading businesses:", error);
    } finally {
      setLoading(false);
    }
  }

  async function handleApprove(id: number) {
    try {
      await approveBusiness(id);
      loadData();
    } catch (error) {
      alert("Approval failed");
    }
  }

  async function handleReject(id: number) {
    const reason = prompt("Please enter the reason for rejection:");
    if (reason === null) return; // User cancelled
    if (!reason.trim()) {
      alert("Rejection reason is required.");
      return;
    }
    try {
      await rejectBusiness(id, reason.trim());
      loadData();
    } catch (error) {
      alert("Rejection failed");
    }
  }

  return (
    <div className="space-y-8">
      <div className="flex flex-col gap-2">
        <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Business approvals</h1>
        <p className="text-sm text-muted">Review and approve member businesses</p>
      </div>

      <div className="flex items-center gap-3 rounded-xl bg-primary-soft p-4">
        <Info className="h-5 w-5 shrink-0 text-primary" />
        <p className="text-sm font-medium text-foreground">
          Review each business carefully before approving it for the community.
        </p>
      </div>

      <div className="grid grid-cols-1 gap-5">
        <AnimatePresence>
          {businesses.map((biz) => (
            <motion.div
              key={biz.id}
              layout
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="glass-card p-6"
            >
              <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div className="flex flex-col sm:flex-row sm:items-center gap-5">
                  <div className="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-2xl font-semibold text-primary">
                    {biz.name?.[0] ?? "?"}
                  </div>
                  <div>
                    <div className="flex items-center gap-3 mb-1.5">
                      <h3 className="text-lg font-semibold text-foreground">{biz.name}</h3>
                      <span className={`rounded-full px-3 py-1 text-xs font-medium ${
                        biz.status === "approved"
                          ? "bg-primary-soft text-primary"
                          : "bg-surface text-muted"
                      }`}>
                        {biz.status}
                      </span>
                    </div>
                    <div className="flex flex-wrap gap-4 text-sm text-muted">
                      <span className="flex items-center gap-1.5"><Briefcase size={14} className="text-primary" /> {biz.category}</span>
                      <span className="flex items-center gap-1.5"><Globe size={14} className="text-primary" /> {biz.website}</span>
                    </div>
                  </div>
                </div>

                <div className="flex items-center gap-3 w-full lg:w-auto">
                  {biz.status === "pending" ? (
                    <>
                      <button
                        onClick={() => handleApprove(biz.id)}
                        className="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition-all hover:opacity-90 active:scale-[0.98]"
                      >
                        <CheckCircle2 size={16} />
                        Approve
                      </button>
                      <button
                        onClick={() => handleReject(biz.id)}
                        className="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 rounded-xl border border-red-100 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-600 transition-all hover:bg-red-100 active:scale-[0.98]"
                      >
                        <XCircle size={16} />
                        Reject
                      </button>
                    </>
                  ) : (
                    <div className="inline-flex items-center gap-2 rounded-full border border-border px-4 py-2 text-sm font-medium text-muted">
                      <ShieldCheck size={15} className={biz.status === "approved" ? "text-primary" : "text-muted"} />
                      {biz.status === "approved" ? "Verified" : biz.status}
                    </div>
                  )}
                </div>
              </div>

              <div className="mt-5 pt-5 border-t border-border">
                <p className="max-w-4xl text-sm leading-relaxed text-muted">
                  {biz.description || "No description available for this business yet."}
                </p>
              </div>
            </motion.div>
          ))}
        </AnimatePresence>

        {businesses.length === 0 && !loading && (
          <div className="rounded-xl border border-dashed border-border py-20 text-center text-muted">
            No businesses to review.
          </div>
        )}
      </div>
    </div>
  );
}

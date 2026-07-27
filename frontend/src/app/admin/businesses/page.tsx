"use client";

import { useEffect, useState, useMemo, useCallback } from "react";
import { useRouter } from "next/navigation";
import { createPortal } from "react-dom";
import {
  fetchAllBusinessesAdmin, approveBusiness, rejectBusiness, deleteBusiness
} from "@/lib/api";
import { API_ORIGIN, assetUrl } from "@/lib/config";
import {
  ShieldCheck, XCircle, CheckCircle2,
  Globe, Info, Search, ChevronLeft, ChevronRight,
  MapPin, Phone, Receipt, X, ZoomIn, Eye, Trash2
} from "lucide-react";
import ConfirmModal from "@/components/shared/ConfirmModal";
import PromptModal from "@/components/shared/PromptModal";
import Pagination from "@/components/shared/Pagination";

interface Business {
  id: number;
  name: string;
  category: string;
  description: string;
  website?: string;
  status: string;
  is_verified: boolean;
  logo?: string;
  cover_image?: string;
  location?: string;
  area?: string;
  city?: string;
  phone?: string;
  tagline?: string;
  payment_screenshot?: string;
}

function getMediaUrl(path?: string) {
  return assetUrl(path);
}

export default function AdminBusinessesPage() {
  const router = useRouter();
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");
  const [currentPage, setCurrentPage] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [totalPages, setTotalPages] = useState(1);
  const [counts, setCounts] = useState<{ all: number; pending: number; approved: number; rejected: number }>({
    all: 0,
    pending: 0,
    approved: 0,
    rejected: 0,
  });

  const [paymentModalUrl, setPaymentModalUrl] = useState<string | null>(null);
  const [rejectingBiz, setRejectingBiz] = useState<{ id: number; name: string } | null>(null);
  const [deletingBiz, setDeletingBiz] = useState<{ id: number; name: string } | null>(null);
  const [actionLoading, setActionLoading] = useState(false);
  const itemsPerPage = 9;

  useEffect(() => {
    window.scrollTo({ top: 0, behavior: "instant" });
  }, [currentPage]);

  const getTabCount = (status: "all" | "pending" | "approved" | "rejected") => {
    return counts[status] ?? 0;
  };

  const loadData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await fetchAllBusinessesAdmin({
        page: currentPage,
        limit: itemsPerPage,
        search: searchTerm,
        status: statusFilter,
      });

      if (res && res.paginator) {
        setBusinesses(res.paginator.data || []);
        setTotalItems(res.paginator.total || 0);
        setTotalPages(res.paginator.last_page || 1);
        if (res.counts) {
          setCounts(res.counts);
        }
      } else if (Array.isArray(res)) {
        setBusinesses(res);
        setTotalItems(res.length);
        setTotalPages(Math.ceil(res.length / itemsPerPage));
      }
    } catch (error) {
      console.error("Error loading businesses:", error);
    } finally {
      setLoading(false);
    }
  }, [currentPage, searchTerm, statusFilter]);

  useEffect(() => {
    loadData();
  }, [loadData]);

  async function handleApprove(id: number) {
    try {
      await approveBusiness(id);
      loadData();
    } catch {
      alert("Approval failed");
    }
  }

  async function handleConfirmReject(reason: string) {
    if (!rejectingBiz) return;
    try {
      setActionLoading(true);
      await rejectBusiness(rejectingBiz.id, reason);
      setRejectingBiz(null);
      loadData();
    } catch {
      alert("Rejection failed");
    } finally {
      setActionLoading(false);
    }
  }

  async function handleConfirmDelete() {
    if (!deletingBiz) return;
    try {
      setActionLoading(true);
      await deleteBusiness(deletingBiz.id);
      setDeletingBiz(null);
      loadData();
    } catch (err: any) {
      alert(err.message || "Delete failed");
    } finally {
      setActionLoading(false);
    }
  }

  const statusColor: Record<string, string> = {
    approved: "bg-emerald-50 text-emerald-700 border-emerald-200",
    pending: "bg-amber-50 text-amber-700 border-amber-200",
    rejected: "bg-red-50 text-red-600 border-red-200",
  };

  return (
    <>
      <div className="space-y-4">
        {/* Page Header */}
        <div className="flex flex-col">
          <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Business Approvals</h1>
          <p className="text-sm text-muted">Review and approve member businesses</p>
        </div>

        <div className="flex items-center gap-3 rounded-xl bg-primary-soft p-3">
          <Info className="h-5 w-5 shrink-0 text-primary" />
          <p className="text-sm font-semibold text-foreground">
            Review each business carefully before approving it for the community.
          </p>
        </div>

        {/* Search + Filter */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div className="relative flex-1 max-w-md">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <input
              type="text"
              placeholder="Search by name, category, or description..."
              className="w-full rounded-xl border border-border bg-white py-2 pl-10 pr-4 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
          <div className="flex items-center gap-1 bg-surface p-1 rounded-xl border border-border self-start sm:self-auto justify-center">
            {(["all", "pending", "approved", "rejected"] as const).map((status) => (
              <button
                key={status}
                onClick={() => {
                  setStatusFilter(status);
                  setCurrentPage(1);
                }}
                className={`inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg capitalize transition-all cursor-pointer ${statusFilter === status
                  ? "bg-white text-foreground shadow-sm"
                  : "text-muted hover:text-foreground"
                  }`}
              >
                {status}
                <span className={`px-1.5 py-0.5 text-[10px] rounded-full font-bold transition-colors ${statusFilter === status
                  ? "bg-primary-soft text-primary"
                  : "bg-slate-200/60 text-slate-500"
                }`}>
                  {getTabCount(status)}
                </span>
              </button>
            ))}
          </div>
        </div>

        {/* Cards Grid */}
        {loading ? (
          <div className="py-20 text-center">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
            <p className="mt-3 text-sm text-muted">Loading businesses...</p>
          </div>
        ) : (
          <>
            {businesses.length === 0 ? (
              <div className="rounded-xl border border-dashed border-border py-20 text-center text-muted">
                {searchTerm || statusFilter !== "all" ? "No businesses match your search/filter." : "No businesses to review."}
              </div>
            ) : (
              <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                {businesses.map((biz) => (
                  <div
                    key={biz.id}
                    className="glass-card p-0 flex flex-col rounded-2xl border border-border"
                  >
                    {/* Cover Image */}
                    <div className="relative h-28 w-full overflow-hidden rounded-t-2xl shrink-0">
                      {biz.cover_image ? (
                        <img
                          src={getMediaUrl(biz.cover_image)}
                          alt={biz.name}
                          className="w-full h-full object-cover"
                        />
                      ) : (
                        <div className="w-full h-full bg-gradient-to-br from-primary/10 via-primary/5 to-slate-100" />
                      )}
                      {/* Status Badge */}
                      <span className={`absolute top-2.5 right-2.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${statusColor[biz.status] || "bg-surface text-muted border-border"}`}>
                        {biz.status.charAt(0).toUpperCase() + biz.status.slice(1)}
                      </span>
                    </div>

                    {/* Card Body */}
                    <div className="px-4 pb-4 flex flex-col gap-2.5 flex-1">
                      {/* Logo + Name */}
                      <div className="flex items-center gap-3 -mt-7">
                        <div className="relative z-10 shrink-0 h-14 w-14 rounded-xl border-2 border-white shadow-lg bg-white overflow-hidden flex items-center justify-center p-1">
                          {biz.logo ? (
                            <img
                              src={getMediaUrl(biz.logo)}
                              alt={biz.name}
                              className="w-full h-full object-contain"
                            />
                          ) : (
                            <span className="text-xl font-bold text-primary">{biz.name?.[0] ?? "?"}</span>
                          )}
                        </div>
                        <div className="flex-1 mt-8">
                          <h3 className="text-sm font-bold text-foreground leading-tight line-clamp-1">{biz.name}</h3>
                          {biz.tagline && (
                            <p className="text-[10px] text-muted italic line-clamp-1 mt-0.5">{biz.tagline}</p>
                          )}
                        </div>
                      </div>

                      {/* Meta info */}
                      <div className="flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-muted font-medium">
                        <span className="flex items-center gap-1 text-primary font-semibold">{biz.category}</span>
                        {([biz.area, biz.city].filter(Boolean).join(", ") || biz.location) && (
                          <span className="flex items-center gap-1"><MapPin size={11} className="text-primary" /> {[biz.area, biz.city].filter(Boolean).join(", ") || biz.location}</span>
                        )}
                        {biz.phone && <span className="flex items-center gap-1"><Phone size={11} className="text-primary" /> {biz.phone}</span>}
                        {biz.website && (
                          <a href={biz.website} target="_blank" rel="noopener noreferrer" className="flex items-center gap-1 text-primary hover:underline">
                            <Globe size={11} /> Website
                          </a>
                        )}
                      </div>

                      {/* Description */}
                      <p className="text-[11px] text-muted leading-relaxed line-clamp-2 flex-1">
                        {biz.description || "No description available for this business."}
                      </p>

                      {/* Payment Screenshot Button */}
                      {biz.payment_screenshot && (
                        <button
                          onClick={() => setPaymentModalUrl(getMediaUrl(biz.payment_screenshot))}
                          className="flex items-center gap-1.5 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-1.5 hover:bg-amber-100 transition-colors w-full justify-center"
                        >
                          <Receipt size={12} /> View Payment Screenshot
                        </button>
                      )}

                      {/* Action buttons: View Details + Delete */}
                      <div className={`flex items-center gap-2 ${!biz.payment_screenshot ? "mt-auto" : ""}`}>
                        <button
                          onClick={() => router.push(`/admin/businesses/${biz.id}`)}
                          className="flex-1 flex items-center gap-1.5 text-[10px] font-bold text-slate-600 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 hover:bg-slate-100 transition-colors justify-center cursor-pointer"
                        >
                          <Eye size={12} /> View Details
                        </button>
                        <button
                          onClick={() => setDeletingBiz({ id: biz.id, name: biz.name })}
                          className="flex items-center gap-1.5 text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 rounded-lg px-2.5 py-1.5 hover:bg-rose-100 transition-colors justify-center cursor-pointer"
                          title="Delete Business"
                        >
                          <Trash2 size={12} /> Delete
                        </button>
                      </div>

                      {/* Action buttons — only for pending */}
                      {biz.status === "pending" && (
                        <div className="flex items-center gap-2 pt-2 border-t border-border">
                          <button
                            onClick={() => handleApprove(biz.id)}
                            className="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-[11px] font-bold text-white transition-all hover:opacity-90 active:scale-[0.98]"
                          >
                            <CheckCircle2 size={13} /> Approve
                          </button>
                          <button
                            onClick={() => setRejectingBiz({ id: biz.id, name: biz.name })}
                            className="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-[11px] font-bold text-red-600 transition-all hover:bg-red-100 active:scale-[0.98]"
                          >
                            <XCircle size={13} /> Reject
                          </button>
                        </div>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            )}

            {/* Pagination */}
            <Pagination
              currentPage={currentPage}
              totalPages={totalPages}
              totalItems={totalItems}
              itemsPerPage={itemsPerPage}
              onPageChange={(page) => setCurrentPage(page)}
              itemLabel="businesses"
            />
          </>
        )}
      </div>

      {/* Rejection Custom Prompt Modal */}
      <PromptModal
        isOpen={Boolean(rejectingBiz)}
        title="Reject Business Submission"
        message={rejectingBiz ? `Please enter the reason for rejecting "${rejectingBiz.name}":` : ""}
        placeholder="Enter details reason for rejection..."
        confirmLabel="Reject Business"
        isLoading={actionLoading}
        onConfirm={handleConfirmReject}
        onCancel={() => setRejectingBiz(null)}
      />

      {/* Delete Business Custom Confirm Modal */}
      <ConfirmModal
        isOpen={Boolean(deletingBiz)}
        title="Delete Business Profile"
        message={deletingBiz ? `Are you sure you want to delete business "${deletingBiz.name}"? This action cannot be undone.` : ""}
        confirmLabel="Delete Business"
        variant="danger"
        isLoading={actionLoading}
        onConfirm={handleConfirmDelete}
        onCancel={() => setDeletingBiz(null)}
      />

      {/* Payment Screenshot Lightbox Modal */}
      {typeof window !== "undefined" && paymentModalUrl && createPortal(
        <div className="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div
            className="fixed inset-0 bg-black/85 backdrop-blur-md"
            onClick={() => setPaymentModalUrl(null)}
          />
          <button
            onClick={() => setPaymentModalUrl(null)}
            className="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2 transition-colors cursor-pointer"
          >
            <X size={20} />
          </button>
          <div className="relative z-40 max-w-2xl w-full flex flex-col items-center gap-4">
            <div className="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-2 text-white text-xs font-semibold flex items-center gap-2">
              <Receipt size={14} className="text-amber-300" /> Payment Screenshot
            </div>
            <img
              src={paymentModalUrl}
              alt="Payment Screenshot"
              className="max-h-[80vh] max-w-full object-contain rounded-2xl shadow-2xl border border-white/10"
            />
          </div>
        </div>,
        document.body
      )}
    </>
  );
}

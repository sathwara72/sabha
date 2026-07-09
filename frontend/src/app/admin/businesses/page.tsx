"use client";

import { useEffect, useState, useMemo } from "react";
import { createPortal } from "react-dom";
import {
  fetchAllBusinessesAdmin, approveBusiness, rejectBusiness
} from "@/lib/api";
import { API_ORIGIN } from "@/lib/config";
import {
  ShieldCheck, XCircle, CheckCircle2,
  Globe, Info, Search, ChevronLeft, ChevronRight,
  MapPin, Phone, Receipt, X, ZoomIn
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";

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
  phone?: string;
  tagline?: string;
  payment_screenshot?: string;
}

function getMediaUrl(path?: string) {
  if (!path) return "";
  if (path.startsWith("http://") || path.startsWith("https://")) return path;
  return `${API_ORIGIN}${path}`;
}

export default function AdminBusinessesPage() {
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");
  const [currentPage, setCurrentPage] = useState(1);
  const [paymentModalUrl, setPaymentModalUrl] = useState<string | null>(null);
  const itemsPerPage = 9;

  const filteredBusinesses = useMemo(() => {
    return businesses.filter((biz) => {
      const matchesSearch =
        biz.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        biz.category.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (biz.description || "").toLowerCase().includes(searchTerm.toLowerCase());
      const matchesStatus = statusFilter === "all" || biz.status === statusFilter;
      return matchesSearch && matchesStatus;
    });
  }, [businesses, searchTerm, statusFilter]);

  const paginatedBusinesses = useMemo(() => {
    const startIndex = (currentPage - 1) * itemsPerPage;
    return filteredBusinesses.slice(startIndex, startIndex + itemsPerPage);
  }, [filteredBusinesses, currentPage]);

  const totalPages = Math.ceil(filteredBusinesses.length / itemsPerPage);

  useEffect(() => { setCurrentPage(1); }, [searchTerm, statusFilter]);
  useEffect(() => { loadData(); }, []);

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
    } catch {
      alert("Approval failed");
    }
  }

  async function handleReject(id: number) {
    const reason = prompt("Please enter the reason for rejection:");
    if (reason === null) return;
    if (!reason.trim()) { alert("Rejection reason is required."); return; }
    try {
      await rejectBusiness(id, reason.trim());
      loadData();
    } catch {
      alert("Rejection failed");
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
        <div className="flex items-center gap-2">
          <span className="text-xs font-semibold text-muted">Status:</span>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="rounded-xl border border-border bg-white px-3 py-2 text-xs font-bold text-foreground outline-none transition-colors focus:border-primary"
          >
            <option value="all">All</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
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
          {filteredBusinesses.length === 0 ? (
            <div className="rounded-xl border border-dashed border-border py-20 text-center text-muted">
              {businesses.length === 0 ? "No businesses to review." : "No businesses match your search/filter."}
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
              <AnimatePresence>
                {paginatedBusinesses.map((biz) => (
                  <motion.div
                    key={biz.id}
                    layout
                    initial={{ opacity: 0, y: 16 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, scale: 0.95 }}
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
                        <div className="relative z-10 shrink-0 h-14 w-14 rounded-xl border-2 border-white shadow-lg bg-white overflow-hidden flex items-center justify-center">
                          {biz.logo ? (
                            <img
                              src={getMediaUrl(biz.logo)}
                              alt={biz.name}
                              className="w-full h-full object-cover"
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
                        {biz.location && <span className="flex items-center gap-1"><MapPin size={11} className="text-primary" /> {biz.location}</span>}
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

                      {/* Action buttons — only for pending */}
                      {biz.status === "pending" && (
                        <div className="flex items-center gap-2 pt-2 border-t border-border mt-auto">
                          <button
                            onClick={() => handleApprove(biz.id)}
                            className="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-[11px] font-bold text-white transition-all hover:opacity-90 active:scale-[0.98]"
                          >
                            <CheckCircle2 size={13} /> Approve
                          </button>
                          <button
                            onClick={() => handleReject(biz.id)}
                            className="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-[11px] font-bold text-red-600 transition-all hover:bg-red-100 active:scale-[0.98]"
                          >
                            <XCircle size={13} /> Reject
                          </button>
                        </div>
                      )}
                    </div>
                  </motion.div>
                ))}
              </AnimatePresence>
            </div>
          )}

          {/* Pagination */}
          {totalPages > 1 && (
            <div className="flex items-center justify-between border-t border-border pt-4 mt-2">
              <p className="text-xs text-muted font-medium">
                Showing <span className="font-semibold">{Math.min((currentPage - 1) * itemsPerPage + 1, filteredBusinesses.length)}</span> to{" "}
                <span className="font-semibold">{Math.min(currentPage * itemsPerPage, filteredBusinesses.length)}</span> of{" "}
                <span className="font-semibold">{filteredBusinesses.length}</span> businesses
              </p>
              <div className="flex items-center gap-2">
                <button
                  onClick={() => setCurrentPage((prev) => Math.max(prev - 1, 1))}
                  disabled={currentPage === 1}
                  className="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-white text-muted hover:bg-surface disabled:opacity-50 transition-colors"
                >
                  <ChevronLeft size={16} />
                </button>
                <span className="text-xs font-bold text-foreground px-2">Page {currentPage} of {totalPages}</span>
                <button
                  onClick={() => setCurrentPage((prev) => Math.min(prev + 1, totalPages))}
                  disabled={currentPage === totalPages}
                  className="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-white text-muted hover:bg-surface disabled:opacity-50 transition-colors"
                >
                  <ChevronRight size={16} />
                </button>
              </div>
            </div>
          )}
        </>
      )}
    </div>

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

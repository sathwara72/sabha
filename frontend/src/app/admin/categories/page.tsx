"use client";

import { useEffect, useState } from "react";
import { fetchAdminCategories, storeCategory, deleteCategory } from "@/lib/api";
import { Tag, Plus, Trash2, AlertCircle, CheckCircle2, Loader2, Search, Briefcase, Layers } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import ConfirmModal from "@/components/shared/ConfirmModal";

export default function AdminCategoriesPage() {
  const [categories, setCategories] = useState<{ id: number; name: string; businesses_count?: number }[]>([]);
  const [loading, setLoading] = useState(true);
  const [newName, setNewName] = useState("");
  const [adding, setAdding] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{ id: number; name: string } | null>(null);
  const [searchTerm, setSearchTerm] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  useEffect(() => {
    loadCategories();
  }, []);

  async function loadCategories() {
    setLoading(true);
    setCurrentPage(1);
    try {
      const data = await fetchAdminCategories();
      setCategories(data);
    } catch {
      setError("Failed to load categories");
    } finally {
      setLoading(false);
    }
  }

  async function handleAdd(e: React.FormEvent) {
    e.preventDefault();
    const name = newName.trim();
    if (!name) return;
    setAdding(true);
    setError("");
    try {
      await storeCategory(name);
      setNewName("");
      setSuccess(`"${name}" category added successfully!`);
      setTimeout(() => setSuccess(""), 3000);
      loadCategories();
    } catch (err: any) {
      setError(err.message || "Failed to add category");
      setTimeout(() => setError(""), 4000);
    } finally {
      setAdding(false);
    }
  }

  function handleDelete(id: number, name: string) {
    setDeleteTarget({ id, name });
  }

  async function handleConfirmDelete() {
    if (!deleteTarget) return;
    const { id, name } = deleteTarget;
    setDeletingId(id);
    setError("");
    try {
      await deleteCategory(id);
      setSuccess(`"${name}" category deleted`);
      setTimeout(() => setSuccess(""), 3000);
      setDeleteTarget(null);
      loadCategories();
    } catch (err: any) {
      setError(err.message || "Failed to delete");
      setTimeout(() => setError(""), 4000);
    } finally {
      setDeletingId(null);
    }
  }

  const filteredCategories = categories.filter((cat) =>
    cat.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const itemsPerPage = 8;
  const totalPages = Math.ceil(filteredCategories.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const paginatedCategories = filteredCategories.slice(startIndex, startIndex + itemsPerPage);

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Business Categories</h1>
          <p className="text-xs text-muted">Manage directory categories displayed in member registrations and search filters</p>
        </div>
      </div>

      {/* Alerts */}
      <AnimatePresence>
        {error && (
          <motion.div
            key="error-alert"
            initial={{ opacity: 0, y: -6 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0 }}
            className="rounded-xl bg-red-50 border border-red-100 p-3 text-xs font-semibold text-red-600"
          >
            {error}
          </motion.div>
        )}
        {success && (
          <motion.div
            key="success-alert"
            initial={{ opacity: 0, y: -6 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0 }}
            className="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-xs font-semibold text-emerald-600 flex items-center gap-2"
          >
            <CheckCircle2 size={14} className="text-emerald-600" /> {success}
          </motion.div>
        )}
      </AnimatePresence>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {/* Left Side: Creation Panel */}
        <div className="space-y-4">
          <div className="glass-card p-5 rounded-2xl border border-border space-y-4">
            <div>
              <h2 className="text-sm font-bold text-foreground flex items-center gap-2">
                <Layers size={15} className="text-primary" /> Create Category
              </h2>
              <p className="text-[11px] text-muted-foreground mt-0.5">Define a new industry classification</p>
            </div>
            
            <form onSubmit={handleAdd} className="space-y-3">
              <div className="relative">
                <Tag className="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                <input
                  type="text"
                  value={newName}
                  onChange={(e) => setNewName(e.target.value)}
                  placeholder="e.g. Renewables & Solar"
                  className="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-4 text-xs text-foreground outline-none transition-all placeholder:text-muted focus:border-primary focus:ring-1 focus:ring-primary/20"
                  disabled={adding}
                />
              </div>
              <button
                type="submit"
                disabled={adding || !newName.trim()}
                className="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50 cursor-pointer shadow-sm"
              >
                {adding ? <Loader2 size={13} className="animate-spin" /> : <Plus size={13} />}
                {adding ? "Creating Category..." : "Create Category"}
              </button>
            </form>
          </div>

          <div className="glass-card p-4 rounded-xl border border-border flex items-start gap-3 bg-primary-soft/40">
            <AlertCircle className="h-4.5 w-4.5 shrink-0 text-primary mt-0.5" />
            <div>
              <h4 className="text-xs font-bold text-foreground mb-0.5">Registration Integration</h4>
              <p className="text-[11px] leading-relaxed text-muted-foreground">
                All active categories immediately populate the member registration forms, search filters, and business profile edit dashboard.
              </p>
            </div>
          </div>
        </div>

        {/* Right Side: Search and Cards Grid */}
        <div className="lg:col-span-2 space-y-4">
          {/* Controls Bar */}
          <div className="flex items-center gap-3">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted" />
              <input
                type="text"
                value={searchTerm}
                onChange={(e) => {
                  setSearchTerm(e.target.value);
                  setCurrentPage(1);
                }}
                placeholder="Search categories..."
                className="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-4 text-xs text-foreground outline-none transition-all placeholder:text-muted focus:border-primary"
              />
            </div>
            <div className="text-[11px] font-bold text-muted bg-surface rounded-xl px-3 py-2 border border-border shrink-0">
              Total: {categories.length}
            </div>
          </div>

          {loading ? (
            <div className="py-24 text-center glass-card rounded-2xl border border-border">
              <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
              <p className="mt-3 text-xs text-muted font-medium">Loading category directory...</p>
            </div>
          ) : filteredCategories.length === 0 ? (
            <div className="py-24 text-center text-muted text-xs glass-card rounded-2xl border border-border italic">
              {searchTerm ? "No categories matching your search query." : "No categories defined. Create one on the left panel."}
            </div>
          ) : (
            <div className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                <AnimatePresence mode="popLayout">
                  {paginatedCategories.map((cat, idx) => (
                    <motion.div
                      key={cat.id}
                      layout
                      initial={{ opacity: 0, y: 10 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, scale: 0.95 }}
                      transition={{ delay: idx * 0.02 }}
                      className="p-3.5 rounded-2xl border border-border bg-white/70 backdrop-blur-sm flex items-center justify-between hover:shadow-md hover:border-primary/30 transition-all group"
                    >
                      <div className="flex items-center gap-3">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                          <Tag size={13} />
                        </div>
                        <div>
                          <h3 className="text-xs font-bold text-foreground group-hover:text-primary transition-colors">{cat.name}</h3>
                          <p className="text-[10px] text-muted flex items-center gap-1 mt-0.5">
                            <Briefcase size={10} />
                            {cat.businesses_count ?? 0} { (cat.businesses_count === 1) ? 'business' : 'businesses' } registered
                          </p>
                        </div>
                      </div>
                      
                      <button
                        onClick={() => handleDelete(cat.id, cat.name)}
                        disabled={deletingId === cat.id}
                        className="h-7 w-7 rounded-lg border border-red-100 bg-red-50 text-red-600 flex items-center justify-center transition-all hover:bg-red-100 active:scale-[0.95] disabled:opacity-50 cursor-pointer shadow-sm md:opacity-0 group-hover:opacity-100"
                        title="Delete Category"
                      >
                        {deletingId === cat.id ? (
                          <Loader2 size={12} className="animate-spin" />
                        ) : (
                          <Trash2 size={12} />
                        )}
                      </button>
                    </motion.div>
                  ))}
                </AnimatePresence>
              </div>

              {/* Pagination Controls */}
              {totalPages > 1 && (
                <div className="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-border">
                  <span className="text-[11px] font-bold text-muted">
                    Showing {startIndex + 1}-{Math.min(startIndex + itemsPerPage, filteredCategories.length)} of {filteredCategories.length}
                  </span>
                  <div className="flex items-center gap-1">
                    <button
                      onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
                      disabled={currentPage === 1}
                      className="h-7 px-2.5 rounded-lg border border-border bg-white text-[11px] font-bold text-foreground transition-all hover:bg-surface disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                      Previous
                    </button>
                    {Array.from({ length: totalPages }).map((_, i) => {
                      const pageNum = i + 1;
                      return (
                        <button
                          key={pageNum}
                          onClick={() => setCurrentPage(pageNum)}
                          className={`h-7 w-7 rounded-lg text-[11px] font-bold transition-all cursor-pointer ${
                            currentPage === pageNum
                              ? "bg-primary text-white border border-primary shadow-sm"
                              : "border border-border bg-white text-foreground hover:bg-surface"
                          }`}
                        >
                          {pageNum}
                        </button>
                      );
                    })}
                    <button
                      onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
                      disabled={currentPage === totalPages}
                      className="h-7 px-2.5 rounded-lg border border-border bg-white text-[11px] font-bold text-foreground transition-all hover:bg-surface disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                      Next
                    </button>
                  </div>
                </div>
              )}
            </div>
          )}
        </div>
      </div>
      {/* Confirm Delete Modal */}
      <ConfirmModal
        isOpen={deleteTarget !== null}
        title="Delete Category"
        message={`Are you sure you want to delete the category "${deleteTarget?.name}"? All associated settings will remain but the category tag will be removed.`}
        confirmLabel="Delete"
        cancelLabel="Cancel"
        variant="danger"
        isLoading={deletingId !== null}
        onConfirm={handleConfirmDelete}
        onCancel={() => setDeleteTarget(null)}
      />
    </div>
  );
}

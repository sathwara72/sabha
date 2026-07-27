"use client";

import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import { fetchAdminCategories, storeCategory, updateCategory, deleteCategory } from "@/lib/api";
import { Tag, Plus, Pencil, Trash2, CheckCircle2, Loader2, Search, Briefcase, X } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import ConfirmModal from "@/components/shared/ConfirmModal";
import Pagination from "@/components/shared/Pagination";

export default function AdminCategoriesPage() {
  const [categories, setCategories] = useState<{ id: number; name: string; businesses_count?: number }[]>([]);
  const [loading, setLoading] = useState(true);

  // Add Category Modal
  const [isAddModalOpen, setIsAddModalOpen] = useState(false);
  const [addName, setAddName] = useState("");
  const [adding, setAdding] = useState(false);

  // Edit Category Modal
  const [editingCategory, setEditingCategory] = useState<{ id: number; name: string } | null>(null);
  const [editName, setEditName] = useState("");
  const [updating, setUpdating] = useState(false);

  // Delete Category
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{ id: number; name: string } | null>(null);

  const [searchTerm, setSearchTerm] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  useEffect(() => {
    window.scrollTo({ top: 0, behavior: "instant" });
  }, [currentPage]);

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
    const name = addName.trim();
    if (!name) return;
    setAdding(true);
    setError("");
    try {
      await storeCategory(name);
      setAddName("");
      setIsAddModalOpen(false);
      setSuccess(`Category "${name}" added successfully!`);
      setTimeout(() => setSuccess(""), 3000);
      loadCategories();
    } catch (err: any) {
      setError(err.message || "Failed to add category");
      setTimeout(() => setError(""), 4000);
    } finally {
      setAdding(false);
    }
  }

  async function handleUpdate(e: React.FormEvent) {
    e.preventDefault();
    if (!editingCategory) return;
    const name = editName.trim();
    if (!name) return;
    setUpdating(true);
    setError("");
    try {
      await updateCategory(editingCategory.id, name);
      setEditingCategory(null);
      setEditName("");
      setSuccess(`Category updated to "${name}" successfully!`);
      setTimeout(() => setSuccess(""), 3000);
      loadCategories();
    } catch (err: any) {
      setError(err.message || "Failed to update category");
      setTimeout(() => setError(""), 4000);
    } finally {
      setUpdating(false);
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

  const itemsPerPage = 9;
  const totalPages = Math.ceil(filteredCategories.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const paginatedCategories = filteredCategories.slice(startIndex, startIndex + itemsPerPage);

  return (
    <div className="space-y-5 font-outfit">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Business Categories</h1>
          <p className="text-xs text-muted">Manage directory categories displayed in member registrations and search filters</p>
        </div>
        <button
          onClick={() => {
            setAddName("");
            setError("");
            setIsAddModalOpen(true);
          }}
          className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer shadow-sm self-start sm:self-auto"
        >
          <Plus size={15} /> Add Category
        </button>
      </div>

      {/* Alerts */}
      <AnimatePresence>
        {error && (
          <motion.div
            key="error-alert"
            initial={{ opacity: 0, y: -5 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0 }}
            className="rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs font-semibold text-rose-600"
          >
            {error}
          </motion.div>
        )}
        {success && (
          <motion.div
            key="success-alert"
            initial={{ opacity: 0, y: -5 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0 }}
            className="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2"
          >
            <CheckCircle2 size={14} className="text-emerald-600" /> {success}
          </motion.div>
        )}
      </AnimatePresence>

      {/* Main Categories Panel */}
      <div className="space-y-4">
        {/* Search & Filter Bar */}
        <div className="flex items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-border shadow-xs">
          <div className="relative flex-1 max-w-md">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => {
                setSearchTerm(e.target.value);
                setCurrentPage(1);
              }}
              placeholder="Search categories..."
              className="w-full rounded-xl border border-border bg-slate-50/50 py-2 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary"
            />
          </div>
          <div className="text-xs font-bold text-slate-600 bg-slate-100 rounded-xl px-3 py-2 border border-slate-200 shrink-0">
            Total Categories: <span className="text-primary font-black">{categories.length}</span>
          </div>
        </div>

        {loading ? (
          <div className="py-24 text-center bg-white rounded-2xl border border-border shadow-xs">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
            <p className="mt-3 text-xs font-semibold text-slate-500">Loading categories directory...</p>
          </div>
        ) : filteredCategories.length === 0 ? (
          <div className="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
            {searchTerm ? "No categories matching your search query." : "No categories defined yet. Click 'Add Category' above to create your first category."}
          </div>
        ) : (
          <div className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
              <AnimatePresence mode="popLayout">
                {paginatedCategories.map((cat, idx) => (
                  <motion.div
                    key={cat.id}
                    layout
                    initial={{ opacity: 0, y: 0 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, scale: 0.95 }}
                    transition={{ delay: idx * 0.02 }}
                    className="p-4 rounded-2xl border border-slate-200/80 bg-white flex items-center justify-between shadow-xs hover:shadow-md hover:border-primary/40 transition-all group"
                  >
                    <div className="flex items-center gap-3">
                      <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary shadow-xs">
                        <Tag size={15} />
                      </div>
                      <div>
                        <h3 className="text-xs font-extrabold text-slate-900 group-hover:text-primary transition-colors">{cat.name}</h3>
                        <p className="text-[11px] font-medium text-slate-500 flex items-center gap-1 mt-0.5">
                          <Briefcase size={11} className="text-slate-400" />
                          {cat.businesses_count ?? 0} { (cat.businesses_count === 1) ? 'business' : 'businesses' } registered
                        </p>
                      </div>
                    </div>

                    <div className="flex items-center gap-1.5">
                      <button
                        onClick={() => {
                          setEditingCategory(cat);
                          setEditName(cat.name);
                        }}
                        className="h-7 w-7 rounded-xl border border-amber-200/80 bg-amber-50 text-amber-700 flex items-center justify-center transition-all hover:bg-amber-100 active:scale-[0.95] cursor-pointer shadow-xs"
                        title="Edit Category"
                      >
                        <Pencil size={12} />
                      </button>
                      <button
                        onClick={() => handleDelete(cat.id, cat.name)}
                        disabled={deletingId === cat.id}
                        className="h-7 w-7 rounded-xl border border-rose-200/80 bg-rose-50 text-rose-600 flex items-center justify-center transition-all hover:bg-rose-100 active:scale-[0.95] disabled:opacity-50 cursor-pointer shadow-xs"
                        title="Delete Category"
                      >
                        {deletingId === cat.id ? (
                          <Loader2 size={12} className="animate-spin" />
                        ) : (
                          <Trash2 size={12} />
                        )}
                      </button>
                    </div>
                  </motion.div>
                ))}
              </AnimatePresence>
            </div>

            {/* Pagination Controls */}
            <Pagination
              currentPage={currentPage}
              totalPages={totalPages}
              totalItems={filteredCategories.length}
              itemsPerPage={itemsPerPage}
              onPageChange={(page) => setCurrentPage(page)}
              itemLabel="categories"
            />
          </div>
        )}
      </div>

      {/* ─── Add Category Pop-up Modal ─── */}
      {typeof window !== "undefined" && isAddModalOpen && createPortal(
        <div className="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div
            className="fixed inset-0 bg-slate-900/65 backdrop-blur-sm"
            onClick={() => !adding && setIsAddModalOpen(false)}
          />
          <div className="relative z-50 w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4">
            <div className="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 className="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <Tag className="text-primary" size={18} /> Add New Category
              </h3>
              <button
                onClick={() => setIsAddModalOpen(false)}
                disabled={adding}
                className="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer"
              >
                <X size={18} />
              </button>
            </div>

            <form onSubmit={handleAdd} className="space-y-4">
              <div className="space-y-1.5">
                <label className="text-xs font-bold text-slate-700">Category Name</label>
                <div className="relative">
                  <Tag className="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                  <input
                    type="text"
                    required
                    value={addName}
                    onChange={(e) => setAddName(e.target.value)}
                    placeholder="e.g. Renewables & Solar"
                    className="w-full rounded-xl border border-border bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20"
                    disabled={adding}
                    autoFocus
                  />
                </div>
              </div>

              <div className="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button
                  type="button"
                  onClick={() => setIsAddModalOpen(false)}
                  disabled={adding}
                  className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  disabled={adding || !addName.trim()}
                  className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 disabled:opacity-50 transition-all cursor-pointer"
                >
                  {adding ? <Loader2 size={14} className="animate-spin" /> : <Plus size={14} />}
                  {adding ? "Creating..." : "Create Category"}
                </button>
              </div>
            </form>
          </div>
        </div>,
        document.body
      )}

      {/* ─── Edit Category Pop-up Modal ─── */}
      {typeof window !== "undefined" && editingCategory && createPortal(
        <div className="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div
            className="fixed inset-0 bg-slate-900/65 backdrop-blur-sm"
            onClick={() => !updating && setEditingCategory(null)}
          />
          <div className="relative z-50 w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4">
            <div className="flex items-center justify-between border-b border-slate-100 pb-3">
              <h3 className="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <Pencil className="text-amber-600" size={18} /> Edit Category
              </h3>
              <button
                onClick={() => setEditingCategory(null)}
                disabled={updating}
                className="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer"
              >
                <X size={18} />
              </button>
            </div>

            <form onSubmit={handleUpdate} className="space-y-4">
              <div className="space-y-1.5">
                <label className="text-xs font-bold text-slate-700">Category Name</label>
                <div className="relative">
                  <Tag className="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                  <input
                    type="text"
                    required
                    value={editName}
                    onChange={(e) => setEditName(e.target.value)}
                    placeholder="Enter category name"
                    className="w-full rounded-xl border border-border bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20"
                    disabled={updating}
                    autoFocus
                  />
                </div>
              </div>

              <div className="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button
                  type="button"
                  onClick={() => setEditingCategory(null)}
                  disabled={updating}
                  className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  disabled={updating || !editName.trim()}
                  className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-amber-700 active:scale-95 disabled:opacity-50 transition-all cursor-pointer"
                >
                  {updating ? <Loader2 size={14} className="animate-spin" /> : <Pencil size={14} />}
                  {updating ? "Saving..." : "Save Changes"}
                </button>
              </div>
            </form>
          </div>
        </div>,
        document.body
      )}

      {/* Confirm Delete Modal */}
      <ConfirmModal
        isOpen={deleteTarget !== null}
        title="Delete Category"
        message={`Are you sure you want to delete the category "${deleteTarget?.name}"?`}
        confirmLabel="Delete Category"
        cancelLabel="Cancel"
        variant="danger"
        isLoading={deletingId !== null}
        onConfirm={handleConfirmDelete}
        onCancel={() => setDeleteTarget(null)}
      />
    </div>
  );
}

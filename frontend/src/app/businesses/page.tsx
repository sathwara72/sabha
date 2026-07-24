"use client";

import { useState, useMemo, useEffect } from "react";
import { Search, Filter, MapPin, ShieldCheck, Star, ChevronLeft, ChevronRight, Lock, Plus, X, Briefcase, Info } from "lucide-react";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import { cn } from "@/lib/utils";
import { useAuth } from "@/lib/auth";
import { fetchBusinesses, submitBusiness, fetchCategories } from "@/lib/api";
import { useLanguage } from "@/lib/language";
import { assetUrl } from "@/lib/config";

export default function BusinessDirectory() {
  const { isAuthenticated, isReady, openLogin, openRegister } = useAuth();
  const { t } = useLanguage();
  const [businesses, setBusinesses] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [currentPage, setCurrentPage] = useState(1);
  const [isSubmitOpen, setIsSubmitOpen] = useState(false);
  const itemsPerPage = 9;

  // Form states for business submission
  const [submitting, setSubmitting] = useState(false);
  const [formError, setFormError] = useState("");
  const [formSuccess, setFormSuccess] = useState("");
  const [formData, setFormData] = useState({
    name: "",
    category: "Software Development",
    description: "",
    website: "",
    services: "",
  });

  const [categories, setCategories] = useState<string[]>(["All"]);

  const [totalItems, setTotalItems] = useState(0);

  // Load categories once on mount
  useEffect(() => {
    async function loadCategories() {
      try {
        const catData = await fetchCategories().catch(() => []);
        if (catData && catData.length > 0) {
          setCategories(["All", ...catData]);
          setFormData(prev => ({ ...prev, category: catData[0] }));
        } else {
          setCategories([
            "All",
            "Software Development",
            "Supply Chain",
            "Digital Marketing",
            "Construction",
            "Financial Services",
            "Renewables",
            "Creative Agency",
            "Venture Capital"
          ]);
        }
      } catch (err) {
        console.error("Failed to load categories:", err);
      }
    }
    loadCategories();
  }, []);

  async function fetchPageData() {
    try {
      setLoading(true);
      const result = await fetchBusinesses({
        page: currentPage,
        limit: itemsPerPage,
        search: searchQuery,
        category: selectedCategory === "All" ? undefined : selectedCategory
      });
      
      if (result && result.data) {
        setBusinesses(result.data);
        setTotalItems(result.total || 0);
      } else {
        const arrayData = Array.isArray(result) ? result : [];
        setBusinesses(arrayData);
        setTotalItems(arrayData.length);
      }
    } catch (err) {
      console.error("Failed to fetch page data:", err);
    } finally {
      setLoading(false);
    }
  }

  // Fetch businesses when page, search, or category changes
  useEffect(() => {
    fetchPageData();
  }, [currentPage, searchQuery, selectedCategory]);

  const totalPages = Math.ceil(totalItems / itemsPerPage);

  const handleCategoryChange = (cat: string) => {
    setSelectedCategory(cat);
    setCurrentPage(1);
  };

  const handleSearchChange = (query: string) => {
    setSearchQuery(query);
    setCurrentPage(1);
  };

  const handleFormSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setFormError("");
    setFormSuccess("");
    setSubmitting(true);

    try {
      const data = new FormData();
      data.append("name", formData.name);
      data.append("category", formData.category);
      data.append("description", formData.description || "");
      data.append("website", formData.website || "");
      data.append("services", formData.services || "");

      await submitBusiness(data);
      setFormSuccess("Your business has been submitted successfully and is pending administrator review!");
      const defaultCategory = categories.filter(c => c !== "All")[0] || "Software Development";
      setFormData({
        name: "",
        category: defaultCategory,
        description: "",
        website: "",
        services: "",
      });
      // Reload business list (though newly added will be pending so it won't show yet)
      fetchPageData();
      setTimeout(() => {
        setIsSubmitOpen(false);
        setFormSuccess("");
      }, 3500);
    } catch (err: any) {
      setFormError(err.message || "Failed to submit business. Please try again.");
    } finally {
      setSubmitting(false);
    }
  };

  // Gate the directory behind login
  if (isReady && !isAuthenticated) {
    return (
      <div className="bg-background font-outfit">
        <div className="mx-auto flex min-h-[60vh] max-w-md flex-col items-center justify-center px-6 py-20 text-center">
          <div className="glass-card w-full p-10">
            <div className="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-soft text-primary">
              <Lock className="h-6 w-6" />
            </div>
            <h1 className="text-2xl font-bold tracking-tight text-foreground">Members only</h1>
            <p className="mx-auto mt-2 max-w-xs text-sm text-muted">
              Log in to browse the business directory and connect with members.
            </p>
            <button
              onClick={openLogin}
              className="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
            >
              Log in to continue
            </button>
            <p className="mt-4 text-sm text-muted">
              Don&apos;t have an account?{" "}
              <button
                onClick={openRegister}
                className="font-semibold text-primary hover:opacity-80 cursor-pointer"
              >
                Create one
              </button>
            </p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-background font-outfit">
      <div className="mx-auto max-w-7xl px-6 py-8 lg:px-8">
        {/* Compact title row */}
        <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between border-b border-border pb-6">
          <div>
            <div className="mb-2 flex items-center gap-2.5">
              <span className="h-4 w-1.5 rounded-full bg-accent" />
              <span className="text-sm font-semibold text-accent">{t("directory.label")}</span>
            </div>
            <h1 className="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
              {t("directory.title")}
            </h1>
            <p className="mt-1 text-sm text-muted">
              {t("directory.subtitle")}
            </p>
          </div>
          <div className="flex items-center gap-4">
            <p className="text-sm font-medium text-muted">
              {totalItems} {totalItems === 1 ? t("directory.business") : t("directory.showing")}
            </p>
          </div>
        </div>

        {/* Search & Filter Bar */}
        <div className="mb-10 mt-8 flex flex-col gap-4">
          <div className="relative max-w-2xl">
            <Search className="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
            <input
              type="text"
              placeholder={t("directory.search_placeholder")}
              className="w-full rounded-xl border border-border bg-white py-3 pl-12 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary font-semibold"
              value={searchQuery}
              onChange={(e) => handleSearchChange(e.target.value)}
            />
          </div>

          <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
            {/* <div className="flex items-center gap-2 text-sm font-medium text-muted">
              <Filter className="h-4 w-4 text-primary" />
              Filter by category
            </div> */}
            <div className="flex flex-wrap gap-2">
              {categories.map((cat) => (
                <button
                  key={cat}
                  onClick={() => handleCategoryChange(cat)}
                  className={cn(
                    "rounded-full border px-4 py-1.5 text-xs font-medium transition-colors cursor-pointer",
                    selectedCategory === cat
                      ? "border-primary bg-primary text-white"
                      : "border-border bg-white text-muted hover:bg-surface hover:text-foreground"
                  )}
                >
                  {cat}
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* Results Grid */}
        {loading ? (
          <div className="py-20 text-center">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
            <p className="mt-3 text-sm text-muted">Loading business listings...</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <AnimatePresence mode="popLayout">
              {businesses.map((business) => (
                <Link
                  key={business.id}
                  href={`/businesses/${business.id}`}
                  className="block h-full cursor-pointer"
                >
                  <motion.div
                    layout
                    initial={{ opacity: 0, y: 16 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: 16 }}
                    className="glass-card group flex h-full flex-col p-6 hover:shadow-md transition-shadow"
                  >
                    <div className="mb-5 flex items-start justify-between">
                       <div className="h-16 w-16 overflow-hidden rounded-xl border border-border bg-white flex items-center justify-center text-primary text-xl font-bold">
                        {business.logo ? (
                          <img
                            src={assetUrl(business.logo)}
                            alt={business.name}
                            className="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105"
                          />
                        ) : (
                          business.name?.[0] ?? "?"
                        )}
                      </div>
                      {business.rating && Number(business.rating) > 0 ? (
                        <div className="flex flex-col items-end gap-2">
                          <div className="inline-flex items-center gap-1.5 text-sm font-medium text-amber-500">
                            <Star size={14} className="fill-current" />
                            {Number(business.rating).toFixed(1)}
                          </div>
                        </div>
                      ) : null}
                    </div>

                    <div className="flex-1">
                      <h3 className="text-lg font-bold text-foreground transition-colors group-hover:text-primary">
                        {business.name}
                      </h3>
                      <p className="mt-1 inline-flex items-center gap-1.5 text-xs text-muted">
                        <MapPin size={13} className="text-primary" />
                        {business.location || "Mumbai"} • {business.category}
                      </p>
                      <p className="mt-3.5 text-xs leading-relaxed text-muted line-clamp-3">
                        {business.description || "No description provided yet."}
                      </p>
                    </div>
                  </motion.div>
                </Link>
              ))}
            </AnimatePresence>
          </div>
        )}

        {totalItems === 0 && !loading && (
          <div className="rounded-xl border border-dashed border-border py-20 text-center">
            <h3 className="text-lg font-semibold text-foreground">No businesses found</h3>
            <p className="mx-auto mt-2 max-w-xs text-sm text-muted">
              Try a different search term or category.
            </p>
          </div>
        )}

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="mt-12 flex items-center justify-center gap-2">
            <button
              onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
              className="flex h-10 w-10 items-center justify-center rounded-xl border border-border bg-white text-foreground transition-colors hover:bg-surface cursor-pointer"
              aria-label="Previous page"
            >
              <ChevronLeft size={18} />
            </button>
            <div className="flex gap-2">
              {[...Array(totalPages)].map((_, i) => (
                <button
                  key={i}
                  onClick={() => setCurrentPage(i + 1)}
                  className={cn(
                    "h-10 w-10 rounded-xl text-sm font-semibold transition-colors cursor-pointer",
                    currentPage === i + 1
                      ? "bg-primary text-white"
                      : "border border-border bg-white text-muted hover:bg-surface hover:text-foreground"
                  )}
                >
                  {i + 1}
                </button>
              ))}
            </div>
            <button
              onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
              className="flex h-10 w-10 items-center justify-center rounded-xl border border-border bg-white text-foreground transition-colors hover:bg-surface cursor-pointer"
              aria-label="Next page"
            >
              <ChevronRight size={18} />
            </button>
          </div>
        )}
      </div>

      {/* Submit Business Modal */}
      <AnimatePresence>
        {isSubmitOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto"
          >
            <div
              className="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
              onClick={() => setIsSubmitOpen(false)}
            />

            <motion.div
              initial={{ opacity: 0, scale: 0.96, y: 12 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.96, y: 12 }}
              transition={{ duration: 0.2 }}
              className="relative w-full max-w-lg rounded-2xl border border-border bg-white p-8 shadow-xl z-10"
            >
              <button
                onClick={() => setIsSubmitOpen(false)}
                className="absolute right-4 top-4 rounded-lg p-1.5 text-muted transition-colors hover:bg-surface hover:text-foreground"
                aria-label="Close"
              >
                <X className="h-5 w-5" />
              </button>

              <div className="mb-6 text-center">
                <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft text-primary">
                  <Briefcase className="h-6 w-6" />
                </div>
                <h2 className="text-xl font-bold text-foreground">Register your business</h2>
                <p className="mt-1 text-sm text-muted">
                  Submit your business details for listings in the directory
                </p>
              </div>

              {formError && (
                <div className="mb-4 rounded-xl bg-red-50 border border-red-100 p-3.5 text-center text-xs font-semibold text-red-600">
                  {formError}
                </div>
              )}

              {formSuccess && (
                <div className="mb-4 rounded-xl bg-emerald-50 border border-emerald-100 p-3.5 text-center text-xs font-semibold text-emerald-600 flex items-center gap-2">
                  <ShieldCheck className="h-4 w-4 shrink-0 text-emerald-600" />
                  <span>{formSuccess}</span>
                </div>
              )}

              <form className="space-y-4" onSubmit={handleFormSubmit}>
                <div className="space-y-1.5">
                  <label className="text-xs font-semibold text-muted">Business name</label>
                  <input
                    type="text"
                    required
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    placeholder="E.g. Nexus Technology"
                    className="w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none focus:border-primary transition-colors"
                  />
                </div>

                <div className="space-y-1.5">
                  <label className="text-xs font-semibold text-muted">Category</label>
                  <select
                    value={formData.category}
                    onChange={(e) => setFormData({ ...formData, category: e.target.value })}
                    className="w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none focus:border-primary transition-colors"
                  >
                    {categories.filter(c => c !== "All").map((cat) => (
                      <option key={cat} value={cat}>{cat}</option>
                    ))}
                  </select>
                </div>

                <div className="space-y-1.5">
                  <label className="text-xs font-semibold text-muted">Website URL</label>
                  <input
                    type="url"
                    value={formData.website}
                    onChange={(e) => setFormData({ ...formData, website: e.target.value })}
                    placeholder="https://example.com"
                    className="w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none focus:border-primary transition-colors"
                  />
                </div>

                <div className="space-y-1.5">
                  <label className="text-xs font-semibold text-muted">Core Services (Comma separated)</label>
                  <input
                    type="text"
                    value={formData.services}
                    onChange={(e) => setFormData({ ...formData, services: e.target.value })}
                    placeholder="E.g. Web Development, Cloud Services, UI Design"
                    className="w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none focus:border-primary transition-colors"
                  />
                </div>

                <div className="space-y-1.5">
                  <label className="text-xs font-semibold text-muted">Description</label>
                  <textarea
                    rows={4}
                    value={formData.description}
                    onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                    placeholder="What does your company do?"
                    className="w-full rounded-xl border border-border bg-white px-4 py-3 text-xs text-foreground outline-none focus:border-primary transition-colors resize-none"
                  />
                </div>

                <div className="pt-2">
                  <button
                    type="submit"
                    disabled={submitting}
                    className="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                  >
                    {submitting ? "Submitting..." : "Submit Registration"}
                  </button>
                </div>
              </form>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

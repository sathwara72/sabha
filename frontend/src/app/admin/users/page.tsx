"use client";

import { useEffect, useState, useMemo } from "react";
import { useRouter } from "next/navigation";
import { createPortal } from "react-dom";
import { fetchUsersAdmin, toggleUserBlock, deleteUserAdmin } from "@/lib/api";
import { assetUrl, hasMediaFile } from "@/lib/config";
import {
  Mail, ShieldCheck, Clock, ArrowUpRight, Search, Zap, X, Ban, UserCheck, ShieldAlert, Trash2, Briefcase, LayoutGrid, List
} from "lucide-react";
import ConfirmModal from "@/components/shared/ConfirmModal";
import Pagination from "@/components/shared/Pagination";

interface Business {
  id: number;
  name: string;
  category: string;
  tagline?: string;
  location?: string;
  description?: string;
  website?: string;
  phone?: string;
  email?: string;
}

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  created_at: string;
  phone?: string;
  city?: string;
  native_city?: string;
  birth_date?: string;
  anniversary_date?: string;
  residence_address?: string;
  avatar?: string;
  is_blocked?: boolean;
  business?: Business;
}

export default function AdminUsersPage() {
  const router = useRouter();
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [businessFilter, setBusinessFilter] = useState<"all" | "with_business" | "without_business">("all");
  const [viewMode, setViewMode] = useState<"card" | "table">("card");
  const [selectedUser, setSelectedUser] = useState<User | null>(null);
  const [blockingUser, setBlockingUser] = useState<User | null>(null);
  const [blockLoading, setBlockLoading] = useState(false);
  const [deletingUser, setDeletingUser] = useState<User | null>(null);
  const [deleteLoading, setDeleteLoading] = useState(false);

  // Server-side Pagination state
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalItems, setTotalItems] = useState(0);
  const [counts, setCounts] = useState<{ all: number; with_business: number; without_business: number }>({
    all: 0,
    with_business: 0,
    without_business: 0,
  });
  const itemsPerPage = 9;

  useEffect(() => {
    loadData();
  }, [currentPage, searchTerm, businessFilter]);

  async function loadData() {
    try {
      setLoading(true);
      const res = await fetchUsersAdmin(currentPage, itemsPerPage, searchTerm, businessFilter);
      
      let userList: User[] = [];
      if (res && res.paginator) {
        userList = res.paginator.data || [];
        setTotalPages(res.paginator.last_page || 1);
        setTotalItems(res.paginator.total || userList.length);
        if (res.counts) {
          setCounts(res.counts);
        }
      } else if (res && res.data && Array.isArray(res.data)) {
        userList = res.data;
        setTotalPages(res.last_page || 1);
        setTotalItems(res.total || res.data.length);
      } else if (Array.isArray(res)) {
        userList = res;
        setTotalPages(1);
        setTotalItems(res.length);
      }
      
      // Sort latest first
      const sortedData = [...userList].sort((a, b) => {
        const dateA = a.created_at ? new Date(a.created_at).getTime() : 0;
        const dateB = b.created_at ? new Date(b.created_at).getTime() : 0;
        if (dateB !== dateA) return dateB - dateA;
        return (b.id || 0) - (a.id || 0);
      });
      setUsers(sortedData);
    } catch (error) {
      console.error("Error loading users:", error);
    } finally {
      setLoading(false);
    }
  }

  const displayUsers = users;

  function handleToggleBlock(user: User) {
    if (user.role === "admin") {
      alert("Cannot block admin users.");
      return;
    }
    setBlockingUser(user);
  }

  async function handleConfirmToggleBlock() {
    if (!blockingUser) return;
    try {
      setBlockLoading(true);
      await toggleUserBlock(blockingUser.id);
      const updatedBlocked = !blockingUser.is_blocked;
      setUsers(prev => prev.map(u => u.id === blockingUser.id ? { ...u, is_blocked: updatedBlocked } : u));
      if (selectedUser?.id === blockingUser.id) {
        setSelectedUser(prev => prev ? { ...prev, is_blocked: updatedBlocked } : null);
      }
      setBlockingUser(null);
    } catch (err: any) {
      alert(err.message || "Failed to update block status");
    } finally {
      setBlockLoading(false);
    }
  }

  function handleDeleteUser(user: User) {
    if (user.role === "admin") {
      alert("Cannot delete admin users.");
      return;
    }
    setDeletingUser(user);
  }

  async function handleConfirmDeleteUser() {
    if (!deletingUser) return;
    try {
      setDeleteLoading(true);
      await deleteUserAdmin(deletingUser.id);
      setUsers(prev => prev.filter(u => u.id !== deletingUser.id));
      if (selectedUser?.id === deletingUser.id) {
        setSelectedUser(null);
      }
      setDeletingUser(null);
      // Reload current page if table is empty
      if (users.length === 1 && currentPage > 1) {
        setCurrentPage(prev => prev - 1);
      } else {
        loadData();
      }
    } catch (err: any) {
      alert(err.message || "Failed to delete member");
    } finally {
      setDeleteLoading(false);
    }
  }

  // Lock body scroll when modal is open
  useEffect(() => {
    if (selectedUser) {
      document.body.style.overflow = "hidden";
      document.documentElement.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
      document.documentElement.style.overflow = "";
    }
    return () => {
      document.body.style.overflow = "";
      document.documentElement.style.overflow = "";
    };
  }, [selectedUser]);

  return (
    <div className="space-y-3">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        {/* Search Bar */}
        <div className="relative flex-1 max-w-md w-full">
          <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <input
            type="text"
            placeholder="Search by name, email, phone number..."
            className="w-full rounded-xl border border-border bg-white py-2 pl-10 pr-4 text-xs font-medium text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
            value={searchTerm}
            onChange={(e) => {
              setSearchTerm(e.target.value);
              setCurrentPage(1);
            }}
          />
        </div>

        {/* Business Filter Tabs + View Mode Toggle */}
        <div className="flex items-center gap-2 self-start sm:self-auto flex-wrap">
          <div className="flex items-center gap-1 bg-surface p-1 rounded-xl border border-border">
            <button
              type="button"
              onClick={() => {
                setBusinessFilter("all");
                setCurrentPage(1);
              }}
              className={`inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer ${
                businessFilter === "all"
                  ? "bg-white text-foreground shadow-xs"
                  : "text-muted hover:text-foreground"
              }`}
            >
              All
              <span className="px-1.5 py-0.5 text-[10px] rounded-full font-bold bg-slate-200/60 text-slate-600">
                {counts.all || totalItems}
              </span>
            </button>

            <button
              type="button"
              onClick={() => {
                setBusinessFilter("with_business");
                setCurrentPage(1);
              }}
              className={`inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer ${
                businessFilter === "with_business"
                  ? "bg-white text-emerald-700 shadow-xs"
                  : "text-muted hover:text-foreground"
              }`}
              title="Members who have registered a business profile"
            >
              💼 Business Members
              <span className="px-1.5 py-0.5 text-[10px] rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                {counts.with_business}
              </span>
            </button>

            <button
              type="button"
              onClick={() => {
                setBusinessFilter("without_business");
                setCurrentPage(1);
              }}
              className={`inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer ${
                businessFilter === "without_business"
                  ? "bg-white text-foreground shadow-xs"
                  : "text-muted hover:text-foreground"
              }`}
            >
              👤 No Business
              <span className="px-1.5 py-0.5 text-[10px] rounded-full font-bold bg-slate-200/60 text-slate-600">
                {counts.without_business}
              </span>
            </button>
          </div>

          {/* Card / Table View Toggle */}
          <div className="flex items-center gap-0.5 bg-surface p-1 rounded-xl border border-border">
            <button
              type="button"
              onClick={() => setViewMode("card")}
              className={`p-1.5 rounded-lg transition-all cursor-pointer ${
                viewMode === "card"
                  ? "bg-white text-primary shadow-xs"
                  : "text-muted-foreground hover:text-foreground"
              }`}
              title="Card View"
            >
              <LayoutGrid size={15} />
            </button>
            <button
              type="button"
              onClick={() => setViewMode("table")}
              className={`p-1.5 rounded-lg transition-all cursor-pointer ${
                viewMode === "table"
                  ? "bg-white text-primary shadow-xs"
                  : "text-muted-foreground hover:text-foreground"
              }`}
              title="Table View"
            >
              <List size={15} />
            </button>
          </div>
        </div>
      </div>
      {loading ? (
        <div className="glass-card py-20 text-center rounded-2xl border border-border">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="mt-3 text-sm font-medium text-muted">Loading community members...</p>
        </div>
      ) : displayUsers.length === 0 ? (
        <div className="rounded-xl border border-dashed border-border py-20 text-center text-muted text-xs font-medium bg-white">
          No members match the selected filter criteria.
        </div>
      ) : viewMode === "card" ? (
        <div className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {displayUsers.map((user) => (
              <div
                key={user.id}
                onClick={() => {
                  if (user.business?.id) {
                    router.push(`/admin/businesses/${user.business.id}`);
                  } else {
                    setSelectedUser(user);
                  }
                }}
                className={`glass-card p-4.5 rounded-2xl border border-border flex flex-col justify-between bg-white shadow-xs transition-all hover:shadow-md gap-4 cursor-pointer hover:border-primary/50 ${
                  user.business?.id ? "hover:bg-slate-50/50" : ""
                }`}
              >
                {/* Top Section */}
                <div className="space-y-3">
                  <div className="flex items-start justify-between gap-3">
                    <div className="flex items-center gap-3 min-w-0">
                      {hasMediaFile(user.avatar) ? (
                        <img
                          src={assetUrl(user.avatar)}
                          alt={user.name}
                          className="h-11 w-11 rounded-2xl object-cover bg-white shrink-0 border border-border/80 shadow-xs p-0.5"
                        />
                      ) : (
                        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-primary-soft font-bold text-sm text-primary shadow-xs">
                          {user.name?.[0] ?? "?"}
                        </div>
                      )}
                      <div className="min-w-0">
                        <h4 className="text-sm font-extrabold text-slate-900 leading-tight truncate">{user.name}</h4>
                        <p className="mt-0.5 flex items-center gap-1 text-xs font-medium text-slate-500 truncate">
                          <Mail size={12} className="text-primary/70 shrink-0" /> <span className="truncate">{user.email}</span>
                        </p>
                      </div>
                    </div>

                    <div className="flex flex-col items-end gap-1 shrink-0">
                      <span className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold ${
                        user.role === "admin"
                          ? "bg-primary-soft text-primary border border-primary/20"
                          : "bg-slate-100 text-slate-600 border border-slate-200"
                      }`}>
                        <ShieldCheck size={11} />
                        {user.role}
                      </span>
                      {user.is_blocked && (
                        <span className="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-[10px] font-bold text-rose-600 border border-rose-200">
                          <Ban size={10} /> Blocked
                        </span>
                      )}
                    </div>
                  </div>

                  {/* Business Status Card Box */}
                  <div
                    onClick={(e) => {
                      if (user.business?.id) {
                        e.stopPropagation();
                        router.push(`/admin/businesses/${user.business.id}`);
                      }
                    }}
                    className={`rounded-xl p-2.5 border flex items-center justify-between gap-2 transition-colors ${
                      user.business?.id
                        ? "bg-emerald-50/40 border-emerald-200/80 hover:bg-emerald-50 hover:border-emerald-300 cursor-pointer"
                        : "bg-surface border-border/70"
                    }`}
                  >
                    <div className="min-w-0">
                      <p className="text-[10px] font-bold uppercase tracking-wider text-muted-foreground">Business Status</p>
                      {user.business?.name ? (
                        <p className="text-xs font-bold text-emerald-800 truncate mt-0.5 flex items-center gap-1">
                          <Briefcase size={12} className="text-emerald-600 shrink-0" />
                          <span className="truncate">{user.business.name}</span>
                          <ArrowUpRight size={11} className="text-emerald-600 shrink-0" />
                        </p>
                      ) : (
                        <p className="text-xs font-medium text-slate-400 mt-0.5">No business profile</p>
                      )}
                    </div>
                    {user.business?.name ? (
                      <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100/80 text-emerald-700 border border-emerald-200 shrink-0">
                        Registered
                      </span>
                    ) : (
                      <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 shrink-0">
                        None
                      </span>
                    )}
                  </div>

                  {/* Joined Date */}
                  <div className="flex items-center justify-between text-xs text-slate-500 pt-1">
                    <span className="text-[11px] font-medium text-muted flex items-center gap-1">
                      <Clock size={11} /> Joined {new Date(user.created_at).toLocaleDateString()}
                    </span>
                    {user.phone && (
                      <span className="text-[11px] font-semibold text-slate-600">
                        📞 {user.phone}
                      </span>
                    )}
                  </div>
                </div>

                {/* Card Footer Actions */}
                <div className="pt-3 border-t border-border flex items-center justify-between gap-2">
                  <div className="flex items-center gap-1.5">
                    {user.role !== "admin" && (
                      <>
                        <button
                          type="button"
                          onClick={(e) => {
                            e.stopPropagation();
                            handleToggleBlock(user);
                          }}
                          className={`inline-flex items-center justify-center gap-1 rounded-xl px-2.5 py-1 text-xs font-bold border transition-all active:scale-95 cursor-pointer shadow-xs ${
                            user.is_blocked
                              ? "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
                              : "bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100"
                          }`}
                          title={user.is_blocked ? "Unblock Member" : "Block Member"}
                        >
                          {user.is_blocked ? (
                            <>
                              <UserCheck size={12} /> Unblock
                            </>
                          ) : (
                            <>
                              <Ban size={12} /> Block
                            </>
                          )}
                        </button>

                        <button
                          type="button"
                          onClick={(e) => {
                            e.stopPropagation();
                            handleDeleteUser(user);
                          }}
                          className="inline-flex h-7 w-7 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 transition-all hover:bg-rose-100 hover:text-rose-700 active:scale-95 cursor-pointer shadow-xs"
                          title="Delete Member"
                        >
                          <Trash2 size={13} />
                        </button>
                      </>
                    )}
                  </div>

                  <button
                    type="button"
                    onClick={(e) => {
                      e.stopPropagation();
                      setSelectedUser(user);
                    }}
                    className="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-2.5 py-1 text-xs font-bold text-slate-700 transition-all hover:bg-slate-50 hover:text-slate-900 active:scale-95 cursor-pointer shadow-xs ml-auto"
                  >
                    <span>View Profile</span>
                    <ArrowUpRight size={13} />
                  </button>
                </div>
              </div>
            ))}
          </div>

          {/* API Driven Pagination for Card View */}
          <div className="pt-2">
            <Pagination
              currentPage={currentPage}
              totalPages={totalPages}
              totalItems={totalItems}
              itemsPerPage={itemsPerPage}
              onPageChange={(page) => {
                setCurrentPage(page);
                window.scrollTo({ top: 0, behavior: "smooth" });
              }}
              itemLabel="members"
            />
          </div>
        </div>
      ) : (
        /* Table Layout Option */
        <div className="bg-white rounded-2xl border border-border/80 shadow-xs overflow-hidden flex flex-col">
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-slate-50/90 border-b border-border/70 backdrop-blur-sm">
                  <th className="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Member</th>
                  <th className="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Joined</th>
                  <th className="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Role / Status</th>
                  <th className="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border/60">
                {displayUsers.map((user) => (
                  <tr
                    key={user.id}
                    onClick={() => {
                      if (user.business?.id) {
                        router.push(`/admin/businesses/${user.business.id}`);
                      } else {
                        setSelectedUser(user);
                      }
                    }}
                    className={`transition-colors hover:bg-slate-50/70 ${user.business?.id ? "cursor-pointer" : ""}`}
                  >
                    <td className="px-5 py-3.5">
                      <div className="flex items-center gap-3">
                        {hasMediaFile(user.avatar) ? (
                          <img
                            src={assetUrl(user.avatar)}
                            alt={user.name}
                            className="h-9 w-9 rounded-xl object-contain bg-white shrink-0 border border-border/80 shadow-xs p-0.5"
                          />
                        ) : (
                          <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft font-bold text-xs text-primary shadow-xs">
                            {user.name?.[0] ?? "?"}
                          </div>
                        )}
                        <div>
                          <p className="text-xs font-extrabold text-slate-900 leading-tight">{user.name}</p>
                          <p className="mt-0.5 flex items-center gap-1 text-[11px] font-medium text-slate-500">
                            <Mail size={11} className="text-primary/70" /> {user.email}
                          </p>
                          {user.business?.name && (
                            <span className="mt-1 inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200/70">
                              <Briefcase size={10} /> {user.business.name}
                            </span>
                          )}
                        </div>
                      </div>
                    </td>
                    <td className="px-5 py-3.5">
                      <div className="flex flex-col">
                        <p className="text-xs font-semibold text-slate-800">
                          {new Date(user.created_at).toLocaleDateString()}
                        </p>
                        <p className="flex items-center gap-1 text-[10px] text-slate-400 mt-0.5">
                          <Clock size={10} /> {new Date(user.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                        </p>
                      </div>
                    </td>
                    <td className="px-5 py-3.5">
                      <div className="flex items-center gap-1.5 flex-wrap">
                        <span className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold ${
                          user.role === "admin"
                            ? "bg-primary-soft text-primary border border-primary/20"
                            : "bg-slate-100 text-slate-600 border border-slate-200"
                        }`}>
                          <ShieldCheck size={11} />
                          {user.role}
                        </span>
                        {user.is_blocked && (
                          <span className="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-[10px] font-bold text-rose-600 border border-rose-200">
                            <Ban size={10} /> Blocked
                          </span>
                        )}
                      </div>
                    </td>
                    <td className="px-5 py-3.5 text-right">
                      <div className="flex items-center justify-end gap-2">
                        {user.role !== "admin" && (
                          <>
                            <button
                              onClick={(e) => {
                                e.stopPropagation();
                                handleToggleBlock(user);
                              }}
                              className={`inline-flex items-center justify-center gap-1 rounded-xl px-2.5 py-1.5 text-[10px] font-extrabold border transition-all active:scale-95 cursor-pointer shadow-xs ${
                                user.is_blocked
                                  ? "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
                                  : "bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100"
                              }`}
                              title={user.is_blocked ? "Unblock Member" : "Block Member"}
                            >
                              {user.is_blocked ? (
                                <>
                                  <UserCheck size={12} /> Unblock
                                </>
                              ) : (
                                <>
                                  <Ban size={12} /> Block
                                </>
                              )}
                            </button>
                            <button
                              onClick={(e) => {
                                e.stopPropagation();
                                handleDeleteUser(user);
                              }}
                              className="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 transition-all hover:bg-rose-100 hover:text-rose-700 active:scale-95 cursor-pointer shadow-xs"
                              title="Delete Member"
                            >
                              <Trash2 size={13} />
                            </button>
                          </>
                        )}
                        <button
                          onClick={(e) => {
                            e.stopPropagation();
                            setSelectedUser(user);
                          }}
                          className="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition-all hover:bg-slate-50 hover:text-slate-900 active:scale-95 cursor-pointer shadow-xs"
                          title="View Member Details"
                        >
                          <ArrowUpRight size={14} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          {/* API Driven Pagination for Table View */}
          <div className="p-4 bg-white border-t border-border rounded-b-2xl">
            <Pagination
              currentPage={currentPage}
              totalPages={totalPages}
              totalItems={totalItems}
              itemsPerPage={itemsPerPage}
              onPageChange={(page) => {
                setCurrentPage(page);
                window.scrollTo({ top: 0, behavior: "smooth" });
              }}
              itemLabel="members"
            />
          </div>
        </div>
      )}

      {selectedUser && createPortal(
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          {/* Backdrop */}
          <div
            className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            onClick={() => setSelectedUser(null)}
          />

          {/* Modal Content */}
          <div className="relative w-full max-w-2xl transform rounded-2xl bg-white p-5 shadow-2xl transition-all border border-border animate-in fade-in zoom-in-95 duration-200">
            {/* Header */}
            <div className="flex items-start justify-between border-b border-border pb-4 mb-4">
              <div className="flex items-center gap-3">
                {hasMediaFile(selectedUser.avatar) ? (
                  <img
                    src={assetUrl(selectedUser.avatar)}
                    alt={selectedUser.name}
                    className="h-12 w-12 rounded-xl object-contain bg-white border border-border"
                  />
                ) : (
                  <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-base font-bold text-primary">
                    {selectedUser.name?.[0] ?? "?"}
                  </div>
                )}
                <div>
                  <div className="flex items-center gap-2">
                    <h3 className="text-base font-bold text-foreground">{selectedUser.name}</h3>
                    <span className={`inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold ${
                      selectedUser.role === "admin"
                        ? "bg-primary-soft text-primary"
                        : "bg-slate-100 text-slate-700"
                    }`}>
                      {selectedUser.role}
                    </span>
                    {selectedUser.is_blocked && (
                      <span className="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600 border border-rose-200">
                        <Ban size={10} /> Blocked
                      </span>
                    )}
                  </div>
                  <p className="text-xs text-muted-foreground mt-0.5">{selectedUser.email}</p>
                </div>
              </div>

              <button
                onClick={() => setSelectedUser(null)}
                className="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors"
              >
                <X size={18} />
              </button>
            </div>

            {/* Grid Content */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5 max-h-[60vh] overflow-y-auto pr-1">
              {/* Profile Details */}
              <div className="space-y-3.5">
                <h4 className="text-xs font-bold uppercase tracking-wider text-muted-foreground">Profile Details</h4>
                
                <div className="space-y-2 text-xs">
                  <div>
                    <span className="text-muted-foreground block mb-0.5">Phone Number</span>
                    <span className="font-semibold text-foreground">{selectedUser.phone || "Not specified"}</span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">City</span>
                    <span className="font-semibold text-foreground">{selectedUser.city || "Not specified"}</span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">Native City (વતન)</span>
                    <span className="font-semibold text-foreground">{selectedUser.native_city || "Not specified"}</span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">Birth Date (જન્મ તારીખ)</span>
                    <span className="font-semibold text-foreground">{selectedUser.birth_date || "Not specified"}</span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">Marriage / Anniversary Date</span>
                    <span className="font-semibold text-foreground">{selectedUser.anniversary_date || "Not specified"}</span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">Residence Address</span>
                    <span className="font-semibold text-foreground">{selectedUser.residence_address || "Not specified"}</span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">Joined Date</span>
                    <span className="font-semibold text-foreground">
                      {new Date(selectedUser.created_at).toLocaleDateString()} {new Date(selectedUser.created_at).toLocaleTimeString()}
                    </span>
                  </div>
                </div>
              </div>

              {/* Business Details */}
              <div className="space-y-3.5 border-t md:border-t-0 md:border-l border-border pt-4 md:pt-0 md:pl-5">
                <h4 className="text-xs font-bold uppercase tracking-wider text-muted-foreground">Registered Business</h4>

                {selectedUser.business ? (
                  <div className="space-y-2 text-xs">
                    <div>
                      <span className="text-muted-foreground block mb-0.5">Business Name</span>
                      <span className="font-semibold text-foreground">{selectedUser.business.name}</span>
                    </div>

                    <div>
                      <span className="text-muted-foreground block mb-0.5">Category</span>
                      <span className="font-semibold text-foreground">{selectedUser.business.category}</span>
                    </div>

                    {selectedUser.business.tagline && (
                      <div>
                        <span className="text-muted-foreground block mb-0.5">Tagline</span>
                        <span className="font-medium text-foreground">{selectedUser.business.tagline}</span>
                      </div>
                    )}

                    {selectedUser.business.location && (
                      <div>
                        <span className="text-muted-foreground block mb-0.5">Location</span>
                        <span className="font-semibold text-foreground">{selectedUser.business.location}</span>
                      </div>
                    )}

                    {selectedUser.business.website && (
                      <div>
                        <span className="text-muted-foreground block mb-0.5">Website</span>
                        <a
                          href={selectedUser.business.website}
                          target="_blank"
                          rel="noreferrer"
                          className="font-semibold text-primary hover:underline flex items-center gap-1 inline-flex"
                        >
                          {selectedUser.business.website}
                          <ArrowUpRight size={12} />
                        </a>
                      </div>
                    )}

                    {selectedUser.business.description && (
                      <div>
                        <span className="text-muted-foreground block mb-0.5">Description</span>
                        <p className="text-foreground leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                          {selectedUser.business.description}
                        </p>
                      </div>
                    )}
                  </div>
                ) : (
                  <div className="flex flex-col items-center justify-center py-10 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <span className="text-xs text-muted-foreground font-medium">No business profile registered.</span>
                  </div>
                )}
              </div>
            </div>

            {/* Footer */}
            <div className="flex justify-between items-center border-t border-border pt-4 mt-4">
              <div className="flex items-center gap-2">
                {selectedUser.role !== "admin" && (
                  <>
                    <button
                      onClick={() => handleToggleBlock(selectedUser)}
                      className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border ${
                        selectedUser.is_blocked
                          ? "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
                          : "bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100"
                      }`}
                    >
                      {selectedUser.is_blocked ? (
                        <>
                          <UserCheck size={14} /> Unblock User
                        </>
                      ) : (
                        <>
                          <Ban size={14} /> Block User
                        </>
                      )}
                    </button>
                    <button
                      onClick={() => handleDeleteUser(selectedUser)}
                      className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100"
                    >
                      <Trash2 size={14} /> Delete Member
                    </button>
                  </>
                )}
              </div>
              <button
                onClick={() => setSelectedUser(null)}
                className="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground transition-colors hover:bg-slate-50 active:scale-95"
              >
                Close
              </button>
            </div>
          </div>
        </div>,
        document.body
      )}

      {/* Block User Custom Confirm Modal */}
      <ConfirmModal
        isOpen={Boolean(blockingUser)}
        title={blockingUser?.is_blocked ? "Unblock Member" : "Block Member"}
        message={blockingUser ? `Are you sure you want to ${blockingUser.is_blocked ? "unblock" : "block"} member "${blockingUser.name}"? ${blockingUser.is_blocked ? "They will regain access to the platform." : "Their session will be terminated immediately."}` : ""}
        confirmLabel={blockingUser?.is_blocked ? "Unblock Member" : "Block Member"}
        variant={blockingUser?.is_blocked ? "success" : "warning"}
        isLoading={blockLoading}
        onConfirm={handleConfirmToggleBlock}
        onCancel={() => setBlockingUser(null)}
      />

      {/* Delete User Custom Confirm Modal */}
      <ConfirmModal
        isOpen={Boolean(deletingUser)}
        title="Delete Member"
        message={deletingUser ? `Are you sure you want to permanently delete member "${deletingUser.name}" (${deletingUser.email})? This action cannot be undone.` : ""}
        confirmLabel="Delete Member"
        variant="danger"
        isLoading={deleteLoading}
        onConfirm={handleConfirmDeleteUser}
        onCancel={() => setDeletingUser(null)}
      />
    </div>
  );
}

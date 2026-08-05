"use client";

import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import { fetchUsersAdmin, toggleUserBlock } from "@/lib/api";
import { assetUrl, hasMediaFile } from "@/lib/config";
import {
  Mail, ShieldCheck, Clock, ArrowUpRight, Search, Zap, X, Ban, UserCheck, ShieldAlert
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
  designation?: string;
  company?: string;
  bio?: string;
  avatar?: string;
  is_blocked?: boolean;
  business?: Business;
}

export default function AdminUsersPage() {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedUser, setSelectedUser] = useState<User | null>(null);
  const [blockingUser, setBlockingUser] = useState<User | null>(null);
  const [blockLoading, setBlockLoading] = useState(false);
  
  // Pagination state
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 10;

  useEffect(() => {
    window.scrollTo({ top: 0, behavior: "instant" });
  }, [currentPage]);

  useEffect(() => {
    loadData();
  }, []);

  async function loadData() {
    try {
      const data = await fetchUsersAdmin();
      setUsers(data);
    } catch (error) {
      console.error("Error loading users:", error);
    } finally {
      setLoading(false);
    }
  }

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

  // Reset page to 1 when search query changes
  useEffect(() => {
    setCurrentPage(1);
  }, [searchTerm]);

  const filteredUsers = users.filter(u =>
    u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    u.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
  const paginatedUsers = filteredUsers.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  return (
    <div className="space-y-3">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div className="flex flex-col">
          <div className="flex items-center gap-2">
            <h1 className="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Members</h1>
            <span className="rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-bold text-primary">
              {loading ? "..." : users.length}
            </span>
          </div>
          <p className="text-xs text-muted">Manage community members</p>
        </div>

        <div className="relative w-full sm:w-72">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
          <input
            type="text"
            placeholder="Search members..."
            className="w-full rounded-xl border border-border bg-white py-1.5 pl-9 pr-4 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
      </div>

      {loading ? (
        <div className="glass-card py-20 text-center rounded-2xl border border-border">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent" />
          <p className="mt-3 text-sm font-medium text-muted">Loading community members...</p>
        </div>
      ) : (
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
                {paginatedUsers.map((user) => (
                  <tr key={user.id} className="transition-colors hover:bg-slate-50/70">
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
                          <button
                            onClick={() => handleToggleBlock(user)}
                            className={`inline-flex items-center justify-center gap-1 rounded-xl px-2.5 py-1.5 text-[10px] font-extrabold border transition-all active:scale-95 cursor-pointer shadow-xs ${
                              user.is_blocked
                                ? "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
                                : "bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100"
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
                        )}
                        <button
                          onClick={() => setSelectedUser(user)}
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
          {filteredUsers.length === 0 && !loading && (
            <div className="py-20 text-center">
              <p className="text-sm text-slate-500">No members found.</p>
            </div>
          )}

          {/* Pagination */}
          <Pagination
            currentPage={currentPage}
            totalPages={totalPages}
            totalItems={filteredUsers.length}
            itemsPerPage={itemsPerPage}
            onPageChange={(page) => setCurrentPage(page)}
            itemLabel="members"
          />
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
                    <span className="text-muted-foreground block mb-0.5">Designation & Company</span>
                    <span className="font-semibold text-foreground">
                      {selectedUser.designation || selectedUser.company
                        ? `${selectedUser.designation || "Member"} at ${selectedUser.company || "Sabha"}`
                        : "Not specified"}
                    </span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">Phone Number</span>
                    <span className="font-semibold text-foreground">{selectedUser.phone || "Not specified"}</span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">City / Location</span>
                    <span className="font-semibold text-foreground">{selectedUser.city || "Not specified"}</span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">Joined Date</span>
                    <span className="font-semibold text-foreground">
                      {new Date(selectedUser.created_at).toLocaleDateString()} {new Date(selectedUser.created_at).toLocaleTimeString()}
                    </span>
                  </div>

                  <div>
                    <span className="text-muted-foreground block mb-0.5">Bio</span>
                    <p className="text-foreground leading-relaxed italic bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                      {selectedUser.bio || "No bio written yet."}
                    </p>
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
              <div>
                {selectedUser.role !== "admin" && (
                  <button
                    onClick={() => handleToggleBlock(selectedUser)}
                    className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all border ${
                      selectedUser.is_blocked
                        ? "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
                        : "bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100"
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
        variant={blockingUser?.is_blocked ? "success" : "danger"}
        isLoading={blockLoading}
        onConfirm={handleConfirmToggleBlock}
        onCancel={() => setBlockingUser(null)}
      />
    </div>
  );
}

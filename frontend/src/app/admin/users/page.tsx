"use client";

import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import { fetchUsersAdmin } from "@/lib/api";
import {
  Mail, ShieldCheck, Clock, ArrowUpRight, Search, Zap, X
} from "lucide-react";

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
  business?: Business;
}

export default function AdminUsersPage() {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedUser, setSelectedUser] = useState<User | null>(null);

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

  const filteredUsers = users.filter(u =>
    u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    u.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-3">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div className="flex flex-col">
          <h1 className="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Members</h1>
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

      <div className="glass-card overflow-hidden p-0">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="bg-surface border-b border-border">
              <th className="px-4 py-2 text-xs font-bold text-muted uppercase tracking-wider">Member</th>
              <th className="px-4 py-2 text-xs font-bold text-muted uppercase tracking-wider">Joined</th>
              <th className="px-4 py-2 text-xs font-bold text-muted uppercase tracking-wider">Role</th>
              <th className="px-4 py-2 text-xs font-bold text-muted uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-border">
            {filteredUsers.map((user) => (
              <tr key={user.id} className="transition-colors hover:bg-surface/50">
                <td className="px-4 py-2.5">
                  <div className="flex items-center gap-2.5">
                    {user.avatar ? (
                      <img
                        src={user.avatar}
                        alt={user.name}
                        className="h-8 w-8 rounded-lg object-cover shrink-0 border border-border"
                      />
                    ) : (
                      <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft font-semibold text-xs text-primary">
                        {user.name?.[0] ?? "?"}
                      </div>
                    )}
                    <div>
                      <p className="text-xs font-bold text-foreground leading-tight">{user.name}</p>
                      <p className="mt-0.5 flex items-center gap-1 text-[11px] text-muted-foreground">
                        <Mail size={11} className="text-primary/70" /> {user.email}
                      </p>
                    </div>
                  </div>
                </td>
                <td className="px-4 py-2.5">
                  <div className="flex flex-col">
                    <p className="text-xs font-medium text-foreground">
                      {new Date(user.created_at).toLocaleDateString()}
                    </p>
                    <p className="flex items-center gap-1 text-[10px] text-muted-foreground/80 mt-0.5">
                      <Clock size={10} /> {new Date(user.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                    </p>
                  </div>
                </td>
                <td className="px-4 py-2.5">
                  <span className={`inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold ${
                    user.role === "admin"
                      ? "bg-primary-soft text-primary"
                      : "bg-surface text-muted-foreground"
                  }`}>
                    <ShieldCheck size={11} />
                    {user.role}
                  </span>
                </td>
                <td className="px-4 py-2.5 text-right">
                  <button
                    onClick={() => setSelectedUser(user)}
                    className="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-border bg-white text-muted-foreground transition-colors hover:bg-surface hover:text-foreground active:scale-95"
                    title="View Member Details"
                  >
                    <ArrowUpRight size={14} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        {filteredUsers.length === 0 && !loading && (
          <div className="py-20 text-center">
            <p className="text-sm text-muted">No members found.</p>
          </div>
        )}
      </div>

     

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
                {selectedUser.avatar ? (
                  <img
                    src={selectedUser.avatar}
                    alt={selectedUser.name}
                    className="h-12 w-12 rounded-xl object-cover border border-border"
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
            <div className="flex justify-end gap-2.5 border-t border-border pt-4 mt-4">
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
    </div>
  );
}

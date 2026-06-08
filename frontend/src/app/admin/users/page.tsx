"use client";

import { useEffect, useState } from "react";
import { fetchUsersAdmin } from "@/lib/api";
import {
  Mail, ShieldCheck, Clock, ArrowUpRight, Search, Zap
} from "lucide-react";

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  created_at: string;
}

export default function AdminUsersPage() {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");

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

  const filteredUsers = users.filter(u =>
    u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    u.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-8">
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div className="flex flex-col gap-2">
          <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Members</h1>
          <p className="text-sm text-muted">Manage community members</p>
        </div>

        <div className="relative w-full md:w-80">
          <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <input
            type="text"
            placeholder="Search members..."
            className="w-full rounded-xl border border-border bg-white py-2.5 pl-11 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
      </div>

      <div className="glass-card overflow-hidden p-0">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="bg-surface border-b border-border">
              <th className="px-6 py-4 text-sm font-semibold text-muted">Member</th>
              <th className="px-6 py-4 text-sm font-semibold text-muted">Joined</th>
              <th className="px-6 py-4 text-sm font-semibold text-muted">Role</th>
              <th className="px-6 py-4 text-sm font-semibold text-muted">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-border">
            {filteredUsers.map((user) => (
              <tr key={user.id} className="transition-colors hover:bg-surface">
                <td className="px-6 py-4">
                  <div className="flex items-center gap-3">
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-soft font-semibold text-primary">
                      {user.name?.[0] ?? "?"}
                    </div>
                    <div>
                      <p className="text-sm font-semibold text-foreground">{user.name}</p>
                      <p className="mt-0.5 flex items-center gap-1.5 text-sm text-muted"><Mail size={13} className="text-primary" /> {user.email}</p>
                    </div>
                  </div>
                </td>
                <td className="px-6 py-4">
                  <div className="flex flex-col gap-0.5">
                    <p className="text-sm font-medium text-foreground">
                      {new Date(user.created_at).toLocaleDateString()}
                    </p>
                    <p className="flex items-center gap-1.5 text-sm text-muted-foreground"><Clock size={12} /> {new Date(user.created_at).toLocaleTimeString()}</p>
                  </div>
                </td>
                <td className="px-6 py-4">
                  <span className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium ${
                    user.role === "admin"
                      ? "bg-primary-soft text-primary"
                      : "bg-surface text-muted"
                  }`}>
                    <ShieldCheck size={13} />
                    {user.role}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <button className="flex h-9 w-9 items-center justify-center rounded-lg border border-border bg-white text-muted transition-colors hover:bg-surface hover:text-foreground">
                    <ArrowUpRight size={16} />
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

      <div className="glass-card p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div className="flex items-center gap-4">
          <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-accent">
            <Zap className="h-6 w-6" />
          </div>
          <div>
            <h4 className="text-lg font-semibold text-foreground mb-1">Member management</h4>
            <p className="max-w-2xl text-sm leading-relaxed text-muted">
              Open a member's detail page to review their activity, role, and account history.
            </p>
          </div>
        </div>
        <button className="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition-all hover:opacity-90 active:scale-[0.98] whitespace-nowrap">
          View details
        </button>
      </div>
    </div>
  );
}

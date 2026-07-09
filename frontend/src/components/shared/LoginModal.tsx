"use client";

import Link from "next/link";
import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Mail, Lock, ArrowRight, X, Sparkles } from "lucide-react";
import { useAuth } from "@/lib/auth";

export default function LoginModal() {
  const { isLoginOpen, closeLogin, login, demoLogin, openRegister } = useAuth();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [demoLoading, setDemoLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await login(email, password);
      setEmail("");
      setPassword("");
    } catch (err: any) {
      setError(err.message || "Failed to log in.");
    } finally {
      setLoading(false);
    }
  };

  const handleDemoLogin = async () => {
    setError("");
    setDemoLoading(true);
    try {
      await demoLogin();
    } catch (err: any) {
      setError(err.message || "Could not start a demo session.");
    } finally {
      setDemoLoading(false);
    }
  };

  return (
    <AnimatePresence>
      {isLoginOpen && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          className="fixed inset-0 z-[100] flex items-center justify-center p-4"
        >
          <div
            className="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
            onClick={closeLogin}
          />

          <motion.div
            initial={{ opacity: 0, scale: 0.96, y: 12 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.96, y: 12 }}
            transition={{ duration: 0.2 }}
            className="relative w-full max-w-sm rounded-2xl border border-border bg-white p-5 shadow-xl"
          >
            <button
              onClick={closeLogin}
              className="absolute right-3 top-3 rounded-lg p-1 text-muted transition-colors hover:bg-surface hover:text-foreground"
              aria-label="Close"
            >
              <X className="h-4 w-4" />
            </button>

            {/* Header */}
            <div className="mb-4 text-center">
              <img src="/logo.png" alt="SABHA" className="mx-auto h-9 w-9 rounded-full object-contain mb-2.5" />
              <h2 className="text-base font-bold text-foreground">Log in to continue</h2>
              <p className="mt-0.5 text-xs text-muted">Please log in to view this content.</p>
            </div>

            {error && (
              <div className="mb-3 rounded-xl bg-red-50 border border-red-100 p-2.5 text-center text-xs font-semibold text-red-600">
                {error}
              </div>
            )}

            <form className="space-y-3" onSubmit={handleSubmit}>
              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">Email</label>
                <div className="group relative">
                  <Mail className="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                  <input
                    type="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="you@example.com"
                    className="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                  />
                </div>
              </div>

              <div className="space-y-1">
                <div className="flex items-center justify-between">
                  <label className="text-xs font-semibold text-foreground">Password</label>
                  <Link
                    href="/forgot-password"
                    onClick={closeLogin}
                    className="text-[11px] font-semibold text-primary hover:opacity-80 transition-opacity"
                  >
                    Forgot password?
                  </Link>
                </div>
                <div className="group relative">
                  <Lock className="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                  <input
                    type="password"
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="Enter your password"
                    className="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                  />
                </div>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="group inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60"
              >
                {loading ? "Logging in..." : "Log in"}
                {!loading && <ArrowRight className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />}
              </button>
            </form>

            <div className="my-3 flex items-center gap-3">
              <span className="h-px flex-1 bg-border" />
              <span className="text-[10px] font-semibold uppercase tracking-wide text-muted-foreground">or</span>
              <span className="h-px flex-1 bg-border" />
            </div>

            <button
              type="button"
              onClick={handleDemoLogin}
              disabled={demoLoading || loading}
              className="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-dashed border-primary/40 bg-primary-soft px-4 py-2 text-xs font-semibold text-primary transition-all hover:bg-primary/10 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
            >
              <Sparkles className="h-3.5 w-3.5" />
              {demoLoading ? "Starting demo..." : "Continue with a demo account"}
            </button>
            <p className="mt-1.5 text-center text-[10px] text-muted-foreground">
              Explore instantly — no signup or password needed.
            </p>

            <p className="mt-4 text-center text-xs text-muted">
              Don&apos;t have an account?{" "}
              <button
                onClick={() => { closeLogin(); openRegister(); }}
                className="font-semibold text-primary hover:opacity-80 transition-opacity"
              >
                Create one
              </button>
            </p>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}

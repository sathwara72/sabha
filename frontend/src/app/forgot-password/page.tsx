"use client";

import { useState } from "react";
import Link from "next/link";
import { Mail, ArrowLeft, ShieldCheck, ArrowRight } from "lucide-react";
import { motion } from "framer-motion";
import { forgotPassword } from "@/lib/api";

export default function ForgotPasswordPage() {
  const [email, setEmail] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setSuccess("");
    setLoading(true);

    try {
      const res = await forgotPassword(email);
      setSuccess(
        res.message ||
          "Password reset link sent! (Simulated password: password123)"
      );
      setEmail("");
    } catch (err: any) {
      setError(err.message || "Failed to process password reset request.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-background font-outfit px-6 pt-24 pb-16">
      <motion.div
        initial={{ opacity: 0, y: 12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.35 }}
        className="w-full max-w-md"
      >
        <div className="text-center mb-8">
          <Link href="/" className="inline-flex items-center gap-2.5 mb-8 group">
            <img src="/logo.png" alt="SABHA" className="h-12 w-12 rounded-full object-contain" />
            <span className="text-2xl font-bold tracking-tight text-primary-dark">SABHA</span>
          </Link>
          <h1 className="text-3xl font-bold text-foreground mb-2">Reset password</h1>
          <p className="text-sm text-muted">We will send you instructions to reset your account password</p>
        </div>

        <div className="bg-white border border-border rounded-2xl shadow-sm p-8 md:p-10">
          {error && (
            <div className="mb-5 rounded-xl bg-red-50 border border-red-100 p-4 text-center text-xs font-semibold text-red-600">
              {error}
            </div>
          )}

          {success ? (
            <div className="space-y-6">
              <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-sm font-semibold text-emerald-800 flex flex-col items-center gap-3">
                <ShieldCheck className="h-8 w-8 text-emerald-600 animate-pulse" />
                <p className="text-center leading-relaxed">{success}</p>
              </div>
              <Link
                href="/login"
                className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]"
              >
                Log in to continue <ArrowRight className="w-4 h-4" />
              </Link>
            </div>
          ) : (
            <form className="space-y-5" onSubmit={handleSubmit}>
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground">Email Address</label>
                <div className="relative group">
                  <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="email"
                    required
                    placeholder="you@example.com"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors"
                  />
                </div>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
              >
                {loading ? "Requesting..." : "Send instructions"}
              </button>

              <div className="pt-2 text-center">
                <Link
                  href="/login"
                  className="inline-flex items-center gap-1.5 text-xs font-semibold text-muted hover:text-primary transition-colors"
                >
                  <ArrowLeft className="w-3.5 h-3.5" /> Back to log in
                </Link>
              </div>
            </form>
          )}
        </div>
      </motion.div>
    </div>
  );
}

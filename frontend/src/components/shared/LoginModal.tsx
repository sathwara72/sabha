"use client";

import Link from "next/link";
import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Mail, Lock, ArrowRight, X, Eye, EyeOff } from "lucide-react";
import { useAuth } from "@/lib/auth";
import { useLanguage } from "@/lib/language";

export default function LoginModal() {
  const { isLoginOpen, closeLogin, login, openRegister } = useAuth();
  const { t } = useLanguage();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!isLoginOpen) {
      setEmail("");
      setPassword("");
      setShowPassword(false);
      setError("");
      setLoading(false);
    }
  }, [isLoginOpen]);

  const handleClose = () => {
    setEmail("");
    setPassword("");
    setShowPassword(false);
    setError("");
    setLoading(false);
    closeLogin();
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await login(email, password);
      setEmail("");
      setPassword("");
      setShowPassword(false);
    } catch (err: any) {
      setError(err.message || t("auth.login_btn"));
    } finally {
      setLoading(false);
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
            onClick={handleClose}
          />

          <motion.div
            initial={{ opacity: 0, scale: 0.96, y: 0 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.96, y: 0 }}
            transition={{ duration: 0.2 }}
            className="relative w-full max-w-sm rounded-2xl border border-border bg-white p-5 shadow-xl"
          >
            <button
              onClick={handleClose}
              className="absolute right-3 top-3 rounded-lg p-1 text-muted transition-colors hover:bg-surface hover:text-foreground"
              aria-label="Close"
            >
              <X className="h-4 w-4" />
            </button>

            {/* Header */}
            <div className="mb-4 text-center">
              <img src="/logo.png" alt="SABHA" className="mx-auto h-9 w-9 rounded-full object-contain mb-2.5" />
              <h2 className="text-base font-bold text-foreground">{t("auth.login_title")}</h2>
              <p className="mt-0.5 text-xs text-muted">{t("auth.login_subtitle")}</p>
            </div>

            {error && (
              <div className="mb-3 rounded-xl bg-red-50 border border-red-100 p-2.5 text-center text-xs font-semibold text-red-600">
                {error}
              </div>
            )}

            <form className="space-y-3" onSubmit={handleSubmit}>
              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">{t("auth.email")}</label>
                <div className="group relative">
                  <Mail className="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                  <input
                    type="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder={t("auth.email_placeholder")}
                    className="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                  />
                </div>
              </div>

              <div className="space-y-1">
                <div className="flex items-center justify-between">
                  <label className="text-xs font-semibold text-foreground">{t("auth.password")}</label>
                  <Link
                    href="/forgot-password"
                    onClick={closeLogin}
                    className="text-[11px] font-semibold text-primary hover:opacity-80 transition-opacity"
                  >
                    {t("auth.forgot_password")}
                  </Link>
                </div>
                <div className="group relative">
                  <Lock className="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                  <input
                    type={showPassword ? "text" : "password"}
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder={t("auth.password_placeholder")}
                    className="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-9 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-0.5"
                    tabIndex={-1}
                    title={showPassword ? t("auth.hide_password") : t("auth.show_password")}
                  >
                    {showPassword ? <EyeOff className="h-3.5 w-3.5" /> : <Eye className="h-3.5 w-3.5" />}
                  </button>
                </div>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="group inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60"
              >
                {loading ? t("auth.logging_in") : t("auth.login_btn")}
                {!loading && <ArrowRight className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />}
              </button>
            </form>

            <p className="mt-4 text-center text-xs text-muted">
              {t("auth.no_account")}{" "}
              <button
                onClick={() => { closeLogin(); openRegister(); }}
                className="font-semibold text-primary hover:opacity-80 transition-opacity"
              >
                {t("auth.create_one")}
              </button>
            </p>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}

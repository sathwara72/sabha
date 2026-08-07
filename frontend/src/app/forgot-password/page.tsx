"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { Mail, ArrowLeft, ShieldCheck, ArrowRight, KeyRound, Lock, Eye, EyeOff, RefreshCw } from "lucide-react";
import { motion } from "framer-motion";
import { forgotPasswordSendOtp, forgotPasswordReset } from "@/lib/api";
import { useAuth } from "@/lib/auth";
import { useLanguage } from "@/lib/language";

export default function ForgotPasswordPage() {
  const router = useRouter();
  const { openLogin } = useAuth();
  const { t } = useLanguage();

  const [step, setStep] = useState<"request" | "verify" | "success">("request");
  const [email, setEmail] = useState("");
  const [otp, setOtp] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const [loading, setLoading] = useState(false);
  const [resending, setResending] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  const handleBackToLogin = (e?: React.MouseEvent) => {
    if (e) e.preventDefault();
    router.push("/");
    openLogin();
  };

  const handleSendOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setSuccess("");
    setLoading(true);

    try {
      const res = await forgotPasswordSendOtp(email);
      setSuccess(res.message || t("auth.forgot_subtitle"));
      setStep("verify");
    } catch (err: any) {
      setError(err.message || t("auth.send_otp"));
    } finally {
      setLoading(false);
    }
  };

  const handleResendOtp = async () => {
    setError("");
    setSuccess("");
    setResending(true);

    try {
      const res = await forgotPasswordSendOtp(email);
      setSuccess(res.message || t("auth.resend_otp"));
    } catch (err: any) {
      setError(err.message || t("auth.resend_otp"));
    } finally {
      setResending(false);
    }
  };

  const handleResetPassword = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    if (!otp || otp.trim().length !== 6) {
      setError(t("auth.invalid_otp"));
      return;
    }

    if (password.length < 6) {
      setError(t("auth.short_password"));
      return;
    }

    if (password !== confirmPassword) {
      setError(t("auth.passwords_not_match"));
      return;
    }

    setLoading(true);

    try {
      const res = await forgotPasswordReset(email, otp.trim(), password);
      setSuccess(res.message || t("auth.reset_success_desc"));
      setStep("success");
    } catch (err: any) {
      setError(err.message || t("auth.reset_password_btn"));
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex flex-col items-center justify-start bg-background font-outfit px-4 sm:px-6 pt-6 pb-12">
      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.25 }}
        className="w-full max-w-md"
      >
        {/* Logo & Page Title Header */}
        <div className="text-center mb-4">
          <Link href="/" className="inline-flex items-center gap-2 mb-2 group">
            <img src="/logo.png" alt="SABHA" className="h-8 w-8 rounded-full object-contain" />
            <span className="text-xl font-bold tracking-tight text-primary-dark">SABHA</span>
          </Link>
          <h1 className="text-xl sm:text-2xl font-extrabold text-foreground mb-1">
            {step === "request" && t("auth.forgot_title")}
            {step === "verify" && t("auth.forgot_verify_title")}
            {step === "success" && t("auth.forgot_success_title")}
          </h1>
          <p className="text-xs text-muted max-w-xs mx-auto leading-relaxed">
            {step === "request" && t("auth.forgot_subtitle")}
            {step === "verify" && `${t("auth.forgot_verify_subtitle")} ${email}`}
            {step === "success" && t("auth.forgot_success_subtitle")}
          </p>
        </div>

        {/* Form Container Card */}
        <div className="bg-white border border-border/80 rounded-2xl shadow-md p-5 sm:p-7">
          {error && (
            <div className="mb-4 rounded-xl bg-red-50 border border-red-100 p-3 text-center text-xs font-semibold text-red-600 animate-fadeIn">
              {error}
            </div>
          )}

          {success && step === "verify" && (
            <div className="mb-4 rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center text-xs font-semibold text-emerald-800 animate-fadeIn">
              {success}
            </div>
          )}

          {step === "request" && (
            <form className="space-y-4" onSubmit={handleSendOtp}>
              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">{t("auth.email_address")}</label>
                <div className="relative group">
                  <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="email"
                    required
                    placeholder={t("auth.email_placeholder")}
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="w-full rounded-xl border border-border bg-white pl-10 pr-4 py-2.5 text-xs text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors"
                  />
                </div>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
              >
                {loading ? t("auth.sending") : t("auth.send_otp")}
                {!loading && <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-0.5" />}
              </button>

              <div className="pt-1 text-center">
                <button
                  type="button"
                  onClick={handleBackToLogin}
                  className="inline-flex items-center gap-1.5 text-xs font-semibold text-muted hover:text-primary transition-colors cursor-pointer"
                >
                  <ArrowLeft className="w-3.5 h-3.5" /> {t("auth.back_to_login")}
                </button>
              </div>
            </form>
          )}

          {step === "verify" && (
            <form className="space-y-3.5" onSubmit={handleResetPassword}>
              {/* Verification Code (OTP) */}
              <div className="space-y-1">
                <div className="flex items-center justify-between">
                  <label className="text-xs font-semibold text-foreground">{t("auth.verify_code")}</label>
                  <button
                    type="button"
                    onClick={() => { setStep("request"); setError(""); setSuccess(""); }}
                    className="text-[11px] font-medium text-primary hover:underline"
                  >
                    {t("auth.change_email")}
                  </button>
                </div>
                <div className="relative group">
                  <KeyRound className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="text"
                    required
                    maxLength={6}
                    placeholder={t("auth.enter_otp_placeholder")}
                    value={otp}
                    onChange={(e) => setOtp(e.target.value.replace(/\D/g, ""))}
                    className="w-full rounded-xl border border-border bg-white pl-10 pr-4 py-2.5 text-xs font-mono tracking-widest text-foreground outline-none placeholder:text-muted-foreground placeholder:font-sans placeholder:tracking-normal focus:border-primary transition-colors"
                  />
                </div>
                {/* Dedicated Resend OTP Action Button */}
                <div className="flex items-center justify-between pt-1">
                  <span className="text-muted text-[11px]">{t("auth.didnt_receive")}</span>
                  <button
                    type="button"
                    disabled={resending}
                    onClick={handleResendOtp}
                    className="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border border-primary/20 bg-primary/5 text-[11px] font-bold text-primary hover:bg-primary/10 transition-colors disabled:opacity-50 cursor-pointer"
                  >
                    <RefreshCw className={`w-3 h-3 ${resending ? "animate-spin" : ""}`} />
                    {resending ? t("auth.resending") : t("auth.resend_otp")}
                  </button>
                </div>
              </div>

              {/* New Password */}
              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">{t("auth.new_password")}</label>
                <div className="relative group">
                  <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type={showPassword ? "text" : "password"}
                    required
                    minLength={6}
                    placeholder={t("auth.min_chars")}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="w-full rounded-xl border border-border bg-white pl-10 pr-9 py-2.5 text-xs text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-1"
                  >
                    {showPassword ? <EyeOff className="w-3.5 h-3.5" /> : <Eye className="w-3.5 h-3.5" />}
                  </button>
                </div>
              </div>

              {/* Confirm New Password */}
              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">{t("auth.confirm_new_password")}</label>
                <div className="relative group">
                  <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type={showConfirmPassword ? "text" : "password"}
                    required
                    minLength={6}
                    placeholder={t("auth.reenter_new_password")}
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    className="w-full rounded-xl border border-border bg-white pl-10 pr-9 py-2.5 text-xs text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors"
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-1"
                  >
                    {showConfirmPassword ? <EyeOff className="w-3.5 h-3.5" /> : <Eye className="w-3.5 h-3.5" />}
                  </button>
                </div>
              </div>

              {/* Submit Button */}
              <button
                type="submit"
                disabled={loading}
                className="group w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer mt-1"
              >
                {loading ? t("auth.resetting") : t("auth.reset_password_btn")}
                {!loading && <ArrowRight className="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" />}
              </button>

              {/* Footer Back to log in link */}
              <div className="pt-2 text-center">
                <button
                  type="button"
                  onClick={handleBackToLogin}
                  className="inline-flex items-center gap-1 text-xs font-semibold text-muted hover:text-primary transition-colors cursor-pointer"
                >
                  <ArrowLeft className="w-3.5 h-3.5" /> {t("auth.back_to_login")}
                </button>
              </div>
            </form>
          )}

          {step === "success" && (
            <div className="space-y-5">
              <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-5 text-sm font-semibold text-emerald-800 flex flex-col items-center gap-2.5">
                <ShieldCheck className="h-9 w-9 text-emerald-600 animate-pulse" />
                <p className="text-center text-sm font-bold text-emerald-900">{t("auth.reset_complete")}</p>
                <p className="text-center text-xs text-emerald-700 leading-relaxed font-normal">
                  {success || t("auth.reset_success_desc")}
                </p>
              </div>
              <button
                type="button"
                onClick={handleBackToLogin}
                className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
              >
                {t("auth.login_to_continue")} <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          )}
        </div>
      </motion.div>
    </div>
  );
}

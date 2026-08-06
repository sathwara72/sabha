"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { Mail, ArrowLeft, ShieldCheck, ArrowRight, KeyRound, Lock, Eye, EyeOff, RefreshCw } from "lucide-react";
import { motion } from "framer-motion";
import { forgotPasswordSendOtp, forgotPasswordReset } from "@/lib/api";
import { useAuth } from "@/lib/auth";

export default function ForgotPasswordPage() {
  const router = useRouter();
  const { openLogin } = useAuth();

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
      setSuccess(res.message || "Verification code sent to your email.");
      setStep("verify");
    } catch (err: any) {
      setError(err.message || "Failed to send reset code.");
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
      setSuccess(res.message || "Verification code resent successfully.");
    } catch (err: any) {
      setError(err.message || "Failed to resend code.");
    } finally {
      setResending(false);
    }
  };

  const handleResetPassword = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    if (!otp || otp.trim().length !== 6) {
      setError("Please enter a valid 6-digit OTP code.");
      return;
    }

    if (password.length < 6) {
      setError("Password must be at least 6 characters long.");
      return;
    }

    if (password !== confirmPassword) {
      setError("Passwords do not match.");
      return;
    }

    setLoading(true);

    try {
      const res = await forgotPasswordReset(email, otp.trim(), password);
      setSuccess(res.message || "Your password has been reset successfully!");
      setStep("success");
    } catch (err: any) {
      setError(err.message || "Failed to reset password.");
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
            {step === "request" && "Reset password"}
            {step === "verify" && "Verify OTP & Reset"}
            {step === "success" && "Password Reset Success"}
          </h1>
          <p className="text-xs text-muted max-w-xs mx-auto leading-relaxed">
            {step === "request" && "We will send you a 6-digit verification code to reset your password"}
            {step === "verify" && `Enter the 6-digit code sent to ${email}`}
            {step === "success" && "Your password has been updated successfully. Log in to continue."}
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
                <label className="text-xs font-semibold text-foreground">Email Address</label>
                <div className="relative group">
                  <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="email"
                    required
                    placeholder="you@example.com"
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
                {loading ? "Sending OTP..." : "Send OTP"}
                {!loading && <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-0.5" />}
              </button>

              <div className="pt-1 text-center">
                <button
                  type="button"
                  onClick={handleBackToLogin}
                  className="inline-flex items-center gap-1.5 text-xs font-semibold text-muted hover:text-primary transition-colors cursor-pointer"
                >
                  <ArrowLeft className="w-3.5 h-3.5" /> Back to log in
                </button>
              </div>
            </form>
          )}

          {step === "verify" && (
            <form className="space-y-3.5" onSubmit={handleResetPassword}>
              {/* Verification Code (OTP) */}
              <div className="space-y-1">
                <div className="flex items-center justify-between">
                  <label className="text-xs font-semibold text-foreground">Verification Code (OTP)</label>
                  <button
                    type="button"
                    onClick={() => { setStep("request"); setError(""); setSuccess(""); }}
                    className="text-[11px] font-medium text-primary hover:underline"
                  >
                    Change Email
                  </button>
                </div>
                <div className="relative group">
                  <KeyRound className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="text"
                    required
                    maxLength={6}
                    placeholder="Enter 6-digit OTP"
                    value={otp}
                    onChange={(e) => setOtp(e.target.value.replace(/\D/g, ""))}
                    className="w-full rounded-xl border border-border bg-white pl-10 pr-4 py-2.5 text-xs font-mono tracking-widest text-foreground outline-none placeholder:text-muted-foreground placeholder:font-sans placeholder:tracking-normal focus:border-primary transition-colors"
                  />
                </div>
                {/* Dedicated Resend OTP Action Button */}
                <div className="flex items-center justify-between pt-1">
                  <span className="text-muted text-[11px]">Didn&apos;t receive code?</span>
                  <button
                    type="button"
                    disabled={resending}
                    onClick={handleResendOtp}
                    className="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border border-primary/20 bg-primary/5 text-[11px] font-bold text-primary hover:bg-primary/10 transition-colors disabled:opacity-50 cursor-pointer"
                  >
                    <RefreshCw className={`w-3 h-3 ${resending ? "animate-spin" : ""}`} />
                    {resending ? "Resending..." : "Resend OTP"}
                  </button>
                </div>
              </div>

              {/* New Password */}
              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">New Password</label>
                <div className="relative group">
                  <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type={showPassword ? "text" : "password"}
                    required
                    minLength={6}
                    placeholder="Minimum 6 characters"
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
                <label className="text-xs font-semibold text-foreground">Confirm New Password</label>
                <div className="relative group">
                  <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type={showConfirmPassword ? "text" : "password"}
                    required
                    minLength={6}
                    placeholder="Re-enter new password"
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
                {loading ? "Resetting Password..." : "Reset Password"}
                {!loading && <ArrowRight className="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" />}
              </button>

              {/* Footer Back to log in link */}
              <div className="pt-2 text-center">
                <button
                  type="button"
                  onClick={handleBackToLogin}
                  className="inline-flex items-center gap-1 text-xs font-semibold text-muted hover:text-primary transition-colors cursor-pointer"
                >
                  <ArrowLeft className="w-3.5 h-3.5" /> Back to log in
                </button>
              </div>
            </form>
          )}

          {step === "success" && (
            <div className="space-y-5">
              <div className="rounded-xl bg-emerald-50 border border-emerald-100 p-5 text-sm font-semibold text-emerald-800 flex flex-col items-center gap-2.5">
                <ShieldCheck className="h-9 w-9 text-emerald-600 animate-pulse" />
                <p className="text-center text-sm font-bold text-emerald-900">Password Reset Complete!</p>
                <p className="text-center text-xs text-emerald-700 leading-relaxed font-normal">
                  {success || "Your account password has been updated successfully. Click below to log in."}
                </p>
              </div>
              <button
                type="button"
                onClick={handleBackToLogin}
                className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
              >
                Log in to continue <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          )}
        </div>
      </motion.div>
    </div>
  );
}

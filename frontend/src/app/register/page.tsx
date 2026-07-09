"use client";

import { useState } from "react";
import Link from "next/link";
import { User, Mail, Lock, Phone, ArrowRight, Key, ShieldCheck } from "lucide-react";
import { motion } from "framer-motion";
import { useAuth } from "@/lib/auth";
import { useRouter } from "next/navigation";

export default function RegisterPage() {
  const { registerSendOtp, registerConfirm } = useAuth();
  const router = useRouter();

  const [step, setStep] = useState<1 | 2>(1);
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [otp, setOtp] = useState("");
  
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [otpSentEmail, setOtpSentEmail] = useState("");

  const handleSendOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    if (password !== confirmPassword) {
      setError("Passwords do not match!");
      return;
    }
    if (phone.length !== 10) {
      setError("Please enter a valid 10-digit mobile number.");
      return;
    }
    setLoading(true);
    try {
      const res = await registerSendOtp(name, email, password);
      setOtpSentEmail(res.email || email);
      setStep(2);
    } catch (err: any) {
      setError(err.message || "Registration validation failed. Try again.");
    } finally {
      setLoading(false);
    }
  };

  const handleVerifyOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      await registerConfirm(email, otp);
      // Redirect to profile or home page
      router.push("/profile");
    } catch (err: any) {
      setError(err.message || "Invalid or expired OTP. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-background font-outfit px-6 pt-28 pb-16">
      <motion.div
        initial={{ opacity: 0, y: 12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.35 }}
        className="w-full max-w-2xl"
      >
        {/* Logo + heading */}
        <div className="text-center mb-8">
          <Link href="/" className="inline-flex items-center gap-2.5 mb-8 group">
            <img src="/logo.png" alt="SABHA" className="h-12 w-12 rounded-full object-contain" />
            <span className="text-2xl font-bold tracking-tight text-primary-dark">SABHA</span>
          </Link>
          <h1 className="text-3xl sm:text-4xl font-bold text-foreground mb-2">
            {step === 1 ? "Create your account" : "Verify your email"}
          </h1>
          <p className="text-sm text-muted">
            {step === 1
              ? "Join the community and list your business"
              : `Enter the 6-digit verification code sent to ${email}`}
          </p>
        </div>

        {/* Card */}
        <div className="bg-white border border-border rounded-2xl shadow-sm p-8 md:p-10">
          {error && (
            <div className="mb-6 rounded-xl bg-red-50 border border-red-100 p-4 text-center text-sm font-semibold text-red-600">
              {error}
            </div>
          )}

          {step === 2 && otpSentEmail && (
            <div className="mb-6 rounded-xl bg-green-50 border border-green-100 p-4 text-sm font-semibold text-green-700 flex items-center gap-2.5">
              <ShieldCheck className="h-5 w-5 text-green-600 shrink-0" />
              <span>A 6-digit OTP has been sent to <strong>{otpSentEmail}</strong>. Please check your inbox.</span>
            </div>
          )}

          {step === 1 ? (
            <form className="grid grid-cols-1 md:grid-cols-2 gap-5" onSubmit={handleSendOtp}>
              {/* Full Name */}
              <div className="space-y-2 md:col-span-2">
                <label className="text-sm font-medium text-foreground">Full name or business name</label>
                <div className="relative group">
                  <User className="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="text"
                    required
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="E.g. John Doe / Acme Corp"
                    className="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors"
                  />
                </div>
              </div>

              {/* Email */}
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground">Email</label>
                <div className="relative group">
                  <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="name@company.com"
                    className="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors"
                  />
                </div>
              </div>

              {/* Phone */}
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground">Phone</label>
                <div className="relative group">
                  <Phone className="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="tel"
                    required
                    value={phone}
                    onChange={(e) => {
                      const val = e.target.value.replace(/\D/g, "").slice(0, 10);
                      setPhone(val);
                    }}
                    placeholder="10-digit mobile number"
                    className="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors"
                  />
                </div>
              </div>

              {/* Password */}
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground">Password</label>
                <div className="relative group">
                  <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="password"
                    required
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="Create a password"
                    className="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors"
                  />
                </div>
              </div>

              {/* Confirm Password */}
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground">Confirm password</label>
                <div className="relative group">
                  <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="password"
                    required
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    placeholder="Re-enter your password"
                    className="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors"
                  />
                </div>
              </div>

              <div className="md:col-span-2 pt-2">
                <button
                  type="submit"
                  disabled={loading}
                  className="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                >
                  {loading ? "Sending Verification code..." : "Create account"}
                  {!loading && <ArrowRight className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />}
                </button>
              </div>
            </form>
          ) : (
            <form className="space-y-6 max-w-md mx-auto" onSubmit={handleVerifyOtp}>
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground">Enter the 6-Digit Code (OTP)</label>
                <div className="relative group">
                  <Key className="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                  <input
                    type="text"
                    required
                    value={otp}
                    onChange={(e) => setOtp(e.target.value)}
                    placeholder="E.g. 123456"
                    className="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors tracking-[0.2em] font-mono text-center text-lg"
                    maxLength={6}
                  />
                </div>
              </div>

              <div className="flex gap-4 pt-2">
                <button
                  type="button"
                  onClick={() => setStep(1)}
                  className="flex-1 rounded-xl border border-border bg-white px-5 py-3.5 text-sm font-semibold text-foreground transition-colors hover:bg-surface cursor-pointer text-center"
                >
                  Go Back
                </button>
                <button
                  type="submit"
                  disabled={loading}
                  className="flex-[2] inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                >
                  {loading ? "Verifying..." : "Verify & Complete"}
                </button>
              </div>
            </form>
          )}

          <p className="mt-8 text-center text-sm text-muted">
            Already have an account?{" "}
            <Link href="/login" className="font-semibold text-primary hover:opacity-80 transition-opacity">
              Log in
            </Link>
          </p>
        </div>

        {/* Plain trust line */}
        <div className="mt-8 flex justify-center gap-8 text-sm text-muted">
          <span>Business focused</span>
          <span>Community driven</span>
        </div>
      </motion.div>
    </div>
  );
}

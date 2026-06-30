"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { User, Mail, Lock, Phone, ArrowRight, X, Key, ShieldCheck, Sparkles } from "lucide-react";
import { useAuth } from "@/lib/auth";

export default function RegisterModal() {
  const { isRegisterOpen, closeRegister, openLogin, registerSendOtp, registerConfirm, demoLogin } = useAuth();
  const [step, setStep] = useState<1 | 2>(1);
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [otp, setOtp] = useState("");
  
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [demoLoading, setDemoLoading] = useState(false);
  const [simulatedOtp, setSimulatedOtp] = useState("");

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

  const handleSendOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    if (password !== confirmPassword) {
      setError("Passwords do not match!");
      return;
    }
    setLoading(true);
    try {
      const res = await registerSendOtp(name, email, password);
      if (res.otp) {
        setSimulatedOtp(res.otp);
      }
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
      // Success! Resets states
      setName("");
      setEmail("");
      setPhone("");
      setPassword("");
      setConfirmPassword("");
      setOtp("");
      setSimulatedOtp("");
      setStep(1);
    } catch (err: any) {
      setError(err.message || "Invalid or expired OTP. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <AnimatePresence>
      {isRegisterOpen && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          className="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto"
        >
          <div
            className="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
            onClick={closeRegister}
          />

          <motion.div
            initial={{ opacity: 0, scale: 0.96, y: 12 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.96, y: 12 }}
            transition={{ duration: 0.2 }}
            className="relative w-full max-w-lg rounded-2xl border border-border bg-white p-8 shadow-xl z-10 font-outfit"
          >
            <button
              onClick={closeRegister}
              className="absolute right-4 top-4 rounded-lg p-1.5 text-muted transition-colors hover:bg-surface hover:text-foreground"
              aria-label="Close"
            >
              <X className="h-5 w-5" />
            </button>

            <div className="mb-6 text-center">
              <img src="/logo.png" alt="SABHA" className="mx-auto h-12 w-12 rounded-full object-contain mb-4" />
              <h2 className="text-xl font-bold text-foreground">
                {step === 1 ? "Create your account" : "Verify your email"}
              </h2>
              <p className="mt-1 text-sm text-muted">
                {step === 1
                  ? "Join the community and list your business"
                  : `Enter the verification code sent to ${email}`}
              </p>
            </div>

            {error && (
              <div className="mb-4 rounded-xl bg-red-50 border border-red-100 p-3.5 text-center text-xs font-semibold text-red-600">
                {error}
              </div>
            )}

            {step === 2 && simulatedOtp && (
              <div className="mb-4 rounded-xl bg-amber-50 border border-amber-100 p-3.5 text-center text-xs font-semibold text-amber-700 flex items-center gap-2">
                <ShieldCheck className="h-4 w-4 text-amber-700 shrink-0 animate-bounce" />
                <span>Simulated Email OTP received: <strong>{simulatedOtp}</strong>. Enter it below to verify.</span>
              </div>
            )}

            {step === 1 ? (
              <form className="space-y-4" onSubmit={handleSendOtp}>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground">Full name or business name</label>
                  <div className="group relative">
                    <User className="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                    <input
                      type="text"
                      required
                      value={name}
                      onChange={(e) => setName(e.target.value)}
                      placeholder="E.g. John Doe / Acme Corp"
                      className="w-full rounded-xl border border-border bg-white py-3 pl-11 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground">Email</label>
                    <div className="group relative">
                      <Mail className="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                      <input
                        type="email"
                        required
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="name@company.com"
                        className="w-full rounded-xl border border-border bg-white py-3 pl-11 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                      />
                    </div>
                  </div>

                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground">Phone</label>
                    <div className="group relative">
                      <Phone className="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                      <input
                        type="tel"
                        required
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                        placeholder="+91 99999 55555"
                        className="w-full rounded-xl border border-border bg-white py-3 pl-11 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                      />
                    </div>
                  </div>
                </div>

                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground">Password</label>
                    <div className="group relative">
                      <Lock className="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                      <input
                        type="password"
                        required
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        placeholder="Create a password"
                        className="w-full rounded-xl border border-border bg-white py-3 pl-11 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                      />
                    </div>
                  </div>

                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground">Confirm password</label>
                    <div className="group relative">
                      <Lock className="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                      <input
                        type="password"
                        required
                        value={confirmPassword}
                        onChange={(e) => setConfirmPassword(e.target.value)}
                        placeholder="Re-enter password"
                        className="w-full rounded-xl border border-border bg-white py-3 pl-11 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                      />
                    </div>
                  </div>
                </div>

                <button
                  type="submit"
                  disabled={loading}
                  className="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] pt-2 disabled:opacity-60 cursor-pointer"
                >
                  {loading ? "Sending verification code..." : "Create account"}
                  {!loading && <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-0.5" />}
                </button>
              </form>
            ) : (
              <form className="space-y-4" onSubmit={handleVerifyOtp}>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground">Verification Code (OTP)</label>
                  <div className="group relative">
                    <Key className="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                    <input
                      type="text"
                      required
                      value={otp}
                      onChange={(e) => setOtp(e.target.value)}
                      placeholder="Enter the 6-digit OTP code"
                      className="w-full rounded-xl border border-border bg-white py-3 pl-11 pr-4 text-sm text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary tracking-[0.2em] font-mono text-center text-lg"
                      maxLength={6}
                    />
                  </div>
                </div>

                <div className="flex gap-3 pt-2">
                  <button
                    type="button"
                    onClick={() => setStep(1)}
                    className="flex-1 rounded-xl border border-border bg-white px-4 py-3 text-sm font-semibold text-foreground transition-colors hover:bg-surface cursor-pointer text-center"
                  >
                    Back
                  </button>
                  <button
                    type="submit"
                    disabled={loading}
                    className="flex-[2] inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                  >
                    {loading ? "Verifying..." : "Verify & Register"}
                  </button>
                </div>
              </form>
            )}

            {step === 1 && (
              <>
                <div className="my-5 flex items-center gap-3">
                  <span className="h-px flex-1 bg-border" />
                  <span className="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">or</span>
                  <span className="h-px flex-1 bg-border" />
                </div>

                <button
                  type="button"
                  onClick={handleDemoLogin}
                  disabled={demoLoading || loading}
                  className="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-primary/40 bg-primary-soft px-6 py-3 text-sm font-semibold text-primary transition-all hover:bg-primary/10 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                >
                  <Sparkles className="h-4 w-4" />
                  {demoLoading ? "Starting demo..." : "Skip — try a demo account"}
                </button>
                <p className="mt-2 text-center text-[11px] text-muted-foreground">
                  No email or OTP needed. Explore the app instantly.
                </p>
              </>
            )}

            <p className="mt-6 text-center text-sm text-muted">
              Already have an account?{" "}
              <button
                onClick={() => {
                  closeRegister();
                  openLogin();
                }}
                className="font-semibold text-primary hover:opacity-80 transition-opacity"
              >
                Log in
              </button>
            </p>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}

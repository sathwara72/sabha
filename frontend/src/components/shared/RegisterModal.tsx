"use client";

import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { User, Mail, Lock, Phone, ArrowRight, X, Key, ShieldCheck, Eye, EyeOff } from "lucide-react";
import { useAuth } from "@/lib/auth";
import { useLanguage } from "@/lib/language";

export default function RegisterModal() {
  const { isRegisterOpen, closeRegister, openLogin, registerSendOtp, registerConfirm } = useAuth();
  const { t } = useLanguage();
  const [step, setStep] = useState<1 | 2>(1);
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [otp, setOtp] = useState("");

  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [otpSentEmail, setOtpSentEmail] = useState("");

  const resetForm = () => {
    setName("");
    setEmail("");
    setPhone("");
    setPassword("");
    setConfirmPassword("");
    setShowPassword(false);
    setShowConfirmPassword(false);
    setOtp("");
    setError("");
    setLoading(false);
    setOtpSentEmail("");
    setStep(1);
  };

  useEffect(() => {
    if (!isRegisterOpen) {
      resetForm();
    }
  }, [isRegisterOpen]);

  const handleClose = () => {
    resetForm();
    closeRegister();
  };

  const handleSendOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    if (password !== confirmPassword) {
      setError(t("auth.passwords_no_match"));
      return;
    }
    if (phone.length !== 10) {
      setError(t("auth.invalid_phone"));
      return;
    }
    setLoading(true);
    try {
      const res = await registerSendOtp(name, email, password);
      setOtpSentEmail(res.email || email);
      setStep(2);
    } catch (err: any) {
      setError(err.message || t("auth.reg_failed"));
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
      resetForm();
    } catch (err: any) {
      setError(err.message || t("auth.otp_failed"));
    } finally {
      setLoading(false);
    }
  };

  const inputClass = "w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary";
  const iconClass = "absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary";
  const labelClass = "text-xs font-semibold text-foreground";

  return (
    <AnimatePresence>
      {isRegisterOpen && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          className="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto"
        >
          <div className="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onClick={handleClose} />

          <motion.div
            initial={{ opacity: 0, scale: 0.96, y: 0 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.96, y: 0 }}
            transition={{ duration: 0.2 }}
            className="relative w-full max-w-md rounded-2xl border border-border bg-white p-5 shadow-xl z-10 font-outfit"
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
              <img src="/logo.png" alt="SABHA" className="mx-auto h-9 w-9 rounded-full object-contain mb-2" />
              <h2 className="text-base font-bold text-foreground">
                {step === 1 ? t("auth.register_title") : t("auth.verify_title")}
              </h2>
              <p className="mt-0.5 text-xs text-muted">
                {step === 1 ? t("auth.register_subtitle") : `${t("auth.verify_subtitle")} ${email}`}
              </p>
            </div>

            {error && (
              <div className="mb-3 rounded-xl bg-red-50 border border-red-100 p-2.5 text-center text-xs font-semibold text-red-600">
                {error}
              </div>
            )}

            {step === 2 && otpSentEmail && (
              <div className="mb-3 rounded-xl bg-green-50 border border-green-100 p-2.5 text-xs font-semibold text-green-700 flex items-center gap-2">
                <ShieldCheck className="h-3.5 w-3.5 text-green-600 shrink-0" />
                <span>{t("auth.otp_sent")} <strong>{otpSentEmail}</strong>. {t("auth.otp_inbox")}</span>
              </div>
            )}

            {step === 1 ? (
              <form className="space-y-3" onSubmit={handleSendOtp}>
                <div className="space-y-1">
                  <label className={labelClass}>{t("auth.full_name")}</label>
                  <div className="group relative">
                    <User className={iconClass} />
                    <input type="text" required value={name} onChange={(e) => setName(e.target.value)} placeholder={t("auth.full_name_placeholder")} className={inputClass} />
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-2">
                  <div className="space-y-1">
                    <label className={labelClass}>{t("auth.email")}</label>
                    <div className="group relative">
                      <Mail className={iconClass} />
                      <input type="email" required value={email} onChange={(e) => setEmail(e.target.value)} placeholder="name@company.com" className={inputClass} />
                    </div>
                  </div>
                  <div className="space-y-1">
                    <label className={labelClass}>{t("auth.phone")}</label>
                    <div className="group relative">
                      <Phone className={iconClass} />
                      <input
                        type="tel"
                        required
                        value={phone}
                        onChange={(e) => {
                          const val = e.target.value.replace(/\D/g, "").slice(0, 10);
                          setPhone(val);
                        }}
                        placeholder={t("auth.phone_placeholder")}
                        className={inputClass}
                      />
                    </div>
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-2">
                  <div className="space-y-1">
                    <label className={labelClass}>{t("auth.password")}</label>
                    <div className="group relative">
                      <Lock className={iconClass} />
                      <input
                        type={showPassword ? "text" : "password"}
                        required
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        placeholder={t("auth.create_password_placeholder")}
                        className={`${inputClass} pr-8`}
                      />
                      <button
                        type="button"
                        onClick={() => setShowPassword(!showPassword)}
                        className="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-0.5"
                        tabIndex={-1}
                        title={showPassword ? t("auth.hide_password") : t("auth.show_password")}
                      >
                        {showPassword ? <EyeOff className="h-3.5 w-3.5" /> : <Eye className="h-3.5 w-3.5" />}
                      </button>
                    </div>
                  </div>
                  <div className="space-y-1">
                    <label className={labelClass}>{t("auth.confirm_password")}</label>
                    <div className="group relative">
                      <Lock className={iconClass} />
                      <input
                        type={showConfirmPassword ? "text" : "password"}
                        required
                        value={confirmPassword}
                        onChange={(e) => setConfirmPassword(e.target.value)}
                        placeholder={t("auth.confirm_password_placeholder")}
                        className={`${inputClass} pr-8`}
                      />
                      <button
                        type="button"
                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                        className="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-0.5"
                        tabIndex={-1}
                        title={showConfirmPassword ? t("auth.hide_password") : t("auth.show_password")}
                      >
                        {showConfirmPassword ? <EyeOff className="h-3.5 w-3.5" /> : <Eye className="h-3.5 w-3.5" />}
                      </button>
                    </div>
                  </div>
                </div>

                <button
                  type="submit"
                  disabled={loading}
                  className="group inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                >
                  {loading ? t("auth.sending_otp") : t("auth.send_otp_btn")}
                  {!loading && <ArrowRight className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />}
                </button>
              </form>
            ) : (
              <form className="space-y-3" onSubmit={handleVerifyOtp}>
                <div className="space-y-1">
                  <label className={labelClass}>{t("auth.otp_label")}</label>
                  <div className="group relative">
                    <Key className={iconClass} />
                    <input
                      type="text"
                      required
                      value={otp}
                      onChange={(e) => setOtp(e.target.value)}
                      placeholder={t("auth.otp_placeholder")}
                      className="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary tracking-[0.2em] font-mono text-center"
                      maxLength={6}
                    />
                  </div>
                </div>

                <div className="flex gap-2">
                  <button
                    type="button"
                    onClick={() => setStep(1)}
                    className="flex-1 rounded-xl border border-border bg-white px-3 py-2 text-xs font-semibold text-foreground transition-colors hover:bg-surface cursor-pointer text-center"
                  >
                    {t("auth.back_btn")}
                  </button>
                  <button
                    type="submit"
                    disabled={loading}
                    className="flex-[2] inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                  >
                    {loading ? t("auth.verifying") : t("auth.verify_register")}
                  </button>
                </div>
              </form>
            )}

            <p className="mt-4 text-center text-xs text-muted">
              {t("auth.have_account")}{" "}
              <button
                onClick={() => { closeRegister(); openLogin(); }}
                className="font-semibold text-primary hover:opacity-80 transition-opacity"
              >
                {t("auth.log_in")}
              </button>
            </p>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}

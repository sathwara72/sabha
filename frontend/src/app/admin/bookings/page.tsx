"use client";

import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import {
  getAllEventRegistrations,
  approveEventRegistration,
  rejectEventRegistration,
  toggleAttendance,
  checkInTicket
} from "@/lib/api";
import {
  ShieldCheck, XCircle, CheckCircle2,
  Info, Calendar, MapPin, User, Mail,
  FileText, X, ExternalLink, RefreshCw, QrCode
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { assetUrl } from "@/lib/config";

interface EventRegistration {
  id: number;
  event_id: number;
  user_id: number;
  ticket_number: string;
  status: string;
  is_attended: boolean;
  payment_screenshot: string | null;
  ticket_type: string;
  amount_paid: string | number;
  rejection_reason: string | null;
  created_at: string;
  user: {
    id: number;
    name: string;
    email: string;
    avatar?: string;
  };
  event: {
    id: number;
    title: string;
    type: string;
    date: string;
    location: string;
  };
}

export default function AdminBookingsPage() {
  const [registrations, setRegistrations] = useState<EventRegistration[]>([]);
  const [loading, setLoading] = useState(true);
  const [previewImage, setPreviewImage] = useState<string | null>(null);

  // Scanner modal state variables
  const [isScannerOpen, setIsScannerOpen] = useState(false);
  const [manualTicketNo, setManualTicketNo] = useState("");
  const [scanResult, setScanResult] = useState<{ success: boolean; message: string } | null>(null);
  const [scanningLoading, setScanningLoading] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  useEffect(() => {
    if (previewImage || isScannerOpen) {
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
  }, [previewImage, isScannerOpen]);

  async function loadData() {
    try {
      setLoading(true);
      const data = await getAllEventRegistrations();
      setRegistrations(data || []);
    } catch (error) {
      console.error("Error loading event registrations:", error);
    } finally {
      setLoading(false);
    }
  }

  async function handleApprove(id: number) {
    try {
      await approveEventRegistration(id);
      loadData();
    } catch (error: any) {
      alert(error.message || "Approval failed");
    }
  }

  async function handleReject(id: number) {
    const reason = prompt("Please enter the reason for rejection:");
    if (reason === null) return;
    if (!reason.trim()) {
      alert("Rejection reason is required.");
      return;
    }
    try {
      await rejectEventRegistration(id, reason.trim());
      loadData();
    } catch (error: any) {
      alert(error.message || "Rejection failed");
    }
  }

  async function handleToggleAttendance(id: number) {
    try {
      await toggleAttendance(id);
      loadData();
    } catch (error: any) {
      alert(error.message || "Failed to update attendance status");
    }
  }

  async function processCheckIn(ticketNo: string) {
    if (!ticketNo || !ticketNo.trim()) return;
    setScanningLoading(true);
    setScanResult(null);
    try {
      const res = await checkInTicket(ticketNo.trim());
      setScanResult({
        success: true,
        message: res.message || "Attendance marked successfully!"
      });
      loadData();
      setManualTicketNo("");
    } catch (err: any) {
      setScanResult({
        success: false,
        message: err.message || "Failed to mark attendance. Make sure ticket number is correct and approved."
      });
    } finally {
      setScanningLoading(false);
    }
  }

  useEffect(() => {
    if (!isScannerOpen || scanResult) {
      return;
    }

    let html5Qrcode: any = null;

    const startScanner = async () => {
      try {
        const { Html5Qrcode } = await import("html5-qrcode");
        html5Qrcode = new Html5Qrcode("qr-reader");
        await html5Qrcode.start(
          { facingMode: "environment" },
          {
            fps: 10,
            qrbox: { width: 150, height: 150 }
          },
          (decodedText: string) => {
            if (html5Qrcode && html5Qrcode.isScanning) {
              html5Qrcode.stop().then(() => {
                processCheckIn(decodedText);
              }).catch(() => {
                processCheckIn(decodedText);
              });
            }
          },
          () => {
            // ignore
          }
        );
      } catch (err) {
        console.error("Camera access error:", err);
      }
    };

    startScanner();

    return () => {
      if (html5Qrcode && html5Qrcode.isScanning) {
        html5Qrcode.stop().catch((err: any) => console.error("Error stopping scanner", err));
      }
    };
  }, [isScannerOpen, scanResult]);

  // Calculate statistics
  const total = registrations.length;
  const pending = registrations.filter(r => r.status === "pending").length;
  const approved = registrations.filter(r => r.status === "approved" || r.status === "confirmed").length;
  const rejected = registrations.filter(r => r.status === "rejected").length;

  return (
    <div className="space-y-3">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div className="flex flex-col">
          <h1 className="text-2xl sm:text-3xl font-semibold tracking-tight text-foreground">Event bookings</h1>
          <p className="text-sm text-muted">Review, verify payments, and approve event seat registrations</p>
        </div>
        <div className="flex items-center gap-3">
          <button
            onClick={() => setIsScannerOpen(true)}
            className="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer"
          >
            <QrCode size={16} />
            Scan Ticket QR
          </button>
          <button
            onClick={loadData}
            disabled={loading}
            className="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-white px-4 py-2.5 text-sm font-semibold text-foreground hover:bg-surface cursor-pointer disabled:opacity-60 transition-all"
          >
            <RefreshCw size={16} className={loading ? "animate-spin" : ""} />
            Refresh
          </button>
        </div>
      </div>

      {/* Stats Banner */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
        {[
          { label: "Total Bookings", value: total, color: "border-border text-foreground" },
          { label: "Pending Review", value: pending, color: "border-amber-200 text-amber-600 bg-amber-50/20" },
          { label: "Approved Seats", value: approved, color: "border-emerald-200 text-emerald-600 bg-emerald-50/20" },
          { label: "Rejected Requests", value: rejected, color: "border-red-200 text-red-600 bg-red-50/20" },
        ].map((stat, i) => (
          <div key={i} className={`rounded-xl border p-4.5 text-center ${stat.color}`}>
            <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">{stat.label}</p>
            <p className="text-2xl font-black mt-1 leading-none">{stat.value}</p>
          </div>
        ))}
      </div>

      <div className="flex items-center gap-3 rounded-xl bg-primary-soft p-4">
        <Info className="h-5 w-5 shrink-0 text-primary" />
        <p className="text-sm font-medium text-foreground">
          Verify that the payment amount matches the standard/verified price and that the screenshot is valid before approval.
        </p>
      </div>

      {/* Bookings List */}
      <div>
        {loading ? (
          <div className="py-20 text-center text-sm text-muted">
            <div className="inline-block animate-spin rounded-full h-6 w-6 border-2 border-primary border-t-transparent mr-2" />
            Loading booking requests...
          </div>
        ) : registrations.length === 0 ? (
          <div className="rounded-xl border border-dashed border-border py-20 text-center text-muted">
            No event booking requests found.
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <AnimatePresence>
              {registrations.map((reg) => (
                <motion.div
                  key={reg.id}
                  layout
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, scale: 0.95 }}
                  className="glass-card p-4 border border-border flex flex-col justify-between h-full bg-white rounded-2xl"
                >
                  <div className="flex flex-col justify-between h-full gap-3">
                    <div className="space-y-3">
                      {/* Attendee Info */}
                      <div className="space-y-2">
                        <p className="text-[10px] font-bold text-muted uppercase tracking-wider">Attendee Info</p>
                        <div className="flex items-center gap-2.5">
                          <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-base font-bold text-primary">
                            {reg.user?.name?.[0] ?? "?"}
                          </div>
                          <div className="min-w-0">
                            <h4 className="text-xs font-bold text-foreground truncate">{reg.user?.name || "Unknown"}</h4>
                            <p className="text-[11px] text-muted-foreground truncate flex items-center gap-1 mt-0.5">
                              <Mail size={11} /> {reg.user?.email}
                            </p>
                          </div>
                        </div>
                        <div className="mt-1">
                          <span className={`inline-flex items-center gap-1 rounded bg-slate-100 border border-slate-200 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider ${reg.ticket_type === "verified" ? "text-emerald-700 bg-emerald-50 border-emerald-100" : "text-muted"
                            }`}>
                            {reg.ticket_type === "verified" ? "⭐ Sabha Member" : "Standard Tier"}
                          </span>
                        </div>
                      </div>

                      {/* Subtle Separator */}
                      <div className="border-t border-border/80" />

                      {/* Ticket and Payment Details */}
                      <div className="space-y-2">
                        <p className="text-[10px] font-bold text-muted uppercase tracking-wider">Payment & Ticket</p>
                        <div className="space-y-1">
                          <p className="text-xs text-muted-foreground">Ticket No: <span className="font-semibold text-foreground font-mono">{reg.ticket_number || "Pending Approval"}</span></p>
                        </div>

                        {/* Payment Screenshot Proof */}
                        {reg.payment_screenshot ? (
                          <div className="flex items-center gap-2">
                            <button
                              onClick={() => setPreviewImage(assetUrl(reg.payment_screenshot!))}
                              className="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline cursor-pointer"
                            >
                              <FileText size={14} /> View Receipt
                            </button>
                          </div>
                        ) : (
                          <p className="text-xs text-red-500 font-semibold flex items-center gap-1">
                            <Info size={12} /> No screenshot uploaded
                          </p>
                        )}
                      </div>
                    </div>

                    {/* Actions Column */}
                    <div className="border-t border-border/80 pt-3 mt-auto">
                      {reg.status === "pending" ? (
                        <div className="flex items-center gap-2 w-full">
                          <button
                            onClick={() => handleApprove(reg.id)}
                            className="flex-1 inline-flex items-center justify-center gap-1 rounded-xl bg-primary py-2 text-xs font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
                          >
                            <CheckCircle2 size={12} />
                            Approve
                          </button>
                          <button
                            onClick={() => handleReject(reg.id)}
                            className="flex-1 inline-flex items-center justify-center gap-1 rounded-xl border border-red-100 bg-red-50 py-2 text-xs font-semibold text-red-600 transition-all hover:bg-red-100 active:scale-[0.98] cursor-pointer"
                          >
                            <XCircle size={12} />
                            Reject
                          </button>
                        </div>
                      ) : (
                        <div className="w-full flex flex-col gap-2">
                          {reg.status === "rejected" && reg.rejection_reason && (
                            <p className="text-[10px] text-red-500 bg-red-50 border border-red-100/50 rounded-lg p-1.5 text-left max-w-full leading-relaxed">
                              <strong>Reason:</strong> {reg.rejection_reason}
                            </p>
                          )}
                          <div className="flex items-center justify-between gap-2">
                            <div className="inline-flex items-center gap-1.5 rounded-full border border-border px-2.5 py-1 text-[10px] font-bold text-muted justify-center shrink-0">
                              <ShieldCheck size={12} className={reg.status === "approved" || reg.status === "confirmed" ? "text-primary" : "text-muted"} />
                              {reg.status === "approved" || reg.status === "confirmed" ? "Verified" : "Rejected"}
                            </div>

                            {(reg.status === "approved" || reg.status === "confirmed") && (
                              <button
                                onClick={() => handleToggleAttendance(reg.id)}
                                className={`inline-flex items-center justify-center gap-1 rounded-xl border px-2.5 py-1 text-[10px] font-bold cursor-pointer transition-all active:scale-[0.98] shrink-0 ${reg.is_attended
                                  ? "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100"
                                  : "bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100"
                                  }`}
                              >
                                <span className={`h-1.5 w-1.5 rounded-full ${reg.is_attended ? "bg-emerald-600 animate-pulse" : "bg-slate-400"}`} />
                                {reg.is_attended ? "Attended" : "Attendance"}
                              </button>
                            )}
                          </div>
                        </div>
                      )}
                    </div>
                  </div>
                </motion.div>
              ))}
            </AnimatePresence>
          </div>
        )}
      </div>

      {/* Image Preview Lightbox Modal */}
      {previewImage && typeof window !== "undefined" && createPortal(
        <div
          className="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-black/90 backdrop-blur-sm"
          onClick={() => setPreviewImage(null)}
        >
          {/* Close Button */}
          <button
            onClick={() => setPreviewImage(null)}
            className="absolute top-4 right-4 md:top-6 md:right-6 z-10 p-2.5 rounded-full bg-white/15 text-white hover:bg-white/25 transition-all hover:scale-105 active:scale-95 cursor-pointer shadow-lg"
          >
            <X size={22} />
          </button>

          {/* Centered Receipt Image */}
          <div className="max-w-[95vw] max-h-[88vh] flex items-center justify-center p-2" onClick={e => e.stopPropagation()}>
            <img
              src={previewImage}
              alt="Payment receipt proof"
              className="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl border border-white/10"
            />
          </div>
        </div>,
        document.body
      )}

      {/* QR Code Scanner / Attendance Check-in Modal */}
      {isScannerOpen && typeof window !== "undefined" && createPortal(
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
          <div className="bg-white rounded-2xl w-full max-w-[340px] overflow-hidden border border-border shadow-2xl flex flex-col">

            {/* Modal Header */}
            <div className="flex items-center justify-between px-4.5 py-3 border-b border-border">
              <div className="flex items-center gap-2">
                <QrCode className="h-4.5 w-4.5 text-primary" />
                <h3 className="text-sm font-bold text-foreground">Scan QR Code / Check-in</h3>
              </div>
              <button
                onClick={() => setIsScannerOpen(false)}
                className="p-1.5 rounded-lg text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors cursor-pointer"
              >
                <X size={16} />
              </button>
            </div>

            {/* Modal Content */}
            <div className="p-4.5 space-y-4 flex-1 overflow-y-auto">

              {/* Scan Results Screen */}
              {scanResult ? (
                <div className="text-center py-4 space-y-3">
                  {scanResult.success ? (
                    <>
                      <div className="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 animate-bounce">
                        <CheckCircle2 size={24} />
                      </div>
                      <h4 className="text-sm font-bold text-foreground">Successful Check-in</h4>
                      <p className="text-xs text-emerald-700 bg-emerald-50/50 border border-emerald-100/50 rounded-xl p-2.5 leading-relaxed font-medium mx-auto max-w-xs">
                        {scanResult.message}
                      </p>
                    </>
                  ) : (
                    <>
                      <div className="inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600 border border-red-100">
                        <XCircle size={24} />
                      </div>
                      <h4 className="text-sm font-bold text-foreground">Check-in Failed</h4>
                      <p className="text-xs text-red-700 bg-red-50/50 border border-red-100/50 rounded-xl p-2.5 leading-relaxed font-medium mx-auto max-w-xs">
                        {scanResult.message}
                      </p>
                    </>
                  )}

                  <div className="pt-2 flex items-center gap-2.5 justify-center">
                    <button
                      onClick={() => setScanResult(null)}
                      className="px-4 py-2 rounded-xl bg-primary text-xs font-semibold text-white shadow-sm hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer"
                    >
                      Scan Next Ticket
                    </button>
                    <button
                      onClick={() => setIsScannerOpen(false)}
                      className="px-4 py-2 rounded-xl border border-border bg-white text-xs font-semibold text-foreground hover:bg-slate-50 transition-all cursor-pointer"
                    >
                      Done
                    </button>
                  </div>
                </div>
              ) : (
                /* Scanner Active Screen */
                <div className="space-y-4">
                  {/* Camera view container */}
                  <div className="relative rounded-xl overflow-hidden bg-slate-900 h-[180px] w-full flex flex-col items-center justify-center border border-slate-800">
                    <div id="qr-reader" className="w-full h-full overflow-hidden [&_video]:object-cover [&_video]:w-full [&_video]:h-full" />

                    {/* Overlay target frame */}
                    <div className="absolute inset-0 border-[20px] border-black/40 pointer-events-none flex items-center justify-center">
                      <div className="w-[110px] h-[110px] border-2 border-dashed border-primary relative">
                        {/* Scanning scanner line laser animation */}
                        <div className="absolute left-0 right-0 h-0.5 bg-primary/80 top-0 animate-[scan_2s_ease-in-out_infinite]" />
                      </div>
                    </div>

                    {scanningLoading && (
                      <div className="absolute inset-0 bg-black/60 backdrop-blur-[2px] flex flex-col items-center justify-center gap-2">
                        <div className="inline-block animate-spin rounded-full h-6 w-6 border-4 border-primary border-t-transparent" />
                        <span className="text-[10px] font-bold text-white tracking-wider">Processing check-in...</span>
                      </div>
                    )}
                  </div>

                  {/* Manual entry options divider */}
                  <div className="relative flex py-1 items-center">
                    <div className="flex-grow border-t border-border"></div>
                    <span className="flex-shrink mx-3 text-[10px] font-bold text-muted uppercase tracking-wider">or Enter Manually</span>
                    <div className="flex-grow border-t border-border"></div>
                  </div>

                  {/* Manual Input form */}
                  <form
                    onSubmit={(e) => {
                      e.preventDefault();
                      processCheckIn(manualTicketNo);
                    }}
                    className="flex gap-2"
                  >
                    <input
                      type="text"
                      placeholder="e.g. 2026-SNM-8148"
                      value={manualTicketNo}
                      onChange={(e) => setManualTicketNo(e.target.value)}
                      className="flex-1 rounded-xl border border-border px-3.5 py-2 text-xs font-semibold text-foreground focus:border-primary focus:outline-none placeholder-muted-foreground/60 transition-colors uppercase font-mono"
                    />
                    <button
                      type="submit"
                      disabled={scanningLoading || !manualTicketNo.trim()}
                      className="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition-all hover:bg-slate-800 active:scale-[0.98] disabled:opacity-50 cursor-pointer shrink-0"
                    >
                      Check In
                    </button>
                  </form>
                </div>
              )}
            </div>
          </div>
        </div>,
        document.body
      )}

      <style>{`
        @keyframes scan {
          0%, 100% { top: 0%; }
          50% { top: 100%; }
        }
      `}</style>
    </div>
  );
}

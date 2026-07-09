"use client";

import { useEffect, useRef } from "react";
import { createPortal } from "react-dom";
import { AlertTriangle, Trash2, X, Info, CheckCircle } from "lucide-react";

export type ConfirmModalVariant = "danger" | "warning" | "info" | "success";

interface ConfirmModalProps {
  isOpen: boolean;
  title: string;
  message: string;
  confirmLabel?: string;
  cancelLabel?: string;
  variant?: ConfirmModalVariant;
  isLoading?: boolean;
  onConfirm: () => void;
  onCancel: () => void;
}

const variantConfig: Record<
  ConfirmModalVariant,
  { icon: React.ReactNode; iconBg: string; confirmBtn: string; titleColor: string }
> = {
  danger: {
    icon: <Trash2 size={20} />,
    iconBg: "bg-red-100 text-red-600",
    confirmBtn:
      "bg-red-600 hover:bg-red-700 text-white focus:ring-red-500",
    titleColor: "text-red-700",
  },
  warning: {
    icon: <AlertTriangle size={20} />,
    iconBg: "bg-amber-100 text-amber-600",
    confirmBtn:
      "bg-amber-500 hover:bg-amber-600 text-white focus:ring-amber-400",
    titleColor: "text-amber-700",
  },
  info: {
    icon: <Info size={20} />,
    iconBg: "bg-blue-100 text-blue-600",
    confirmBtn:
      "bg-primary hover:opacity-90 text-white focus:ring-primary",
    titleColor: "text-foreground",
  },
  success: {
    icon: <CheckCircle size={20} />,
    iconBg: "bg-green-100 text-green-600",
    confirmBtn:
      "bg-green-600 hover:bg-green-700 text-white focus:ring-green-500",
    titleColor: "text-green-700",
  },
};

export default function ConfirmModal({
  isOpen,
  title,
  message,
  confirmLabel = "Confirm",
  cancelLabel = "Cancel",
  variant = "danger",
  isLoading = false,
  onConfirm,
  onCancel,
}: ConfirmModalProps) {
  const cancelRef = useRef<HTMLButtonElement>(null);
  const cfg = variantConfig[variant];

  // Keyboard: Escape → cancel
  useEffect(() => {
    if (!isOpen) return;
    const handler = (e: KeyboardEvent) => {
      if (e.key === "Escape") onCancel();
    };
    window.addEventListener("keydown", handler);
    return () => window.removeEventListener("keydown", handler);
  }, [isOpen, onCancel]);

  // Auto-focus Cancel button for safety
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => cancelRef.current?.focus(), 50);
    }
  }, [isOpen]);

  if (!isOpen) return null;

  return createPortal(
    <div
      className="fixed inset-0 z-[9999] flex items-center justify-center p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="confirm-modal-title"
    >
      {/* Backdrop */}
      <div
        className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
        onClick={onCancel}
      />

      {/* Panel */}
      <div className="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-border animate-in fade-in zoom-in-95 duration-200">
        {/* Close button */}
        <button
          onClick={onCancel}
          className="absolute top-3 right-3 rounded-lg p-1.5 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors"
          aria-label="Close"
        >
          <X size={16} />
        </button>

        {/* Icon + Title */}
        <div className="flex flex-col items-center text-center gap-3 mb-4">
          <div className={`flex h-12 w-12 items-center justify-center rounded-full ${cfg.iconBg}`}>
            {cfg.icon}
          </div>
          <div>
            <h2
              id="confirm-modal-title"
              className={`text-base font-bold ${cfg.titleColor}`}
            >
              {title}
            </h2>
            <p className="mt-1 text-xs text-muted font-medium leading-relaxed">
              {message}
            </p>
          </div>
        </div>

        {/* Divider */}
        <div className="border-t border-border my-4" />

        {/* Actions */}
        <div className="flex gap-2">
          <button
            ref={cancelRef}
            onClick={onCancel}
            disabled={isLoading}
            className="flex-1 rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50 active:scale-95 transition-all disabled:opacity-50"
          >
            {cancelLabel}
          </button>
          <button
            onClick={onConfirm}
            disabled={isLoading}
            className={`flex-1 rounded-xl px-4 py-2 text-xs font-bold active:scale-95 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 ${cfg.confirmBtn}`}
          >
            {isLoading ? (
              <span className="flex items-center justify-center gap-1.5">
                <span className="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white border-t-transparent" />
                Please wait...
              </span>
            ) : (
              confirmLabel
            )}
          </button>
        </div>
      </div>
    </div>,
    document.body
  );
}

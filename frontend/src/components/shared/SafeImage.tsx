"use client";

import { useState, useEffect } from "react";
import { Calendar, Building, Image as ImageIcon, Sparkles, User, LucideIcon } from "lucide-react";
import { cn } from "@/lib/utils";

interface SafeImageProps extends Omit<React.ImgHTMLAttributes<HTMLImageElement>, "src"> {
  src?: string | null;
  alt?: string;
  className?: string;
  containerClassName?: string;
  fallbackType?: "event" | "business" | "gallery" | "banner" | "avatar" | "generic";
  title?: string;
  date?: string;
  icon?: LucideIcon;
}

export default function SafeImage({
  src,
  alt = "",
  className = "h-full w-full object-cover",
  containerClassName = "relative h-full w-full overflow-hidden",
  fallbackType = "generic",
  title = "",
  date = "",
  icon: CustomIcon,
  ...props
}: SafeImageProps) {
  const [error, setError] = useState(false);

  // Reset error if src changes
  useEffect(() => {
    setError(false);
  }, [src]);

  const hasValidSrc = Boolean(src && src.trim() !== "" && !error);

  if (hasValidSrc) {
    return (
      <img
        src={src!}
        alt={alt || title}
        onError={() => setError(true)}
        className={className}
        {...props}
      />
    );
  }

  // Helper to parse date string for Event calendar badge
  const getParsedDate = () => {
    if (!date) return null;
    try {
      const d = new Date(date);
      if (isNaN(d.getTime())) return null;
      const monthShort = d.toLocaleDateString("en-US", { month: "short" }).toUpperCase();
      const dayNum = String(d.getDate()).padStart(2, "0");
      return { monthShort, dayNum };
    } catch {
      return null;
    }
  };

  const parsedDate = getParsedDate();
  const initialLetter = (title || alt || "?").trim().charAt(0).toUpperCase();

  /* All Fallbacks strictly use Blue Color Shades:
   * Primary Royal Blue: #00379D
   * Dark Navy Blue: #0F3459
   * Slate Navy: #113552
   * Bright Royal Blue: #1d4ed8
   */

  // Fallback views with ONLY BLUE color variations
  if (fallbackType === "event") {
    return (
      <div className={cn("h-full w-full bg-gradient-to-br from-[#00379D] via-[#0F3459] to-[#091E36] flex flex-col items-center justify-center p-3 relative overflow-hidden select-none", containerClassName)}>
        {/* Subtle Mesh Dot Pattern */}
        <div
          className="absolute inset-0 opacity-20 pointer-events-none"
          style={{
            backgroundImage: "radial-gradient(circle, #fff 1px, transparent 1px)",
            backgroundSize: "12px 12px",
          }}
        />

        {/* Ambient Blue Glow Blobs */}
        <div className="absolute -top-8 -right-8 w-24 h-24 bg-[#1d4ed8]/30 rounded-full blur-xl pointer-events-none" />
        <div className="absolute -bottom-8 -left-8 w-24 h-24 bg-[#00379D]/40 rounded-full blur-xl pointer-events-none" />

        {/* 3D Calendar Sheet in Blue Theme */}
        <div className="relative z-10 flex flex-col items-center justify-center">
          {parsedDate ? (
            <div className="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white shadow-2xl overflow-hidden border border-white/70 flex flex-col items-center transform transition-transform duration-300 group-hover:scale-105">
              {/* Top Bar: Royal Blue */}
              <div className="w-full bg-gradient-to-r from-[#00379D] to-[#1d4ed8] py-0.5 text-center shadow-xs">
                <span className="text-[10px] font-black tracking-widest text-white uppercase block leading-none">
                  {parsedDate.monthShort}
                </span>
              </div>
              {/* Day Number: Dark Navy */}
              <div className="flex-1 flex items-center justify-center bg-white w-full">
                <span className="text-xl sm:text-2xl font-black text-[#0F3459] leading-none tracking-tight">
                  {parsedDate.dayNum}
                </span>
              </div>
            </div>
          ) : (
            <div className="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/25 flex items-center justify-center text-white shadow-lg">
              {CustomIcon ? <CustomIcon size={24} /> : <Calendar size={24} />}
            </div>
          )}

          {title && (
            <span className="mt-2.5 text-[11px] font-bold text-white/95 truncate max-w-[85%] bg-[#0F3459]/75 backdrop-blur-xs px-2.5 py-0.5 rounded-full border border-white/20">
              {title}
            </span>
          )}
        </div>
      </div>
    );
  }

  if (fallbackType === "business") {
    return (
      <div className={cn("h-full w-full bg-gradient-to-br from-[#00379D] via-[#0F3459] to-[#113552] flex items-center justify-center p-2 relative overflow-hidden select-none text-white font-extrabold", containerClassName)}>
        <div
          className="absolute inset-0 opacity-15 pointer-events-none"
          style={{
            backgroundImage: "radial-gradient(circle, #fff 1px, transparent 1px)",
            backgroundSize: "10px 10px",
          }}
        />
        <div className="absolute -bottom-6 -right-6 w-16 h-16 bg-[#2563eb]/25 rounded-full blur-lg pointer-events-none" />
        <div className="relative z-10 flex items-center justify-center">
          {initialLetter !== "?" ? (
            <span className="text-xl sm:text-2xl font-extrabold tracking-wider text-white drop-shadow-md">
              {initialLetter}
            </span>
          ) : (
            <Building size={22} className="text-white drop-shadow-md" />
          )}
        </div>
      </div>
    );
  }

  if (fallbackType === "gallery") {
    return (
      <div className={cn("h-full w-full bg-gradient-to-br from-[#1d4ed8] via-[#00379D] to-[#0F3459] flex flex-col items-center justify-center p-3 relative overflow-hidden select-none", containerClassName)}>
        <div
          className="absolute inset-0 opacity-15 pointer-events-none"
          style={{
            backgroundImage: "radial-gradient(circle, #fff 1px, transparent 1px)",
            backgroundSize: "12px 12px",
          }}
        />
        <div className="absolute -top-6 -left-6 w-20 h-20 bg-[#3b82f6]/30 rounded-full blur-lg pointer-events-none" />
        <div className="relative z-10 flex flex-col items-center justify-center text-white">
          <div className="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center shadow-lg mb-1">
            <ImageIcon size={22} />
          </div>
          {title && (
            <span className="text-[10px] font-semibold text-white/90 line-clamp-1 max-w-[85%] text-center">
              {title}
            </span>
          )}
        </div>
      </div>
    );
  }

  if (fallbackType === "avatar") {
    return (
      <div className={cn("h-full w-full bg-gradient-to-br from-[#00379D] to-[#0F3459] flex items-center justify-center text-white font-bold select-none", containerClassName)}>
        {initialLetter !== "?" ? (
          <span className="text-sm font-extrabold">{initialLetter}</span>
        ) : (
          <User size={16} />
        )}
      </div>
    );
  }

  if (fallbackType === "banner") {
    return (
      <div className={cn("h-full w-full bg-gradient-to-r from-[#0F3459] via-[#00379D] to-[#091E36] flex items-center justify-center relative overflow-hidden select-none", containerClassName)}>
        <div
          className="absolute inset-0 opacity-20 pointer-events-none"
          style={{
            backgroundImage: "radial-gradient(circle, #fff 1px, transparent 1px)",
            backgroundSize: "16px 16px",
          }}
        />
        <div className="relative z-10 flex items-center gap-2 text-white/90 font-semibold text-sm">
          <Sparkles size={18} className="text-sky-300" />
          <span>{title || "SABHA Community"}</span>
        </div>
      </div>
    );
  }

  // Default generic fallback using Blue theme palette
  return (
    <div className={cn("h-full w-full bg-gradient-to-br from-[#00379D] via-[#0F3459] to-[#113552] flex flex-col items-center justify-center p-3 relative overflow-hidden select-none", containerClassName)}>
      <div
        className="absolute inset-0 opacity-15 pointer-events-none"
        style={{
          backgroundImage: "radial-gradient(circle, #fff 1px, transparent 1px)",
          backgroundSize: "12px 12px",
        }}
      />
      <div className="relative z-10 flex flex-col items-center justify-center text-white">
        <Sparkles size={24} className="text-white/90 mb-1" />
        {title && (
          <span className="text-xs font-semibold text-white/90 line-clamp-1 max-w-[85%] text-center">
            {title}
          </span>
        )}
      </div>
    </div>
  );
}

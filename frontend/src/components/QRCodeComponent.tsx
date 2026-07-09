import React from "react";
import { QrCode } from "@/lib/qr";

interface QRCodeProps {
  value: string;
  size?: number;
  className?: string;
  bg?: string;
  fg?: string;
}

export const QRCodeComponent: React.FC<QRCodeProps> = ({
  value,
  size = 150,
  className = "",
  bg = "#ffffff",
  fg = "#000000",
}) => {
  try {
    const qr = QrCode.encodeText(value, QrCode.Ecc.MEDIUM);
    const numModules = qr.size;

    // Generate path data for dark modules
    let path = "";
    for (let y = 0; y < numModules; y++) {
      for (let x = 0; x < numModules; x++) {
        if (qr.getModule(x, y)) {
          path += `M${x},${y}h1v1h-1z`;
        }
      }
    }

    return (
      <svg
        width={size}
        height={size}
        viewBox={`0 0 ${numModules} ${numModules}`}
        className={className}
        style={{ backgroundColor: bg }}
      >
        <path d={path} fill={fg} shapeRendering="crispEdges" />
      </svg>
    );
  } catch (error) {
    console.error("Failed to generate QR Code SVG path", error);
    return null;
  }
};

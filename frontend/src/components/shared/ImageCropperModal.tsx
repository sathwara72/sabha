"use client";

import React, { useState, useRef, useEffect } from "react";
import { createPortal } from "react-dom";
import { Crop, ZoomIn, ZoomOut, RotateCw, Check, X, Move } from "lucide-react";

interface ImageCropperModalProps {
  isOpen: boolean;
  imageSrc: string;
  aspectRatio?: number; // width / height ratio e.g., 3/1 for cover banner, 1/1 for logo
  title?: string;
  onClose: () => void;
  onCropComplete: (croppedFile: File, previewUrl: string) => void;
}

export default function ImageCropperModal({
  isOpen,
  imageSrc,
  aspectRatio = 3 / 1, // Default banner aspect ratio ~ 3:1
  title = "Crop Image Portion",
  onClose,
  onCropComplete,
}: ImageCropperModalProps) {
  const [zoom, setZoom] = useState(1);
  const [offset, setOffset] = useState({ x: 0, y: 0 });
  const [isDragging, setIsDragging] = useState(false);
  const [dragStart, setDragStart] = useState<{ clientX: number; clientY: number; offsetX: number; offsetY: number }>({
    clientX: 0,
    clientY: 0,
    offsetX: 0,
    offsetY: 0,
  });
  const [imageLoaded, setImageLoaded] = useState(false);
  const [naturalSize, setNaturalSize] = useState({ width: 0, height: 0 });

  const containerRef = useRef<HTMLDivElement>(null);
  const imageRef = useRef<HTMLImageElement>(null);

  useEffect(() => {
    if (isOpen) {
      setZoom(1);
      setOffset({ x: 0, y: 0 });
      setImageLoaded(false);
    }
  }, [isOpen, imageSrc]);

  if (!isOpen || !imageSrc) return null;

  const handleImageLoad = (e: React.SyntheticEvent<HTMLImageElement>) => {
    const img = e.currentTarget;
    setNaturalSize({ width: img.naturalWidth, height: img.naturalHeight });
    setImageLoaded(true);
  };

  // Mouse & Touch Drag Handlers
  const handleMouseDown = (e: React.MouseEvent | React.TouchEvent) => {
    setIsDragging(true);
    const clientX = "touches" in e ? e.touches[0].clientX : e.clientX;
    const clientY = "touches" in e ? e.touches[0].clientY : e.clientY;
    setDragStart({ clientX, clientY, offsetX: offset.x, offsetY: offset.y });
  };

  const handleMouseMove = (e: React.MouseEvent | React.TouchEvent) => {
    if (!isDragging) return;
    const clientX = "touches" in e ? e.touches[0].clientX : e.clientX;
    const clientY = "touches" in e ? e.touches[0].clientY : e.clientY;

    const deltaX = clientX - dragStart.clientX;
    const deltaY = clientY - dragStart.clientY;

    setOffset({
      x: dragStart.offsetX + deltaX,
      y: dragStart.offsetY + deltaY,
    });
  };

  const handleMouseUp = () => {
    setIsDragging(false);
  };

  // Crop & Export via Canvas
  const handleApplyCrop = () => {
    if (!imageRef.current || !containerRef.current) return;

    const img = imageRef.current;
    const container = containerRef.current;

    const containerRect = container.getBoundingClientRect();
    const cropBoxWidth = containerRect.width;
    const cropBoxHeight = containerRect.width / aspectRatio;

    // Calculate crop parameters on the natural image
    const canvas = document.createElement("canvas");
    const outputWidth = Math.min(1920, naturalSize.width || 1200);
    const outputHeight = Math.round(outputWidth / aspectRatio);

    canvas.width = outputWidth;
    canvas.height = outputHeight;
    const ctx = canvas.getContext("2d");

    if (!ctx) return;

    // Fill canvas background
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, outputWidth, outputHeight);

    // Compute scale between displayed image inside crop box and natural image
    const displayedImageWidth = img.width * zoom;
    const displayedImageHeight = img.height * zoom;

    const scaleX = naturalSize.width / (img.width || 1);
    const scaleY = naturalSize.height / (img.height || 1);

    // Center offset relative to crop box
    const cropCenterX = cropBoxWidth / 2;
    const cropCenterY = cropBoxHeight / 2;

    const imgCenterX = cropBoxWidth / 2 + offset.x;
    const imgCenterY = cropBoxHeight / 2 + offset.y;

    // Source rect on natural image
    const srcW = (cropBoxWidth / (zoom * (img.width / naturalSize.width))) ;
    const srcH = (cropBoxHeight / (zoom * (img.height / naturalSize.height)));

    const srcX = (naturalSize.width / 2) - (offset.x * scaleX / zoom) - (srcW / 2);
    const srcY = (naturalSize.height / 2) - (offset.y * scaleY / zoom) - (srcH / 2);

    ctx.drawImage(
      img,
      Math.max(0, srcX),
      Math.max(0, srcY),
      Math.min(naturalSize.width, srcW),
      Math.min(naturalSize.height, srcH),
      0,
      0,
      outputWidth,
      outputHeight
    );

    canvas.toBlob(
      (blob) => {
        if (!blob) return;
        const croppedFile = new File([blob], `cropped_cover_${Date.now()}.jpeg`, {
          type: "image/jpeg",
        });
        const previewUrl = URL.createObjectURL(blob);
        onCropComplete(croppedFile, previewUrl);
        onClose();
      },
      "image/jpeg",
      0.92
    );
  };

  const modalContent = (
    <div className="fixed inset-0 z-[9999] flex items-center justify-center p-4 font-outfit">
      {/* Backdrop */}
      <div
        className="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity"
        onClick={onClose}
      />

      {/* Modal Box */}
      <div className="relative z-50 w-full max-w-md bg-white rounded-3xl p-5 shadow-2xl border border-slate-200/80 space-y-3">
        {/* Header */}
        <div className="flex items-center justify-between border-b border-slate-100 pb-2.5">
          <div>
            <h3 className="text-sm font-extrabold text-slate-900 flex items-center gap-2">
              <Crop className="text-primary" size={16} /> {title}
            </h3>
            <p className="text-[10px] font-medium text-slate-500 mt-0.5">
              Drag image to position & use slider to zoom portion
            </p>
          </div>
          <button
            onClick={onClose}
            className="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer"
          >
            <X size={16} />
          </button>
        </div>

        {/* Interactive Cropper Workspace */}
        <div className="relative w-full bg-slate-950 rounded-xl overflow-hidden shadow-inner flex flex-col items-center justify-center h-[180px] sm:h-[200px] select-none">
          {/* Outer Mask Container */}
          <div
            ref={containerRef}
            onMouseDown={handleMouseDown}
            onMouseMove={handleMouseMove}
            onMouseUp={handleMouseUp}
            onTouchStart={handleMouseDown}
            onTouchMove={handleMouseMove}
            onTouchEnd={handleMouseUp}
            className="relative w-full h-full overflow-hidden flex items-center justify-center cursor-grab active:cursor-grabbing"
            style={{ aspectRatio: `${aspectRatio}` }}
          >
            {/* Image Layer */}
            <img
              ref={imageRef}
              src={imageSrc}
              alt="Crop target"
              onLoad={handleImageLoad}
              draggable={false}
              style={{
                transform: `translate(${offset.x}px, ${offset.y}px) scale(${zoom})`,
                transition: isDragging ? "none" : "transform 0.1s ease-out",
                maxHeight: "100%",
                maxWidth: "100%",
                objectFit: "contain",
              }}
              className="pointer-events-none select-none"
            />

            {/* Grid overlay for framing guide */}
            <div className="absolute inset-0 border-2 border-white/80 pointer-events-none rounded-xl shadow-[0_0_0_9999px_rgba(0,0,0,0.65)]">
              <div className="w-full h-full grid grid-cols-3 grid-rows-3">
                <div className="border-r border-b border-white/25" />
                <div className="border-r border-b border-white/25" />
                <div className="border-b border-white/25" />
                <div className="border-r border-b border-white/25" />
                <div className="border-r border-b border-white/25" />
                <div className="border-b border-white/25" />
                <div className="border-r border-white/25" />
                <div className="border-r border-white/25" />
                <div />
              </div>
            </div>

            <div className="absolute bottom-2 right-2 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-[10px] font-bold text-white flex items-center gap-1">
              <Move size={12} className="text-primary" /> Drag to adjust position
            </div>
          </div>
        </div>

        {/* Controls: Zoom Slider & Reset */}
        <div className="flex items-center gap-4 bg-slate-50 p-3 rounded-2xl border border-slate-100">
          <span className="text-xs font-bold text-slate-700 flex items-center gap-1.5 shrink-0">
            <ZoomIn size={14} className="text-primary" /> Zoom:
          </span>
          <input
            type="range"
            min="1"
            max="3"
            step="0.05"
            value={zoom}
            onChange={(e) => setZoom(parseFloat(e.target.value))}
            className="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary"
          />
          <span className="text-xs font-bold text-slate-900 w-10 text-right">
            {Math.round(zoom * 100)}%
          </span>
          <button
            type="button"
            onClick={() => {
              setZoom(1);
              setOffset({ x: 0, y: 0 });
            }}
            className="text-[11px] font-bold text-slate-500 hover:text-primary transition-colors shrink-0"
          >
            Reset
          </button>
        </div>

        {/* Footer Actions */}
        <div className="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
          <button
            type="button"
            onClick={onClose}
            className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="button"
            onClick={handleApplyCrop}
            className="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all cursor-pointer"
          >
            <Check size={15} /> Crop & Save Portion
          </button>
        </div>
      </div>
    </div>
  );

  return typeof window !== "undefined" ? createPortal(modalContent, document.body) : null;
}

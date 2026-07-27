"use client";

import React, { useState, useRef, useEffect, useCallback } from "react";
import { createPortal } from "react-dom";
import {
  Crop, ZoomIn, RotateCw, Check, X, Move, Maximize2
} from "lucide-react";

interface ImageCropperModalProps {
  isOpen: boolean;
  imageSrc: string;
  aspectRatio?: number; // width / height ratio e.g., 3.2/1 for cover banner, 1/1 for logo
  title?: string;
  onClose: () => void;
  onCropComplete: (croppedFile: File, previewUrl: string) => void;
}

type AspectRatioMode = "default" | "free" | "1:1" | "16:9" | "4:3";

export default function ImageCropperModal({
  isOpen,
  imageSrc,
  aspectRatio = 3.2 / 1,
  title = "Crop Image",
  onClose,
  onCropComplete,
}: ImageCropperModalProps) {
  // 1. useState: Zoom & Rotation state
  const [zoom, setZoom] = useState(1);
  const [rotation, setRotation] = useState(0);

  // 2. useState: Aspect ratio mode
  const [aspectMode, setAspectMode] = useState<AspectRatioMode>("free");

  // 3. useState: Natural size of loaded image
  const [naturalSize, setNaturalSize] = useState({ width: 0, height: 0 });

  // 4. useState: Crop Box position & size in percentage (0 - 100) relative to displayed image
  const [cropBox, setCropBox] = useState<{ x: number; y: number; w: number; h: number }>({
    x: 0,
    y: 0,
    w: 100,
    h: 100,
  });

  // 5. useState: Active dragging handle
  const [activeHandle, setActiveHandle] = useState<string | null>(null);

  // 6. useState: Drag start coords and crop box
  const [dragStart, setDragStart] = useState<{
    clientX: number;
    clientY: number;
    crop: { x: number; y: number; w: number; h: number };
  }>({
    clientX: 0,
    clientY: 0,
    crop: { x: 0, y: 0, w: 100, h: 100 },
  });

  // 7. useState: Bounding rect of displayed image relative to workspace
  const [imgBox, setImgBox] = useState<{ left: number; top: number; width: number; height: number }>({
    left: 0,
    top: 0,
    width: 0,
    height: 0,
  });

  // 8. useRef: DOM elements
  const workspaceRef = useRef<HTMLDivElement>(null);
  const imageRef = useRef<HTMLImageElement>(null);

  // 9. useCallback: Calculate numeric ratio based on mode
  const getTargetRatio = useCallback(() => {
    switch (aspectMode) {
      case "default":
        return aspectRatio;
      case "1:1":
        return 1;
      case "16:9":
        return 16 / 9;
      case "4:3":
        return 4 / 3;
      case "free":
      default:
        return 0; // freeform
    }
  }, [aspectMode, aspectRatio]);

  // 10. useCallback: Update rendered image bounding box relative to workspace container
  const updateImgBox = useCallback(() => {
    if (!imageRef.current || !workspaceRef.current) return;
    const imgRect = imageRef.current.getBoundingClientRect();
    const wsRect = workspaceRef.current.getBoundingClientRect();
    if (wsRect.width > 0 && wsRect.height > 0 && imgRect.width > 0 && imgRect.height > 0) {
      setImgBox({
        left: imgRect.left - wsRect.left,
        top: imgRect.top - wsRect.top,
        width: imgRect.width,
        height: imgRect.height,
      });
    }
  }, []);

  // 11. useCallback: Reset crop box position relative to image bounds
  const resetCropBox = useCallback(() => {
    const targetRatio = getTargetRatio();

    let displayW = imgBox.width;
    let displayH = imgBox.height;

    if (displayW === 0 || displayH === 0) {
      if (imageRef.current && workspaceRef.current) {
        const imgRect = imageRef.current.getBoundingClientRect();
        displayW = imgRect.width;
        displayH = imgRect.height;
      }
    }

    if (targetRatio > 0 && displayW > 0 && displayH > 0) {
      let wPercent = 95;
      let hPercent = ((displayW * 0.95) / targetRatio / displayH) * 100;

      if (hPercent > 95) {
        hPercent = 95;
        wPercent = ((displayH * 0.95) * targetRatio / displayW) * 100;
      }

      const xPercent = Math.max(0, (100 - wPercent) / 2);
      const yPercent = Math.max(0, (100 - hPercent) / 2);

      setCropBox({
        x: xPercent,
        y: yPercent,
        w: Math.min(100, wPercent),
        h: Math.min(100, hPercent),
      });
    } else {
      setCropBox({ x: 0, y: 0, w: 100, h: 100 });
    }
  }, [getTargetRatio, imgBox.width, imgBox.height]);

  // 12. useCallback: Process dragging move (relative to displayed image box)
  const handleDragMove = useCallback((e: MouseEvent | TouchEvent) => {
    if (!activeHandle || !workspaceRef.current) return;

    const clientX = "touches" in e ? e.touches[0].clientX : e.clientX;
    const clientY = "touches" in e ? e.touches[0].clientY : e.clientY;

    const currentImgWidth = imgBox.width || (imageRef.current ? imageRef.current.getBoundingClientRect().width : 300);
    const currentImgHeight = imgBox.height || (imageRef.current ? imageRef.current.getBoundingClientRect().height : 200);

    if (currentImgWidth === 0 || currentImgHeight === 0) return;

    const deltaXPercent = ((clientX - dragStart.clientX) / currentImgWidth) * 100;
    const deltaYPercent = ((clientY - dragStart.clientY) / currentImgHeight) * 100;

    const start = dragStart.crop;

    if (activeHandle === "move") {
      const newX = Math.max(0, Math.min(100 - start.w, start.x + deltaXPercent));
      const newY = Math.max(0, Math.min(100 - start.h, start.y + deltaYPercent));
      setCropBox({ ...start, x: newX, y: newY });
      return;
    }

    let newX = start.x;
    let newY = start.y;
    let newW = start.w;
    let newH = start.h;

    const targetRatio = getTargetRatio();
    const isFree = aspectMode === "free";

    if (activeHandle.includes("e")) {
      newW = Math.max(8, Math.min(100 - start.x, start.w + deltaXPercent));
    }
    if (activeHandle.includes("w")) {
      const possibleW = start.w - deltaXPercent;
      if (possibleW >= 8 && start.x + deltaXPercent >= 0) {
        newX = start.x + deltaXPercent;
        newW = possibleW;
      }
    }

    if (activeHandle.includes("s")) {
      newH = Math.max(8, Math.min(100 - start.y, start.h + deltaYPercent));
    }
    if (activeHandle.includes("n")) {
      const possibleH = start.h - deltaYPercent;
      if (possibleH >= 8 && start.y + deltaYPercent >= 0) {
        newY = start.y + deltaYPercent;
        newH = possibleH;
      }
    }

    if (!isFree && targetRatio > 0 && currentImgWidth > 0 && currentImgHeight > 0) {
      if (activeHandle === "n" || activeHandle === "s") {
        newW = (newH * targetRatio * currentImgHeight) / currentImgWidth;
        if (newX + newW > 100) {
          newW = Math.max(5, 100 - newX);
          newH = (newW * currentImgWidth) / (targetRatio * currentImgHeight);
        }
      } else {
        const calculatedH = (newW * currentImgWidth) / (targetRatio * currentImgHeight);
        if (newY + calculatedH <= 100) {
          newH = calculatedH;
        } else {
          newW = (newH * targetRatio * currentImgHeight) / currentImgWidth;
        }
      }
    }

    setCropBox({
      x: Math.max(0, Math.min(100 - newW, newX)),
      y: Math.max(0, Math.min(100 - newH, newY)),
      w: Math.max(5, Math.min(100, newW)),
      h: Math.max(5, Math.min(100, newH)),
    });
  }, [activeHandle, dragStart, aspectMode, getTargetRatio, imgBox.width, imgBox.height]);

  // 13. useEffect: Reset & update imgBox when isOpen, imageSrc, zoom, or rotation changes
  useEffect(() => {
    if (isOpen) {
      setZoom(1);
      setRotation(0);
      setAspectMode("free");
      const timer = setTimeout(() => {
        updateImgBox();
        resetCropBox();
      }, 50);
      return () => clearTimeout(timer);
    }
  }, [isOpen, imageSrc, updateImgBox, resetCropBox]);

  // 14. useEffect: Reset cropBox when aspectMode changes
  useEffect(() => {
    if (isOpen) {
      resetCropBox();
    }
  }, [isOpen, aspectMode, resetCropBox]);

  // 15. useEffect: Update imgBox when zoom or rotation changes
  useEffect(() => {
    if (isOpen) {
      const timer = setTimeout(() => updateImgBox(), 30);
      return () => clearTimeout(timer);
    }
  }, [isOpen, zoom, rotation, updateImgBox]);

  // 16. useEffect: Handle window resize to re-measure image bounds
  useEffect(() => {
    if (!isOpen) return;
    const handleResize = () => {
      updateImgBox();
    };
    window.addEventListener("resize", handleResize);
    return () => window.removeEventListener("resize", handleResize);
  }, [isOpen, updateImgBox]);

  // 17. useEffect: Drag pointer event listeners
  useEffect(() => {
    if (!activeHandle) return;

    const onPointerMove = (e: MouseEvent | TouchEvent) => handleDragMove(e);
    const onPointerUp = () => setActiveHandle(null);

    window.addEventListener("mousemove", onPointerMove);
    window.addEventListener("mouseup", onPointerUp);
    window.addEventListener("touchmove", onPointerMove);
    window.addEventListener("touchend", onPointerUp);

    return () => {
      window.removeEventListener("mousemove", onPointerMove);
      window.removeEventListener("mouseup", onPointerUp);
      window.removeEventListener("touchmove", onPointerMove);
      window.removeEventListener("touchend", onPointerUp);
    };
  }, [activeHandle, handleDragMove]);

  // Non-hook helper functions
  const handleImageLoad = (e: React.SyntheticEvent<HTMLImageElement>) => {
    const img = e.currentTarget;
    setNaturalSize({ width: img.naturalWidth, height: img.naturalHeight });
    updateImgBox();
    resetCropBox();
  };

  const handleStartDrag = (handle: string, e: React.MouseEvent | React.TouchEvent) => {
    e.stopPropagation();
    e.preventDefault();

    const clientX = "touches" in e ? e.touches[0].clientX : e.clientX;
    const clientY = "touches" in e ? e.touches[0].clientY : e.clientY;

    setActiveHandle(handle);
    setDragStart({
      clientX,
      clientY,
      crop: { ...cropBox },
    });
  };

  const handleApplyCrop = () => {
    if (!imageRef.current) return;

    const img = imageRef.current;
    const NW = naturalSize.width || img.naturalWidth || 800;
    const NH = naturalSize.height || img.naturalHeight || 600;

    const isRotated90or270 = rotation === 90 || rotation === 270;
    const rotWidth = isRotated90or270 ? NH : NW;
    const rotHeight = isRotated90or270 ? NW : NH;

    const fullCanvas = document.createElement("canvas");
    fullCanvas.width = rotWidth;
    fullCanvas.height = rotHeight;
    const fullCtx = fullCanvas.getContext("2d");
    if (!fullCtx) return;

    fullCtx.translate(rotWidth / 2, rotHeight / 2);
    fullCtx.rotate((rotation * Math.PI) / 180);
    fullCtx.drawImage(img, -NW / 2, -NH / 2, NW, NH);

    // cropBox percentages are 100% relative to actual image natural resolution
    const cropX = Math.round((cropBox.x / 100) * rotWidth);
    const cropY = Math.round((cropBox.y / 100) * rotHeight);
    const cropW = Math.round((cropBox.w / 100) * rotWidth);
    const cropH = Math.round((cropBox.h / 100) * rotHeight);

    const outputWidth = Math.max(10, cropW);
    const outputHeight = Math.max(10, cropH);

    let finalW = outputWidth;
    let finalH = outputHeight;

    if (aspectRatio > 0 && Math.abs(outputWidth / outputHeight - aspectRatio) > 0.08) {
      const currentRatio = outputWidth / outputHeight;
      if (currentRatio < aspectRatio) {
        finalH = outputHeight;
        finalW = Math.round(outputHeight * aspectRatio);
      } else {
        finalW = outputWidth;
        finalH = Math.round(outputWidth / aspectRatio);
      }
    }

    const canvas = document.createElement("canvas");
    canvas.width = finalW;
    canvas.height = finalH;
    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    if (finalW !== outputWidth || finalH !== outputHeight) {
      try {
        ctx.filter = "blur(24px)";
        ctx.drawImage(fullCanvas, cropX, cropY, cropW, cropH, -30, -30, finalW + 60, finalH + 60);
        ctx.filter = "none";
      } catch (e) {
        ctx.fillStyle = "#0f172a";
        ctx.fillRect(0, 0, finalW, finalH);
      }

      ctx.fillStyle = "rgba(15, 23, 42, 0.45)";
      ctx.fillRect(0, 0, finalW, finalH);

      const drawX = Math.round((finalW - outputWidth) / 2);
      const drawY = Math.round((finalH - outputHeight) / 2);
      ctx.drawImage(
        fullCanvas,
        cropX,
        cropY,
        cropW,
        cropH,
        drawX,
        drawY,
        outputWidth,
        outputHeight
      );
    } else {
      ctx.fillStyle = "#ffffff";
      ctx.fillRect(0, 0, outputWidth, outputHeight);
      ctx.drawImage(
        fullCanvas,
        cropX,
        cropY,
        cropW,
        cropH,
        0,
        0,
        outputWidth,
        outputHeight
      );
    }

    canvas.toBlob(
      (blob) => {
        if (!blob) return;
        const croppedFile = new File([blob], `cropped_image_${Date.now()}.jpg`, {
          type: "image/jpeg",
        });
        const previewUrl = URL.createObjectURL(blob);
        onCropComplete(croppedFile, previewUrl);
        onClose();
      },
      "image/jpeg",
      0.93
    );
  };

  // CONDITIONAL RETURN IS STRICTLY AFTER ALL 17 HOOKS HAVE BEEN CALLED
  if (!isOpen || !imageSrc) return null;

  const modalContent = (
    <div className="fixed inset-0 z-[9999] flex items-center justify-center p-3 sm:p-5 font-outfit select-none">
      {/* Dark Blur Backdrop */}
      <div
        className="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity"
        onClick={onClose}
      />

      {/* Main Modal Box */}
      <div className="relative z-50 w-full max-w-lg bg-white rounded-2xl p-4 sm:p-5 shadow-2xl border border-slate-200/80 flex flex-col gap-3 max-h-[95vh] overflow-hidden">
        {/* Header */}
        <div className="flex items-center justify-between border-b border-slate-100 pb-2">
          <div>
            <h3 className="text-sm font-bold text-slate-900 flex items-center gap-2">
              <Crop className="text-primary" size={16} /> {title}
            </h3>
            <p className="text-[11px] font-medium text-slate-500 mt-0.5">
              Drag & resize the selection box over the image portion to crop
            </p>
          </div>
          <button
            onClick={onClose}
            className="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer"
          >
            <X size={16} />
          </button>
        </div>

        {/* Toolbar Controls: Selection & Rotate */}
        <div className="flex flex-wrap items-center justify-between gap-1.5 bg-slate-50 p-2 rounded-xl border border-slate-100">
          <div className="flex items-center gap-1.5 flex-wrap">
            <button
              type="button"
              onClick={() => {
                setAspectMode("free");
                setCropBox({ x: 0, y: 0, w: 100, h: 100 });
              }}
              className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-slate-200/80 text-[11px] font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
            >
              <Maximize2 size={12} className="text-primary" /> Select Full Image
            </button>
            <button
              type="button"
              onClick={() => {
                setAspectMode("default");
                resetCropBox();
              }}
              className={`px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer ${
                aspectMode === "default"
                  ? "bg-primary text-white shadow-xs"
                  : "bg-white text-slate-600 border border-slate-200/80 hover:bg-slate-100"
              }`}
            >
              Lock Banner Ratio (3.2:1)
            </button>
          </div>

          {/* Rotate button */}
          <button
            type="button"
            onClick={() => setRotation((prev) => (prev + 90) % 360)}
            className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-slate-200/80 text-[11px] font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer ml-auto"
          >
            <RotateCw size={12} className="text-primary" /> Rotate 90°
          </button>
        </div>

        {/* Interactive Cropper Workspace */}
        <div
          ref={workspaceRef}
          className="relative w-full bg-slate-950 rounded-xl overflow-hidden shadow-inner flex items-center justify-center min-h-[190px] sm:min-h-[220px] h-[240px] select-none"
        >
          {/* Base Image Layer */}
          <div className="relative w-full h-full flex items-center justify-center overflow-hidden p-3">
            <img
              ref={imageRef}
              src={imageSrc}
              alt="Crop workspace image"
              onLoad={handleImageLoad}
              draggable={false}
              style={{
                transform: `rotate(${rotation}deg) scale(${zoom})`,
                transition: "transform 0.15s ease-out",
                maxHeight: "100%",
                maxWidth: "100%",
                objectFit: "contain",
              }}
              className="pointer-events-none select-none max-h-full max-w-full"
            />
          </div>

          {/* Crop Overlay Container bound EXACTLY to displayed image bounds */}
          {imgBox.width > 0 && imgBox.height > 0 && (
            <div
              className="absolute pointer-events-none"
              style={{
                left: `${imgBox.left}px`,
                top: `${imgBox.top}px`,
                width: `${imgBox.width}px`,
                height: `${imgBox.height}px`,
              }}
            >
              {/* Dark Mask outside crop box */}
              <div
                className="absolute inset-0 pointer-events-none"
                style={{
                  clipPath: `polygon(
                    0% 0%, 100% 0%, 100% 100%, 0% 100%, 0% 0%,
                    ${cropBox.x}% ${cropBox.y}%,
                    ${cropBox.x}% ${cropBox.y + cropBox.h}%,
                    ${cropBox.x + cropBox.w}% ${cropBox.y + cropBox.h}%,
                    ${cropBox.x + cropBox.w}% ${cropBox.y}%,
                    ${cropBox.x}% ${cropBox.y}%
                  )`,
                  backgroundColor: "rgba(15, 23, 42, 0.75)",
                }}
              />

              {/* Interactive Resizable & Draggable Crop Box */}
              <div
                onMouseDown={(e) => handleStartDrag("move", e)}
                onTouchStart={(e) => handleStartDrag("move", e)}
                className="absolute border-2 border-white rounded-lg shadow-2xl cursor-grab active:cursor-grabbing group pointer-events-auto"
                style={{
                  left: `${cropBox.x}%`,
                  top: `${cropBox.y}%`,
                  width: `${cropBox.w}%`,
                  height: `${cropBox.h}%`,
                }}
              >
                {/* 3x3 Rule-of-Thirds Grid */}
                <div className="w-full h-full grid grid-cols-3 grid-rows-3 pointer-events-none opacity-40">
                  <div className="border-r border-b border-white/60" />
                  <div className="border-r border-b border-white/60" />
                  <div className="border-b border-white/60" />
                  <div className="border-r border-b border-white/60" />
                  <div className="border-r border-b border-white/60" />
                  <div className="border-b border-white/60" />
                  <div className="border-r border-white/60" />
                  <div className="border-r border-white/60" />
                  <div />
                </div>

                {/* Move Badge */}
                <div className="absolute inset-0 flex items-center justify-center pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                  <div className="bg-black/60 backdrop-blur-sm px-3 py-1 rounded-full text-[10px] font-bold text-white flex items-center gap-1.5 border border-white/20 shadow-md">
                    <Move size={12} className="text-primary" /> Drag box to position
                  </div>
                </div>

                {/* 8 Drag Handles */}
                {[
                  { id: "nw", pos: "-top-2 -left-2 cursor-nwse-resize" },
                  { id: "n", pos: "-top-2 left-1/2 -translate-x-1/2 cursor-ns-resize" },
                  { id: "ne", pos: "-top-2 -right-2 cursor-nesw-resize" },
                  { id: "e", pos: "top-1/2 -right-2 -translate-y-1/2 cursor-ew-resize" },
                  { id: "se", pos: "-bottom-2 -right-2 cursor-nwse-resize" },
                  { id: "s", pos: "-bottom-2 left-1/2 -translate-x-1/2 cursor-ns-resize" },
                  { id: "sw", pos: "-bottom-2 -left-2 cursor-nesw-resize" },
                  { id: "w", pos: "top-1/2 -left-2 -translate-y-1/2 cursor-ew-resize" },
                ].map((handle) => (
                  <div
                    key={handle.id}
                    onMouseDown={(e) => handleStartDrag(handle.id, e)}
                    onTouchStart={(e) => handleStartDrag(handle.id, e)}
                    className={`absolute w-3.5 h-3.5 bg-white border-2 border-primary rounded-sm shadow-md hover:scale-125 transition-transform ${handle.pos}`}
                  />
                ))}
              </div>
            </div>
          )}
        </div>

        {/* Zoom Slider Control */}
        <div className="flex items-center gap-3 bg-slate-50 p-2 px-3 rounded-xl border border-slate-100">
          <span className="text-[11px] font-bold text-slate-700 flex items-center gap-1 shrink-0">
            <ZoomIn size={13} className="text-primary" /> Zoom:
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
          <span className="text-[11px] font-extrabold text-slate-900 w-9 text-right shrink-0">
            {Math.round(zoom * 100)}%
          </span>
          <button
            type="button"
            onClick={() => {
              setZoom(1);
              setRotation(0);
              resetCropBox();
            }}
            className="text-[10px] font-bold text-slate-500 hover:text-primary transition-colors shrink-0"
          >
            Reset All
          </button>
        </div>

        {/* Footer Actions */}
        <div className="flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
          <button
            type="button"
            onClick={onClose}
            className="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="button"
            onClick={handleApplyCrop}
            className="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-5 py-1.5 text-xs font-bold text-white shadow-md hover:opacity-90 active:scale-95 transition-all cursor-pointer"
          >
            <Check size={14} /> Crop & Save Portion
          </button>
        </div>
      </div>
    </div>
  );

  return typeof window !== "undefined" ? createPortal(modalContent, document.body) : null;
}

"use client";

import { ReactNode } from "react";
import { motion } from "framer-motion";

interface PageHeaderProps {
  kicker?: string;
  title: ReactNode;
  subtitle?: string;
  /** Optional content rendered on the right (e.g. a count chip or action). */
  right?: ReactNode;
  /** Optional content rendered below the title (e.g. a search bar). */
  children?: ReactNode;
  align?: "left" | "center";
}

export default function PageHeader({
  kicker,
  title,
  subtitle,
  right,
  children,
  align = "left",
}: PageHeaderProps) {
  const centered = align === "center";

  return (
    <section className="hero-surface relative border-b border-border">
      <div className="mx-auto max-w-7xl px-6 py-9 lg:px-8 lg:py-11">
        <div
          className={
            centered
              ? "flex flex-col items-center text-center"
              : "flex flex-col gap-5 md:flex-row md:items-end md:justify-between"
          }
        >
          <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.3 }}
            className={centered ? "max-w-2xl" : "max-w-2xl"}
          >
            {kicker && (
              <div
                className={
                  centered
                    ? "mb-3 flex items-center justify-center gap-2.5"
                    : "mb-3 flex items-center gap-2.5"
                }
              >
                <span className="h-4 w-1.5 rounded-full bg-accent" />
                <span className="text-sm font-semibold text-accent">{kicker}</span>
              </div>
            )}
            <h1 className="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
              {title}
            </h1>
            {subtitle && (
              <p className="mt-2 text-base leading-relaxed text-muted">{subtitle}</p>
            )}
          </motion.div>

          {right && <div className="shrink-0">{right}</div>}
        </div>

        {children && <div className="mt-6">{children}</div>}
      </div>
    </section>
  );
}

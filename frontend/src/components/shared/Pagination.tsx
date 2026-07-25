"use client";

interface PaginationProps {
  currentPage: number;
  totalPages: number;
  totalItems: number;
  itemsPerPage: number;
  onPageChange: (page: number) => void;
  itemLabel?: string;
}

export function getPageNumbers(currentPage: number, totalPages: number): (number | string)[] {
  if (totalPages <= 7) {
    return Array.from({ length: totalPages }, (_, i) => i + 1);
  }
  if (currentPage <= 4) {
    return [1, 2, 3, 4, 5, "...", totalPages];
  }
  if (currentPage >= totalPages - 3) {
    return [1, "...", totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
  }
  return [1, "...", currentPage - 1, currentPage, currentPage + 1, "...", totalPages];
}

export default function Pagination({
  currentPage,
  totalPages,
  totalItems,
  itemsPerPage,
  onPageChange,
  itemLabel = "items",
}: PaginationProps) {
  if (totalPages <= 1) return null;

  const pages = getPageNumbers(currentPage, totalPages);
  const startItem = (currentPage - 1) * itemsPerPage + 1;
  const endItem = Math.min(currentPage * itemsPerPage, totalItems);

  return (
    <div className="flex flex-col sm:flex-row items-center justify-between border-t border-border pt-4 mt-2 gap-3">
      <p className="text-xs text-muted font-medium">
        Showing <span className="font-semibold text-foreground">{startItem}</span> to{" "}
        <span className="font-semibold text-foreground">{endItem}</span> of{" "}
        <span className="font-semibold text-foreground">{totalItems}</span> {itemLabel}
      </p>
      <div className="flex items-center gap-1 flex-wrap justify-center">
        <button
          onClick={() => onPageChange(Math.max(1, currentPage - 1))}
          disabled={currentPage === 1}
          className="rounded-lg border border-border bg-white px-2.5 py-1 text-xs font-medium text-foreground transition-colors hover:bg-slate-50 disabled:opacity-50 disabled:pointer-events-none cursor-pointer"
        >
          Previous
        </button>
        {pages.map((page, idx) =>
          typeof page === "string" ? (
            <span key={`ellipsis-${idx}`} className="px-1.5 text-xs text-muted-foreground font-semibold">
              ...
            </span>
          ) : (
            <button
              key={page}
              onClick={() => onPageChange(page)}
              className={`h-7 w-7 rounded-lg text-xs font-semibold border transition-all cursor-pointer ${
                currentPage === page
                  ? "border-primary bg-primary text-white"
                  : "border-border bg-white text-muted hover:bg-surface hover:text-foreground"
              }`}
            >
              {page}
            </button>
          )
        )}
        <button
          onClick={() => onPageChange(Math.min(totalPages, currentPage + 1))}
          disabled={currentPage === totalPages}
          className="rounded-lg border border-border bg-white px-2.5 py-1 text-xs font-medium text-foreground transition-colors hover:bg-slate-50 disabled:opacity-50 disabled:pointer-events-none cursor-pointer"
        >
          Next
        </button>
      </div>
    </div>
  );
}

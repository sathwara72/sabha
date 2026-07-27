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
    <div className="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-border bg-slate-50/50 rounded-b-2xl">
      <p className="text-xs text-slate-500 font-medium">
        Showing <span className="font-extrabold text-slate-800">{startItem}</span> to{" "}
        <span className="font-extrabold text-slate-800">{endItem}</span> of{" "}
        <span className="font-extrabold text-slate-800">{totalItems}</span> {itemLabel}
      </p>
      <div className="flex items-center gap-1.5 flex-wrap justify-center">
        <button
          onClick={() => onPageChange(Math.max(1, currentPage - 1))}
          disabled={currentPage === 1}
          className="inline-flex items-center justify-center rounded-xl border border-border bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition-all hover:bg-slate-100 hover:border-slate-300 disabled:opacity-40 disabled:pointer-events-none cursor-pointer shadow-xs active:scale-95"
        >
          Previous
        </button>
        {pages.map((page, idx) =>
          typeof page === "string" ? (
            <span key={`ellipsis-${idx}`} className="px-1 text-xs text-slate-400 font-bold">
              ...
            </span>
          ) : (
            <button
              key={page}
              onClick={() => onPageChange(page)}
              className={`h-7 min-w-[28px] px-2 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-xs flex items-center justify-center ${
                currentPage === page
                  ? "bg-primary text-white border border-primary shadow-sm shadow-primary/20 scale-105"
                  : "border border-border bg-white text-slate-600 hover:bg-slate-100 hover:text-foreground hover:border-slate-300 active:scale-95"
              }`}
            >
              {page}
            </button>
          )
        )}
        <button
          onClick={() => onPageChange(Math.min(totalPages, currentPage + 1))}
          disabled={currentPage === totalPages}
          className="inline-flex items-center justify-center rounded-xl border border-border bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition-all hover:bg-slate-100 hover:border-slate-300 disabled:opacity-40 disabled:pointer-events-none cursor-pointer shadow-xs active:scale-95"
        >
          Next
        </button>
      </div>
    </div>
  );
}

// Origin of the Laravel backend (no trailing slash).
// Override per-environment with NEXT_PUBLIC_API_ORIGIN (e.g. http://localhost:8000).
export const API_ORIGIN = (
  process.env.NEXT_PUBLIC_API_ORIGIN || "http://localhost:8000"
).replace(/\/$/, "");

// Base URL for the JSON API.
export const API_BASE_URL = (
  process.env.NEXT_PUBLIC_API_BASE_URL || `${API_ORIGIN}/api`
).replace(/\/$/, "");

export function hasMediaFile(path?: string | null): boolean {
  if (!path) return false;
  const str = String(path).trim().toLowerCase();
  if (
    !str ||
    str === "null" ||
    str === "undefined" ||
    str === "none" ||
    str === "n/a" ||
    str === "false" ||
    str === "0"
  ) {
    return false;
  }
  if (str.endsWith("/") || str.endsWith("\\")) return false;
  return true;
}

// Build an absolute URL for a backend-served asset path like "/storage/avatars/x.png".
// Returns "" for empty input and passes through already-absolute URLs untouched.
export function assetUrl(path?: string | null): string {
  if (!hasMediaFile(path)) return "";
  const str = String(path).trim();
  if (/^https?:\/\//i.test(str)) {
    // If it's a Google Drive URL, convert it to a direct image link
    if (str.includes("drive.google.com") || str.includes("docs.google.com")) {
      const idMatchQuery = str.match(/[?&]id=([^&]+)/);
      if (idMatchQuery && idMatchQuery[1]) {
        return `https://lh3.googleusercontent.com/d/${idMatchQuery[1]}`;
      }
      const idMatchPath = str.match(/\/file\/d\/([^/]+)/);
      if (idMatchPath && idMatchPath[1]) {
        return `https://lh3.googleusercontent.com/d/${idMatchPath[1]}`;
      }
      const idMatchD = str.match(/\/d\/([^/]+)/);
      if (idMatchD && idMatchD[1]) {
        return `https://lh3.googleusercontent.com/d/${idMatchD[1]}`;
      }
    }
    return str;
  }
  return `${API_ORIGIN}${str.startsWith("/") ? "" : "/"}${str}`;
}

export function parseGoogleMapsIframeSrc(input?: string | null): string | null {
  if (!input) return null;
  const str = input.trim();
  if (!str) return null;

  // If user pasted full <iframe ... src="https://www.google.com/maps/embed?..." ...></iframe>
  const srcMatch = str.match(/src=["']([^"']+)["']/i);
  if (srcMatch && srcMatch[1]) {
    return srcMatch[1];
  }

  // If user pasted direct URL
  if (/^https?:\/\//i.test(str)) {
    return str;
  }

  return null;
}

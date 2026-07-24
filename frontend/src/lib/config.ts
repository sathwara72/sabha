// Origin of the Laravel backend (no trailing slash).
// Override per-environment with NEXT_PUBLIC_API_ORIGIN (e.g. http://localhost:8000).
export const API_ORIGIN = (
  process.env.NEXT_PUBLIC_API_ORIGIN || "http://localhost:8000"
).replace(/\/$/, "");

// Base URL for the JSON API.
export const API_BASE_URL = (
  process.env.NEXT_PUBLIC_API_BASE_URL || `${API_ORIGIN}/api`
).replace(/\/$/, "");

// Build an absolute URL for a backend-served asset path like "/storage/avatars/x.png".
// Returns "" for empty input and passes through already-absolute URLs untouched.
export function assetUrl(path?: string | null): string {
  if (!path) return "";
  if (/^https?:\/\//i.test(path)) {
    // If it's a Google Drive URL, convert it to a direct image link
    if (path.includes("drive.google.com") || path.includes("docs.google.com")) {
      const idMatchQuery = path.match(/[?&]id=([^&]+)/);
      if (idMatchQuery && idMatchQuery[1]) {
        return `https://lh3.googleusercontent.com/d/${idMatchQuery[1]}`;
      }
      const idMatchPath = path.match(/\/file\/d\/([^/]+)/);
      if (idMatchPath && idMatchPath[1]) {
        return `https://lh3.googleusercontent.com/d/${idMatchPath[1]}`;
      }
      const idMatchD = path.match(/\/d\/([^/]+)/);
      if (idMatchD && idMatchD[1]) {
        return `https://lh3.googleusercontent.com/d/${idMatchD[1]}`;
      }
    }
    return path;
  }
  return `${API_ORIGIN}${path}`;
}

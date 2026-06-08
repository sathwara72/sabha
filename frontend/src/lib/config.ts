// Origin of the Laravel backend (no trailing slash).
// Override per-environment with NEXT_PUBLIC_API_ORIGIN (e.g. https://api.yourdomain.com).
export const API_ORIGIN = (
  process.env.NEXT_PUBLIC_API_ORIGIN || "http://localhost:8000"
).replace(/\/$/, "");

// Base URL for the JSON API.
export const API_BASE_URL = `${API_ORIGIN}/api`;

// Build an absolute URL for a backend-served asset path like "/storage/avatars/x.png".
// Returns "" for empty input and passes through already-absolute URLs untouched.
export function assetUrl(path?: string | null): string {
  if (!path) return "";
  if (/^https?:\/\//i.test(path)) return path;
  return `${API_ORIGIN}${path}`;
}

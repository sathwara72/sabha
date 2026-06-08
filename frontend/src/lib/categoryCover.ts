/**
 * Default cover photos for each business category.
 * Used when a business hasn't uploaded their own cover image.
 * All images from Unsplash with optimized quality/size parameters.
 */

const categoryCoverMap: Record<string, string> = {
  "Software Development":
    "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=1400&auto=format&fit=crop",
  "Supply Chain":
    "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1400&auto=format&fit=crop",
  "Digital Marketing":
    "https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1400&auto=format&fit=crop",
  "Construction":
    "https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=1400&auto=format&fit=crop",
  "Financial Services":
    "https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1400&auto=format&fit=crop",
  "Renewables":
    "https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=1400&auto=format&fit=crop",
  "Creative Agency":
    "https://images.unsplash.com/photo-1558655146-9f40138edfeb?q=80&w=1400&auto=format&fit=crop",
  "Venture Capital":
    "https://images.unsplash.com/photo-1559136555-9303baea8ebd?q=80&w=1400&auto=format&fit=crop",
};

const DEFAULT_COVER =
  "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1400&auto=format&fit=crop";

/**
 * Returns the appropriate cover image URL for a business.
 * Priority: uploaded cover → category default → generic fallback.
 */
export function getCoverImage(uploadedCover: string | undefined | null, category?: string): string {
  if (uploadedCover) return uploadedCover;
  if (category && categoryCoverMap[category]) return categoryCoverMap[category];
  return DEFAULT_COVER;
}

export { categoryCoverMap, DEFAULT_COVER };

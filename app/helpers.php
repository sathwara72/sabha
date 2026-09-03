<?php

if (! function_exists('admin_authorize')) {
    /**
     * Guards a mutating admin action. Full admins always pass; sub-admins
     * pass only when granted the specific module/ability; aborts 403
     * otherwise. Called at the top of the actual state-changing method
     * (not the "open modal" methods that precede it) — opening a confirm
     * dialog isn't itself a security boundary, submitting the mutation is.
     */
    function admin_authorize(string $module, string $ability): void
    {
        if (! auth()->check() || ! auth()->user()->hasModuleAbility($module, $ability)) {
            abort(403);
        }
    }
}

if (! function_exists('linkify_text')) {
    /**
     * Escapes a raw chat message body first (XSS safety), then wraps
     * http(s):// and www. URLs in clickable, new-tab anchor tags. Escaping
     * happens before linking so injected HTML in the message text can never
     * survive as markup — only the URLs this function itself adds do.
     */
    function linkify_text(string $body): string
    {
        $escaped = e($body);

        $pattern = '#(https?://[^\s<]+[^\s<\.\,\)]|www\.[^\s<]+[^\s<\.\,\)])#i';

        return preg_replace_callback($pattern, function ($matches) {
            $url = $matches[1];
            $href = str_starts_with(strtolower($url), 'http') ? $url : 'https://' . $url;

            return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer nofollow" class="underline">' . $url . '</a>';
        }, $escaped);
    }
}

if (! function_exists('has_media_file')) {
    /**
     * Mirrors the frontend's hasMediaFile() — filters out stray placeholder
     * strings ("null", "undefined", "n/a", ...) that can end up stored in
     * media path columns, not just genuinely empty values.
     */
    function has_media_file(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        $str = strtolower(trim($path));
        if (in_array($str, ['', 'null', 'undefined', 'none', 'n/a', 'false', '0'], true)) {
            return false;
        }

        return ! str_ends_with($str, '/') && ! str_ends_with($str, '\\');
    }
}

if (! function_exists('media_url')) {
    /**
     * Resolve a backend-stored media path (or already-absolute URL) to an
     * absolute URL. Verifies existence of local files so missing media
     * seamlessly falls back to brand placeholders instead of 404 broken images.
     */
    function media_url(?string $path): ?string
    {
        if (! has_media_file($path)) {
            return null;
        }

        $trimmed = trim($path);

        // Convert Google Drive view/open URLs to direct public CDN image thumbnails
        if (preg_match('#(?:drive\.google\.com/(?:file/d/|open\?(?:.*&)?id=|uc\?(?:.*&)?id=)|docs\.google\.com/uc\?(?:.*&)?id=)([a-zA-Z0-9_-]{25,})#i', $trimmed, $m)) {
            return 'https://lh3.googleusercontent.com/d/' . $m[1];
        }

        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return $trimmed;
        }

        $cleanPath = ltrim($trimmed, '/');
        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        if (str_starts_with($cleanPath, 'storage/')) {
            $relative = substr($cleanPath, 8);
            if (file_exists(storage_path('app/public/' . $relative))) {
                return asset($cleanPath);
            }
        }

        return null;
    }
}

if (! function_exists('temporary_media_url')) {
    /**
     * Safely resolve Livewire temporary upload URL across local & production,
     * ensuring HTTPS and host matching the current request.
     */
    function temporary_media_url($file): ?string
    {
        if (! $file || ! method_exists($file, 'temporaryUrl')) {
            return null;
        }

        try {
            $url = $file->temporaryUrl();
            $currentHost = request()->getSchemeAndHttpHost();
            if ($currentHost && ! str_contains($currentHost, 'localhost') && ! str_contains($currentHost, '127.0.0.1')) {
                $parsed = parse_url($url);
                if (isset($parsed['host']) && in_array($parsed['host'], ['localhost', '127.0.0.1'], true)) {
                    $pathAndQuery = ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
                    $url = rtrim($currentHost, '/') . $pathAndQuery;
                }
                if (str_starts_with($url, 'http://') && (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https')) {
                    $url = 'https://' . substr($url, 7);
                }
            }

            return $url;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

if (! function_exists('parse_google_maps_iframe_src')) {
    /**
     * Extracts a usable Google Maps embed src from either a pasted
     * <iframe> snippet or a raw Maps URL. Mirrors the frontend's
     * parseGoogleMapsIframeSrc() helper.
     */
    function parse_google_maps_iframe_src(?string $input): ?string
    {
        $str = trim((string) $input);
        if ($str === '') {
            return null;
        }

        if (preg_match('/src=["\']([^"\']+)["\']/i', $str, $m)) {
            return $m[1];
        }

        if (preg_match('#^https?://#i', $str)) {
            if (str_contains($str, '/embed') || str_contains($str, 'output=embed')) {
                return $str;
            }

            return 'https://maps.google.com/maps?q=' . urlencode($str) . '&output=embed';
        }

        return null;
    }
}

if (! function_exists('youtube_video_id')) {
    /**
     * Extracts YouTube video ID from any format URL (watch, shorts, embed, youtu.be).
     */
    function youtube_video_id(?string $url): ?string
    {
        $str = trim((string) $url);
        if ($str === '') {
            return null;
        }

        if (preg_match('#youtube(?:-nocookie)?\.com/embed/([A-Za-z0-9_-]{6,})#i', $str, $m)) {
            return $m[1];
        }

        if (preg_match('#youtu\.be/([A-Za-z0-9_-]{6,})#i', $str, $m)) {
            return $m[1];
        }

        if (preg_match('#youtube\.com/shorts/([A-Za-z0-9_-]{6,})#i', $str, $m)) {
            return $m[1];
        }

        if (preg_match('#[?&]v=([A-Za-z0-9_-]{6,})#i', $str, $m)) {
            return $m[1];
        }

        return null;
    }
}

if (! function_exists('youtube_thumbnail_url')) {
    /**
     * Returns high-quality YouTube thumbnail URL for a YouTube URL.
     */
    function youtube_thumbnail_url(?string $url): ?string
    {
        $id = youtube_video_id($url);

        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }
}

if (! function_exists('youtube_embed_url')) {
    /**
     * Extracts a YouTube video ID from a watch/short/embed URL and returns
     * an embeddable src, or null if the input isn't a recognizable YouTube
     * URL. Mirrors parse_google_maps_iframe_src()'s "accept whatever the
     * admin pasted" approach.
     */
    function youtube_embed_url(?string $url): ?string
    {
        $id = youtube_video_id($url);

        return $id ? 'https://www.youtube.com/embed/' . $id : null;
    }
}

if (! function_exists('is_video_file')) {
    /**
     * Mirrors the frontend's isVideoFile() — checked by extension only,
     * since gallery/event media uploads accept both images and videos.
     */
    function is_video_file(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv'], true);
    }
}

if (! function_exists('qr_code_svg')) {
    /**
     * Renders an inline SVG QR code for the given value. Mirrors the
     * frontend's hand-rolled QrCode.encodeText() + SVG path renderer
     * (medium error correction, no margin) using a standard library
     * instead of a vendored encoder — same visual output for the same data.
     */
    function qr_code_svg(string $value, int $size = 150): string
    {
        $builder = new \Endroid\QrCode\Builder\Builder(
            writer: new \Endroid\QrCode\Writer\SvgWriter(),
            data: $value,
            errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 0,
        );

        return $builder->build()->getString();
    }
}

if (! function_exists('category_cover_image')) {
    /**
     * Default cover photo per business category, used when a business
     * hasn't uploaded its own cover image. Mirrors categoryCover.ts.
     */
    function category_cover_image(?string $uploadedCover, ?string $category = null): string
    {
        if (has_media_file($uploadedCover)) {
            return $uploadedCover;
        }

        $map = [
            'Software Development' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=1400&auto=format&fit=crop',
            'IT & Software' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=1400&auto=format&fit=crop',
            'Supply Chain' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1400&auto=format&fit=crop',
            'Digital Marketing' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1400&auto=format&fit=crop',
            'Construction' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=1400&auto=format&fit=crop',
            'Financial Services' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1400&auto=format&fit=crop',
            'Finance & Insurance' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1400&auto=format&fit=crop',
            'Finance' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1400&auto=format&fit=crop',
            'Renewables' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=1400&auto=format&fit=crop',
            'Creative Agency' => 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?q=80&w=1400&auto=format&fit=crop',
            'Venture Capital' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?q=80&w=1400&auto=format&fit=crop',
            'Healthcare' => 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?q=80&w=1400&auto=format&fit=crop',
            'Real Estate' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=1400&auto=format&fit=crop',
            'Manufacturing' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=1400&auto=format&fit=crop',
            'Education' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1400&auto=format&fit=crop',
            'Retail' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1400&auto=format&fit=crop',
        ];

        return $map[$category] ?? 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1400&auto=format&fit=crop';
    }
}

if (! function_exists('format_price')) {
    /**
     * Formats an event ticket or registration price cleanly:
     * - "Free" / "0" -> "Free"
     * - "2500" -> "₹2,500"
     * - "₹1500" -> "₹1,500"
     * - null / empty -> "N/A"
     */
    function format_price(?string $price): string
    {
        if (blank($price)) {
            return 'N/A';
        }

        $trimmed = trim($price);
        if (strtolower($trimmed) === 'free' || $trimmed === '0') {
            return 'Free';
        }

        // Clean out any existing currency symbols or commas to isolate numeric part
        $cleanNumber = preg_replace('/[^\d.]/', '', $trimmed);
        if ($cleanNumber !== '' && is_numeric($cleanNumber)) {
            $formatted = number_format((float) $cleanNumber);
            // Check if there was any trailing unit text e.g. "/ person"
            $unit = trim(preg_replace('/^[₹\s\d,.]+/', '', $trimmed));
            return '₹' . $formatted . ($unit ? ' ' . $unit : '');
        }

        // If it starts with ₹ already
        if (str_starts_with($trimmed, '₹')) {
            return $trimmed;
        }

        return '₹' . $trimmed;
    }
}


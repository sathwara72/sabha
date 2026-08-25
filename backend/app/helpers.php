<?php

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
     * absolute URL. Mirrors the frontend's assetUrl() helper now that
     * Blade views are served same-origin with the storage disk.
     */
    function media_url(?string $path): ?string
    {
        if (! has_media_file($path)) {
            return null;
        }

        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : asset($path);
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
            'Supply Chain' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1400&auto=format&fit=crop',
            'Digital Marketing' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1400&auto=format&fit=crop',
            'Construction' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=1400&auto=format&fit=crop',
            'Financial Services' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1400&auto=format&fit=crop',
            'Renewables' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=1400&auto=format&fit=crop',
            'Creative Agency' => 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?q=80&w=1400&auto=format&fit=crop',
            'Venture Capital' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?q=80&w=1400&auto=format&fit=crop',
        ];

        return $map[$category] ?? 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1400&auto=format&fit=crop';
    }
}

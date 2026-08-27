<?php

namespace App\Services;

use App\Models\EventRegistration;
use App\Utils\QRCode;

/**
 * Renders a single downloadable PNG "ticket card" for an approved event
 * registration — event name, date/time, location and ticket number laid
 * out around the check-in QR code, rather than handing the user a bare QR
 * image with no context.
 */
class TicketCardGenerator
{
    private const WIDTH = 900;

    private const HEIGHT = 1500;

    private const COLOR_PRIMARY = [0x00, 0x37, 0x9D];

    private const COLOR_PRIMARY_DARK = [0x0F, 0x34, 0x59];

    private const COLOR_FOREGROUND = [0x11, 0x35, 0x52];

    private const COLOR_MUTED = [0x64, 0x74, 0x8B];

    private const COLOR_BACKGROUND = [0xF7, 0xF9, 0xFC];

    private const COLOR_BORDER = [0xDB, 0xE3, 0xEF];

    private const COLOR_WHITE = [0xFF, 0xFF, 0xFF];

    private string $fontPath;

    public function __construct()
    {
        $this->fontPath = base_path('vendor/endroid/qr-code/assets/open_sans.ttf');
    }

    public function render(EventRegistration $registration): string
    {
        $event = $registration->event;
        $ticketNo = (string) $registration->ticket_number;

        $img = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagesavealpha($img, true);
        $this->fill($img, 0, 0, self::WIDTH, self::HEIGHT, self::COLOR_BACKGROUND);

        // Outer card (bottom border position is finalized after content is
        // laid out, once the true content height is known)
        $cardX1 = 50;
        $cardY1 = 50;
        $cardX2 = self::WIDTH - 50;
        $this->fill($img, $cardX1, $cardY1, $cardX2, self::HEIGHT - 50, self::COLOR_WHITE);

        // Header band
        $headerH = 190;
        $this->fill($img, $cardX1, $cardY1, $cardX2, $cardY1 + $headerH, self::COLOR_PRIMARY);
        $this->centeredText($img, self::WIDTH / 2, $cardY1 + 80, 34, self::COLOR_WHITE, 'SABHA', true);
        $this->centeredText($img, self::WIDTH / 2, $cardY1 + 130, 15, [0xBF, 0xD3, 0xF2], 'COMMUNITY EVENT TICKET');

        $y = $cardY1 + $headerH + 60;

        // Event title (wrapped)
        $titleLines = $this->wrapText($event->title, 30, 4);
        foreach ($titleLines as $line) {
            $this->centeredText($img, self::WIDTH / 2, $y, 28, self::COLOR_FOREGROUND, $line, true);
            $y += 40;
        }
        $y += 20;

        $this->dashedLine($img, $cardX1 + 40, $y, $cardX2 - 40, $y, self::COLOR_BORDER);
        $y += 60;

        // Info rows: DATE, TIME, LOCATION, TICKET NO
        $rows = [
            ['DATE', $event->date->format('l, F j, Y')],
            ['TIME', $event->date->format('g:i A')],
            ['LOCATION', $event->location],
            ['TICKET NUMBER', $ticketNo ?: 'PENDING'],
        ];

        foreach ($rows as [$label, $value]) {
            $this->centeredText($img, self::WIDTH / 2, $y, 13, self::COLOR_MUTED, $label);
            $y += 30;
            $this->centeredText($img, self::WIDTH / 2, $y, 21, self::COLOR_FOREGROUND, $value, true);
            $y += 48;
        }

        $y += 10;
        $this->dashedLine($img, $cardX1 + 40, $y, $cardX2 - 40, $y, self::COLOR_BORDER);
        $y += 50;

        // QR code
        $qrSize = 380;
        $qrX = (int) ((self::WIDTH - $qrSize) / 2);
        $this->drawQrCode($img, $ticketNo ?: 'PENDING', $qrX, $y, $qrSize);
        $y += $qrSize + 40;

        $this->centeredText($img, self::WIDTH / 2, $y, 14, self::COLOR_MUTED, 'PRESENT THIS QR CODE AT THE ENTRANCE');
        $y += 40;
        $this->centeredText($img, self::WIDTH / 2, $y, 13, self::COLOR_MUTED, 'sabha.global');
        $y += 60;

        $cardY2 = min(self::HEIGHT - 50, (int) $y);
        // Undo the card's white fill below its true (now-known) bottom edge,
        // restoring the page background so the card doesn't bleed past its border.
        $this->fill($img, 0, $cardY2 + 1, self::WIDTH, self::HEIGHT, self::COLOR_BACKGROUND);
        $this->rect($img, $cardX1, $cardY1, $cardX2, $cardY2, self::COLOR_BORDER);

        $img = imagecrop($img, ['x' => 0, 'y' => 0, 'width' => self::WIDTH, 'height' => min(self::HEIGHT, $cardY2 + 50)]);

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return $data;
    }

    private function drawQrCode($canvas, string $value, int $x, int $y, int $size): void
    {
        $qr = new QRCode($value, ['s' => 'qrm', 'sf' => 6, 'p' => 1]);
        $qrImage = $qr->render_image();
        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        // Nearest-neighbor only: QR modules need hard black/white edges to
        // stay scannable — smooth resampling blurs them into unreadable gray.
        imagecopyresized($canvas, $qrImage, $x, $y, 0, 0, $size, $size, $qrWidth, $qrHeight);
        imagedestroy($qrImage);
    }

    private function fill($img, int $x1, int $y1, int $x2, int $y2, array $rgb): void
    {
        imagefilledrectangle($img, $x1, $y1, $x2, $y2, imagecolorallocate($img, ...$rgb));
    }

    private function rect($img, int $x1, int $y1, int $x2, int $y2, array $rgb): void
    {
        imagerectangle($img, $x1, $y1, $x2, $y2, imagecolorallocate($img, ...$rgb));
    }

    private function dashedLine($img, int $x1, int $y, int $x2, int $y2, array $rgb): void
    {
        $color = imagecolorallocate($img, ...$rgb);
        $dash = 10;
        $gap = 8;
        $x = $x1;
        while ($x < $x2) {
            $end = min($x + $dash, $x2);
            imageline($img, $x, $y, $end, $y2, $color);
            $x = $end + $gap;
        }
    }

    private function centeredText($img, float $centerX, float $y, int $size, array $rgb, string $text, bool $emphasis = false): void
    {
        $color = imagecolorallocate($img, ...$rgb);
        $box = imagettfbbox($size, 0, $this->fontPath, $text);
        $textWidth = abs($box[4] - $box[0]);
        $x = $centerX - ($textWidth / 2);

        imagettftext($img, $size, 0, (int) $x, (int) $y, $color, $this->fontPath, $text);

        if ($emphasis) {
            // Open Sans only ships a Regular weight here; a 1px re-stroke
            // approximates bold without needing a second font file.
            imagettftext($img, $size, 0, (int) $x + 1, (int) $y, $color, $this->fontPath, $text);
        }
    }

    /**
     * @return string[]
     */
    private function wrapText(string $text, int $maxCharsPerLine, int $maxLines): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = trim($current . ' ' . $word);
            if (mb_strlen($candidate) > $maxCharsPerLine && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $lines[$maxLines - 1] = rtrim($lines[$maxLines - 1]) . '…';
        }

        return $lines;
    }
}

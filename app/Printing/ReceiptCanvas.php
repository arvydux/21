<?php

namespace App\Printing;

use GdImage;
use Mike42\Escpos\GdEscposImage;
use Mike42\Escpos\Printer;

/**
 * Draws a receipt as a picture, so the printer prints every Lithuanian letter
 * even though its own character tables don't have them.
 */
class ReceiptCanvas
{
    /** Printable width of an 80 mm thermal printer in dots. */
    public const WIDTH = 576;

    /** Height of one image strip sent to the printer, keeps each command small. */
    private const STRIP_HEIGHT = 240;

    /**
     * @var list<array{type: string, text?: string, right?: string, size?: int, align?: string, indent?: int, height?: int}>
     */
    private array $blocks = [];

    private string $fontPath;

    public function __construct(?string $fontPath = null)
    {
        $this->fontPath = $fontPath ?? resource_path('fonts/DejaVuSansMono-Bold.ttf');
    }

    public function text(string $text, int $size, string $align = 'left', int $indent = 0): static
    {
        $this->blocks[] = ['type' => 'text', 'text' => $text, 'size' => $size, 'align' => $align, 'indent' => $indent];

        return $this;
    }

    /**
     * A line with text on the left and a price on the right.
     */
    public function row(string $left, string $right, int $size, int $indent = 0): static
    {
        $this->blocks[] = ['type' => 'row', 'text' => $left, 'right' => $right, 'size' => $size, 'indent' => $indent];

        return $this;
    }

    public function separator(): static
    {
        $this->blocks[] = ['type' => 'separator', 'height' => 14];

        return $this;
    }

    public function space(int $height): static
    {
        $this->blocks[] = ['type' => 'space', 'height' => $height];

        return $this;
    }

    public function toImage(): GdImage
    {
        $height = $this->draw(null);
        $image = imagecreatetruecolor(self::WIDTH, max($height, 1));
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        $this->draw($image);

        return $image;
    }

    public function print(Printer $printer): void
    {
        $image = $this->toImage();
        $height = imagesy($image);

        for ($top = 0; $top < $height; $top += self::STRIP_HEIGHT) {
            $strip = imagecrop($image, ['x' => 0, 'y' => $top, 'width' => self::WIDTH, 'height' => min(self::STRIP_HEIGHT, $height - $top)]);
            $escposImage = new GdEscposImage;
            $escposImage->readImageFromGdResource($strip);
            $printer->bitImage($escposImage);
        }
    }

    /**
     * Lays out the blocks and draws them when an image is given.
     *
     * @return int The total height in dots.
     */
    private function draw(?GdImage $image): int
    {
        $black = $image ? -imagecolorallocate($image, 0, 0, 0) : 0;
        $top = 0;

        foreach ($this->blocks as $block) {
            if ($block['type'] === 'space') {
                $top += $block['height'];

                continue;
            }

            if ($block['type'] === 'separator') {
                if ($image) {
                    imagefilledrectangle($image, 0, $top + 5, self::WIDTH - 1, $top + 7, -$black);
                }
                $top += $block['height'];

                continue;
            }

            $size = $block['size'];
            $lineHeight = (int) round($size * 1.75);
            $indent = $block['indent'];
            $rightWidth = $block['type'] === 'row' ? $this->textWidth($block['right'], $size) : 0;
            $available = self::WIDTH - $indent - ($rightWidth ? $rightWidth + $this->textWidth(' ', $size) : 0);
            $lines = $this->wrap($block['text'], $size, $available);

            foreach ($lines as $index => $line) {
                $baseline = $top + (int) round($size * 1.35);
                if ($image) {
                    $x = match ($block['align'] ?? 'left') {
                        'center' => (int) ((self::WIDTH - $this->textWidth($line, $size)) / 2),
                        'right' => self::WIDTH - $this->textWidth($line, $size),
                        default => $indent,
                    };
                    imagettftext($image, $size, 0, $x, $baseline, $black, $this->fontPath, $line);
                    if ($index === 0 && $rightWidth) {
                        imagettftext($image, $size, 0, self::WIDTH - $rightWidth, $baseline, $black, $this->fontPath, $block['right']);
                    }
                }
                $top += $lineHeight;
            }
        }

        return $top;
    }

    /**
     * Splits text into lines that fit the width, breaking long words when needed.
     *
     * @return list<string>
     */
    public function wrap(string $text, int $size, int $width): array
    {
        $lines = [];
        $current = '';

        foreach (preg_split('/\s+/u', trim($text)) as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;
            if ($this->textWidth($candidate, $size) <= $width) {
                $current = $candidate;

                continue;
            }
            if ($current !== '') {
                $lines[] = $current;
            }
            while ($this->textWidth($word, $size) > $width && mb_strlen($word) > 1) {
                $fits = 1;
                while ($fits < mb_strlen($word) && $this->textWidth(mb_substr($word, 0, $fits + 1), $size) <= $width) {
                    $fits++;
                }
                $lines[] = mb_substr($word, 0, $fits);
                $word = mb_substr($word, $fits);
            }
            $current = $word;
        }

        $lines[] = $current;

        return $lines;
    }

    public function textWidth(string $text, int $size): int
    {
        if ($text === '') {
            return 0;
        }
        $box = imagettfbbox($size, 0, $this->fontPath, $text);

        return (int) ceil(max($box[2], $box[4]) - min($box[0], $box[6]));
    }
}

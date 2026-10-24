<?php

namespace App\Printing;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
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

    /** Gap after right-aligned text, so letters with tails are not cut off at the paper edge. */
    private const RIGHT_MARGIN = 6;

    /** Height of one image strip sent to the printer, keeps each command small. */
    private const STRIP_HEIGHT = 240;

    /**
     * @var list<array<string, mixed>>
     */
    private array $blocks = [];

    private string $fontPath;

    public function __construct(?string $fontPath = null)
    {
        $this->fontPath = $fontPath ?? resource_path('fonts/DejaVuSansMono-Bold.ttf');
    }

    public function text(string $text, int $size, string $align = 'left', int $indent = 0, ?string $font = null): static
    {
        $this->blocks[] = ['type' => 'text', 'text' => $text, 'size' => $size, 'align' => $align, 'indent' => $indent, 'font' => $font];

        return $this;
    }

    /**
     * A black and white picture centered at its own size.
     */
    public function image(string $path): static
    {
        $this->blocks[] = ['type' => 'image', 'path' => $path];

        return $this;
    }

    /**
     * A QR code centered on the receipt, drawn dot by dot so every receipt can carry its own link.
     *
     * @param  int  $moduleSize  Size of one QR dot in printer dots.
     * @param  int  $quietZone  White border around the code, in QR dots.
     */
    public function qrCode(string $data, int $moduleSize = 5, int $quietZone = 3): static
    {
        $matrix = (new QRCode(new QROptions(['eccLevel' => EccLevel::M, 'addQuietzone' => false])))
            ->addByteSegment($data)
            ->getQRMatrix();

        $this->blocks[] = ['type' => 'qr', 'matrix' => $matrix, 'moduleSize' => $moduleSize, 'quietZone' => $quietZone];

        return $this;
    }

    /**
     * An order line in three columns: the amount, the name wrapped in its own column and the price at the right.
     *
     * @param  array{amount: string, name: string, price: string}  $fonts  Font path for each column.
     */
    public function line(string $amount, string $name, string $price, int $size, int $nameColumn, array $fonts): static
    {
        $this->blocks[] = ['type' => 'line', 'amount' => $amount, 'text' => $name, 'right' => $price, 'size' => $size, 'nameColumn' => $nameColumn, 'fonts' => $fonts];

        return $this;
    }

    /**
     * A band of the given height with texts placed on exact baselines, for layouts where lines overlap or share a row.
     *
     * @param  list<array{text: string, font: string, size: float, align: string, baseline: int}>  $texts
     */
    public function group(int $height, array $texts): static
    {
        $this->blocks[] = ['type' => 'group', 'height' => $height, 'texts' => $texts];

        return $this;
    }

    public function separator(bool $dashed = false, int $thickness = 3, int $dash = 12, int $gap = 12): static
    {
        $this->blocks[] = ['type' => 'separator', 'height' => 11 + $thickness, 'dashed' => $dashed, 'thickness' => $thickness, 'dash' => $dash, 'gap' => $gap];

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
            $top += match ($block['type']) {
                'space' => $block['height'],
                'image' => $this->drawPicture($image, $block, $top),
                'qr' => $this->drawQrCode($image, $block, $top),
                'line' => $this->drawLine($image, $block, $top),
                'group' => $this->drawGroup($image, $block, $top),
                'separator' => $this->drawSeparator($image, $block, $top),
                default => $this->drawText($image, $block, $top, $black),
            };
        }

        return $top;
    }

    /**
     * @param  array{text: string, size: int, align: string, indent: int, font: ?string}  $block
     * @return int The height of the text.
     */
    private function drawText(?GdImage $image, array $block, int $top, int $black): int
    {
        $size = $block['size'];
        $lineHeight = (int) round($size * 1.75);
        $font = $block['font'] ?? $this->fontPath;
        $lines = $this->wrap($block['text'], $size, self::WIDTH - $block['indent'], $font);

        foreach ($lines as $index => $line) {
            if ($image) {
                $lineWidth = $this->textWidth($line, $size, $font);
                $x = match ($block['align']) {
                    'center' => (int) ((self::WIDTH - $lineWidth) / 2),
                    'right' => self::WIDTH - $lineWidth - self::RIGHT_MARGIN,
                    default => $block['indent'],
                };
                imagettftext($image, $size, 0, $x, $top + $index * $lineHeight + (int) round($size * 1.35), $black, $font, $line);
            }
        }

        return count($lines) * $lineHeight;
    }

    /**
     * @param  array{dashed: bool, thickness: int, dash: int, gap: int, height: int}  $block
     * @return int The height of the separator.
     */
    private function drawSeparator(?GdImage $image, array $block, int $top): int
    {
        if ($image) {
            $dash = $block['dashed'] ? $block['dash'] : self::WIDTH;
            $step = $dash + ($block['dashed'] ? $block['gap'] : 0);
            for ($x = 0; $x < self::WIDTH; $x += $step) {
                imagefilledrectangle($image, $x, $top + 5, min($x + $dash, self::WIDTH) - 1, $top + 4 + $block['thickness'], imagecolorallocate($image, 0, 0, 0));
            }
        }

        return $block['height'];
    }

    /**
     * @param  array{height: int, texts: list<array{text: string, font: string, size: float, align: string, baseline: int}>}  $block
     * @return int The height of the band.
     */
    private function drawGroup(?GdImage $image, array $block, int $top): int
    {
        if ($image) {
            $black = -imagecolorallocate($image, 0, 0, 0);

            foreach ($block['texts'] as $text) {
                $width = $this->textWidth($text['text'], $text['size'], $text['font']);
                $x = match ($text['align']) {
                    'center' => (int) ((self::WIDTH - $width) / 2),
                    'right' => self::WIDTH - self::RIGHT_MARGIN - $width,
                    default => 0,
                };
                imagettftext($image, $text['size'], 0, $x, $top + $text['baseline'], $black, $text['font'], $text['text']);
            }
        }

        return $block['height'];
    }

    /**
     * @param  array{path: string}  $block
     * @return int The height of the picture.
     */
    private function drawPicture(?GdImage $image, array $block, int $top): int
    {
        $picture = imagecreatefrompng($block['path']);
        $width = min(imagesx($picture), self::WIDTH);

        if ($image) {
            imagecopy($image, $picture, (int) ((self::WIDTH - $width) / 2), $top, 0, 0, $width, imagesy($picture));
        }

        return imagesy($picture);
    }

    /**
     * @param  array{matrix: \chillerlan\QRCode\Data\QRMatrix, moduleSize: int, quietZone: int}  $block
     * @return int The height of the code with its white border.
     */
    private function drawQrCode(?GdImage $image, array $block, int $top): int
    {
        $modules = $block['matrix']->getSize();
        $dot = $block['moduleSize'];
        $border = $block['quietZone'] * $dot;

        if ($image) {
            $black = imagecolorallocate($image, 0, 0, 0);
            $left = (int) ((self::WIDTH - $modules * $dot) / 2);
            for ($y = 0; $y < $modules; $y++) {
                for ($x = 0; $x < $modules; $x++) {
                    if ($block['matrix']->check($x, $y)) {
                        imagefilledrectangle($image, $left + $x * $dot, $top + $border + $y * $dot, $left + ($x + 1) * $dot - 1, $top + $border + ($y + 1) * $dot - 1, $black);
                    }
                }
            }
        }

        return $modules * $dot + 2 * $border;
    }

    /**
     * @param  array{amount: string, text: string, right: string, size: int, nameColumn: int, fonts: array{amount: string, name: string, price: string}}  $block
     * @return int The height of the line.
     */
    private function drawLine(?GdImage $image, array $block, int $top): int
    {
        $size = $block['size'];
        $fonts = $block['fonts'];
        $lineHeight = (int) round($size * 1.6);
        $priceWidth = $block['right'] === '' ? 0 : $this->priceWidth($block['right'], $size, $fonts['price']);
        $nameWidth = self::WIDTH - $block['nameColumn'] - self::RIGHT_MARGIN - ($priceWidth ? $priceWidth + $size : 0);
        $lines = $this->wrap($block['text'], $size, $nameWidth, $fonts['name']);

        if ($image) {
            $black = -imagecolorallocate($image, 0, 0, 0);
            $baseline = $top + (int) round($size * 1.3);
            imagettftext($image, $size, 0, 0, $baseline, $black, $fonts['amount'], $block['amount']);
            foreach ($lines as $index => $line) {
                imagettftext($image, $size, 0, $block['nameColumn'], $baseline + $index * $lineHeight, $black, $fonts['name'], $line);
            }
            if ($priceWidth) {
                $this->drawPrice($image, $block['right'], $size, self::WIDTH - self::RIGHT_MARGIN - $priceWidth, $baseline, $black, $fonts['price']);
            }
        }

        return count($lines) * $lineHeight;
    }

    /**
     * Splits text into lines that fit the width, breaking long words when needed.
     * Words in parentheses, like "(400 ml)", stay on one line.
     *
     * @return list<string>
     */
    public function wrap(string $text, int $size, int $width, ?string $font = null): array
    {
        $font ??= $this->fontPath;
        $lines = [];
        $current = '';

        foreach (preg_split('/\s+(?![^(]*\))/u', trim($text)) as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;
            if ($this->textWidth($candidate, $size, $font) <= $width) {
                $current = $candidate;

                continue;
            }
            if ($current !== '') {
                $lines[] = $current;
            }
            while ($this->textWidth($word, $size, $font) > $width && mb_strlen($word) > 1) {
                $fits = 1;
                while ($fits < mb_strlen($word) && $this->textWidth(mb_substr($word, 0, $fits + 1), $size, $font) <= $width) {
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

    /**
     * Width of a price drawn with every digit in a cell of the same width, so prices line up in a column.
     */
    public function priceWidth(string $price, int $size, ?string $font = null): int
    {
        $width = 0;
        foreach (mb_str_split($price) as $character) {
            $width += $this->priceCharacterWidth($character, $size, $font ?? $this->fontPath);
        }

        return $width;
    }

    private function drawPrice(GdImage $image, string $price, int $size, int $left, int $baseline, int $color, string $font): void
    {
        foreach (mb_str_split($price) as $character) {
            $cell = $this->priceCharacterWidth($character, $size, $font);
            if ($character !== ' ') {
                imagettftext($image, $size, 0, $left + (int) (($cell - $this->textWidth($character, $size, $font)) / 2), $baseline, $color, $font, $character);
            }
            $left += $cell;
        }
    }

    private function priceCharacterWidth(string $character, int $size, string $font): int
    {
        return match (true) {
            $character === ' ' => (int) round($size * 0.35),
            ctype_digit($character) => $this->textWidth('0', $size, $font) + (int) round($size * 0.08),
            default => $this->textWidth($character, $size, $font) + (int) round($size * 0.08),
        };
    }

    public function textWidth(string $text, float $size, ?string $font = null): int
    {
        if ($text === '') {
            return 0;
        }
        $box = imagettfbbox($size, 0, $font ?? $this->fontPath, $text);

        return (int) ceil(max($box[2], $box[4]) - min($box[0], $box[6]));
    }
}

<?php

namespace Eckinox\PdfBundle\Pdf;

class Format
{
    public const DEFAULT_MARGINS = [
        'top' => '.75in',
        'right' => '.75in',
        'bottom' => '.75in',
        'left' => '.75in',
    ];

    /**
     * @param string                                                          $width   accepts the following units: `px`, `in`, `cm`, `mm`
     * @param string                                                          $height  accepts the following units: `px`, `in`, `cm`, `mm`
     * @param array{top: string, bottom: string, left: string, right: string} $margins
     */
    public function __construct(
        private string $width,
        private string $height,
        private bool $landscape = false,
        private array $margins = self::DEFAULT_MARGINS,
    ) {}

    public function setLanscape(bool $isLandscape): self
    {
        $this->landscape = $isLandscape;

        return $this;
    }

    /**
     * @param array{top: string, bottom: string, left: string, right: string} $margins
     */
    public function setMargins(array $margins): self
    {
        $this->margins = $margins;

        return $this;
    }

    /**
     * Returns the format as an array for Puppeteer.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $width = $this->width;
        $height = $this->height;
        $mmWidth = $this->convertToMillimeter($width);
        $mmHeight = $this->convertToMillimeter($height);

        // Enfore the orientation (Puppeteer's `landscape` only works with predefined formats)
        if (($this->landscape && $mmWidth < $mmHeight)
            || (!$this->landscape && $mmHeight < $mmWidth)) {
            $width = $this->height;
            $height = $this->width;
        }

        return [
            'width' => $width,
            'height' => $height,
            'margins' => $this->margins,
            'printBackground' => true,
        ];
    }

    private function convertToMillimeter(string $dimension): float
    {
        $unit = strtolower(substr(trim($dimension), -2));
        $number = floatval($dimension);

        return match ($unit) {
            'mm' => $number,
            'cm' => $number * 10,
            'in' => $number * 25.4,
            'px' => $number / .352778,
            default => $number
        };
    }
}

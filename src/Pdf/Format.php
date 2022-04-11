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
	) {
	}

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
		return [
			'width' => $this->width,
			'height' => $this->height,
			'margins' => $this->margins,
			'landscape' => $this->landscape,
			'printBackground' => true,
		];
	}
}

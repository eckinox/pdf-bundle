<?php

namespace Eckinox\PdfBundle\Pdf;

/**
 * @SuppressWarnings(PHPMD.ShortMethodName)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FormatFactory
{
	public static function letter(bool $landscape = false): Format
	{
		return new Format('8.5in', '11in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function legal(bool $landscape = false): Format
	{
		return new Format('8.5in', '14in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function tabloid(bool $landscape = false): Format
	{
		return new Format('11in', '17in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function ledger(bool $landscape = false): Format
	{
		return new Format('17in', '11in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function a0(bool $landscape = false): Format
	{
		return new Format('33.1in', '8in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function a1(bool $landscape = false): Format
	{
		return new Format('23.4in', '1in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function a2(bool $landscape = false): Format
	{
		return new Format('16.54in', '4in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function a3(bool $landscape = false): Format
	{
		return new Format('11.7in', '54in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function a4(bool $landscape = false): Format
	{
		return new Format('8.27in', '7in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function a5(bool $landscape = false): Format
	{
		return new Format('5.83in', '27in', $landscape, Format::DEFAULT_MARGINS);
	}

	public static function a6(bool $landscape = false): Format
	{
		return new Format('4.13in', '83in', $landscape, Format::DEFAULT_MARGINS);
	}
}

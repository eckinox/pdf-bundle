<?php

namespace Eckinox\PdfBundle\Pdf\Driver;

use Eckinox\PdfBundle\Exception\PdfGenerationException;
use Eckinox\PdfBundle\Pdf\Format;
use Eckinox\PdfBundle\Pdf\FormatFactory;
use Eckinox\PdfBundle\Pdf\Pdf;
use Eckinox\PdfBundle\Pdf\PdfGeneratorInterface;
use Eckinox\PdfBundle\Pdf\PdfInterface;
use Eckinox\PhpPuppeteer\Browser;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

/**
 * Renders a PDF from a Twig template by using Puppeteer to load and render
 * the content.
 */
class PuppeteerPdfGenerator implements PdfGeneratorInterface
{
	private string $publicDir;

	public function __construct(
		private ParameterBagInterface $parameterBag,
		private Environment $twig,
	) {
		$this->publicDir = $this->parameterBag->get('kernel.project_dir').'/public/';
	}

	/**
	 * Renders the provided Twig template in a PDF with the
	 * provided configuration.
	 *
	 * @param array<string,mixed> $context
	 * @param Format|null         $format  Format settings for the PDF's size and margins. Defaults to Letter (portrait mode) with .75in margins.
	 *
	 * @throws PdfGenerationException
	 */
	public function renderPdf(string $template, array $context = [], ?Format $format = null): PdfInterface
	{
		if (!$format) {
			$format = FormatFactory::letter();
		}

		$html = $this->twig->render($template, $context);
		$localHtml = $this->makeUrlsLocal($html);
		$tmpFilename = $this->createTmpFile($localHtml);

		try {
			$pdf = $this->renderInPuppeteer($tmpFilename, $format);
		} finally {
			unlink($tmpFilename);
		}

		return $pdf;
	}

	/**
	 * Replace internal asset links to use local file:// URLs instead.
	 *
	 * This allows us to bypass the web server, speeding up page loads and avoiding
	 * potential authorization issues for protected environements (ex.: htpasswd).
	 */
	private function makeUrlsLocal(string $html): string
	{
		// Update absolute URLs for local URLS
		$baseUrl = $this->getPublicUrl();
		$escapedUrl = preg_quote($baseUrl, '~');
		$absolutePattern = '~(href|src|url)="'.$escapedUrl.'~';
		$absoluteReplacement = '$1="file://'.$this->publicDir;
		$html = preg_replace($absolutePattern, $absoluteReplacement, $html);

		// Update relative URLs for local URLs
		$relativePattern = '~(href|src|url)="/([^/])~';
		$relativeReplacement = '$1="file://'.$this->publicDir.'$2';
		$html = preg_replace($relativePattern, $relativeReplacement, $html);

		return $html;
	}

	private function getPublicUrl(): string
	{
		$baseUrl = $this->parameterBag->get('router.request_context.scheme').'://'.
			$this->parameterBag->get('router.request_context.host').
			$this->parameterBag->get('router.request_context.base_url');

		if (!str_ends_with($baseUrl, '/')) {
			$baseUrl .= '/';
		}

		return $baseUrl;
	}

	/**
	 * Stores the HTMl in a new temporary file in the public directory
	 * and returns the temporary filename;.
	 *
	 * @return string Filename of the new tmp file
	 */
	private function createTmpFile(string $html): string
	{
		$tmpDir = $this->publicDir.'tmp/';
		$tmpFile = $tmpDir.str_replace('.', '_', uniqid('pdf_', true)).'.html';

		if (!is_dir($tmpDir)) {
			mkdir($tmpDir);
		}

		file_put_contents($tmpFile, $html);

		return $tmpFile;
	}

	/**
	 * Renders the provided HTML in Puppeteer with the provided
	 * configuration, and returns the content in a `Pdf` instance.
	 */
	private function renderInPuppeteer(string $filename, Format $format): PdfInterface
	{
		$config = [
			'url' => "file://$filename",
			'pdf' => $format->toArray(),
		];
		$browser = new Browser();
		$content = $browser->pdf($config);

		if (!is_string($content) || strlen($content) < 100) {
			throw new PdfGenerationException();
		}

		return new Pdf($content);
	}
}

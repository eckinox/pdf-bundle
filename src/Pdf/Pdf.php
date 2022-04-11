<?php

namespace Eckinox\PdfBundle\Pdf;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

class Pdf implements PdfInterface
{
	public function __construct(
		private string $content
	) {
	}

	public function getContent(): string
	{
		return $this->getContent();
	}

	public function output(string $filename): Response
	{
		return $this->buildResponse($filename, HeaderUtils::DISPOSITION_INLINE);
	}

	public function download(string $filename): Response
	{
		return $this->buildResponse($filename, HeaderUtils::DISPOSITION_ATTACHMENT);
	}

	private function buildResponse(string $filename, string $dispositionType): Response
	{
		$response = new Response(
			$this->content,
			200,
			['Content-Type' => 'application/pdf']
		);

		$disposition = HeaderUtils::makeDisposition(
			$dispositionType,
			$filename,
			$this->sanitizeFilename($filename)
		);
		$response->headers->set('Content-Disposition', $disposition);

		return $response;
	}

	private function sanitizeFilename(string $filename): string
	{
		$filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);

		return mb_ereg_replace("([\.]{2,})", '', $filename);
	}
}

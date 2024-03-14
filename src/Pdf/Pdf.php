<?php

namespace Eckinox\PdfBundle\Pdf;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Pdf implements PdfInterface
{
    public function __construct(
        private string $content
    ) {}

    public function getContent(): string
    {
        return $this->content;
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
        $filename = $this->normalizeFilename($filename);
        $extensionlessFilename = str_replace('.pdf', '', $filename);

        $response = new Response(
            $this->content,
            200,
            ['Content-Type' => 'application/pdf']
        );

        $disposition = HeaderUtils::makeDisposition(
            $dispositionType,
            $this->normalizeFilename($filename),
            $this->sanitizeFilename($extensionlessFilename).'.pdf'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    private function sanitizeFilename(string $filename): string
    {
        $slugger = new AsciiSlugger();
        $sluggedFilename = $slugger->slug($filename)->toString();

        return strtolower($sluggedFilename);
    }

    private function normalizeFilename(string $filename): string
    {
        // Make sure the PDF extension is present in the filename
        if (!str_ends_with(strtolower($filename), '.pdf')) {
            $filename .= '.pdf';
        }

        return mb_ereg_replace("([\.]{2,})", '.', $filename);
    }
}

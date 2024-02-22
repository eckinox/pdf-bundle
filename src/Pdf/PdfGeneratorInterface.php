<?php

namespace Eckinox\PdfBundle\Pdf;

use Eckinox\PdfBundle\Exception\PdfGenerationException;

/**
 * PDF generators allow users to render PDFs based on Twig templates
 * and provided formatting configurations.
 */
interface PdfGeneratorInterface
{
    /**
     * Renders the provided Twig template in a PDF with the
     * provided configuration.
     *
     * @param array<string,mixed> $context
     * @param Format|null         $format  Format settings for the PDF's size and margins. Defaults to Letter (portrait mode) with .75in margins.
     *
     * @throws PdfGenerationException
     */
    public function renderPdf(string $template, array $context = [], ?Format $format = null): PdfInterface;
}

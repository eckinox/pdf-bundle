<?php

namespace Eckinox\PdfBundle\Pdf;

use Symfony\Component\HttpFoundation\Response;

interface PdfInterface
{
    public function getContent(): string;

    public function output(string $filename): Response;

    public function download(string $filename): Response;
}

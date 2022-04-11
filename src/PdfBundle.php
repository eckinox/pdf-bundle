<?php

namespace Eckinox\PdfBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PdfBundle extends Bundle
{
	public function getPath(): string
	{
		return dirname(__DIR__);
	}
}

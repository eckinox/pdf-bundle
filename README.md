# Generate PDFs from Twig templates in Symfony

Generating PDFs in Symfony should be simple.  
And now, it is!

With this bundle, you can just render your PDFs like you render your Twig templates.


## Getting started

### 1. Install Puppeteer
Before you proceed, make sure you have `node` and `npm` installed.

To install Puppeteer and its dependencies, we recommend you take a look at 
[Puppeteer's official installation guide](https://developers.google.com/web/tools/puppeteer/get-started) 
as well as their [official troubleshooting guide](https://github.com/puppeteer/puppeteer/blob/main/docs/troubleshooting.md).

Here is a snippet for Ubuntu (tested on 20.04) that works well at the time of writing:

```bash
curl -sL https://deb.nodesource.com/setup_16.x | sudo -E bash -
sudo apt-get install -y nodejs gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget libappindicator3-1 libatk-bridge2.0-0 libgbm1
sudo npm install --global --unsafe-perm puppeteer
sudo chmod -R o+rx /usr/lib/node_modules/puppeteer/.local-chromium
```


### 2. Install this package via Composer

```bash
composer require eckinox/pdf-bundle
```


### 3. Configure the request context globally

The bundle needs to know the URL of your app to do some nifty stuff under the hood.  
You must therefore define the request context for your app in your parameters.

To learn more about these parameters, check out [Symfony's console documentation](https://symfony.com/doc/4.1/console/request_context.html#configuring-the-request-context-globally).

```yaml
# config/services.yaml
parameters:
    router.request_context.host: 'myapp.com'
    router.request_context.scheme: 'https'
    router.request_context.base_url: '/'
```


### 4. Start generating PDFs!

Here's a basic example to get you started.  
For more complete examples and information, check out the documentation below.

```php
<?php

namespace App\Controller;

use Eckinox\PdfBundle\Pdf\PdfGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/my-report", name="my_report")
     */
    public function overview(PdfGeneratorInterface $pdfGenerator): Response
    {
        $pdf = $pdfGenerator->renderPdf("your_template.html.twig", [
					"key" => "value",
					"foo" => "bar",
				]);

				return $pdf->output();
    }
```


## Formats

The `PdfGeneratorInterface::renderPdf()` accepts a third optional parameter named `$format`.
This allows you to define the format and orientation that will be used to generate the PDF.

### Using built-in formats

The easiest and most common way of specifying a format is to use the built-in `FormatFactory`,
which can provide you with all of the most common formats. 

Specifying a format using one of the built-in formats looks like the following:
```php
<?php

namespace App\Controller;

use Eckinox\PdfBundle\Pdf\FormatFactory;
use Eckinox\PdfBundle\Pdf\PdfGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/my-report", name="my_report")
     */
    public function overview(PdfGeneratorInterface $pdfGenerator): Response
    {
        $pdf = $pdfGenerator->renderPdf("your_template.html.twig", [], FormatFactory::a4());

				return $pdf->output();
    }
```

Each format factory method accepts a boolean parameter `$landscape` that allows you to determine the 
desired orientation. This parameter defaults to Portrait mode (`false`).

#### List of built-in formats

Here is the list of built-in formats:

| Format  | Size          | Factory method                                    |
| ------- | ------------- | ------------------------------------------------- |
| Letter  | 8.5in x 11in  | `FormatFactory::letter(bool $landscape = false)`  |
| Legal   | 8.5in x 14in  | `FormatFactory::legal(bool $landscape = false)`   |
| Tabloid | 11in x 17in   | `FormatFactory::tabloid(bool $landscape = false)` |
| Ledger  | 17in x 11in   | `FormatFactory::ledger(bool $landscape = false)`  |
| A0      | 33.1in x 8in  | `FormatFactory::a0(bool $landscape = false)`      |
| A1      | 23.4in x 1in  | `FormatFactory::a1(bool $landscape = false)`      |
| A2      | 16.54in x 4in | `FormatFactory::a2(bool $landscape = false)`      |
| A3      | 11.7in x 54in | `FormatFactory::a3(bool $landscape = false)`      |
| A4      | 8.27in x 7in  | `FormatFactory::a4(bool $landscape = false)`      |
| A5      | 5.83in x 27in | `FormatFactory::a5(bool $landscape = false)`      |
| A6      | 4.13in x 83in | `FormatFactory::a6(bool $landscape = false)`      |


### Specifying a custom format

If the built-in formats don't offer the size you're looking for, you can always create your
own formats.

Simply create an instance of `Format` and specify your desired width and height. Optionnally,
you may define orientation and margins as well.

The width and height arguments accept the following units: `px`, `in`, `cm` and `mm`.

```php
$format = new Format("4.25in", "5.5in");
```


### Margins

You can define the width and height of margins by using the `Format::setMargins()` method.

It accepts an array of margins defined with the `top`, `right`, `bottom` and `left` keys.

Just like the format sizes, margins accept the following units: `px`, `in`, `cm` and `mm`.

Here's an example of custom margins for a built-in format:

```php
$format = FormatFactory::a4();
$format->setMargins([
    'top' => '1.5in',
    'right' => '1in',
    'bottom' => '1in',
    'left' => '1in',
]);
```


## Outputting, downloading and storing PDFs

The `PdfInterface` instance returned by the PDF generator has three methods you can use:

- `PdfInterface::output(string $filename)` : returns a `Response` that outputs the PDF in the browser.
- `PdfInterface::download(string $filename)` : returns a `Response` that triggers a download of the PDF.
- `PdfInterface::getContent()` : returns the PDF's content as a string. 
  - This is useful to store the PDF to the local filesystem or to external storage like Amazon S3 or DO Spaces.


## License

This bundle is licensed under the MIT license.

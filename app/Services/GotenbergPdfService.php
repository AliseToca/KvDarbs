<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GotenbergPdfService
{
    protected string $baseUrl;

    public function __construct()
    {
        // Set GOTENBERG_URL in your .env, e.g. http://gotenberg:3000
        $this->baseUrl = rtrim(config('services.gotenberg.url', 'http://localhost:3000'), '/');
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Render a Blade view to PDF and return a download response.
     *
     * @param  string  $view       Blade view name, e.g. 'pdf.recipe'
     * @param  array   $data       Data passed to the view
     * @param  string  $filename   Downloaded filename, e.g. 'recipe.pdf'
     * @param  array   $options    Optional Gotenberg form options (see $this->defaultOptions())
     */
    public function fromView(
        string $view,
        array  $data = [],
        string $filename = 'document.pdf',
        array  $options = [],
        bool   $inline = true
    ): \Illuminate\Http\Response {
        $html = View::make($view, $data)->render();

        return $this->fromHtml($html, $filename, $options, $inline);
    }

    /**
     * Convert a raw HTML string to PDF and return a download response.
     */
    public function fromHtml(
        string $html,
        string $filename = 'document.pdf',
        array  $options = [],
        bool   $inline = true
    ): \Illuminate\Http\Response {
        $pdfContent = $this->requestPdf($html, $options);

        return $this->downloadResponse($pdfContent, $filename, $inline);
    }

    /**
     * Convert a raw HTML string to PDF and save it to a path on disk.
     * Returns the absolute path of the saved file.
     */
    public function saveToDisk(
        string $html,
        string $destinationPath,
        array  $options = []
    ): string {
        $pdfContent = $this->requestPdf($html, $options);
        file_put_contents($destinationPath, $pdfContent);

        return $destinationPath;
    }

    /**
     * Render a Blade view to PDF and save it to disk.
     * Returns the absolute path of the saved file.
     */
    public function saveViewToDisk(
        string $view,
        array  $data = [],
        string $destinationPath = '',
        array  $options = []
    ): string {
        $html = View::make($view, $data)->render();

        return $this->saveToDisk($html, $destinationPath, $options);
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    /**
     * Send the HTML to Gotenberg and return raw PDF bytes.
     *
     * @throws \RuntimeException  on HTTP or Gotenberg error
     */
    protected function requestPdf(string $html, array $options = []): string
    {
        $mergedOptions = array_merge($this->defaultOptions(), $options);

        $response = $this->client()
            ->attach('index.html', $html, 'index.html')
            ->post("{$this->baseUrl}/forms/chromium/convert/html", $mergedOptions);

        if ($response->failed()) {
            throw new \RuntimeException(
                "Gotenberg PDF generation failed [{$response->status()}]: {$response->body()}"
            );
        }

        return $response->body();
    }

    /**
     * Build the base HTTP client with appropriate timeouts.
     */
    protected function client(): PendingRequest
    {
        return Http::timeout(60)
            ->connectTimeout(10)
            ->asMultipart();
    }

    /**
     * Default Gotenberg options. Override any of these per-call via $options.
     *
     * Full option reference: https://gotenberg.dev/docs/routes#html-file-into-pdf-route
     */
    protected function defaultOptions(): array
    {
        return [
            // Paper size (A4)
            'paperWidth'  => '8.27',
            'paperHeight' => '11.69',

            // Margins in inches
            'marginTop'    => '0.5',
            'marginBottom' => '0.5',
            'marginLeft'   => '0.5',
            'marginRight'  => '0.5',

            // Print background graphics (colours, images)
            'printBackground' => 'true',

            // Scale factor
            'scale' => '1',
        ];
    }

    /**
     * Build a streamed download response from raw PDF bytes.
     */
    protected function downloadResponse(string $content, string $filename, bool $inline = true): \Illuminate\Http\Response
    {
        $disposition = $inline ? 'inline' : 'attachment';

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
        ]);
    }
}

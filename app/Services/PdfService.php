<?php

namespace App\Services;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Illuminate\Http\Response;

class PdfService
{
    public function render(string $view, array $data, string $filename, bool $inline = true): Response
    {
        $html = view($view, $data)->render();

        $mpdf = new Mpdf([
            'margin_top'    => 10,
            'margin_bottom' => 10,
            'margin_left'   => 14,
            'margin_right'  => 14,
            'default_font'  => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);

        $output = $mpdf->Output('', Destination::STRING_RETURN);

        return response($output, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => ($inline ? 'inline' : 'attachment') . '; filename="' . $filename . '"',
        ]);
    }
}

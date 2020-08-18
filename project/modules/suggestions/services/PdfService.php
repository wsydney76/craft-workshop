<?php

namespace project\modules\suggestions\services;

use craft\base\Component;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService extends Component
{
    public function createPdfFromHtml($html)
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->output();
    }
}

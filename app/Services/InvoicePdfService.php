<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    /**
     * Generate PDF for an invoice
     */
    public function generate(Invoice $invoice)
    {
        $invoice->load('client', 'items', 'user');

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
        ]);

        $pdf->setPaper('A4');
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isPhpEnabled', true);

        return $pdf;
    }

    /**
     * Download PDF
     */
    public function download(Invoice $invoice)
    {
        $filename = 'racun-' . $invoice->invoice_number . '.pdf';
        return $this->generate($invoice)->download($filename);
    }

    /**
     * Store PDF in storage
     */
    public function store(Invoice $invoice, $path = 'invoices')
    {
        $filename = 'racun-' . $invoice->invoice_number . '.pdf';
        $pdf = $this->generate($invoice)->output();

        Storage::put($path . '/' . $filename, $pdf);

        return $path . '/' . $filename;
    }
}

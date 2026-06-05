<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoicesExport;

class InvoiceExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Export invoices to Excel
     */
    public function excel(Request $request)
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with('client')
            ->orderBy('invoice_date', 'desc')
            ->get();

        return Excel::download(new InvoicesExport($invoices), 'racuni-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export single invoice to CSV
     */
    public function csv(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load('client', 'items');

        $filename = 'racun-' . $invoice->invoice_number . '.csv';
        $handle = fopen('php://memory', 'w');

        // Header
        fputcsv($handle, ['Račun #' . $invoice->invoice_number], ';');
        fputcsv($handle, [], ';');

        // Invoice info
        fputcsv($handle, ['Datum:', $invoice->invoice_date->format('d.m.Y')], ';');
        fputcsv($handle, ['Klijent:', $invoice->client->name], ';');
        fputcsv($handle, [], ';');

        // Items header
        fputcsv($handle, ['Opis', 'Količina', 'Cena/Kos', 'PDV %', 'Skupno'], ';');

        // Items
        foreach ($invoice->items as $item) {
            fputcsv($handle, [
                $item->description,
                number_format($item->quantity, 2, ',', '.'),
                number_format($item->unit_price, 2, ',', '.'),
                number_format($item->tax_rate, 2, ',', '.'),
                number_format($item->line_total + $item->tax_total, 2, ',', '.'),
            ], ';');
        }

        fputcsv($handle, [], ';');
        fputcsv($handle, ['Vmesni Sešteveк:', number_format($invoice->subtotal, 2, ',', '.')], ';');
        fputcsv($handle, ['PDV:', number_format($invoice->tax_amount, 2, ',', '.')], ';');
        fputcsv($handle, ['SKUPNO:', number_format($invoice->total, 2, ',', '.')], ';');

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}

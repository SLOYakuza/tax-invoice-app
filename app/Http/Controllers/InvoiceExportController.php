<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoicesExport;
use App\Exports\InvoiceDetailExport;
use App\Exports\InvoicesMultiSheetExport;

class InvoiceExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Export all invoices to Excel (summary)
     */
    public function excel(Request $request)
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with('client')
            ->orderBy('invoice_date', 'desc')
            ->get();

        $filename = 'racuni-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new InvoicesExport($invoices), $filename);
    }

    /**
     * Export single invoice to Excel (detailed)
     */
    public function excelDetail(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('client', 'items', 'user');

        $filename = 'racun-' . $invoice->invoice_number . '.xlsx';
        return Excel::download(new InvoiceDetailExport($invoice), $filename);
    }

    /**
     * Export all invoices with multiple sheets
     */
    public function excelMultiSheet(Request $request)
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with('client', 'items')
            ->orderBy('invoice_date', 'desc')
            ->get();

        $filename = 'racuni-detaljno-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new InvoicesMultiSheetExport($invoices), $filename);
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

        // Set UTF-8 BOM
        fwrite($handle, "\xEF\xBB\xBF");

        // Header
        fputcsv($handle, ['RAČUN #' . $invoice->invoice_number], ';');
        fputcsv($handle, [], ';');

        // Invoice info
        fputcsv($handle, ['Datum:', $invoice->invoice_date->format('d.m.Y')], ';');
        fputcsv($handle, ['Klijent:', $invoice->client->name], ';');
        fputcsv($handle, ['Status:', $this->getStatusLabel($invoice->status)], ';');
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
        fputcsv($handle, ['Vmesni Seštevek:', number_format($invoice->subtotal, 2, ',', '.')], ';');
        fputcsv($handle, ['PDV:', number_format($invoice->tax_amount, 2, ',', '.')], ';');
        fputcsv($handle, ['SKUPNO:', number_format($invoice->total, 2, ',', '.')], ';');

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export all invoices to CSV
     */
    public function csvAll(Request $request)
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with('client')
            ->orderBy('invoice_date', 'desc')
            ->get();

        $filename = 'racuni-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://memory', 'w');

        // Set UTF-8 BOM
        fwrite($handle, "\xEF\xBB\xBF");

        // Header
        fputcsv($handle, ['Številka Računa', 'Klijent', 'Datum', 'Rok Plačila', 'Vmesni Seštevek', 'DDV', 'Skupno', 'Status', 'Kreirano'], ';');

        // Data
        foreach ($invoices as $invoice) {
            fputcsv($handle, [
                $invoice->invoice_number,
                $invoice->client->name,
                $invoice->invoice_date->format('d.m.Y'),
                $invoice->due_date ? $invoice->due_date->format('d.m.Y') : '-',
                number_format($invoice->subtotal, 2, ',', '.'),
                number_format($invoice->tax_amount, 2, ',', '.'),
                number_format($invoice->total, 2, ',', '.'),
                $this->getStatusLabel($invoice->status),
                $invoice->created_at->format('d.m.Y H:i'),
            ], ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function getStatusLabel($status)
    {
        return match($status) {
            'draft' => 'Nacrt',
            'issued' => 'Izdan',
            'paid' => 'Plačan',
            'cancelled' => 'Preklican',
            default => $status,
        };
    }
}

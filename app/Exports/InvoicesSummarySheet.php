<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\PatternFill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InvoicesSummarySheet implements FromArray, WithStyles, ShouldAutoSize, WithTitle
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function array(): array
    {
        $data = [];
        $data[] = ['POVZETEK RAČUNOV'];
        $data[] = [];

        // Summary statistics
        $totalInvoices = $this->invoices->count();
        $totalAmount = $this->invoices->sum('total');
        $totalTax = $this->invoices->sum('tax_amount');
        $paidAmount = $this->invoices->where('status', 'paid')->sum('total');
        $pendingAmount = $this->invoices->where('status', 'issued')->sum('total');

        $data[] = ['Skupno računov:', $totalInvoices];
        $data[] = ['Skupni znesek (z DDV):', $totalAmount];
        $data[] = ['Skupni DDV:', $totalTax];
        $data[] = ['Plačani znesek:', $paidAmount];
        $data[] = ['Zahtevani znesek:', $pendingAmount];
        $data[] = [];

        // Status summary
        $data[] = ['POVZETEK PO STATUSU'];
        $data[] = ['Status', 'Število', 'Znesek'];

        $statuses = ['draft', 'issued', 'paid', 'cancelled'];
        foreach ($statuses as $status) {
            $invoicesWithStatus = $this->invoices->where('status', $status);
            $data[] = [
                $this->getStatusLabel($status),
                $invoicesWithStatus->count(),
                $invoicesWithStatus->sum('total'),
            ];
        }

        $data[] = [];
        $data[] = [];

        // Monthly summary
        $data[] = ['POVZETEK PO MESECIH'];
        $data[] = ['Mesec', 'Število', 'Znesek'];

        $monthly = $this->invoices->groupBy(function ($invoice) {
            return $invoice->invoice_date->format('Y-m');
        });

        foreach ($monthly as $month => $invoices) {
            $data[] = [
                $month,
                $invoices->count(),
                $invoices->sum('total'),
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '0D6EFD'],
            ],
        ]);

        $sheet->getStyle('A3:B7')->applyFromArray([
            'fill' => [
                'fillType' => PatternFill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7F0FF'],
            ],
        ]);

        $sheet->getStyle('A10:C10')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => PatternFill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Povzetek';
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

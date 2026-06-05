<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InvoicesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices->map(function ($invoice) {
            return [
                $invoice->invoice_number,
                $invoice->client->name,
                $invoice->invoice_date->format('d.m.Y'),
                $invoice->due_date ? $invoice->due_date->format('d.m.Y') : '-',
                number_format($invoice->subtotal, 2, ',', '.'),
                number_format($invoice->tax_amount, 2, ',', '.'),
                number_format($invoice->total, 2, ',', '.'),
                $this->getStatusLabel($invoice->status),
                $invoice->created_at->format('d.m.Y H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Številka Računa',
            'Klijent',
            'Datum',
            'Rok Plačila',
            'Vmesni Sešteveк',
            'PDV',
            'Skupno',
            'Status',
            'Kreirano',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
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

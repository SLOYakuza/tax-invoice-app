<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\PatternFill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InvoiceDetailExport implements FromArray, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $invoice;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    public function array(): array
    {
        $data = [];

        // Header
        $data[] = ['RAČUN #' . $this->invoice->invoice_number];
        $data[] = [];

        // Invoice Info
        $data[] = ['Izdajatelj:', $this->invoice->user->name];
        if ($this->invoice->user->company_name) {
            $data[] = ['Podjetje:', $this->invoice->user->company_name];
        }
        if ($this->invoice->user->tax_id) {
            $data[] = ['Davčna številka:', $this->invoice->user->tax_id];
        }
        if ($this->invoice->user->address) {
            $data[] = ['Naslov:', $this->invoice->user->address];
        }
        if ($this->invoice->user->city) {
            $data[] = ['Mesto:', $this->invoice->user->postal_code . ' ' . $this->invoice->user->city];
        }

        $data[] = [];

        // Client Info
        $data[] = ['Prejemnik:', $this->invoice->client->name];
        if ($this->invoice->client->tax_id) {
            $data[] = ['Davčna številka klijenta:', $this->invoice->client->tax_id];
        }
        if ($this->invoice->client->address) {
            $data[] = ['Naslov:', $this->invoice->client->address];
        }
        if ($this->invoice->client->city) {
            $data[] = ['Mesto:', $this->invoice->client->postal_code . ' ' . $this->invoice->client->city];
        }

        $data[] = [];

        // Invoice Dates
        $data[] = ['Datum računa:', $this->invoice->invoice_date->format('d.m.Y')];
        if ($this->invoice->due_date) {
            $data[] = ['Rok plačila:', $this->invoice->due_date->format('d.m.Y')];
        }
        $data[] = ['Status:', $this->getStatusLabel($this->invoice->status)];

        $data[] = [];
        $data[] = [];

        // Items Header
        $data[] = ['Opis', 'Količina', 'Cena/Kos', 'PDV %', 'Skupno'];

        // Items
        foreach ($this->invoice->items as $item) {
            $data[] = [
                $item->description,
                $item->quantity,
                $item->unit_price,
                $item->tax_rate,
                $item->line_total + $item->tax_total,
            ];
        }

        $data[] = [];

        // Totals
        $data[] = ['Vmesni Seštevek:', '', '', '', $this->invoice->subtotal];
        $data[] = ['PDV:', '', '', '', $this->invoice->tax_amount];
        $data[] = ['SKUPNO:', '', '', '', $this->invoice->total];

        if ($this->invoice->notes) {
            $data[] = [];
            $data[] = ['Opombe:', $this->invoice->notes];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '0D6EFD'],
            ],
        ]);

        // Section headers
        $sections = [3, 9, 15, 22];
        foreach ($sections as $row) {
            if (isset($sheet->getRowIterator($row, $row))) {
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => PatternFill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7F0FF'],
                    ],
                ]);
            }
        }

        // Items header
        $itemHeaderRow = 22;
        $sheet->getStyle('A' . $itemHeaderRow . ':E' . $itemHeaderRow)->applyFromArray([
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
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Totals styling
        $totalStartRow = count($this->invoice->items) + 25;
        $sheet->getStyle('A' . $totalStartRow . ':E' . ($totalStartRow + 2))->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => PatternFill::FILL_SOLID,
                'startColor' => ['rgb' => 'F0F0F0'],
            ],
        ]);

        // Final total styling
        $sheet->getStyle('A' . ($totalStartRow + 2) . ':E' . ($totalStartRow + 2))->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '0D6EFD'],
            ],
            'fill' => [
                'fillType' => PatternFill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7F0FF'],
            ],
        ]);

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '#,##0.00',
            'C' => '€ #,##0.00',
            'D' => '#,##0.00"%"',
            'E' => '€ #,##0.00',
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

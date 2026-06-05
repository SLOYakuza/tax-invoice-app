<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\PatternFill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class InvoicesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
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
                $invoice->subtotal,
                $invoice->tax_amount,
                $invoice->total,
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
            'Vmesni Seštevek',
            'PDV',
            'Skupno',
            'Status',
            'Kreirano',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
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
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Data rows styling
        $lastRow = $sheet->getHighestRow();
        for ($i = 2; $i <= $lastRow; $i++) {
            // Alternating row colors
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':I' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => PatternFill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA'],
                    ],
                ]);
            }

            // Center alignment for dates and status
            $sheet->getStyle('C' . $i . ':D' . $i)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            // Right alignment for numbers
            $sheet->getStyle('E' . $i . ':G' . $i)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ]);

            // Center alignment for status
            $sheet->getStyle('H' . $i)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            // Borders
            $sheet->getStyle('A' . $i . ':I' . $i)->applyFromArray([
                'border' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'EEEEEE'],
                    ],
                ],
            ]);
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(16);

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '€ #,##0.00',
            'F' => '€ #,##0.00',
            'G' => '€ #,##0.00',
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

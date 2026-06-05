<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <title>Račun #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            max-width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            background: white;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
        }
        .company-info {
            flex: 1;
        }
        .company-info h1 {
            font-size: 24px;
            color: #0d6efd;
            margin-bottom: 10px;
        }
        .company-info p {
            font-size: 12px;
            margin: 5px 0;
            color: #666;
        }
        .invoice-info {
            flex: 1;
            text-align: right;
        }
        .invoice-info h2 {
            font-size: 28px;
            color: #0d6efd;
            margin-bottom: 10px;
        }
        .invoice-info p {
            font-size: 12px;
            margin: 5px 0;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        .details-box {
            flex: 1;
        }
        .details-box h3 {
            font-size: 12px;
            color: #0d6efd;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .details-box p {
            font-size: 12px;
            margin: 5px 0;
            line-height: 1.5;
        }
        .details-box strong {
            display: block;
            font-size: 13px;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 12px;
        }
        table thead {
            background-color: #f8f9fa;
            border-top: 2px solid #0d6efd;
            border-bottom: 2px solid #0d6efd;
        }
        table thead th {
            padding: 10px 5px;
            text-align: left;
            font-weight: bold;
            color: #0d6efd;
        }
        table tbody td {
            padding: 10px 5px;
            border-bottom: 1px solid #dee2e6;
        }
        table tbody tr:last-child td {
            border-bottom: 2px solid #0d6efd;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .totals-table {
            width: 250px;
        }
        .totals-table table {
            width: 100%;
            margin: 0;
        }
        .totals-table table th,
        .totals-table table td {
            padding: 8px;
            text-align: right;
            font-size: 12px;
        }
        .totals-table table th {
            background-color: transparent;
            border: none;
            font-weight: bold;
            color: #333;
        }
        .totals-table table td {
            border: none;
        }
        .total-row {
            border-top: 2px solid #0d6efd;
            border-bottom: 2px solid #0d6efd;
            font-weight: bold;
            font-size: 14px !important;
            color: #0d6efd;
        }
        .footer {
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            font-size: 11px;
            color: #666;
        }
        .footer p {
            margin: 5px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-draft {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .status-issued {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>📄 {{ config('app.name', 'Davčni Računi') }}</h1>
                @if($invoice->user->company_name)
                    <p><strong>{{ $invoice->user->company_name }}</strong></p>
                @endif
                @if($invoice->user->tax_id)
                    <p>Davčna številka: {{ $invoice->user->tax_id }}</p>
                @endif
                @if($invoice->user->address)
                    <p>{{ $invoice->user->address }}</p>
                @endif
                @if($invoice->user->city && $invoice->user->postal_code)
                    <p>{{ $invoice->user->postal_code }} {{ $invoice->user->city }}</p>
                @endif
                @if($invoice->user->phone)
                    <p>Tel: {{ $invoice->user->phone }}</p>
                @endif
                @if($invoice->user->email)
                    <p>E-pošta: {{ $invoice->user->email }}</p>
                @endif
            </div>
            <div class="invoice-info">
                <h2>RAČUN</h2>
                <p><strong>#{{ $invoice->invoice_number }}</strong></p>
                <p>
                    <span class="status-badge status-{{ $invoice->status }}">
                        @if($invoice->status === 'draft')
                            NACRT
                        @elseif($invoice->status === 'issued')
                            IZDAN
                        @elseif($invoice->status === 'paid')
                            PLAČAN
                        @else
                            PREKLICAN
                        @endif
                    </span>
                </p>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="details-box">
                <h3>Izdajatelj:</h3>
                <strong>{{ $invoice->user->name }}</strong>
                @if($invoice->user->company_name)
                    <p>{{ $invoice->user->company_name }}</p>
                @endif
                @if($invoice->user->address)
                    <p>{{ $invoice->user->address }}</p>
                @endif
                @if($invoice->user->city)
                    <p>{{ $invoice->user->postal_code ?? '' }} {{ $invoice->user->city }}</p>
                @endif
            </div>
            <div class="details-box">
                <h3>Prejemnik:</h3>
                <strong>{{ $invoice->client->name }}</strong>
                @if($invoice->client->tax_id)
                    <p>Dš: {{ $invoice->client->tax_id }}</p>
                @endif
                @if($invoice->client->address)
                    <p>{{ $invoice->client->address }}</p>
                @endif
                @if($invoice->client->city)
                    <p>{{ $invoice->client->postal_code ?? '' }} {{ $invoice->client->city }}</p>
                @endif
            </div>
            <div class="details-box">
                <h3>Podatki Računa:</h3>
                <p><strong>Datum:</strong> {{ $invoice->invoice_date->format('d.m.Y') }}</p>
                @if($invoice->due_date)
                    <p><strong>Rok plačila:</strong> {{ $invoice->due_date->format('d.m.Y') }}</p>
                @endif
                @if($invoice->paid_at)
                    <p><strong>Plačano:</strong> {{ $invoice->paid_at->format('d.m.Y') }}</p>
                @endif
                <p><strong>Valuta:</strong> {{ $invoice->currency }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Opis</th>
                    <th class="text-right">Količina</th>
                    <th class="text-right">Cena/Kos</th>
                    <th class="text-right">PDV %</th>
                    <th class="text-right">Skupno</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                        <td class="text-right">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->tax_rate, 2, ',', '.') }}%</td>
                        <td class="text-right">€ {{ number_format($item->line_total + $item->tax_total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="totals-table">
                <table>
                    <thead>
                        <tr>
                            <th>Vmesni Seštevek:</th>
                            <td>€ {{ number_format($invoice->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>PDV (skupno):</th>
                            <td>€ {{ number_format($invoice->tax_amount, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="total-row">
                            <th>SKUPNO:</th>
                            <td>€ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        @if($invoice->notes)
            <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd; margin-bottom: 20px; font-size: 12px;">
                <p><strong>Opombe:</strong></p>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>📄 Ustvarjeno s sistemom Davčni Računi</p>
            <p>Generirano: {{ now()->format('d.m.Y H:i') }}</p>
            <p>Časovni žig: {{ now()->timestamp }}</p>
        </div>
    </div>
</body>
</html>

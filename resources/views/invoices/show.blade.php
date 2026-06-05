@extends('layouts.app')

@section('title', 'Račun ' . $invoice->invoice_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="fas fa-file-invoice me-2"></i>Račun #{{ $invoice->invoice_number }}
        </h1>
        <div>
            @if($invoice->status === 'draft')
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit"></i> Uredi
                </a>
                <form method="POST" action="{{ route('invoices.send', $invoice) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success me-2">
                        <i class="fas fa-check"></i> Izda Račun
                    </button>
                </form>
            @elseif($invoice->status === 'issued' && !$invoice->paid_at)
                <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success me-2">
                        <i class="fas fa-check-circle"></i> Označi kot Plačan
                    </button>
                </form>
            @endif
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-danger me-2">
                <i class="fas fa-file-pdf"></i> Preuzmi PDF
            </a>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Nazaj
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Podatki Računa</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <strong>Datum Računa:</strong><br>
                                {{ $invoice->invoice_date->format('d.m.Y') }}
                            </p>
                            @if($invoice->due_date)
                                <p>
                                    <strong>Rok Plačila:</strong><br>
                                    {{ $invoice->due_date->format('d.m.Y') }}
                                </p>
                            @endif
                            @if($invoice->paid_at)
                                <p>
                                    <strong>Datum Plačila:</strong><br>
                                    {{ $invoice->paid_at->format('d.m.Y H:i') }}
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Klijent:</strong><br>
                                {{ $invoice->client->name }}
                            </p>
                            @if($invoice->client->tax_id)
                                <p>
                                    <strong>Davčna Številka:</strong><br>
                                    {{ $invoice->client->tax_id }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Stavke Računa</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Opis</th>
                                <th class="text-end">Količina</th>
                                <th class="text-end">Cena/Kos</th>
                                <th class="text-end">PDV %</th>
                                <th class="text-end">Skupno</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td class="text-end">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $item->tax_rate }}%</td>
                                    <td class="text-end">€ {{ number_format($item->line_total + $item->tax_total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Povzetek</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Vmesni Seštevek:</span>
                            <strong>€ {{ number_format($invoice->subtotal, 2, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>PDV:</span>
                            <strong>€ {{ number_format($invoice->tax_amount, 2, ',', '.') }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Skupno:</h5>
                        <h4>€ {{ number_format($invoice->total, 2, ',', '.') }}</h4>
                    </div>
                    <div class="mb-3">
                        <span class="badge
                            @if($invoice->status === 'draft') bg-secondary
                            @elseif($invoice->status === 'issued') bg-warning
                            @elseif($invoice->status === 'paid') bg-success
                            @else bg-danger
                            @endif
                        ">
                            @if($invoice->status === 'draft') Nacrt
                            @elseif($invoice->status === 'issued') Izdan
                            @elseif($invoice->status === 'paid') Plačan
                            @else Preklican
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            @if($invoice->notes)
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Opombe</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $invoice->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

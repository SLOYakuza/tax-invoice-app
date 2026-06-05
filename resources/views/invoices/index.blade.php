@extends('layouts.app')

@section('title', 'Računi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="fas fa-file-invoice me-2"></i>Računi
        </h1>
        <div>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus"></i> Novi Račun
            </a>
            <div class="btn-group" role="group">
                <button id="exportDropdown" type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download"></i> Izvezi
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('invoices.export.excel') }}"><i class="fas fa-file-excel"></i> Excel (Povzetek)</a></li>
                    <li><a class="dropdown-item" href="{{ route('invoices.export.excel.multi') }}"><i class="fas fa-file-excel"></i> Excel (Detaljno)</a></li>
                    <li><a class="dropdown-item" href="{{ route('invoices.export.csv.all') }}"><i class="fas fa-file-csv"></i> CSV</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Seznam Računov</h5>
                </div>
                <div class="col-md-6">
                    <form class="d-flex" method="GET" action="{{ route('invoices.index') }}">
                        <input class="form-control form-control-sm me-2" type="search" name="search" placeholder="Iskanje..." value="{{ request('search') }}">
                        <button class="btn btn-sm btn-outline-primary" type="submit">Iskanje</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Številka</th>
                        <th>Klijent</th>
                        <th>Datum</th>
                        <th>Rok Plačila</th>
                        <th>Znesek</th>
                        <th>Status</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>
                                <strong>#{{ $invoice->invoice_number }}</strong>
                            </td>
                            <td>{{ $invoice->client->name }}</td>
                            <td>{{ $invoice->invoice_date->format('d.m.Y') }}</td>
                            <td>
                                @if($invoice->due_date)
                                    {{ $invoice->due_date->format('d.m.Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>€ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                            <td>
                                @if($invoice->status === 'draft')
                                    <span class="badge bg-secondary">Nacrt</span>
                                @elseif($invoice->status === 'issued')
                                    <span class="badge bg-warning">Izdan</span>
                                @elseif($invoice->status === 'paid')
                                    <span class="badge bg-success">Plačan</span>
                                @else
                                    <span class="badge bg-danger">Preklican</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-info" title="Ogled">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($invoice->status === 'draft')
                                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning" title="Uredi">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-danger" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Izvezi">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('invoices.export.excel.detail', $invoice) }}"><i class="fas fa-file-excel"></i> Excel</a></li>
                                        <li><a class="dropdown-item" href="{{ route('invoices.export.csv', $invoice) }}"><i class="fas fa-file-csv"></i> CSV</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> Ni še nobenih računov
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $invoices->links() }}
        </div>
    </div>
@endsection

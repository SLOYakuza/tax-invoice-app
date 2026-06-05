@extends('layouts.app')

@section('title', 'Nadzorna Plošča')

@section('content')
    <div class="page-header mb-4">
        <h1 class="h3">
            <i class="fas fa-chart-line me-2"></i>Nadzorna Plošča
        </h1>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Skupno Računov</h6>
                    <h3 class="mb-0">{{ $stats['total_invoices'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card success">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Plačanih</h6>
                    <h3 class="mb-0">{{ $stats['paid_invoices'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card warning">
                <div class="card-body">
                    <h6 class="text-muted mb-2">V Teku</h6>
                    <h3 class="mb-0">{{ $stats['pending_invoices'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Skupni Prihodek</h6>
                    <h3 class="mb-0">€ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Hitri Pregled</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="text-muted mb-1">Klijenti:</p>
                            <h4 class="mb-3">{{ $clients_count }}</h4>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1">Proizvodi:</p>
                            <h4 class="mb-3">{{ $products_count }}</h4>
                        </div>
                    </div>
                    <a href="{{ route('clients.create') }}" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-plus"></i> Dodaj Klijenta
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Dodaj Proizvod
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Hitri Dostop</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('invoices.create') }}" class="btn btn-lg btn-primary w-100 mb-2">
                        <i class="fas fa-plus"></i> Ustvari Nov Račun
                    </a>
                    <a href="{{ route('invoices.index') }}" class="btn btn-lg btn-outline-primary w-100">
                        <i class="fas fa-list"></i> Poglej Vse Račune
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Nedavni Računi</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Številka Računa</th>
                        <th>Klijent</th>
                        <th>Datum</th>
                        <th>Znesek</th>
                        <th>Status</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_invoices as $invoice)
                        <tr>
                            <td>
                                <strong>#{{ $invoice->invoice_number }}</strong>
                            </td>
                            <td>{{ $invoice->client->name }}</td>
                            <td>{{ $invoice->invoice_date->format('d.m.Y') }}</td>
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
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> Ni še nobenih računov
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

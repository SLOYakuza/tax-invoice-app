@extends('layouts.app')

@section('title', 'Klijenti')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="fas fa-users me-2"></i>Klijenti
        </h1>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novi Klijent
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Seznam Klijentov</h5>
                </div>
                <div class="col-md-6">
                    <form class="d-flex" method="GET" action="{{ route('clients.index') }}">
                        <input class="form-control form-control-sm me-2" type="search" name="search" placeholder="Iskanje...">
                        <button class="btn btn-sm btn-outline-primary" type="submit">Iskanje</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ime</th>
                        <th>E-pošta</th>
                        <th>Telefonska Številka</th>
                        <th>Davčna Številka</th>
                        <th>Mesto</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->email ?? '-' }}</td>
                            <td>{{ $client->phone ?? '-' }}</td>
                            <td>{{ $client->tax_id ?? '-' }}</td>
                            <td>{{ $client->city ?? '-' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('clients.destroy', $client) }}" class="d-inline" onsubmit="return confirm('Ali si prepričan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> Ni še nobenih klijentov
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $clients->links() }}
        </div>
    </div>
@endsection

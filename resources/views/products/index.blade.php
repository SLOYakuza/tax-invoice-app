@extends('layouts.app')

@section('title', 'Proizvodi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="fas fa-box me-2"></i>Proizvodi
        </h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novi Proizvod
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Seznam Proizvodov</h5>
                </div>
                <div class="col-md-6">
                    <form class="d-flex" method="GET" action="{{ route('products.index') }}">
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
                        <th>Naziv</th>
                        <th>Opis</th>
                        <th>Cena</th>
                        <th>Enota</th>
                        <th>PDV %</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->description ?? '-' }}</td>
                            <td>€ {{ number_format($product->price, 2, ',', '.') }}</td>
                            <td>{{ $product->unit }}</td>
                            <td>{{ $product->tax_rate }}%</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Ali si prepričan?');">
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
                                <i class="fas fa-inbox"></i> Ni še nobenih proizvodov
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $products->links() }}
        </div>
    </div>
@endsection

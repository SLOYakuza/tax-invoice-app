@extends('layouts.app')

@section('title', 'Ustvari Nov Račun')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="fas fa-file-invoice-dollar me-2"></i>Ustvari Nov Račun
        </h1>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Nazaj
        </a>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Podatki Računa</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Klijent <span class="text-danger">*</span></label>
                            <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                <option value="">-- Izberi klijenta --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="invoice_date" class="form-label">Datum Računa <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
                            @error('invoice_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Rok Plačila</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Opis</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Opombe</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Povzetek Računa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="text-muted">Vmesni Seštevek:</p>
                                <h5 id="subtotal">€ 0,00</h5>
                            </div>
                            <div class="col-6">
                                <p class="text-muted">PDV:</p>
                                <h5 id="tax">€ 0,00</h5>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <p class="text-muted">Skupno:</p>
                                <h3 id="total">€ 0,00</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Stavke Računa</h5>
                <button type="button" class="btn btn-sm btn-success" id="addItem">
                    <i class="fas fa-plus"></i> Dodaj Stavko
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Opis</th>
                            <th>Količina</th>
                            <th>Cena/Kos</th>
                            <th>PDV %</th>
                            <th>Skupno</th>
                            <th>Akcija</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td>
                                <input type="text" class="form-control form-control-sm" name="items[0][description]" placeholder="Opis" required>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm quantity" name="items[0][quantity]" value="1" min="1" step="0.01" required>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm unit-price" name="items[0][unit_price]" value="0" min="0" step="0.01" required>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm tax-rate" name="items[0][tax_rate]" value="22" min="0" max="100" step="0.01" required>
                            </td>
                            <td>
                                <span class="item-total">€ 0,00</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Ustvari Račun
            </button>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-times"></i> Prekliči
            </a>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        let itemCount = 1;
        const form = document.getElementById('invoiceForm');
        const itemsBody = document.getElementById('itemsBody');
        const addItemBtn = document.getElementById('addItem');

        function calculateTotals() {
            let subtotal = 0;
            let totalTax = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                const taxRate = parseFloat(row.querySelector('.tax-rate').value) || 0;

                const lineTotal = quantity * unitPrice;
                const lineTax = lineTotal * (taxRate / 100);

                row.querySelector('.item-total').textContent = '€ ' + lineTotal.toFixed(2).replace('.', ',');

                subtotal += lineTotal;
                totalTax += lineTax;
            });

            const total = subtotal + totalTax;
            document.getElementById('subtotal').textContent = '€ ' + subtotal.toFixed(2).replace('.', ',');
            document.getElementById('tax').textContent = '€ ' + totalTax.toFixed(2).replace('.', ',');
            document.getElementById('total').textContent = '€ ' + total.toFixed(2).replace('.', ',');
        }

        addItemBtn.addEventListener('click', function() {
            const newRow = document.createElement('tr');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <td>
                    <input type="text" class="form-control form-control-sm" name="items[${itemCount}][description]" placeholder="Opis" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm quantity" name="items[${itemCount}][quantity]" value="1" min="1" step="0.01" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm unit-price" name="items[${itemCount}][unit_price]" value="0" min="0" step="0.01" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm tax-rate" name="items[${itemCount}][tax_rate]" value="22" min="0" max="100" step="0.01" required>
                </td>
                <td>
                    <span class="item-total">€ 0,00</span>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            itemsBody.appendChild(newRow);
            itemCount++;
            attachRowListeners(newRow);
        });

        function attachRowListeners(row) {
            row.querySelector('.quantity').addEventListener('input', calculateTotals);
            row.querySelector('.unit-price').addEventListener('input', calculateTotals);
            row.querySelector('.tax-rate').addEventListener('input', calculateTotals);
            row.querySelector('.remove-item').addEventListener('click', function() {
                row.remove();
                calculateTotals();
            });
        }

        document.querySelectorAll('.item-row').forEach(row => {
            attachRowListeners(row);
        });

        calculateTotals();
    </script>
@endsection

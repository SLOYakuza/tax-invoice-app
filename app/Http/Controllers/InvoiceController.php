<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with('client')
            ->orderBy('invoice_date', 'desc')
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        return view('invoices.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after:invoice_date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        $invoice = new Invoice();
        $invoice->user_id = Auth::id();
        $invoice->client_id = $validated['client_id'];
        $invoice->invoice_number = $this->generateInvoiceNumber();
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->due_date = $validated['due_date'];
        $invoice->description = $validated['description'];
        $invoice->notes = $validated['notes'];
        $invoice->currency = 'EUR';
        $invoice->save();

        $subtotal = 0;
        $tax_amount = 0;

        foreach ($validated['items'] as $item) {
            $line_total = $item['quantity'] * $item['unit_price'];
            $tax_total = $line_total * ($item['tax_rate'] / 100);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'],
                'line_total' => $line_total,
                'tax_total' => $tax_total,
            ]);

            $subtotal += $line_total;
            $tax_amount += $tax_total;
        }

        $invoice->subtotal = $subtotal;
        $invoice->tax_amount = $tax_amount;
        $invoice->total = $subtotal + $tax_amount;
        $invoice->save();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Račun je uspešno kreiran.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('client', 'items');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Možete uređivati samo nacrte.');
        }

        $clients = Client::where('user_id', Auth::id())->get();
        $invoice->load('items');

        return view('invoices.edit', compact('invoice', 'clients'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Možete uređivati samo nacrte.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after:invoice_date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        $invoice->client_id = $validated['client_id'];
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->due_date = $validated['due_date'];
        $invoice->description = $validated['description'];
        $invoice->notes = $validated['notes'];

        $invoice->items()->delete();

        $subtotal = 0;
        $tax_amount = 0;

        foreach ($validated['items'] as $item) {
            $line_total = $item['quantity'] * $item['unit_price'];
            $tax_total = $line_total * ($item['tax_rate'] / 100);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'],
                'line_total' => $line_total,
                'tax_total' => $tax_total,
            ]);

            $subtotal += $line_total;
            $tax_amount += $tax_total;
        }

        $invoice->subtotal = $subtotal;
        $invoice->tax_amount = $tax_amount;
        $invoice->total = $subtotal + $tax_amount;
        $invoice->save();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Račun je uspešno ažuriran.');
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('client', 'items', 'user');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('racun-' . $invoice->invoice_number . '.pdf');
    }

    public function send(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $invoice->status = 'issued';
        $invoice->sent_at = now();
        $invoice->save();

        return back()->with('success', 'Račun je označen kao izdat.');
    }

    public function markPaid(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $invoice->status = 'paid';
        $invoice->paid_at = now();
        $invoice->save();

        return back()->with('success', 'Račun je označen kao plaćen.');
    }

    private function generateInvoiceNumber()
    {
        $year = date('Y');
        $count = Invoice::where('user_id', Auth::id())
            ->whereYear('invoice_date', $year)
            ->count() + 1;

        return $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

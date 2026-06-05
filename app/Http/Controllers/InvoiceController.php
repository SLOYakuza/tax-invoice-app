<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoiceItem;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Services\InvoicePdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $pdfService;

    public function __construct(InvoicePdfService $pdfService)
    {
        $this->pdfService = $pdfService;
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

    public function store(StoreInvoiceRequest $request)
    {
        $validated = $request->validated();

        $invoice = new Invoice();
        $invoice->user_id = Auth::id();
        $invoice->client_id = $validated['client_id'];
        $invoice->invoice_number = $this->generateInvoiceNumber();
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->due_date = $validated['due_date'] ?? null;
        $invoice->description = $validated['description'] ?? null;
        $invoice->notes = $validated['notes'] ?? null;
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
            return back()->with('error', 'Lahko urejate samo nacrte.');
        }

        $clients = Client::where('user_id', Auth::id())->get();
        $invoice->load('items');

        return view('invoices.edit', compact('invoice', 'clients'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Lahko urejate samo nacrte.');
        }

        $validated = $request->validated();

        $invoice->client_id = $validated['client_id'];
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->due_date = $validated['due_date'] ?? null;
        $invoice->description = $validated['description'] ?? null;
        $invoice->notes = $validated['notes'] ?? null;

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
        return $this->pdfService->download($invoice);
    }

    public function send(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $invoice->status = 'issued';
        $invoice->sent_at = now();
        $invoice->save();

        return back()->with('success', 'Račun je označen kot izdan.');
    }

    public function markPaid(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $invoice->status = 'paid';
        $invoice->paid_at = now();
        $invoice->save();

        return back()->with('success', 'Račun je označen kot plačan.');
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

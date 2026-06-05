<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_invoices' => Invoice::where('user_id', $user->id)->count(),
            'paid_invoices' => Invoice::where('user_id', $user->id)
                ->where('status', 'paid')
                ->count(),
            'total_revenue' => Invoice::where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('total'),
            'pending_invoices' => Invoice::where('user_id', $user->id)
                ->where('status', 'issued')
                ->count(),
        ];

        $recent_invoices = Invoice::where('user_id', $user->id)
            ->with('client')
            ->orderBy('invoice_date', 'desc')
            ->limit(5)
            ->get();

        $clients_count = Client::where('user_id', $user->id)->count();
        $products_count = Product::where('user_id', $user->id)->count();

        return view('dashboard', compact('stats', 'recent_invoices', 'clients_count', 'products_count'));
    }
}

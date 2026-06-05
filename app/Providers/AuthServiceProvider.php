<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use App\Policies\InvoicePolicy;
use App\Policies\ClientPolicy;
use App\Policies\ProductPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
        Client::class => ClientPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

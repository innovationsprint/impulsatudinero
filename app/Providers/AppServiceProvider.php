<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Livewire::component('stock-transactions-table', \App\Http\Livewire\StockTransactionsTable::class);
    }
}

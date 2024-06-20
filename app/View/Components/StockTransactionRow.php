<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StockTransactionRow extends Component
{
    public $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function render()
    {
        return view('components.stock-transaction-row');
    }
}

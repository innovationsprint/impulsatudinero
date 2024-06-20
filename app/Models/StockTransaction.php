<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_symbol', 
        'transaction_type', 
        'quantity', 
        'price_per_share', 
        'commission', 
        'transaction_date',
        'trade_id', 
        'cost_basis', 
        'fifo_pnl_realized',
        'logo_path' // Añadimos el campo logo_path
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'price_per_share' => 'float',
    ];

    public static function getTotalPurchaseValue($stockSymbol)
    {
        return self::where('stock_symbol', $stockSymbol)
                   ->get()
                   ->sum(function ($transaction) {
                       return $transaction->quantity * $transaction->price_per_share;
                   });
    }
}

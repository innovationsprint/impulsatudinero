<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('stock_symbol');
            $table->enum('transaction_type', ['buy', 'sell']);
            $table->decimal('quantity', 15, 4); // Cambiado de integer a decimal con 4 decimales
            $table->decimal('price_per_share', 10, 4); // Cambiado a 4 decimales
            $table->decimal('commission', 10, 2)->default(0.00);
            $table->date('transaction_date');
            $table->string('trade_id', 36)->nullable();  // Permitir valores nulos para TradeID y cambiar a string
            $table->decimal('cost_basis', 10, 2)->nullable();  // Nuevo campo CostBasis, permitiendo nulos
            $table->decimal('fifo_pnl_realized', 10, 2)->nullable();  // Nuevo campo FifoPnlRealized, permitiendo nulos
            $table->string('logo_path')->nullable();  // Campo para almacenar la ruta del logo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_transactions');
    }
}

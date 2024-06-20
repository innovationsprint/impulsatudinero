<?php
namespace App\Http\Livewire;

use App\Models\StockTransaction;
use App\Services\FinnhubService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class StockTransactionsTable extends Component
{
    public $sortField = 'stock_symbol';
    public $sortAsc = true;
    protected $finnhubService;

    public function mount(FinnhubService $finnhubService)
    {
        $this->finnhubService = $finnhubService;
        Log::info('FinnhubService injected in mount', ['service' => $this->finnhubService]);
    }

    public function hydrate(FinnhubService $finnhubService)
    {
        $this->finnhubService = $finnhubService;
        Log::info('FinnhubService re-injected in hydrate', ['service' => $this->finnhubService]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortField = $field;
            $this->sortAsc = true;
        }

        $this->render(); // Llama a render para actualizar la vista
    }

    private function formatQuantity($quantity)
    {
        if ($quantity == 0) {
            return '-';
        } elseif (floor($quantity) == $quantity) {
            return number_format($quantity, 0);
        } else {
            return number_format($quantity, 4);
        }
    }

    public function getPrice($symbol)
    {
        if (!$this->finnhubService) {
            Log::error('FinnhubService is null');
            return 0;
        }

        Log::info('Getting price for symbol', ['symbol' => $symbol]);
        $price = $this->finnhubService->getStockPrice($symbol);
        if ($price === null || $price === 'N/A') {
            Log::error('Received null price for symbol', ['symbol' => $symbol]);
            return 0; // Devuelve 0 en lugar de 'N/A' para evitar errores de tipo
        }
        Log::info('Precio Actual Obtenido', ['symbol' => $symbol, 'price' => $price]);
        return floatval($price); // Asegúrate de devolver un número
    }

    private function sortCollection(Collection $collection)
    {
        return $collection->sortBy(function ($transaction) {
            $field = $this->sortField;
            if (in_array($field, ['formatted_market_price', 'formatted_profit_loss', 'formatted_profit_loss_percentage'])) {
                // Reemplazar porcentaje y comas por vacío y convertir a float
                return floatval(str_replace(['%', ','], ['', ''], $transaction->$field));
            }
            return $transaction->$field;
        }, SORT_REGULAR, !$this->sortAsc);
    }

    private function formatTransaction($transaction)
    {
        $transaction->formatted_total_quantity = $this->formatQuantity($transaction->total_quantity);
    
        // Verifica y convierte a número antes de formatear
        $averagePrice = is_numeric($transaction->average_price) ? (float) $transaction->average_price : 0.0;
        $totalCostBasis = is_numeric($transaction->total_cost_basis) ? (float) $transaction->total_cost_basis : 0.0;
        $currentPrice = is_numeric($this->getPrice($transaction->stock_symbol)) ? (float) $this->getPrice($transaction->stock_symbol) : 0.0;
        $totalQuantity = is_numeric($transaction->total_quantity) ? (float) $transaction->total_quantity : 0.0;
    
        $marketPrice = $currentPrice * $totalQuantity;
        $profitLoss = $marketPrice - $totalCostBasis;
    
        $transaction->formatted_average_price = number_format($averagePrice, 2);
        $transaction->formatted_total_cost_basis = number_format($totalCostBasis, 2);
        $transaction->current_price = $currentPrice;
        $transaction->formatted_market_price = number_format($marketPrice, 2);
        $transaction->formatted_profit_loss = number_format($profitLoss, 2);
    
        // Evitar división por cero y formatear el porcentaje
        if ($totalCostBasis != 0.0) {
            $profitLossPercentage = ($profitLoss / $totalCostBasis) * 100;
            $transaction->formatted_profit_loss_percentage = number_format($profitLossPercentage, 2) . '%';
        } else {
            $transaction->formatted_profit_loss_percentage = '0.00%';
        }
    
        return $transaction;
    }
    

    public function render()
    {
        $transactionsWithQuantity = StockTransaction::selectRaw('
                stock_symbol,
                SUM(quantity) as total_quantity,
                SUM(cost_basis) as total_cost_basis,
                SUM(cost_basis) / NULLIF(SUM(quantity), 0) as average_price,
                MAX(transaction_date) as last_transaction_date
            ')
            ->groupBy('stock_symbol')
            ->having('total_quantity', '>', 0)
            ->get();

        // Obtener precios para todas las transacciones con cantidad
        $transactionsWithQuantity->each(function ($transaction) {
            $transaction->current_price = $this->getPrice($transaction->stock_symbol);
        });

        $transactionsWithQuantity = $transactionsWithQuantity->map(fn($transaction) => $this->formatTransaction($transaction));

        // Ordenar la colección en PHP
        $transactionsWithQuantity = $this->sortCollection($transactionsWithQuantity);

        $transactionsWithoutQuantity = StockTransaction::selectRaw('
                stock_symbol,
                SUM(quantity) as total_quantity,
                SUM(cost_basis) as total_cost_basis,
                SUM(cost_basis) / NULLIF(SUM(quantity), 0) as average_price,
                MAX(transaction_date) as last_transaction_date
            ')
            ->groupBy('stock_symbol')
            ->having('total_quantity', '=', 0)
            ->get()
            ->map(function ($transaction) {
                $transaction->formatted_total_quantity = $this->formatQuantity($transaction->total_quantity);
                $transaction->formatted_average_price = '-';
                $transaction->formatted_total_cost_basis = $transaction->total_cost_basis;
                $transaction->current_price = $this->getPrice($transaction->stock_symbol);
                $transaction->formatted_market_price = '-';
                $transaction->formatted_profit_loss = '-';
                $transaction->formatted_profit_loss_percentage = '-';
                return $transaction;
            });

        return view('livewire.stock-transactions-table', [
            'transactionsWithQuantity' => $transactionsWithQuantity,
            'transactionsWithoutQuantity' => $transactionsWithoutQuantity,
        ]);
    }
}

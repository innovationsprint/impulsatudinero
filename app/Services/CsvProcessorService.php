<?php

namespace App\Services;

use App\Models\StockTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CsvProcessorService
{
    public function process($file)
    {
        StockTransaction::truncate();

        $csvData = file_get_contents($file);
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            if (count($row) >= 9) {
                if ($row[0] == 'EUR.USD') {
                    continue; // Excluir el stock con el símbolo "EUR.USD"
                }

                $transactionDate = $this->parseDate($row[8]);

                StockTransaction::create([
                    'user_id' => Auth::id(),
                    'stock_symbol' => $row[0],
                    'trade_id' => $row[1],
                    'quantity' => $row[2],
                    'price_per_share' => $row[3],
                    'commission' => $row[4],
                    'cost_basis' => $row[5],
                    'fifo_pnl_realized' => $row[6],
                    'transaction_type' => strtolower($row[7]) == 'buy' ? 'buy' : 'sell',
                    'transaction_date' => $transactionDate,
                ]);
            }
        }
    }

    private function parseDate($date)
    {
        try {
            return Carbon::createFromFormat('d/m/Y;H:i:s', $date)->toDateTimeString();
        } catch (\Exception $e) {
            throw new \Exception('Invalid date format in CSV file');
        }
    }
}

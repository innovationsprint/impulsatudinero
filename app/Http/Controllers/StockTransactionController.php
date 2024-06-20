<?php
namespace App\Http\Controllers;

use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StockTransactionController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function create()
    {
        return view('stock-transactions.create');
    }

    public function show($stock_symbol)
    {
        $transactions = StockTransaction::where('stock_symbol', $stock_symbol)
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Aplicar la lógica de formateo de la cantidad
        $transactions->transform(function ($transaction) {
            $transaction->formatted_quantity = $this->formatQuantity($transaction->quantity);
            return $transaction;
        });

        $totalQuantity = $transactions->sum('quantity');
        $averagePrice = $transactions->avg('price_per_share');
        $totalCommission = $transactions->sum('commission');

        return view('stock-transactions.show', compact('transactions', 'totalQuantity', 'averagePrice', 'totalCommission', 'stock_symbol'));
    }

    private function formatQuantity($quantity)
    {
        return floor($quantity) == $quantity ? (int) $quantity : number_format($quantity, 4);
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_symbol' => 'required|string|max:10',
            'transaction_type' => 'required|in:buy,sell',
            'quantity' => 'required|numeric|min:0.0001',
            'price_per_share' => 'required|numeric|min:0.000',
            'commission' => 'nullable|numeric|min:0.000',
            'transaction_date' => 'required|date',
        ]);

        $data = $request->only([
            'stock_symbol',
            'transaction_type',
            'quantity',
            'price_per_share',
            'commission',
            'transaction_date',
        ]);

        $data['commission'] = $data['commission'] ?? 0.00;
        $data['user_id'] = auth()->id();

        StockTransaction::create($data);

        return redirect()->route('dashboard')->with('success', 'Transaction recorded successfully.');
    }

    public function showUploadForm()
    {
        return view('stock-transactions.upload');
    }

    public function processUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        // Borrar todos los registros existentes en la tabla
        StockTransaction::truncate();

        $file = $request->file('csv_file');
        $csvData = file_get_contents($file);
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            if (count($row) >= 9) {
                if ($row[0] == 'EUR.USD') {
                    continue; // Excluir el stock con el símbolo "EUR.USD"
                }

                try {
                    $transactionDate = Carbon::createFromFormat('d/m/Y;H:i:s', $row[8])->toDateTimeString();
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors(['csv_file' => 'Invalid date format in CSV file']);
                }

                StockTransaction::create([
                    'user_id' => auth()->id(),
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

        return redirect()->route('dashboard')->with('success', 'CSV importado correctamente !!!');
    }

    public function uploadLogo(Request $request, $stock_symbol)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png|max:2048',
        ]);

        $fileName = $stock_symbol . '.' . $request->file('logo')->extension();
        $path = $request->file('logo')->storeAs('logos', $fileName, 'public');

        StockTransaction::where('stock_symbol', $stock_symbol)->update(['logo_path' => $path]);

        return response()->json(['success' => 'Logo subido con éxito.']);
    }
}

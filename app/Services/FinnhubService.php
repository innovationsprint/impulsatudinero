<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FinnhubService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('FINNHUB_API_KEY');
        Log::info('FinnhubService initialized with API Key', ['apiKey' => $this->apiKey]);
    }

    public function getStockPrice($symbol)
    {
        $cacheKey = "stock_price_{$symbol}";
        $cachedPrice = Cache::get($cacheKey);

        if ($cachedPrice) {
            return floatval($cachedPrice); // Asegúrate de que siempre se retorna un número
        }

        try {
            $response = Http::timeout(10) // Establece un tiempo de espera de 10 segundos
                ->retry(3, 100) // Reintenta la solicitud hasta 3 veces con 100ms de retraso entre cada intento
                ->get('https://finnhub.io/api/v1/quote', [
                    'symbol' => $symbol,
                    'token' => $this->apiKey,
                ]);

            Log::info('Finnhub API Response', ['response' => $response->json()]);

            if ($response->successful()) {
                $data = $response->json();
                $price = isset($data['c']) ? $data['c'] : 'N/A';

                if ($price !== 'N/A') {
                    Cache::put($cacheKey, $price, now()->addMinutes(30)); // Cache por 30 minutos
                    return floatval($price); // Asegúrate de que siempre se retorna un número
                } else {
                    return 'N/A';
                }
            } else {
                Log::error('Finnhub API Error', ['response' => $response->json()]);
                return 'N/A';
            }
        } catch (\Exception $e) {
            Log::error('Finnhub API Connection Error', ['error' => $e->getMessage()]);
            return 'N/A';
        }
    }
}

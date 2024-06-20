<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class YahooFinanceService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('YAHOO_FINANCE_API_KEY');
    }

    public function getStockPrice($symbol)
    {
        $cacheKey = "stock_price_{$symbol}";
        $cachedPrice = Cache::get($cacheKey);

        if ($cachedPrice) {
            return $cachedPrice;
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->get('https://yfapi.net/v6/finance/quote', [
            'symbols' => $symbol,
        ]);

        $price = isset($response->json()['quoteResponse']['result'][0]['regularMarketPrice']) 
            ? $response->json()['quoteResponse']['result'][0]['regularMarketPrice'] 
            : 'N/A';

        Cache::put($cacheKey, $price, now()->addMinutes(30)); // Cache por 30 minutos

        return $price;
    }
}

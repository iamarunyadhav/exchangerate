<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExchangeRateRequest;
use App\Http\Resources\ExchangeRateCollection;
use App\Http\Resources\ExchangeRateResource;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use App\Traits\Auditable;

class ExchangeRateController extends Controller
{
    protected $exchangeRateService;
    use Auditable;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    public function fetchCurrentRate(Request $request, $baseCurrency = 'USD')
    {
        $data = $this->exchangeRateService->getCurrentRate($baseCurrency, $request->query('date'));

        return isset($data['error']) && $data['error']? response()->json(['error' => $data['error']], $data['status'] ?? 400): new ExchangeRateResource($data['rate']);

        if (!$data['error']) {
            self::logAction('exchange_rate_fetch', "Fetched rate", [
                'base_currency' => $baseCurrency,
                'date' => $request->query('date')
            ]);
        }
        // return response()->json(['data' => ExchangeRateResource::collection($data['rate'])]);
    }

    public function getLastSevenDaysRates(Request $request, $baseCurrency)
    {
        $targetCurrency = 'LKR';
        $selectedDate = $request->query('date', now()->toDateString());

        // Fetch the last 7 days of exchange rates before the selected date
        $rates = $this->exchangeRateService->getLastSevenDaysRates($baseCurrency, $selectedDate);

        // Calculate the weekly average
        $weeklyAverage = $rates->avg('rate');

        // Return response using the new resource collection
        return new ExchangeRateCollection($rates, $baseCurrency, $targetCurrency, $selectedDate, $weeklyAverage);
    }

    public function storeManualRate(ExchangeRateRequest $request)
    {
        $message = $this->exchangeRateService->storeManualRate($request->validated());

        self::logAction('exchange_rate_create', "Added rate: {$request->target_currency}", [
            'base_currency' => $request->base_currency,
            'rate' => $request->rate,
            'date' => $request->date
        ]);
        return response()->json(['message' => $message]);
    }

    public function getRecentRates()
    {
        $rates = $this->exchangeRateService->getRecentRates();
        return response()->json(['data' => ExchangeRateResource::collection($rates)]);
    }
}

<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Carbon\Carbon;
use App\Traits\Auditable;
use Illuminate\Support\Facades\Auth;

class ExchangeRateService
{
    use Auditable;
    public function getCurrentRate($baseCurrency, $date)
    {
        $allowedBaseCurrencies = ['USD', 'AUD', 'CAD', 'GBP'];
        $baseCurrency = strtoupper($baseCurrency);

        if (!in_array($baseCurrency, $allowedBaseCurrencies)) {
            return ['error' => "Invalid base currency. Allowed: USD, AUD, CAD, GBP", 'status' => 400];
        }

        $date = $date ?: now()->toDateString();
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return ['error' => "Invalid date format. Use YYYY-MM-DD.", 'status' => 400];
        }

        $existingRate = ExchangeRate::where([
            'base_currency' => $baseCurrency,
            'target_currency' => 'LKR',
            'date' => $date
        ])->first();

        if ($existingRate) {
            return ['rate' => $existingRate];
        }

        $rate = (new ExchangeRate())->exchangeRate($baseCurrency, 'LKR', $date);
        if ($rate === null) {
            return ['error' => "Failed to fetch rate for {$baseCurrency}/LKR on {$date}", 'status' => 500];
        }

        return ['rate' => ExchangeRate::create([
            'base_currency' => $baseCurrency,
            'target_currency' => 'LKR',
            'date' => $date,
            'rate' => $rate
        ])];
    }

    public function getLastSevenDaysRates($baseCurrency, $selectedDate)
    {
        $selectedDate = Carbon::parse($selectedDate ?: now())->toDateString();
        return ExchangeRate::where('base_currency', strtoupper($baseCurrency))
            ->where('target_currency', 'LKR')
            ->whereBetween('date', [Carbon::parse($selectedDate)->subDays(6)->toDateString(), $selectedDate])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function storeManualRate($validated)
    {
        $existingRate = ExchangeRate::where([
            'base_currency' => $validated['base_currency'],
            'target_currency' => $validated['target_currency'],
            'date' => $validated['date']
        ])->first();

        if ($existingRate) {
            $existingRate->update(['rate' => $validated['rate']]);
            return 'Exchange rate updated successfully';
        }

        ExchangeRate::create($validated);
        // self::logAction('rate_created', "New rate for {$validated['target_currency']}");
        return 'Exchange rate stored successfully';
    }

    public function getRecentRates()
    {
        return ExchangeRate::where('target_currency', 'LKR')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
    }
}

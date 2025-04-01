<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $currencies = ['LKR', 'AUD', 'CAD', 'GBP'];
        $baseCurrency = 'USD';

        // Fetch today's exchange rate from API
        $apiUrl = "https://api.exchangerate-api.com/v4/latest/{$baseCurrency}";
        $response = Http::get($apiUrl, ['base' => $baseCurrency]);

        if ($response->successful() && isset($response->json()['rates'])) {
            $apiRates = $response->json()['rates'];
        } else {
            $apiRates = [];
        }

        // Insert exchange rates for the last 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->toDateString();

            foreach ($currencies as $currency) {
                // Use API rate for today, otherwise generate a random rate
                $rate = ($i === 0 && isset($apiRates[$currency]))
                    ? $apiRates[$currency] // Use today's API rate
                    : rand(2000, 3000) / 10; // Random rate for past days

                ExchangeRate::updateOrCreate(
                    [
                        'base_currency' => $baseCurrency,
                        'target_currency' => $currency,
                        'date' => $date,
                    ],
                    [
                        'rate' => $rate,
                    ]
                );
            }
        }

        dump("Exchange rates seeded: Today's from API, past days random.");
    }
}

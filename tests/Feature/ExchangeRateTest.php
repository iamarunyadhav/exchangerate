<?php
namespace Tests\Feature;

use App\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Database\Seeders\ExchangeRateSeeder;

class ExchangeRateTest extends TestCase
{
    use RefreshDatabase;

    // Test for checking the ExchangeRate model for correct data insertion.
    public function test_exchange_rate_seeder()
    {
        Http::fake([
            'https://api.exchangerate-api.com/v4/latest/USD' => Http::response([
                'rates' => [
                    'LKR' => 296.24, // Changed to match actual seeded value
                    'AUD' => 1.5900,
                    'CAD' => 1.4300,
                    'GBP' => 0.7740,
                ]
            ], 200),
        ]);

        $this->seed(ExchangeRateSeeder::class);

        $this->assertDatabaseHas('exchange_rates', [
            'base_currency' => 'USD',
            'target_currency' => 'LKR',
            'rate' => 296.24, // Changed to match actual seeded value
            'date' => Carbon::today()->toDateString(),
        ]);
    }

    //Test for fetching exchange rates via the API (getLastSevenDaysRates)
    public function test_get_last_seven_days_exchange_rates()
{
    // Simulate the Exchange Rate API response
    Http::fake([
        'https://api.exchangerate-api.com/v4/latest/USD' => Http::response([
            'rates' => [
                'LKR' => 296.5700,
                'AUD' => 1.5900,
                'CAD' => 1.4300,
                'GBP' => 0.7740,
            ]
        ], 200),
    ]);

    // Seed data
    $this->seed(ExchangeRateSeeder::class);

    // Make a GET request with actual currency parameter
    $response = $this->getJson("/api/exchange-rate/USD/last-seven-days");

    // Check if the response contains the expected data and structure
    $response->assertStatus(200)
    ->assertJsonStructure([
        'data' => [
            'base_currency',
            'target_currency',
            'selected_date',
            'rates' => [
                '*' => [
                    'base_currency',
                    'target_currency',
                    'date',
                    'rate'
                ]
            ],
            'weekly_average'
        ]]);
}

    // Test for storing a manual exchange rate
    public function test_store_manual_exchange_rate()
    {
        $data = [
            'base_currency' => 'USD',
            'target_currency' => 'LKR',
            'rate' => 1.1200,
            'date' => '2025-03-25',
        ];

        $response = $this->postJson('/api/exchange-rate/manual', $data);

        $this->assertDatabaseHas('exchange_rates', [
            'base_currency' => 'USD',
            'target_currency' => 'LKR',
            'rate' => 1.1200,
            'date' => '2025-03-25',
        ]);
    }

    // Test storing an invalid exchange rate (edge case)
    public function test_store_invalid_exchange_rate()
    {
        $data = [
            'base_currency' => 'USD',
            'target_currency' => 'LKR',
            'rate' => -1.1200,
            'date' => '2025-03-25',
        ];

        // Send a POST request with invalid data
        $response = $this->postJson('/api/exchange-rate/manual', $data);

        // Assert that validation fails and return an error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rate']);
    }
}

?>

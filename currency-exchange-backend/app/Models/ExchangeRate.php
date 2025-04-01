<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_currency', 'target_currency', 'rate', 'date'
    ];

    public $timestamps = false;
    public function exchangeRate($base, $target)
    {
        try {
            $url = "https://api.exchangerate-api.com/v4/latest/{$base}";
            $response = Http::get($url);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            return $data['rates'][$target] ?? null;
        } catch (\Exception $e) {
            return null; // Return null if API fails
        }
    }
}


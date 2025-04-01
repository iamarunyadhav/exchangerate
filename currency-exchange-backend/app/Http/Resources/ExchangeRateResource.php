<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'base_currency' => $this['base_currency'],
            'target_currency' => $this->target_currency,
            'date' => $this->date,
            'rate' => number_format((float) ($this->rate ?? 0), 4), // Ensure `rate` is cast to float
        ];
    }
}


<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExchangeRateCollection extends ResourceCollection
{
    private $weeklyAverage;
    private $baseCurrency;
    private $targetCurrency;
    private $selectedDate;

    public function __construct($resource, $baseCurrency, $targetCurrency, $selectedDate, $weeklyAverage)
    {
        parent::__construct($resource);
        $this->baseCurrency = $baseCurrency;
        $this->targetCurrency = $targetCurrency;
        $this->selectedDate = $selectedDate;
        $this->weeklyAverage = $weeklyAverage;
    }

    public function toArray($request)
    {
        return [
            'base_currency' => $this->baseCurrency,
            'target_currency' => $this->targetCurrency,
            'selected_date' => $this->selectedDate,
            'rates' => ExchangeRateResource::collection($this->collection), // Individual rates
            'weekly_average' => number_format($this->weeklyAverage, 4), // Format average
        ];
    }
}

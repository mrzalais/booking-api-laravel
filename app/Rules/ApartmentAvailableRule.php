<?php

namespace App\Rules;

use App\Models\Apartment;
use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ApartmentAvailableRule implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $apartment = Apartment::find($value);
        if (!$apartment) {
            $fail('Sorry, this apartment is not found');
        }
        if ($apartment->capacity_adults < data_get($this->data, ['guests_adults'])
            || $apartment->capacity_children < data_get($this->data, ['guests_children'])) {
            $fail('Sorry, this apartment does not fit all your guests');
        }
        if (Booking::where('apartment_id', $value)
            ->validForRange([data_get($this->data, ['start_date']), data_get($this->data, ['end_date'])])
            ->exists()) {
            $fail('Sorry, this apartment is not available for those dates');
        }
    }

    public function setData($data): static
    {
        $this->data = $data;

        return $this;
    }
}

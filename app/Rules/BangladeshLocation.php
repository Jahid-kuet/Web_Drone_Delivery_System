<?php

namespace App\Rules;

use App\Services\BangladeshLocationService;
use Illuminate\Contracts\Validation\Rule;

class BangladeshLocation implements Rule
{
    protected $latitude;
    protected $longitude;
    protected $strictKhulna;
    protected $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @param float $latitude
     * @param float $longitude
     * @param bool $strictKhulna Enforce Khulna division only
     */
    public function __construct(float $latitude, float $longitude, bool $strictKhulna = false)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->strictKhulna = $strictKhulna;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validation = BangladeshLocationService::validateLocation(
            $this->latitude,
            $this->longitude,
            $this->strictKhulna
        );

        if (!$validation['valid']) {
            $this->errorMessage = $validation['error'];
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage ?? 'The location must be within Bangladesh.';
    }
}

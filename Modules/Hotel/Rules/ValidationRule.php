<?php

namespace Modules\Hotel\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidationRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_array($value)) {
            return false;
        }

        // Check if both "customer_id" and "customer_id_request" are not empty
        return !empty($value[0]['customer_id']);
        // return !empty($value[0]['customer_id']) && !empty($value[0]['customer_id_request']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // return 'The validation error message.';
        return 'The :attribute must have non-empty customer_id and customer_id_request values.';
    }
}

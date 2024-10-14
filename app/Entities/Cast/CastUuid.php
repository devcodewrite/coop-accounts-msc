<?php

namespace App\Entities\Cast;

use CodeIgniter\Entity\Cast\BaseCast;

class CastUuid extends BaseCast
{
    /**
     * Casts the value when the attribute is retrieved.
     *
     * @param mixed  $value  The original value from the database.
     * @param array  $params Additional parameters (not used in this case).
     *
     * @return string|null The formatted value, e.g., 1234-5678-9012.
     */
    public static function get($value, array $params = [])
    {
        // Ensure the value is a 12-digit string before formatting
        if (is_string($value) && strlen($value) === 12 && ctype_digit($value)) {
            return substr($value, 0, 4) . '-' . substr($value, 4, 4) . '-' . substr($value, 8, 4);
        }

        return $value; // Return as-is if not a 12-digit string
    }

    /**
     * Casts the value when the attribute is set.
     *
     * @param mixed  $value  The value to set.
     * @param array  $params Additional parameters (not used in this case).
     *
     * @return string The plain value, e.g., 123456789012.
     */
    public static function set($value, array $params = [])
    {
        // Remove non-digit characters (in case a formatted string is passed)
        return preg_replace('/\D/', '', $value);
    }
}

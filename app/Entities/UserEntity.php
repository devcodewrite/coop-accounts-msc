<?php

namespace App\Entities;

use App\Entities\Cast\CastUuid;
use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    protected $attributes = [
        'id'       => null,
        'email'    => null,
        'phone'    => null,
        'username' => null,
        'name'     => null,
        'social_id' => null,
    ];

    protected $datamap = [
        // property_name => db_column_name
    ];

    // Specify which attributes should be hidden in the output (e.g., JSON)
    protected $hidden = ['password','social_id'];

    // Specify casts for specific fields
    protected $casts = [
        'email_verified' => 'bool',
        'phone_verified' => 'bool'
    ];

    protected $castHandlers = [
        'uuid' => CastUuid::class,
    ];

    /**
     * Automatically hash password when setting it.
     *
     * @param string|null $password
     */
    public function setPassword(string $password = null)
    {
        if ($password === null) return;

        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    // Override toArray method
    public function toArray(bool $onlyChanged = false, bool $cast = true, bool $recursive = false): array
    {
        // Create a copy of the attributes
        $data = parent::toArray($onlyChanged, $cast, $recursive);

        // Remove hidden fields
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }

        return $data; // Return the modified array
    }

    /**
     * Generate a unique 9-digit identifier.
     *
     * @return string
     */
    public function generateUniqueId(): string
    {
        // Generate a random 12-digit ID
        return str_pad((string) mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
    }

    /**
     * Verify user password
     */
    public function verifyPassword($password): bool |null
    {
        return password_verify($password, $this->password);
    }
}

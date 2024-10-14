<?php

namespace App\Models;

use App\Entities\UserEntity;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $primaryKeyType   = 'string';
    protected $useAutoIncrement = false;
    protected $returnType       = UserEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'type',
        'owner',
        'social_id',
        'social_provider',
        'username',
        'password',
        'name',
        'email',
        'phone',
        'email_verified',
        'phone_verified',
        'given_name',
        'family_name',
        'middle_name',
        'address',
        'gender',
        'picture',
        'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    /**
     * Return array of role ids
     */
    public function getRoleIDs(...$roles): array
    {
        return [];
    }

    /**
     * Return array of roles
     */
    public function getRoles(...$roles): array
    {
        return [];
    }
}

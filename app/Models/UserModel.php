<?php

namespace App\Models;

use App\Entities\UserEntity;
use CodeIgniter\Model;
use Google\Service\Dfareporting\UserRole;
use Google\Service\Drive\Permission;

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
    protected $afterInsert    = ['setupUserRoles'];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    protected function setupUserRoles(array $data)
    {
        $userId = $data['id']; // Get the ID of the inserted record
        $userData = $data['data'];

        if ($data['data']['type'] === 'root_user') {
            $owner = $userId;
        } else {
            $owner = $userData['owner'];
        }
        $userRoleModel = new UserRoleModel();
        $permModel = new PermissionModel();
        $roleModel = new RoleModel();

        $this->db->transStart();

        $roleModel->save(['name' => 'ManageRootAccountRole', 'owner' => $owner, 'editable' => false]);
        $role1 = $roleModel->getInsertID();

        $roleModel->save(['name' => 'ManageMyPasswordRole', 'owner' => $owner, 'editable' => false]);
        $role2 = $roleModel->getInsertID();

        $roleModel->save(['name' => 'ManageMyProfileRole', 'owner' => $owner, 'editable' => false]);
        $role3 = $roleModel->getInsertID();

        $fiter = json_encode(['owner' => [$owner]]);
        $scope = json_encode([$userId]);

        $permModel->insertBatch([
            //user accounts role
            ['resource_id' => 'groups', 'role_id' => $role1, 'actions' => '["create","view","update","delete"]', 'scopes' => null, 'filters' => $fiter],
            ['resource_id' => 'roles', 'role_id' => $role1, 'actions' => '["create","view","update","delete"]', 'scopes' => null, 'filters' => $fiter],
            ['resource_id' => 'users', 'role_id' => $role1, 'actions' => '["create","view","update","delete"]', 'scopes' => null, 'filters' => $fiter],
            ['resource_id' => 'passwords', 'role_id' => $role1, 'actions' => '["update"]', 'scopes' => $scope, 'filters' => $fiter],

            //cooperatives role
            ['resource_id' => 'organizations', 'role_id' => $role1, 'actions' => '["create","view","update","delete"]', 'scopes' => null, 'filters' => $fiter],
            ['resource_id' => 'offices', 'role_id' => $role1, 'actions' => '["create","view","update","delete"]', 'scopes' => null, 'filters' => $fiter],
            ['resource_id' => 'communities', 'role_id' => $role1, 'actions' => '["create","view","update","delete"]', 'scopes' => null, 'filters' => $fiter],
            ['resource_id' => 'associations', 'role_id' => $role1, 'actions' => '["create","view","update","delete"]', 'scopes' => null, 'filters' => $fiter],
            ['resource_id' => 'accounts', 'role_id' => $role1, 'actions' => '["create","view","update","delete"]', 'scopes' => null, 'filters' => $fiter],
            ['resource_id' => 'passbooks', 'role_id' => $role1, 'actions' => '["create","view","update","delete"]', 'scopes' => null, 'filters' => $fiter],
            //sub accounts
            ['resource_id' => 'passwords', 'role_id' => $role2, 'actions' => '["update"]', 'scopes' => $scope, 'filters' => $fiter],
            ['resource_id' => 'users', 'role_id' => $role3, 'actions' => '["create","view","update","delete"]', 'scopes' => $scope, 'filters' => $fiter],
        ]);

        $userRoles = [
            ['role_id' => $role2, 'user_id' => $userId],
            ['role_id' => $role3, 'user_id' => $userId]
        ];

        if ($data['data']['type'] === 'root_user') {
            array_push($userRoles, [
                'role_id' => $role1,
                'user_id' => $userId,
            ]);
        }
        $userRoleModel->insertBatch($userRoles);
        $this->db->transComplete();

        return $data;
    }
}

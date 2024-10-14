<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            //FullAccess
            ['resource_id' => 'resources', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'roles', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'permissions', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'users', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'groups', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'accounts', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'associations', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'communities', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'districts', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'regions', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'offices', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'organizations', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            ['resource_id' => 'passbooks', 'role_id' => 1, 'actions' => "create,view,update,delete", "conditions" => null],
            //ManageRootAccountRole
            ['resource_id' => 'roles', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'permissions', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'users', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'groups', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'accounts', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'associations', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'communities', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'districts', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'regions', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'offices', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'organizations', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['resource_id' => 'passbooks', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            // ManageMyPasswordRole
            ['resource_id' => 'users', 'role_id' => 3, 'actions' => "view,update,delete", "conditions" => '{"allowed":{"change_password":["{sub}"]}, "denied": {}}'],
            // ManageMyProfileRole
            ['resource_id' => 'users', 'role_id' => 4, 'actions' => "view,update", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
        ];
        //
        $this->db->table('permissions')->insertBatch($data);
    }
}

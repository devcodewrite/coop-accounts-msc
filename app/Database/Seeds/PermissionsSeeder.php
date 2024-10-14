<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            //FullAccess
            ['id' => 'resources', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'roles', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'permissions', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'users', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'groups', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'accounts', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'associations', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'communities', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'districts', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'regions', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'offices', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'organizations', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            ['id' => 'passbooks', 'role_id' => 1, 'actions' => "create,view,update,delete"],
            //ManageRootAccountRole
            ['id' => 'roles', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'permissions', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'users', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'groups', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'accounts', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'associations', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'communities', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'districts', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'regions', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'offices', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'organizations', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            ['id' => 'passbooks', 'role_id' => 2, 'actions' => "create,view,update,delete", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
            // ManageMyPasswordRole
            ['id' => 'users', 'role_id' => 3, 'actions' => "view,update,delete", "conditions" => '{"allowed":{"change_password":["{sub}"]}, "denied": {}}'],
            // ManageMyProfileRole
            ['id' => 'users', 'role_id' => 4, 'actions' => "view,update", "conditions" => '{"allowed":{"owner":["{sub}"]}, "denied": {}}'],
        ];
        //
        $this->db->table('permissions')->insertBatch($data);
    }
}

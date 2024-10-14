<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 'resources', 'role_id' => '', 'actions' => ""],
            ['id' => 'roles', 'role_id' => '', 'actions' => ""],
            ['id' => 'permissions', 'role_id' => '', 'actions' => ""],
            ['id' => 'users', 'role_id' => '', 'actions' => ""],
            ['id' => 'groups', 'role_id' => '', 'actions' => ""],
            ['id' => 'accounts', 'role_id' => '', 'actions' => ""],
            ['id' => 'associations', 'role_id' => '', 'actions' => ""],
            ['id' => 'communities', 'role_id' => '', 'actions' => ""],
            ['id' => 'districts', 'role_id' => '', 'actions' => ""],
            ['id' => 'regions', 'role_id' => '', 'actions' => ""],
            ['id' => 'offices', 'role_id' => '', 'actions' => ""],
            ['id' => 'organizations', 'role_id' => '', 'actions' => ""],
            ['id' => 'passbooks', 'role_id' => '', 'actions' => ""]
        ];
        //
        $this->db->table('permissions')->insertBatch($data);
    }
}

<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ResourcesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 'resources', 'name' => 'Resources'],
            ['id' => 'roles', 'name' => 'Roles'],
            ['id' => 'permissions', 'name' => 'Permissions'],
            ['id' => 'users', 'name' => 'Users'],
            ['id' => 'groups', 'name' => 'Groups'],
            ['id' => 'accounts', 'name' => 'Member Accounts'],
            ['id' => 'associations', 'name' => 'Associations'],
            ['id' => 'communities', 'name' => 'Communities'],
            ['id' => 'districts', 'name' => 'Districts'],
            ['id' => 'regions', 'name' => 'Regions'],
            ['id' => 'offices', 'name' => 'Offices'],
            ['id' => 'organizations', 'name' => 'Organizations'],
            ['id' => 'passbooks', 'name' => 'Passbooks'],
            ['id' => 'passwords', 'name' => 'Passbooks']
        ];
        //
        $this->db->table('resources')->insertBatch($data);
    }
}

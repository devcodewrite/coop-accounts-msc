<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        //
        $data = [
            ['id' => 1, 'name' => 'FullSystemAccessRole', 'editable' => false],
            ['id' => 2, 'name' => 'ManageRootAccountRole', 'editable' => false],
            ['id' => 3, 'name' => 'ManageMyPasswordRole', 'editable' => false],
            ['id' => 4, 'name' => 'ManageMyProfileRole', 'editable' => false],
        ];
        //
        $this->db->table('roles')->insertBatch($data);
    }
}

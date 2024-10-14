<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGroupsRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'group_id' => [
                'type' => 'INT',
            ],
            'role_id' => [
                'type' => 'INT',
            ],
        ]);

        $this->forge->addKey(['role_id', 'group_id'], true);
        $this->forge->addForeignKey('group_id','groups','id','','cascade');
        $this->forge->addForeignKey('role_id','roles','id','','cascade');
        $this->forge->createTable('groups_roles');
    }

    public function down()
    {
        $this->forge->dropTable('groups_roles');
    }
}

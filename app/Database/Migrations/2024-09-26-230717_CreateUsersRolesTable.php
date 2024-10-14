<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'role_id' => [
                'type' => 'INT',
            ],
        ]);

        $this->forge->addKey(['role_id', 'user_id'], true);
        $this->forge->addForeignKey('user_id','users','id','','cascade');
        $this->forge->addForeignKey('role_id','roles','id','','cascade');
        $this->forge->createTable('users_roles');
    }

    public function down()
    {
        $this->forge->dropTable('users_roles');
    }
}

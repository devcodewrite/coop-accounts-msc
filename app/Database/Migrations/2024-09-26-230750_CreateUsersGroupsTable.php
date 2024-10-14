<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersGroupsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'group_id' => [
                'type' => 'INT',
            ],
        ]);

        $this->forge->addKey(['group_id', 'user_id'], true);
        $this->forge->addForeignKey('user_id','users','id','','cascade');
        $this->forge->addForeignKey('group_id','groups','id','','restrict');
        $this->forge->createTable('users_groups');
    }

    public function down()
    {
        $this->forge->dropTable('users_groups');
    }
}

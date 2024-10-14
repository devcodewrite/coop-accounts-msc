<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'resource_id' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'role_id' => [
                'type' => 'INT',
            ],
            'actions' => [
                'type' => 'VARCHAR',
                'constraint' => '60',
            ],
            'conditions' => [
                'type' => 'TEXT',
                'null' => true
            ],
        ]);

        $this->forge->addKey(['resource_id', 'role_id'], true);
        $this->forge->addForeignKey('resource_id','resources','id','','cascade');
        $this->forge->addForeignKey('role_id','roles','id','','cascade');
        $this->forge->createTable('permissions');
    }

    public function down()
    {
        $this->forge->dropTable('permissions');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateAppsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'api_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => false,
            ],
            'app_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => false,
            ],
            'scopes' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'serialize object of the scopes'
            ],
            
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'revoked', 'pending'],
                'default'    => 'active',
                'null'       => false,
                'comment'    => 'active, revoked, pending',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ]
        ]);

        // Adding primary key, foreign key, and indexes
        $this->forge->addKey('api_key', true); // Primary Key
        $this->forge->createTable('apps', true); // Create `apps` table if it doesn't exist
    }

    public function down()
    {
        // Drop the `apps` table
        $this->forge->dropTable('apps', true);
    }
}

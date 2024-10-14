<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => false,
            ],
            'access_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Access token for social logins',
            ],
            'refresh_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Refresh token for renewing access',
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
        $this->forge->addKey('id', true); // Primary Key
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE'); // Foreign key to `users` table
        $this->forge->createTable('tokens', true); // Create `tokens` table if it doesn't exist
    }

    public function down()
    {
        // Drop the `tokens` table
        $this->forge->dropTable('tokens', true);
    }
}

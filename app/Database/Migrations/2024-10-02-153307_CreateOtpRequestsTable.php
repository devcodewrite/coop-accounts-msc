<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateOtpRequestsTable extends Migration
{
    public function up()
    {
        // Define the fields for the `otp_requests` table
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id'      => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => false,
            ],
            'type'         => [
                'type'       => 'ENUM',
                'constraint' => ['email_verification', 'phone_verification', 'password_reset'],
                'null'       => false,
            ],
            'identifier'   => [
                'type'       => 'VARCHAR',
                'constraint' => 100,  // Can be email or phone
                'null'       => false,
            ],
            'otp'          => [
                'type'       => 'VARCHAR',
                'constraint' => 10,  // OTP field for phone or password reset
                'null'       => true,
            ],
            'token'        => [
                'type'       => 'VARCHAR',
                'constraint' => 255,  // Token field for email verification or password reset
                'null'       => true,
            ],
            'expires_at'   => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at'   => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at'   => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);

        // Define the primary key
        $this->forge->addKey('id', true);

        // Define the foreign key constraint for `user_id` field
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        // Create the `otp_requests` table
        $this->forge->createTable('otp_requests');
    }

    public function down()
    {
        // Drop the `otp_requests` table if it exists
        $this->forge->dropTable('otp_requests', true);
    }
}

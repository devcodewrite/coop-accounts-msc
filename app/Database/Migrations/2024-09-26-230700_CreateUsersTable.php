<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['provider', 'root_user', 'sub_user'],
            ],
            'owner' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => true,
            ],
            'social_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'      => true,
            ],
            'social_provider' => [
                'type'       => 'ENUM',
                 'constraint' => ['app', 'google', 'facebook'],
                 'null'       => false,
                 'default'    => 'app',
                 'comment'    => '(e.g., app, google, facebook)',
             ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '60',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'email_verified' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'phone_verified' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'given_name' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ],
            'family_name' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ],
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
                'null' => true,
            ],
            'picture' => [
                'type' => 'TINYTEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('social_id');
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('phone');
        $this->forge->addForeignKey('owner','users','id','restrict','restrict');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}

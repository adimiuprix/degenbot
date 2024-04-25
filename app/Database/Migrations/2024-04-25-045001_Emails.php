<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Emails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'          => 'VARCHAR',
                'constraint'    => 255,
                'null' => true,
            ],
            'chat_id' => [
                'type'          => 'BIGINT',
                'constraint'    => 255,
                'null' => true,
            ],
            'email' => [
                'type'          => 'VARCHAR',
                'constraint'    => 255,
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('emails');
    }

    public function down()
    {
        $this->forge->dropTable('emails');
    }
}

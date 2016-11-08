<?php
use Migrations\AbstractMigration;

class Auth extends AbstractMigration
{
    public function up()
    {

        $this->table('users')
            ->addColumn('username', 'string', [
                'default' => null,
                'limit' => 45,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('role', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
            ])
            ->create();

    }

    public function down()
    {
        $this->dropTable('users');
    }
}

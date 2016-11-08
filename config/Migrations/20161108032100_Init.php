<?php
use Migrations\AbstractMigration;

class Init extends AbstractMigration
{
    public function up()
    {

        $this->table('divisions')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 45,
                'null' => false,
            ])
            ->addColumn('league_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'league_id',
                ]
            )
            ->create();

        $this->table('events')
            ->addColumn('division_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 45,
                'null' => false,
            ])
            ->addIndex(
                [
                    'division_id',
                ]
            )
            ->create();

        $this->table('leagues')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 45,
                'null' => false,
            ])
            ->create();

        $this->table('matches')
            ->addColumn('num', 'string', [
                'default' => null,
                'limit' => 45,
                'null' => false,
            ])
            ->addColumn('team_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('rp', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('qp', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('score', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('event_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'event_id',
                ]
            )
            ->addIndex(
                [
                    'team_id',
                ]
            )
            ->create();

        $this->table('teams')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 45,
                'null' => false,
            ])
            ->addColumn('division_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'division_id',
                ]
            )
            ->create();

        $this->table('divisions')
            ->addForeignKey(
                'league_id',
                'leagues',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION'
                ]
            )
            ->update();

        $this->table('events')
            ->addForeignKey(
                'division_id',
                'divisions',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION'
                ]
            )
            ->update();

        $this->table('matches')
            ->addForeignKey(
                'team_id',
                'teams',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION'
                ]
            )
            ->addForeignKey(
                'event_id',
                'events',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION'
                ]
            )
            ->update();

        $this->table('teams')
            ->addForeignKey(
                'division_id',
                'divisions',
                'id',
                [
                    'update' => 'NO_ACTION',
                    'delete' => 'NO_ACTION'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('divisions')
            ->dropForeignKey(
                'league_id'
            );

        $this->table('events')
            ->dropForeignKey(
                'division_id'
            );

        $this->table('matches')
            ->dropForeignKey(
                'blue_team_1_id'
            )
            ->dropForeignKey(
                'blue_team_2_id'
            )
            ->dropForeignKey(
                'event_id'
            )
            ->dropForeignKey(
                'red_team_1_id'
            )
            ->dropForeignKey(
                'red_team_2_id'
            );

        $this->table('teams')
            ->dropForeignKey(
                'division_id'
            );

        $this->dropTable('divisions');
        $this->dropTable('events');
        $this->dropTable('leagues');
        $this->dropTable('matches');
        $this->dropTable('teams');
    }
}

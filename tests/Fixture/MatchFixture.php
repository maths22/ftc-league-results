<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MatchFixture
 *
 */
class MatchFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'match';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'num' => ['type' => 'string', 'length' => 45, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'red_team_1_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'red_team_2_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'blue_team_1_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'blue_team_2_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'red_tbp' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'red_tbp' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'blue_tbp' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'blue_tbp' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'event_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'blue_team_1_id' => ['type' => 'index', 'columns' => ['blue_team_1_id'], 'length' => []],
            'blue_team_2_id' => ['type' => 'index', 'columns' => ['blue_team_2_id'], 'length' => []],
            'event_id' => ['type' => 'index', 'columns' => ['event_id'], 'length' => []],
            'red_team_1_id' => ['type' => 'index', 'columns' => ['red_team_1_id'], 'length' => []],
            'red_team_2_id' => ['type' => 'index', 'columns' => ['red_team_2_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'match_ibfk_1' => ['type' => 'foreign', 'columns' => ['blue_team_1_id'], 'references' => ['teams', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'match_ibfk_2' => ['type' => 'foreign', 'columns' => ['blue_team_2_id'], 'references' => ['teams', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'match_ibfk_3' => ['type' => 'foreign', 'columns' => ['event_id'], 'references' => ['event', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'match_ibfk_4' => ['type' => 'foreign', 'columns' => ['red_team_1_id'], 'references' => ['teams', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'match_ibfk_5' => ['type' => 'foreign', 'columns' => ['red_team_2_id'], 'references' => ['teams', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'num' => 'Lorem ipsum dolor sit amet',
            'red_team_1_id' => 1,
            'red_team_2_id' => 1,
            'blue_team_1_id' => 1,
            'blue_team_2_id' => 1,
            'red_tbp' => 1,
            'red_rp' => 1,
            'blue_tbp' => 1,
            'blue_rp' => 1,
            'event_id' => 1
        ],
    ];
}

<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MatchTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MatchTable Test Case
 */
class MatchTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\MatchTable
     */
    public $Match;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.match',
        'app.teams',
        'app.event',
        'app.divisions',
        'app.leagues'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Matches') ? [] : ['className' => 'App\Model\Table\MatchTable'];
        $this->Match = TableRegistry::get('Matches', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Match);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

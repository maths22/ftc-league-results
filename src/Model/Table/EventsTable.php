<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Events Model
 *
 * @property \App\Model\Table\LeaguesTable|\Cake\ORM\Association\BelongsTo $Leagues
 * @property \App\Model\Table\DivisionsTable|\Cake\ORM\Association\BelongsTo $Divisions
 * @property \App\Model\Table\MatchesTable|\Cake\ORM\Association\HasMany $Matches
 *
 * @method \App\Model\Entity\Event get($primaryKey, $options = [])
 * @method \App\Model\Entity\Event newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Event[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Event|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Event patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Event[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Event findOrCreate($search, callable $callback = null, $options = [])
 */
class EventsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('events');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Leagues', [
            'foreignKey' => 'league_id'
        ]);
        $this->belongsTo('Divisions', [
            'foreignKey' => 'division_id'
        ]);
        $this->hasMany('Matches', [
            'foreignKey' => 'event_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('type')
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->date('date')
            ->requirePresence('date', 'create')
            ->notEmpty('date');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['league_id'], 'Leagues'));
        $rules->add($rules->existsIn(['division_id'], 'Divisions'));

        return $rules;
    }
}

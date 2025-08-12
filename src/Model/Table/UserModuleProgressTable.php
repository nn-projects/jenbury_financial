<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserModuleProgress Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ModulesTable&\Cake\ORM\Association\BelongsTo $Modules
 *
 * @method \App\Model\Entity\UserModuleProgres newEmptyEntity()
 * @method \App\Model\Entity\UserModuleProgres newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\UserModuleProgres> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserModuleProgres get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\UserModuleProgres findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\UserModuleProgres patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\UserModuleProgres> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserModuleProgres|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\UserModuleProgres saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\UserModuleProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserModuleProgres>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UserModuleProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserModuleProgres> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UserModuleProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserModuleProgres>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UserModuleProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserModuleProgres> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserModuleProgressTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('user_module_progress');
        $this->setDisplayField('status');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Modules', [
            'foreignKey' => 'module_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->integer('module_id')
            ->notEmptyString('module_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->notEmptyString('status')
            ->inList('status', ['not_started', 'in_progress', 'completed'], 'Invalid status value.'); // Add inList validation

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['user_id', 'module_id']), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['module_id'], 'Modules'), ['errorField' => 'module_id']);

        return $rules;
    }
}

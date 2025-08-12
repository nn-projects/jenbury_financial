<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserContentProgress Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ContentsTable&\Cake\ORM\Association\BelongsTo $Contents
 *
 * @method \App\Model\Entity\UserContentProgres newEmptyEntity()
 * @method \App\Model\Entity\UserContentProgres newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\UserContentProgres> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserContentProgres get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\UserContentProgres findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\UserContentProgres patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\UserContentProgres> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserContentProgres|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\UserContentProgres saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\UserContentProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserContentProgres>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UserContentProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserContentProgres> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UserContentProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserContentProgres>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UserContentProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserContentProgres> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserContentProgressTable extends Table
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

        $this->setTable('user_content_progress');
        $this->setDisplayField('status');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Contents', [
            'foreignKey' => 'content_id',
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
            ->integer('content_id')
            ->notEmptyString('content_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->notEmptyString('status');

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
        $rules->add($rules->isUnique(['user_id', 'content_id']), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['content_id'], 'Contents'), ['errorField' => 'content_id']);

        return $rules;
    }
}

<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContentBlocks Model
 *
 * @method \App\Model\Entity\ContentBlock newEmptyEntity()
 * @method \App\Model\Entity\ContentBlock newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ContentBlock> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContentBlock get(mixed $primaryKey, array $options = [])
 * @method \App\Model\Entity\ContentBlock findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ContentBlock patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ContentBlock> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContentBlock|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ContentBlock saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ContentBlock>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContentBlock>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContentBlock>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContentBlock> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContentBlock>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContentBlock>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContentBlock>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContentBlock> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ContentBlocksTable extends Table
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

        $this->setTable('content_blocks'); // Name of the database table
        $this->setDisplayField('slug'); // Field to display in lists
        $this->setPrimaryKey('id'); // Primary key of the table

        $this->addBehavior('Timestamp'); // Automatically manage created and modified fields
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
            ->scalar('slug')
            ->maxLength('slug', 255)
            ->requirePresence('slug', 'create')
            ->notEmptyString('slug', 'Slug is required.')
            ->add('slug', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Slug must be unique.',
            ]);

        $validator
            ->scalar('parent')
            ->maxLength('parent', 255)
            ->allowEmptyString('parent');

        $validator
            ->scalar('type')
            ->maxLength('type', 50)
            ->requirePresence('type', 'create')
            ->notEmptyString('type', 'Type is required.')
            ->inList('type', ['text', 'html', 'json'], 'Invalid type.');

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmptyString('description');

        $validator
            ->scalar('value')
            ->notEmptyString('value', 'The content cannot be empty.');

        $validator
            ->scalar('previous_value')
            ->allowEmptyString('previous_value');

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
        $rules->add($rules->isUnique(['slug']), ['errorField' => 'slug']);

        return $rules;
    }
}
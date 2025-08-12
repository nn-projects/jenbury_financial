<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Contents Model
 *
 * @property \App\Model\Table\ModulesTable&\Cake\ORM\Association\BelongsTo $Modules
 *
 * @method \App\Model\Entity\Content newEmptyEntity()
 * @method \App\Model\Entity\Content newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Content> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Content get(mixed $primaryKey, array $options = [])
 * @method \App\Model\Entity\Content findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Content patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Content> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Content|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Content saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Content>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Content>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Content>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Content> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Content>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Content>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Content>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Content> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ContentsTable extends Table
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

        $this->setTable('contents');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Modules', [
            'foreignKey' => 'module_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('UserContentProgress', [
            'foreignKey' => 'content_id',
        ]);
        // Association for UserCourseProgress based on the last accessed content
        $this->hasMany('UserCourseProgress', [
            'foreignKey' => 'last_accessed_content_id',
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
            ->integer('module_id')
            ->notEmptyString('module_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('type')
            ->maxLength('type', 50)
            ->requirePresence('type', 'create')
            ->notEmptyString('type')
            ->inList('type', ['text', 'video', 'image', 'file']);

        $validator
            ->scalar('content')
            ->requirePresence('content', 'create')
            ->notEmptyString('content');

        $validator
            ->scalar('file_path')
            ->maxLength('file_path', 255)
            ->allowEmptyString('file_path');

        $validator
            ->integer('order')
            ->requirePresence('order', 'create')
            ->notEmptyString('order');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

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
        $rules->add($rules->existsIn(['module_id'], 'Modules'), ['errorField' => 'module_id']);

        return $rules;
    }
}
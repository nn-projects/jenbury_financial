<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Modules Model
 *
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 * @property \App\Model\Table\ContentsTable&\Cake\ORM\Association\HasMany $Contents
 * @property \App\Model\Table\PurchasesTable&\Cake\ORM\Association\HasMany $Purchases
 *
 * @method \App\Model\Entity\Module newEmptyEntity()
 * @method \App\Model\Entity\Module newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Module> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Module get(mixed $primaryKey, array $options = [])
 * @method \App\Model\Entity\Module findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Module patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Module> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Module|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Module saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Module>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Module>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Module>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Module> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Module>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Module>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Module>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Module> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ModulesTable extends Table
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

        $this->setTable('modules');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Contents', [
            'foreignKey' => 'module_id',
        ]);
        $this->hasMany('Purchases', [
            'foreignKey' => 'module_id',
        ]);
        $this->hasMany('UserModuleProgress', [
            'foreignKey' => 'module_id'
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
            ->integer('course_id')
            ->notEmptyString('course_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->integer('order', 'Display order must be a whole number.') // Added message
            ->greaterThan('order', 0, 'Display order must be a positive number.') // Ensure positive
            ->requirePresence('order', 'create', 'Display order is required.') // Added message
            ->notEmptyString('order', 'Display order cannot be empty.'); // Added message

        $validator
             ->integer('duration', 'Duration must be a whole number in minutes.') // Add duration validation
             ->greaterThan('duration', 0, 'Duration must be a positive number of minutes.') // Ensure positive
             ->requirePresence('duration', 'create', 'Duration is required.')
             ->notEmptyString('duration', 'Duration cannot be empty.');

        $validator
            ->numeric('price', 'Please enter a valid price.')
            ->decimal('price', 2, 'Price must have up to two decimal places.') // Ensure max 2 decimal places
            ->lessThanOrEqual('price', 999999.99, 'Price must be less than or equal to 999,999.99.') // Set max value
            ->allowEmptyString('price'); // Make price optional on server-side too

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
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);

        return $rules;
    }
}
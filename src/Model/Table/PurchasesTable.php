<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Purchases Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 * @property \App\Model\Table\ModulesTable&\Cake\ORM\Association\BelongsTo $Modules
 *
 * @method \App\Model\Entity\Purchase newEmptyEntity()
 * @method \App\Model\Entity\Purchase newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Purchase> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Purchase get(mixed $primaryKey, array $options = [])
 * @method \App\Model\Entity\Purchase findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Purchase patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Purchase> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Purchase|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Purchase saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Purchase>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Purchase>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Purchase>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Purchase> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Purchase>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Purchase>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Purchase>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Purchase> deleteManyOrFail(iterable $entities, array $options = [])
 */
class PurchasesTable extends Table
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

        $this->setTable('purchases');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
        ]);
        $this->belongsTo('Modules', [
            'foreignKey' => 'module_id',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => 'transaction_id',
            'bindingKey' => 'transaction_id',
            'joinType' => 'LEFT', // Use LEFT JOIN in case some purchases don't have a matching order (though ideally they should)
            // Ensure 'Orders.transaction_id' is a unique key or handle potential multiple matches if not.
            // If transaction_id is not unique on Orders, this relationship might fetch the first match.
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
            ->integer('course_id')
            ->allowEmptyString('course_id');

        $validator
            ->integer('module_id')
            ->allowEmptyString('module_id');

        $validator
            ->numeric('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->scalar('payment_status')
            ->maxLength('payment_status', 50)
            ->requirePresence('payment_status', 'create')
            ->notEmptyString('payment_status')
            ->inList('payment_status', ['pending', 'completed', 'failed', 'refunded']);

        $validator
            ->scalar('transaction_id')
            ->maxLength('transaction_id', 255)
            ->allowEmptyString('transaction_id');

        $validator
            ->dateTime('expires')
            ->allowEmptyDateTime('expires');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);
        $rules->add($rules->existsIn(['module_id'], 'Modules'), ['errorField' => 'module_id']);
        
        // Either course_id or module_id must be set
        $rules->add(function ($entity) {
            return !empty($entity->course_id) || !empty($entity->module_id);
        }, 'requireCourseOrModule', [
            'errorField' => 'course_id',
            'message' => 'Either a course or a module must be specified'
        ]);

        return $rules;
    }
}
<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DiscountCodes Model
 *
 * @method \App\Model\Entity\DiscountCode newEmptyEntity()
 * @method \App\Model\Entity\DiscountCode newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\DiscountCode[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DiscountCode get($primaryKey, $options = [])
 * @method \App\Model\Entity\DiscountCode findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\DiscountCode patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DiscountCode[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\DiscountCode|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DiscountCode saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DiscountCode[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\DiscountCode[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\DiscountCode[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\DiscountCode[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DiscountCodesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('discount_codes');
        $this->setDisplayField('code');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ],
            ]
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
            ->scalar('code')
            ->maxLength('code', 20, 'Code cannot be longer than 20 characters.')
            ->minLength('code', 3, 'Code must be at least 3 characters long.')
            ->requirePresence('code', 'create')
            ->notEmptyString('code')
            ->add('code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This discount code is already in use. Please choose a different one.']);

        $validator
            ->decimal('percentage')
            ->requirePresence('percentage', 'create')
            ->notEmptyString('percentage')
            ->range('percentage', [0, 100], 'Percentage must be between 0 and 100.');

        $validator
            ->boolean('is_active');

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
        $rules->add($rules->isUnique(['code']), ['errorField' => 'code', 'message' => 'This discount code is already in use. Please choose a different one.']);

        return $rules;
    }
}
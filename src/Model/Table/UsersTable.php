<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\PurchasesTable&\Cake\ORM\Association\HasMany $Purchases
 *
 * Password field must be at least 8 characters long and include at least one uppercase letter and one special character.
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\User> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get(mixed $primaryKey, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\User> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\User>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\User> deleteManyOrFail(iterable $entities, array $options = [])
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Purchases', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('UserContentProgress', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('UserCourseProgress', [
            'foreignKey' => 'user_id',
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
        ->email('email')
        ->requirePresence('email', 'create')
        ->notEmptyString('email', 'Please enter an email address.')
        ->add('email', [
            'unique' => [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This email is already in use.',
            ],
        ]);

    $validator
        ->scalar('password')
        ->requirePresence('password', 'create')
        ->notEmptyString('password', 'Please enter a password.')
        ->add('password', [
            'minLength' => [
                'rule' => ['minLength', 8],
                'message' => 'Password must be at least 8 characters long.',
            ],
            'uppercase' => [
                'rule' => function ($value) {
                    return is_string($value) && preg_match('/[A-Z]/', $value);
                },
                'message' => 'Password must include at least one uppercase letter.',
            ],
            'specialCharacter' => [
                'rule' => function ($value) {
                    return is_string($value) && preg_match('/[\W_]/', $value);
                },
                'message' => 'Password must include at least one special character.',
            ],
        ]);

    $validator
        ->requirePresence('confirm_password', 'create')
        ->notEmptyString('confirm_password', 'Please confirm your password.')
        ->add('confirm_password', 'custom', [
            'rule' => function ($value, $context) {
                return isset($context['data']['password']) && $value === $context['data']['password'];
            },
            'message' => 'Passwords do not match.',
        ]);

        $validator
            ->scalar('first_name')
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name')
            ->maxLength('first_name', 70)
            ->minLength('first_name', 2, 'Name must be at least 2 characters long.');

        $validator
            ->scalar('last_name')
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name')
            ->maxLength('last_name', 70)
            ->minLength('last_name', 2, 'Name must be at least 2 characters long.');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->boolean('email_verified')
            ->notEmptyString('email_verified');
            
        $validator
            ->scalar('role')
            ->maxLength('role', 20)
            ->notEmptyString('role')
            ->inList('role', ['member','student', 'admin'], 'Role must be either "member", "student" or "admin"');

        // Validation for the terms checkbox
        $validator
            ->requirePresence('terms', 'create', 'You must agree to the Terms & Services.')
            ->equals('terms', 1, 'You must agree to the Terms & Services.'); // Checkboxes usually submit '1' when checked

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
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }
}
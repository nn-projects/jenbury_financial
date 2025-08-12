<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Courses Model
 *
 * @property \App\Model\Table\ModulesTable&\Cake\ORM\Association\HasMany $Modules
 * @property \App\Model\Table\PurchasesTable&\Cake\ORM\Association\HasMany $Purchases
 *
 * @method \App\Model\Entity\Course newEmptyEntity()
 * @method \App\Model\Entity\Course newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Course> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Course get(mixed $primaryKey, array $options = [])
 * @method \App\Model\Entity\Course findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Course patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Course> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Course|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Course saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Course>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Course>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Course>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Course> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Course>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Course>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Course>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Course> deleteManyOrFail(iterable $entities, array $options = [])
 */
class CoursesTable extends Table
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

        $this->setTable('courses');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        /*
        $this->addBehavior('FileUpload', [
            'fields' => [
                'image_file' => [
                    'path_field' => 'image',
                ],
            ],
            'path' => WWW_ROOT . 'img' . DS . 'courses',
            'allowedTypes' => ['image/jpeg', 'image/png', 'image/gif','image/jpg'],
            'maxFileSize' => 5242880, // 5MB
            'maxWidth' => 2048,
            'maxHeight' => 2048,
        ]);
        */
        $this->hasMany('Modules', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('Purchases', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('UserCourseProgress', [
            'foreignKey' => 'course_id',
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
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->scalar('image')
            ->maxLength('image', 255)
            ->allowEmptyString('image');

        $validator
            ->numeric('price', 'Please enter a valid price.') // Added custom message
            ->decimal('price', 2, 'Price must have up to two decimal places.') // Ensure max 2 decimal places
            ->lessThanOrEqual('price', 999999.99, 'Price must be less than or equal to 999,999.99.') // Set max value fitting roughly in 8 chars (e.g., 123456.78)
            ->requirePresence('price', 'create', 'Price is required.') // Added custom message
            ->notEmptyString('price', 'Price cannot be empty.'); // Added custom message

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        return $validator;
    }
}
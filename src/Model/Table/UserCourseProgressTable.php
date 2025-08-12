<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserCourseProgress Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 * @property \App\Model\Table\ContentsTable&\Cake\ORM\Association\BelongsTo $LastAccessedContents
 *
 * @method \App\Model\Entity\UserCourseProgres newEmptyEntity()
 * @method \App\Model\Entity\UserCourseProgres newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\UserCourseProgres> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserCourseProgres get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\UserCourseProgres findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\UserCourseProgres patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\UserCourseProgres> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserCourseProgres|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\UserCourseProgres saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\UserCourseProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserCourseProgres>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UserCourseProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserCourseProgres> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UserCourseProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserCourseProgres>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UserCourseProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UserCourseProgres> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserCourseProgressTable extends Table
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

        $this->setTable('user_course_progress');
        $this->setDisplayField('status');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('LastAccessedContents', [
            'foreignKey' => 'last_accessed_content_id',
            'className' => 'Contents',
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
            ->notEmptyString('course_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->notEmptyString('status');

        $validator
            ->integer('last_accessed_content_id')
            ->allowEmptyString('last_accessed_content_id');

        $validator
            ->dateTime('completion_date')
            ->allowEmptyDateTime('completion_date');

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
        $rules->add($rules->isUnique(['user_id', 'course_id']), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);
        $rules->add($rules->existsIn(['last_accessed_content_id'], 'LastAccessedContents'), ['errorField' => 'last_accessed_content_id']);

        return $rules;
    }
}

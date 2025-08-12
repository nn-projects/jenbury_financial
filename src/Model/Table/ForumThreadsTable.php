<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use Cake\ORM\Entity;
use Cake\Utility\Text;

class ForumThreadsTable extends Table
{
    public function beforeSave(EventInterface $event, Entity $entity, \ArrayObject $options): void
    {
        if ($entity->isNew() && empty($entity->slug) && !empty($entity->title)) {
            $slug = strtolower(Text::slug($entity->title));
            $entity->slug = $slug;
        }
    }  
    
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('forum_threads');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        
        $this->addBehavior('CounterCache', [
            'ForumCategories' => ['thread_count']
        ]);

        $this->belongsTo('ForumCategories', [
            'foreignKey' => 'forum_category_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('ForumPosts', [
            'foreignKey' => 'forum_thread_id',
            'dependent' => true,
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
            ->integer('forum_category_id')
            ->requirePresence('forum_category_id', 'create')
            ->notEmptyString('forum_category_id');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');
        
        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');
        
        $validator
            ->scalar('slug')
            ->maxLength('slug', 255)
            ->add('slug', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Slug must be unique.',
            ]);
        
        $validator
            ->integer('post_count')
            ->allowEmptyString('post_count');
        
        $validator
            ->integer('last_post_id')
            ->allowEmptyString('last_post_id');

        $validator
            ->integer('last_post_user_id')
            ->allowEmptyString('last_post_user_id');
        
        $validator
            ->dateTime('last_post_created')
            ->allowEmptyDateTime('last_post_created');
        
        $validator
            ->boolean('is_locked')
            ->notEmptyString('is_locked');
        
        $validator
            ->boolean('is_sticky')
            ->notEmptyString('is_sticky');
 
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
        $rules->add($rules->existsIn(['forum_category_id'], 'ForumCategories'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }
}
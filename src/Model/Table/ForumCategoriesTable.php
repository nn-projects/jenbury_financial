<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Text;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;

class ForumCategoriesTable extends Table
{

    public function beforeSave(EventInterface $event, EntityInterface $entity, \ArrayObject $options): void
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

        $this->setTable('forum_categories');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // Association with ForumThreads
        $this->hasMany('ForumThreads', [
            'foreignKey' => 'forum_category_id',
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
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 255)
            ->add('slug', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Slug must be unique.',
            ]);

        

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

        return $rules;
    }


    public function sumPostCountFromThreads(int $categoryId): int
    {
        $threadsTable = $this->getAssociation('ForumThreads')->getTarget();

        $sum = $threadsTable->find()
            ->where(['forum_category_id' => $categoryId])
            ->select(['total_posts' => $threadsTable->find()->func()->sum('post_count')])
            ->first()
            ->total_posts;

        return (int) $sum;
    }

}
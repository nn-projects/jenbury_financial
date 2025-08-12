<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Log\Log; // Added for logging in placeholder

/**
 * CartItems Model
 *
 * @property \App\Model\Table\CartsTable&\Cake\ORM\Association\BelongsTo $Carts
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 * @property \App\Model\Table\ModulesTable&\Cake\ORM\Association\BelongsTo $Modules
 *
 * @method \App\Model\Entity\CartItem newEmptyEntity()
 * @method \App\Model\Entity\CartItem newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\CartItem> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CartItem get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\CartItem findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\CartItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\CartItem> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CartItem|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\CartItem saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\CartItem>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CartItem>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CartItem>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CartItem> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CartItem>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CartItem>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CartItem>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CartItem> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CartItemsTable extends Table
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

        $this->setTable('cart_items');
        $this->setDisplayField('item_type');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Carts', [
            'foreignKey' => 'cart_id',
            'joinType' => 'INNER',
        ]);
        // Note: These relationships are based on item_id matching the foreign key.
        // You'll need to use the 'item_type' field in your queries/logic
        // to determine which relationship is relevant for a specific cart item.
        $this->belongsTo('Courses', [
            'foreignKey' => 'item_id',
            'conditions' => ['CartItems.item_type' => 'Course'], // Condition helps, but still requires careful querying
        ]);
        $this->belongsTo('Modules', [
            'foreignKey' => 'item_id',
            'conditions' => ['CartItems.item_type' => 'Module'], // Condition helps, but still requires careful querying
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
            ->integer('cart_id')
            ->notEmptyString('cart_id');

        $validator
            ->integer('item_id')
            ->requirePresence('item_id', 'create')
            ->notEmptyString('item_id');

        $validator
            ->scalar('item_type')
            ->maxLength('item_type', 50)
            ->requirePresence('item_type', 'create')
            ->notEmptyString('item_type');

        $validator
            ->integer('quantity')
            ->notEmptyString('quantity');

        $validator
            ->decimal('price')
            ->requirePresence('price', 'create')
            ->notEmptyString('price');

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
        $rules->add($rules->existsIn(['cart_id'], 'Carts'), ['errorField' => 'cart_id']);

        return $rules;
    }

    /**
     * Checks if a specific item is already owned by the user through a completed order.
     *
     * @param int $userId The ID of the user.
     * @param int $itemId The ID of the item (Course or Module).
     * @param string $itemType The type of the item ('Course' or 'Module').
     * @return bool True if the user owns the item, false otherwise.
     */
    public function isItemOwnedByUser(int $userId, int $itemId, string $itemType): bool
    {
        /** @var \App\Model\Table\OrdersTable $OrdersTable */
        $OrdersTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Orders');

        $query = $OrdersTable->find();
        $query->innerJoinWith('OrderItems', function (SelectQuery $q) use ($itemId, $itemType) {
                return $q->where(['OrderItems.item_id' => $itemId, 'OrderItems.item_type' => $itemType]);
            })
            ->where([
                'Orders.user_id' => $userId,
                'Orders.payment_status' => 'completed', // Corrected column name
            ]);

        return $query->count() > 0;
    }
}

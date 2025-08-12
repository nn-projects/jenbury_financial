<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Log\Log; // Added for logging in placeholder

/**
 * Carts Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CartItemsTable&\Cake\ORM\Association\HasMany $CartItems
 *
 * @method \App\Model\Entity\Cart newEmptyEntity()
 * @method \App\Model\Entity\Cart newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Cart> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cart get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Cart findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Cart patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Cart> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cart|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Cart saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Cart>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cart>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Cart>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cart> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Cart>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cart>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Cart>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cart> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CartsTable extends Table
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

        $this->setTable('carts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('CartItems', [
            'foreignKey' => 'cart_id',
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

        return $rules;
    }

    /**
     * Calculates the total price of all items in a given cart.
     *
     * Iterates through CartItems, using the price stored at the time the item
     * was added, multiplies by quantity, and sums the results.
     *
     * @param int $cartId The ID of the cart.
     * @return float The calculated total price based on prices stored in cart items.
     */
    public function calculateTotal(int $cartId): float
    {
        $total = 0.0;

        /** @var \App\Model\Table\CartItemsTable $cartItemsTable */
        $cartItemsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('CartItems');

        // Fetch only necessary fields from CartItems
        $cartItems = $cartItemsTable->find()
            ->select(['id', 'price', 'quantity']) // Select only needed fields
            ->where(['cart_id' => $cartId])
            ->all();

        /** @var \App\Model\Entity\CartItem $item */
        foreach ($cartItems as $item) {
            // Use the price stored in the cart item itself
            $price = (float)$item->price;
            $total += $price * $item->quantity;
        }

        return $total;
    }
}

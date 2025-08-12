<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property bool $is_active
 * @property bool $email_verified
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Purchase[] $purchases
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        'email' => true,
        'password' => true,
        'first_name' => true,
        'last_name' => true,
        'is_active' => true,
        'email_verified' => true,
        'role' => true,
        'terms' => true, // Added for registration form validation
        'created' => true,
        'modified' => true,
        'purchases' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected array $_hidden = [
        'password',
    ];
    
    /**
     * Hash password before saving
     *
     * @param string $password The password to hash
     * @return string|null
     */
    protected function _setPassword(string $password): ?string
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }
        
        return null;
    }
}
<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddRoleToUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('role', 'string', [
            'default' => 'user',
            'limit' => 20,
            'null' => false,
            'after' => 'email_verified'
        ]);
        $table->update();
        
        // Update the admin user to have the admin role
        $this->execute("UPDATE users SET role = 'admin' WHERE email = 'admin@jenburyfinancial.com'");
    }
}
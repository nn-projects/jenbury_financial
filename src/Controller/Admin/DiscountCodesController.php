<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AdminController;

/**
 * DiscountCodes Controller
 *
 * @property \App\Model\Table\DiscountCodesTable $DiscountCodes
 */
class DiscountCodesController extends AdminController
{
    public function initialize(): void
    {
        parent::initialize();
        // Assuming you have authentication and authorization set up
        // $this->loadComponent('Auth'); // Or similar
        // $this->loadComponent('Authorization.Authorization');

        // Unlock the 'add' action for FormProtectionComponent
        if ($this->components()->has('FormProtection')) {
            $formProtection = $this->FormProtection;
            $unlockedActions = $formProtection->getConfig('unlockedActions', []);
            if (!in_array('add', $unlockedActions)) {
                $unlockedActions[] = 'add';
            }
            $formProtection->setConfig('unlockedActions', $unlockedActions);
        }

        $this->viewBuilder()->setLayout('jenbury');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Optional: Add authorization check here if needed
        // $this->Authorization->authorize($this->DiscountCodes);

        $discountCodes = $this->paginate($this->DiscountCodes);

        $this->set(compact('discountCodes'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $discountCode = $this->DiscountCodes->newEmptyEntity();
        // Optional: Add authorization check here if needed
        // $this->Authorization->authorize($discountCode);

        if ($this->request->is('post')) {
            $discountCode = $this->DiscountCodes->patchEntity($discountCode, $this->request->getData());
            // Default is_active to true when creating
            $discountCode->is_active = true;
            if ($this->DiscountCodes->save($discountCode)) {
                $this->Flash->success(__('The discount code has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The discount code could not be saved. Please, try again.'));
        }
        $this->set(compact('discountCode'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Discount Code id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $discountCode = $this->DiscountCodes->get($id, [
            'contain' => [],
        ]);
        // Optional: Add authorization check here if needed
        // $this->Authorization->authorize($discountCode);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $discountCode = $this->DiscountCodes->patchEntity($discountCode, $this->request->getData());
            if ($this->DiscountCodes->save($discountCode)) {
                $this->Flash->success(__('The discount code has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The discount code could not be saved. Please, try again.'));
        }
        $this->set(compact('discountCode'));
    }

     /**
     * Toggle Status method
     *
     * @param string|null $id Discount Code id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function toggleStatus($id = null)
    {
        $this->request->allowMethod(['post']);
        $discountCode = $this->DiscountCodes->get($id);
        // Optional: Add authorization check here if needed
        // $this->Authorization->authorize($discountCode, 'update'); // Or a specific policy action

        $discountCode->is_active = !$discountCode->is_active;
        if ($this->DiscountCodes->save($discountCode)) {
            $this->Flash->success(__('The discount code status has been updated.'));
        } else {
            $this->Flash->error(__('The discount code status could not be updated. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    /**
     * Delete method
     *
     * @param string|null $id Discount Code id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $discountCode = $this->DiscountCodes->get($id);
        // Optional: Add authorization check here if needed
        // $this->Authorization->authorize($discountCode);

        if ($this->DiscountCodes->delete($discountCode)) {
            $this->Flash->success(__('The discount code has been deleted.'));
        } else {
            $this->Flash->error(__('The discount code could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
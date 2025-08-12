<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\ContentBlocksTable; // Import the ContentBlocksTable class
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;

class ContentBlocksController extends AppController
{
    /**
     * @var \App\Model\Table\ContentBlocksTable|null
     */
    protected ?ContentBlocksTable $ContentBlocks = null;

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Use dependency injection to load the ContentBlocks model
        $this->ContentBlocks = $this->fetchTable('ContentBlocks');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $contentBlocks = $this->ContentBlocks->find('all')->toArray();

        // Group content blocks by their parent field
        $groupedBlocks = [];
        foreach ($contentBlocks as $block) {
            $groupedBlocks[$block->parent][] = $block;
        }

        // Define the desired order
        $desiredOrder = ['home', 'Navigation', 'Footer']; // Corrected case for 'home'
        $orderedGroupedBlocks = [];

        // Add blocks in the desired order
        foreach ($desiredOrder as $parent) {
            if (isset($groupedBlocks[$parent])) {
                $orderedGroupedBlocks[$parent] = $groupedBlocks[$parent];
                unset($groupedBlocks[$parent]); // Remove from original to avoid duplication
            }
        }

        // Add any remaining groups
        foreach ($groupedBlocks as $parent => $blocks) {
            $orderedGroupedBlocks[$parent] = $blocks;
        }

        $this->set('groupedBlocks', $orderedGroupedBlocks); // Pass the ordered array to the view
    }

    /**
     * Edit method
     *
     * @param int|null $id Content block ID
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise
     */
    public function edit($id = null)
    {
        try {
            $contentBlock = $this->ContentBlocks->get($id);
        } catch (\Exception $e) {
            $this->Flash->error(__('Content block not found.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post', 'put'])) {
            // Store the current value before patching
            $contentBlock->previous_value = $contentBlock->value;

            $contentBlock = $this->ContentBlocks->patchEntity($contentBlock, $this->request->getData());

            if ($this->ContentBlocks->save($contentBlock)) {
                $this->Flash->success(__('The content block has been updated.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The content block could not be updated. Please try again.'));
        }

        $this->set(compact('contentBlock'));
    }

    /**
     * Restore method
     *
     * @param int|null $id Content block ID
     * @return \Cake\Http\Response|null Redirects to index
     */
    public function restore($id = null)
    {
        try {
            $contentBlock = $this->ContentBlocks->get($id);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Content block not found.'));
            return $this->redirect(['action' => 'index']);
        }

        // Check if a previous value exists before attempting restore
        if (!empty($contentBlock->previous_value)) { // Use !empty() to check for null, '', false, 0 etc.
            $contentBlock->value = $contentBlock->previous_value; // Restore the value

            if ($this->ContentBlocks->save($contentBlock)) {
                $this->Flash->success(__('The content block has been restored to its previous version.'));
            } else {
                // Log error or provide more specific feedback if possible
                $this->Flash->error(__('Unable to restore the content block. Please try again.'));
            }
        } else {
            // No previous value exists, inform the user and do not change the current value
            $this->Flash->info(__('There is no previous version recorded for this content block. No changes were made.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Restore All method
     *
     * @return \Cake\Http\Response|null Redirects to index
     */
    public function restoreAll()
    {
        $contentBlocks = $this->ContentBlocks->find('all');

        foreach ($contentBlocks as $block) {
            // Check if a non-empty previous value exists before attempting restore
            if (!empty($block->previous_value)) {
                $block->value = $block->previous_value; // Restore the value
                // Note: Error handling for individual saves within the loop is omitted for brevity,
                // but could be added if granular feedback is needed.
                $this->ContentBlocks->save($block);
            }
            // If previous_value is empty, do nothing for this specific block.
        }

        $this->Flash->success(__('All content blocks have been restored.'));
        return $this->redirect(['action' => 'index']);
    }
}
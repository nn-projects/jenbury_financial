<?php
/**
 * Jenbury Financial - Module Content Page
 */
$this->assign('title', h($content->title));
?>
<?php
// Determine Next/Finish button details for use in header and bottom navigation
$nextContentUrl = null;
$nextContentTitle = 'Next Lesson';
$currentIndex = null;

// Determine current index first
foreach ($module->contents as $index => $moduleContentItem) {
    if ($moduleContentItem->id == $content->id) {
        $currentIndex = $index;
        break;
    }
}

if ($currentIndex !== null && isset($module->contents[$currentIndex + 1])) {
    $nextContent = $module->contents[$currentIndex + 1];
    $nextContentUrl = ['controller' => 'Modules', 'action' => 'content', $module->id, $nextContent->id];
} else {
    // Last content item, or content not found in list
    $nextContentUrl = ['controller' => 'Modules', 'action' => 'view', $module->id];
    $nextContentTitle = 'Finish Module';
}
?>

<div class="content-header">
    <div class="row">
        <div class="column column-80"> <?php // Adjusted column width ?>
            <div class="breadcrumbs">
                <?= $this->Html->link('Courses', ['controller' => 'Courses', 'action' => 'index']) ?> >
                <?= $this->Html->link(h($module->course->title), ['controller' => 'Courses', 'action' => 'view', $module->course->id]) ?> >
                <?= $this->Html->link(h($module->title), ['controller' => 'Modules', 'action' => 'view', $module->id]) ?> >
                <?= h($content->title) ?>
            </div>
            <h1><?= h($content->title) ?></h1>
        </div>
        <div class="column column-20 text-right"> <?php // Added column for next button ?>
            <?php if ($nextContentUrl): ?>
                <?= $this->Html->link($nextContentTitle . ' ›', $nextContentUrl, ['id' => 'next-content-button-header', 'class' => 'button button-primary', 'escape' => false]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<main class="module-content-page">
    <div class="row">
        <div class="module-content-main">
            <div class="card">
                <div class="card-body" style="padding: var(--space-6);">
                    <div class="content-display">
                        <?php
                        // Render the raw HTML content stored in the 'content' column
                        echo $content->content;
                        ?>
                    </div>
                </div>
            </div>
            <div class="content-navigation">
                <?= $this->Html->link('Back to Module', ['controller' => 'Modules', 'action' => 'view', $module->id], ['class' => 'button button-secondary']) ?>
                <?php // Logic for $nextContentUrl and $nextContentTitle is now at the top of the file ?>
                <?php if ($nextContentUrl): ?>
                    <?= $this->Html->link($nextContentTitle, $nextContentUrl, ['id' => 'next-content-button-footer', 'class' => 'button button-primary']) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="module-content-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Module Contents</h3>
                </div>
                <div class="card-body" style="padding: var(--space-6);">
                    <ul class="content-list-sidebar">
                        <?php foreach ($module->contents as $moduleContent): ?>
                            <li class="<?= $moduleContent->id == $content->id ? 'active' : '' ?>">
                                <?= $this->Html->link(h($moduleContent->title), ['controller' => 'Modules', 'action' => 'content', $module->id, $moduleContent->id]) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentId = <?= json_encode($content->id) ?>;
    const csrfToken = <?= json_encode($this->request->getAttribute('csrfToken')) ?>; // Get CSRF token

    function handleNextButtonClick(event) {
        event.preventDefault(); // Prevent default link navigation
        const button = event.currentTarget;
        const navigationUrl = button.href;

        // Make AJAX call to mark content as complete
        fetch('<?= $this->Url->build(['controller' => 'Progress', 'action' => 'markContentComplete']) ?>/' + contentId, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // Important for CakePHP to recognize as AJAX
            },
            // No body needed as contentId is in URL
        })
        .then(response => response.json())
        .then(data => {
            console.log('Progress update response:', data);
            // Proceed with navigation regardless of minor update issues for now
            window.location.href = navigationUrl;
        })
        .catch(error => {
            console.error('Error updating progress:', error);
            // Still proceed with navigation
            window.location.href = navigationUrl;
        });
    }

    const nextButtonHeader = document.getElementById('next-content-button-header');
    const nextButtonFooter = document.getElementById('next-content-button-footer');

    if (nextButtonHeader) {
        nextButtonHeader.addEventListener('click', handleNextButtonClick);
    }
    if (nextButtonFooter) {
        nextButtonFooter.addEventListener('click', handleNextButtonClick);
    }
});
</script>
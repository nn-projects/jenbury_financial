<?php
/**
 * @var \App\View\AppView $this
 * @var string $message The error message.
 * @var string $url The URL that was requested.
 * @var string $code Error code (e.g., 404)
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'jenbury'; // Ensure it uses the main layout
$this->assign('title', 'Page Unavailable'); // Set the page title

// Set default layout variables using set() so they are available in the layout
$this->set('loggedIn', $this->get('loggedIn', false)); // Pass existing value or default to false
$this->set('currentUser', $this->get('currentUser', null)); // Pass existing value or default to null

// In a real application, you might have logic here to suggest alternative pages
// based on $url or common navigation patterns.
// $suggestedPages = $this->MyHelper->findSimilarPages($url);

?>
<div class="error-page-container" role="main" aria-labelledby="error-heading">
    <h1 id="error-heading">Oops! We can't seem to find that page.</h1>
    <p>The page you requested at <code><?= h($url) ?></code> isn't available.</p>
    <p>This might be because the address was typed incorrectly, or the page may have been moved or removed.</p>

    <!-- Placeholder for potential page suggestions -->
    <?php /*
    if (!empty($suggestedPages)) {
        echo '<p>Maybe you were looking for one of these?</p>';
        echo '<ul>';
        foreach ($suggestedPages as $page) {
            echo '<li>' . $this->Html->link($page['title'], $page['url']) . '</li>';
        }
        echo '</ul>';
    }
    */ ?>

    <button onclick="history.back();" class="button btn-primary" role="button">
        Go Back
    </button>
</div>

<?php
// CSS styles for error pages should be added to JenburyFinancial/webroot/css/jenbury.css
?>
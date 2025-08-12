<?php
/**
 * @var \App\View\AppView $this
 * @var string $message The error message.
 * @var string $url The URL that was requested.
 * @var string $code Error code (e.g., 400)
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'jenbury'; // Use the main Jenbury layout
$this->assign('title', 'Request Problem'); // Set the page title

// Set default layout variables using set() so they are available in the layout
$this->set('loggedIn', $this->get('loggedIn', false)); // Pass existing value or default to false
$this->set('currentUser', $this->get('currentUser', null)); // Pass existing value or default to null

?>
<div class="error-page-container" role="main" aria-labelledby="error-heading">
    <h1 id="error-heading">Hmm, there seems to be an issue with the request.</h1>
    <p>We couldn't understand the request sent by your browser.</p>
    <p>Please check the web address (URL) or try going back to the previous page.</p>

    <button onclick="history.back();" class="button btn-primary" role="button">
        Go Back
    </button>
</div>

<?php
// CSS styles for error pages should be added to JenburyFinancial/webroot/css/jenbury.css
?>

<?php
/**
 * @var \App\View\AppView $this
 * @var \Throwable $error The error object.
 * @var string $message The error message (potentially sensitive, avoid displaying directly).
 * @var string $url The URL that was requested.
 * @var string $code Error code (e.g., 500)
 * @var string|null $request_id Optional unique request identifier for support.
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'jenbury'; // Ensure it uses the main layout
$this->assign('title', 'Server Problem'); // Set the page title

// Set default layout variables using set() so they are available in the layout
$this->set('loggedIn', $this->get('loggedIn', false)); // Pass existing value or default to false
$this->set('currentUser', $this->get('currentUser', null)); // Pass existing value or default to null

// Retrieve Request ID if passed from the Exception Renderer
$requestId = $this->get('request_id'); // Use get() for safe access

?>
<div class="error-page-container" role="main" aria-labelledby="error-heading">
    <h1 id="error-heading">Oops! Something went wrong.</h1>
    <p>We encountered a temporary problem on our server while trying to load this page.</p>
    <p>We apologize for the inconvenience. Our technical team has been automatically notified and is working to resolve the issue.</p>

    <?php if ($requestId): ?>
        <p class="request-id">If you need to contact support, please provide this Request ID: <strong><?= h($requestId) ?></strong></p>
    <?php endif; ?>

    <button onclick="history.back();" class="button btn-primary" role="button">
        Go Back
    </button>
</div>

<?php
// CSS styles for error pages should be added to JenburyFinancial/webroot/css/jenbury.css

// Security Note: Ensure that sensitive details from the $error object or $message
// are NOT displayed here. They should only be logged server-side.
// Example of what NOT to do: echo h($message);
// Example of what NOT to do: Debugger::dump($error);
?>
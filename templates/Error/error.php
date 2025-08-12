<?php
/**
 * Generic Error Template
 *
 * @var \App\View\AppView $this
 * @var \Throwable $error The error object.
 * @var string $message The error message.
 * @var string $url The URL that was requested.
 * @var string $code Error code (if available)
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'jenbury'; // Use the main Jenbury layout
$this->assign('title', 'Problem Encountered'); // Set the page title

// Set default layout variables using set() so they are available in the layout
$this->set('loggedIn', $this->get('loggedIn', false)); // Pass existing value or default to false
$this->set('currentUser', $this->get('currentUser', null)); // Pass existing value or default to null

// Determine a generic heading based on code if possible
$errorCode = $this->get('code', null);
$errorHeading = $errorCode ? 'Problem (' . h($errorCode) . ')' : 'An Unexpected Problem Occurred';

?>
<div class="error-page-container" role="main" aria-labelledby="error-heading">
    <h1 id="error-heading"><?= $errorHeading ?></h1>
    <p>We encountered an unexpected issue while trying to process your request.</p>
    <p>You could try going back to the previous page, or try again later. If the problem continues, please contact support.</p>

    <?php
    // Optionally display Request ID if available (less common for generic errors but possible)
    $requestId = $this->get('request_id');
    if ($requestId):
    ?>
        <p class="request-id">If you need to contact support, please provide this Request ID: <strong><?= h($requestId) ?></strong></p>
    <?php endif; ?>

    <button onclick="history.back();" class="button btn-primary" role="button">
        Go Back
    </button>
</div>

<?php
// CSS styles for error pages should be added to JenburyFinancial/webroot/css/jenbury.css

// Security Note: Avoid displaying sensitive details from $error or $message here.
?>

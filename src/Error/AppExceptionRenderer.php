<?php
namespace App\Error;

use Cake\Error\Renderer\WebExceptionRenderer as ExceptionRenderer;
use Cake\Error\ExceptionRendererInterface;

class AppExceptionRenderer extends ExceptionRenderer implements ExceptionRendererInterface
{
    public function missingController($error)
    {
        // Check the type of exception to decide if it's a 400 or 500 error
    if ($error instanceof \Cake\Error\MissingControllerException) {
        // Redirect to error400 page for missing controller
        return $this->controller->redirect(['prefix' => false, 'controller' => 'Error', 'action' => 'error400']);
    } else {
        // For other exceptions, redirect to error500 page (you can expand this for more specific exceptions)
        return $this->controller->redirect(['prefix' => false, 'controller' => 'Error', 'action' => 'error500']);
    }
    }
}

<?php
namespace PrimPack\Controller;

use Prim\Controller;

/**
 * Errors
 *
 */
class Error extends Controller
{
    /**
     * This method handles the error page that will be shown when a page is not found
     */
    public function handleError($e, $allowedMethods = '')
    {
        if($e == 404) {
            header('HTTP/1.1 404 Not Found');
        } else if ($e == 405) {
            header('HTTP/1.1 405 Method Not Allowed');
            header($allowedMethods);
            $e = 404;
        } else if ($e == 500) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        $this->design("errors/$e", 'PrimPack');
    }

    public function debug($e)
    {
        $this->setTemplate('prim', 'PrimPack');

        $this->design('debug', 'PrimPack', [
            'error' => $e,
            'xdebug' => function_exists('xdebug_get_code_coverage')
        ]);

        exit;
    }
}
<?php
namespace PrimPack\Controller;

use Prim\Controller;

/**
 * Errors
 *
 */
class Error extends Controller
{
    protected function cleanOutput() : bool {
        $xdebugEnabled = function_exists('xdebug_time_index');

        if($xdebugEnabled) {
            xdebug_disable();
        }

        if(ob_get_length() > 0) {
            ob_end_clean();
        }

        return $xdebugEnabled;
    }

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
            $this->cleanOutput();

            header('HTTP/1.1 500 Internal Server Error');
        }

        $this->design("errors/$e", 'PrimPack');
    }

    public function debug($e)
    {
        $xdebugEnabled = $this->cleanOutput();

        $this->setTemplate('prim', 'PrimPack');

        $this->design('debug', 'PrimPack', [
            'error' => $e,
            'xdebug' => $xdebugEnabled
        ]);

        exit;
    }
}
<?php
namespace PrimPack\Controller;

use Prim\Controller;

/**
 * Errors
 *
 */
class Error extends Controller
{
    public  $httpErrors = [
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable'
    ];

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
        $this->cleanOutput();

        header("HTTP/1.1 $e {$this->httpErrors[$e]}");

        if ($e === 405) {
            header($allowedMethods);
            $e = 404;
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
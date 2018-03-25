<?php
namespace PrimPack\Controller;

use Prim\Controller;

/**
 * Errors
 *
 */
class Error extends Controller
{
    protected $messages = [];

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
    public function handleError($code, $allowedMethods = '', $e = null)
    {
        $this->cleanOutput();

        header("HTTP/1.1 $code {$this->httpErrors[$code]}");

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = "From: {$this->options['error_mail_from']}";

        $this->addMessage("HTTP code: $code");

        if($e !== null) {
            $this->addMessage('Type: '.get_class($e));
            $this->addMessage("Message: {$e->getMessage()}");
            $this->addMessage("File: {$e->getFile()}");
            $this->addMessage("Line: {$e->getLine()}");

            // SQL server is down\unreachable
            if(get_class($e) === 'PDOException') {
                $code = 503;
            }

            // The query and params shouldn't be sended by email but logged
            else if(strpos($e->getMessage(), 'PDO') !== false) {
                $PDO = $this->container->getPDO();

                $this->addMessage('Query: ' . nl2br($PDO->lastQuery));
                $this->addMessage('Params: ' . var_export($PDO->lastParams));
            }
        }

        $this->addMessage("Uri: {$_SERVER['REQUEST_URI']}");

        $message = wordwrap(implode("\r\n", $this->messages), 70, "\r\n");

        mail($this->options['error_mail'], 'PHP Error', $message, implode("\r\n", $headers));

        if ($code === 405) {
            header($allowedMethods);
            $code = 404;
        }

        $this->design("errors/$code", 'PrimPack');
    }

    public function addMessage(string $message)
    {
        $this->messages[] = $message;
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
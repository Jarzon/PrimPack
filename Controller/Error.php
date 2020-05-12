<?php
namespace PrimPack\Controller;

use Prim\AbstractController;
use Prim\Container;
use PrimPack\Service\PDO;

class Error extends AbstractController
{
    protected array $messages = [];
    public Container $container;

    public array $httpErrors = [
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable'
    ];

    public function __construct($view, array $options = [], Container $container)
    {
        parent::__construct($view, $options);

        $this->container = $container;
    }

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

        $this->addMessage("HTTP code: $code");

        // SQL server is down\unreachable
        if($e !== null && get_class($e) === 'PDOException') {
            $code = 503;
        }

        if($this->options['debug'] == false && $code !== 404) {
            $this->logError($e);
        }

        if ($code === 405) {
            header('Allow: '. implode(', ', $allowedMethods));
            $code = 404;
        }

        $this->design("errors/$code", 'PrimPack');
    }

    protected function getLine($e) {
        foreach ($e as $i) {
            if(isset($i['file']) && isset($i['line']) && isset($i['class']) && strpos($i['class'], $this->options['project_name']) !== false) {
                return [$i['file'], $i['line']];
            }
        }

        return [];
    }

    public function logError(\Throwable $e)
    {
        $this->addMessage('Date: '.date('Y-m-d H:i:s'));
        $this->addMessage("Uri: {$_SERVER['REQUEST_URI']}");
        $this->addMessage("IP: {$_SERVER['REMOTE_ADDR']}");

        if($e !== null) {
            $this->addMessage('Type: '.get_class($e));
            $this->addMessage("Message: {$e->getMessage()}");
            $this->addMessage("Finale file: {$e->getFile()}");
            $this->addMessage("Finale line: {$e->getLine()}");

            $line = $this->getLine($e->getTrace());

            if(!empty($line)) {
                $this->addMessage("File: {$line[0]}");
                $this->addMessage("Line: {$line[1]}");
            }

            if($e instanceof \PDOException || strpos($e->getMessage(), 'PDO') !== false) {
                $PDO = $this->container->get('pdo');

                if($PDO instanceof PDO) {
                    $this->addMessage('Query: ' . $PDO->lastQuery);
                    $this->addMessage('Params: ' . var_export($PDO->lastParams, true));
                }
            }
        }

        if(isset($_SESSION)) {
            $this->addMessage("Session: " . var_export($_SESSION, true));
        }

        if(isset($_POST)) {
            $this->addMessage("POST: " . var_export($_POST, true));
        }

        $message = implode("\r\n", $this->messages);

        file_put_contents($this->options['root'] . 'data/logs/' . date('Ymd:His') .'_'. strlen($message), $message);
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
            'xdebug' => $xdebugEnabled,
            'container' => $this->container
        ]);

        exit;
    }
}

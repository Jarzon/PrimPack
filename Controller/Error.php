<?php
namespace PrimPack\Controller;

use Prim\AbstractController;
use Prim\Container;
use PrimPack\Service\Logger;
use PrimPack\Service\PDO;

class Error extends AbstractController
{
    public Logger $logger;
    public Container $container;

    public array $httpErrors = [
        401 => 'Unauthorized',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable'
    ];

    public function __construct($view, array $options, Logger $logger, Container $container)
    {
        parent::__construct($view, $options);

        $this->logger = $logger;
        $this->container = $container;
    }

    protected function cleanOutput(): void
    {
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
    }

    /**
     * This method handles the error page that will be shown when a page is not found
     */
    public function handleError($code, $allowedMethods = '', $e = null)
    {
        $this->cleanOutput();

        header("HTTP/1.1 $code {$this->httpErrors[$code]}");

        $this->logger->addMessage("HTTP code: $code");

        // SQL server is down\unreachable
        if($e !== null && get_class($e) === 'PDOException') {
            $code = 503;
        }

        if($this->options['debug'] === false && ((int)floor($code / 100) * 100) === 500) {
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
        $this->logger->addMessage('Date: '.date('Y-m-d H:i:s'));
        $this->logger->addMessage("Uri: {$_SERVER['REQUEST_URI']}");
        $this->logger->addMessage("IP: {$_SERVER['REMOTE_ADDR']}");

        if($e !== null) {
            $this->logger->addMessage('Type: '.get_class($e));
            if(get_class($e) === 'GuzzleHttp\Exception\ClientException') {
                $this->logger->addMessage("Message: {$e->getResponse()->getBody()->getContents()}");
            } else {
                $this->logger->addMessage("Message: {$e->getMessage()}");
            }

            $this->logger->addMessage("Finale file: {$e->getFile()}");
            $this->logger->addMessage("Finale line: {$e->getLine()}");

            $line = $this->getLine($e->getTrace());

            if(!empty($line)) {
                $this->logger->addMessage("File: {$line[0]}");
                $this->logger->addMessage("Line: {$line[1]}");
            }

            if($e instanceof \PDOException || strpos($e->getMessage(), 'PDO') !== false) {
                $PDO = $this->container->get('pdo');

                if($PDO instanceof PDO) {
                    $this->logger->addMessage('Query: ' . $PDO->lastQuery);
                    $this->logger->addMessage('Params: ' . var_export($PDO->lastParams, true));
                }
            }
        }

        if(isset($_SESSION)) {
            $this->logger->addMessage("Session: " . var_export($_SESSION, true));
        }

        if(isset($_POST)) {
            $this->logger->addMessage("POST: " . var_export($_POST, true));
        }

        $this->logger->logMessages();
    }

    public function debug($e)
    {
        $this->cleanOutput();

        $this->setTemplate('prim', 'PrimPack');

        $this->design('debug', 'PrimPack', [
            'error' => $e,
            'xdebug' => function_exists('xdebug_time_index'),
            'container' => $this->container
        ]);

        exit;
    }
}

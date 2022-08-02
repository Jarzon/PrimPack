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
        400 => 'Bad Request',
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
    public function handleError(int $code, $allowedMethods = '', $e = null)
    {
        $this->cleanOutput();

        header("HTTP/1.1 $code {$this->httpErrors[$code]}");

        $this->logger->addMessage("HTTP code: $code");

        if($this->options['debug'] === false) {
            if($code === 500) {
                $this->logError($e);
            }
            else if ($code === 404 && isset($_SESSION['user_id'])) {
                $this->logger->addMessage("Referer: {$_SERVER['HTTP_REFERER']}");
                $this->logError(throw new \Exception("Missing page: {$_SERVER['REQUEST_URI']}"));
            }
            else if ($code === 405) {
                header('Allow: '. implode(', ', $allowedMethods));
                $code = 404;
                if(isset($_SESSION['user_id'])) $this->logError(throw new \Exception("Method not allowed: {$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']}"));
            }
        }

        $this->design("errors/$code", 'PrimPack');
    }

    public function logError(\Throwable $e)
    {
        if($e !== null) {
            if($e instanceof \PDOException || strpos($e->getMessage(), 'PDO') !== false) {
                $PDO = $this->container->get('pdo');

                if($PDO instanceof PDO) {
                    $this->logger->addMessage('Query: ' . $PDO->lastQuery);
                    $this->logger->addMessage('Params: ' . var_export($PDO->lastParams, true));
                }
            }

            if(get_class($e) === GuzzleHttp\Exception\ClientException::class) {
                $this->logger->addMessage("Message: {$e->getResponse()->getBody()->getContents()}");
            } else {
                $this->logger->addMessage("Message: {$e->getMessage()}");
            }
        }

        $this->logger->logError($e);
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

<?php
namespace PrimPack\Controller;

use Prim\AbstractController;
use Prim\Container;
use PrimPack\Service\Logger;
use PrimPack\Service\PDO;
use Throwable;

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
    public function handleError(int $code, array $allowedMethods = [], Throwable $e = null): void
    {
        $this->cleanOutput();

        header("HTTP/1.1 $code {$this->httpErrors[$code]}");

        $this->logger->addMessage("HTTP code: $code");

        if($this->options['debug'] === false && $code === 500) {
            $this->logError($e);
        }
        else if ($this->options['debug'] === false && $code === 404 && isset($_SESSION['user_id']) && $_SERVER['REQUEST_URI'] !== '/favicon.ico') {
            if(isset($_SERVER['HTTP_REFERER'])) {
                $this->logger->addMessage("Referer: {$_SERVER['HTTP_REFERER']}");
            }
            $this->logger->addMessage("Uri: {$_SERVER['REQUEST_URI']}");
            $this->logger->logError(NULL);
        }
        else if ($code === 405) {
            header('Allow: ' . implode(', ', $allowedMethods));
            $code = 404;
            if (isset($_SESSION['user_id'])) {
                $this->logError(throw new \Exception("Method not allowed: {$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']}"));
            }
        }

        $this->design("errors/$code", 'PrimPack');
    }

    public function logError(Throwable $e): void
    {
        if($e instanceof \PDOException || str_contains($e->getMessage(), 'PDO')) {
            $PDO = $this->container->get('pdo');

            if($PDO instanceof PDO) {
                $this->logger->addMessage('Query: ' . $PDO->queries[array_key_last($PDO->queries)][1]);
            }
        }

        /** @phpstan-ignore class.notFound */
        if($e instanceof GuzzleHttp\Exception\ClientException) {
            /** @phpstan-ignore class.notFound */
            $this->logger->addMessage("Message: {$e->getResponse()->getBody()->getContents()}");
        } else {
            $this->logger->addMessage("Message: {$e->getMessage()}");
        }

        $this->logger->logError($e);
    }

    public function debug(Throwable $e): void
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

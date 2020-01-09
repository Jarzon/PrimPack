<?php
namespace PrimPack\Controller;

use Prim\AbstractController;
use Prim\Container;

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
            $this->emailReportError($e);
        }

        if ($code === 405) {
            header('Allow: '. implode(', ', $allowedMethods));
            $code = 404;
        }

        $this->design("errors/$code", 'PrimPack');
    }

    public function emailReportError($e)
    {
        if($e !== null) {
            $this->addMessage('Type: '.get_class($e));
            $this->addMessage("Message: {$e->getMessage()}");
            $this->addMessage("File: {$e->getFile()}");
            $this->addMessage("Line: {$e->getLine()}");

            // The query and params shouldn't be sended by email but logged only
            if(strpos($e->getMessage(), 'PDO') !== false) {
                $PDO = $this->container->get('pdo');

                $this->addMessage('Query: ' . nl2br($PDO->lastQuery));
                $this->addMessage('Params: ' . var_export($PDO->lastParams));
            }
        }

        $this->addMessage("Uri: {$_SERVER['REQUEST_URI']}");

        $this->addMessage("IP: {$_SERVER['REMOTE_ADDR']}");

        $message = wordwrap(implode("\r\n", $this->messages), 70, "\r\n");

        $this->sendEmail($this->options['error_email'], 'PHP Error', $message);
    }

    protected function sendEmail(string $email, string $subject, string $message) {
        $transport = (new \Swift_SmtpTransport($this->options['smtp_url'], $this->options['smtp_port'], $this->options['smtp_secure']))
            ->setUsername($this->options['smtp_email'])
            ->setPassword($this->options['smtp_password']);

        $mailer = new \Swift_Mailer($transport);

        $body = (new \Swift_Message)
            ->setSubject($subject)
            ->setFrom([$this->options['smtp_email'] => "{$this->options['project_name']} Error report"])
            ->setTo($email)
            ->setBody($message);

        return $mailer->send($body);
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
<?php
namespace PrimPack\Service;

class Logger
{
    protected array $options = [];
    protected array $messages = [];

    public function __construct(array $options) {
        $this->options = $options;
    }

    public function addMessage(string $message)
    {
        $this->messages[] = $message;
    }

    public function logMessages()
    {
        $message = implode("\r\n", $this->messages);

        file_put_contents($this->options['root'] . 'data/logs/' . date('Ymd:His') . '_'. strlen($message), $message);
    }

    public function logError(\Throwable $e)
    {
        $this->addMessage('Date: '.date('Y-m-d H:i:s'));
        $this->addMessage("Uri: {$_SERVER['REQUEST_URI']}");
        $this->addMessage("IP: {$_SERVER['REMOTE_ADDR']}");

        if($e !== null) {
            $this->addMessage('Type: '.get_class($e));

            $this->addMessage("Finale file: {$e->getFile()}");
            $this->addMessage("Finale line: {$e->getLine()}");

            $line = $this->getLine($e->getTrace());

            if(!empty($line)) {
                $this->addMessage("File: {$line[0]}");
                $this->addMessage("Line: {$line[1]}");
            }
        }

        if(isset($_SESSION)) {
            $this->addMessage("Session: " . var_export($_SESSION, true));
        }

        if(isset($_POST)) {
            $this->addMessage("POST: " . var_export($_POST, true));
        }

        $this->logMessages();
    }

    protected function getLine($e)
    {
        foreach ($e as $i) {
            if(isset($i['file']) && isset($i['line']) && isset($i['class']) && strpos($i['class'], $this->options['project_name']) !== false) {
                return [$i['file'], $i['line']];
            }
        }

        return [];
    }
}

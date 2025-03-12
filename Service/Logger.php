<?php
namespace PrimPack\Service;

class Logger
{
    protected string $type = 'err';
    protected array $messages = [];

    public function __construct(protected array $options) {}

    public function type(string $type): Logger
    {
        $this->type = $type;

        return $this;
    }

    public function addMessage(string $message): Logger
    {
        $this->messages[] = $message;

        return $this;
    }

    public function logMessages(): void
    {
        $message = implode("\r\n", $this->messages);

        file_put_contents($this->options['root'] . 'data/logs/' . $this->type. '_' . date('Ymd:His') . '_'. strlen($message), $message);
    }

    public function logError(\Throwable|null $e = null): void
    {
        $this->addMessage('Date: '.date('Y-m-d H:i:s'));
        if(isset($_SERVER['REQUEST_URI'])) $this->addMessage("Uri: {$_SERVER['REQUEST_URI']}");
        if(isset($_SERVER['REMOTE_ADDR'])) $this->addMessage("IP: {$_SERVER['REMOTE_ADDR']}");

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

        if(!empty($_SESSION)) {
            $this->addMessage("Session: " . var_export($_SESSION, true));
        }

        if(!empty($_POST)) {
            $this->addMessage("POST: " . var_export($_POST, true));
        }

        $this->logMessages();
    }

    protected function getLine($e): array
    {
        foreach ($e as $i) {
            if(isset($i['file']) && isset($i['line']) && isset($i['class']) && strpos($i['class'], $this->options['project_name']) !== false) {
                return [$i['file'], $i['line']];
            }
        }

        return [];
    }
}

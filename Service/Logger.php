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
}

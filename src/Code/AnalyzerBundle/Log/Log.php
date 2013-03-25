<?php

namespace Code\AnalyzerBundle\Log;

class Log
{
    const TYPE_COMMAND = 'command';
    const TYPE_OUTPUT = 'output';
    const TYPE_ERROR = 'error';
    const TYPE_PROCESS = 'process';

    /**
     * @var callable
     */
    private $logCallback = null;

    /**
     * @var array
     */
    private $items = array();

    /**
     * @param callable $logCallback
     */
    public function __construct(callable $logCallback = null)
    {
        $this->logCallback = $logCallback;
    }

    /**
     * Add command
     *
     * @param string $command
     */
    public function addCommand($command)
    {
        if (trim($command)) {
            $this->addItem(self::TYPE_COMMAND, $command);
        }
    }

    /**
     * Add output
     *
     * @param string $output
     */
    public function addOutput($output)
    {
        if (trim($output)) {
            $this->addItem(self::TYPE_OUTPUT, $output);
        }
    }

    /**
     * Add error
     *
     * @param string $error
     */
    public function addError($error)
    {
        if (trim($error)) {
            $this->addItem(self::TYPE_ERROR, $error);
        }
    }

    /**
     * Add post process
     *
     * @param string $postProcess
     */
    public function addProcess($postProcess)
    {
        if (trim($postProcess)) {
            $this->addItem(self::TYPE_PROCESS, $postProcess);
        }
    }

    /**
     * Return log items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add item
     *
     * @param string $type
     * @param string $line
     */
    private function addItem($type, $line)
    {
        $this->items[] = array(
            'type' => $type,
            'line' => $line
        );

        if ($this->logCallback) {
            call_user_func($this->logCallback, $type, $line);
        }
    }
}
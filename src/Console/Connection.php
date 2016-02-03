<?php

namespace Dan\Console;

use Dan\Contracts\ConnectionContract;

class Connection implements ConnectionContract
{
    /**
     * @var \Symfony\Component\Console\Input\ArrayInput
     */
    public $input;

    /**
     * @var \Dan\Console\OutputStyle
     */
    public $output;

    /**
     * @var resource
     */
    protected $stream;

    /**
     * Connection constructor.
     */
    public function __construct()
    {
        $this->input = dan('input');
        $this->output = new OutputStyle($this->input, dan('output'));
    }

    /**
     * The name of the connection.
     *
     * @return string
     */
    public function getName()
    {
        return 'console';
    }

    /**
     * Gets the stream resource for the connection.
     *
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Connects to the connection.
     *
     * @return void
     */
    public function connect()
    {
        if (!is_null($this->stream)) {
            return;
        }

        $this->stream = fopen('php://stdin', 'r');
        stream_set_blocking($this->stream, 0);
    }

    /**
     * Disconnects from the connection.
     *
     * @return bool
     */
    public function disconnect() : bool
    {
        // TODO: Implement disconnect() method.
    }

    /**
     * Reads the resource.
     *
     * @param resource $resource
     *
     * @return void
     */
    public function read($resource)
    {
        $message = trim(fgets($resource));

        $this->write($message);
    }

    /**
     * Writes to the resource.
     *
     * @return void
     */
    public function write($line)
    {
        $this->output->writeln($line);
    }
}
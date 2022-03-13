<?php

namespace WP_CLI_Secure;

interface CommandInterface {

    /**
     * Get command name
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Executes the command
     *
     * @param $arguments
     * @param $options
     *
     */
    public function __invoke($arguments, $options);

    /**
     * Get the positional and associative arguments a command accepts.
     *
     * @return array
     */
    public function getSynopsis() : array;

    /**
     * Get the command description.
     *
     * @return string
     */
    public function getDescription() : string;
}

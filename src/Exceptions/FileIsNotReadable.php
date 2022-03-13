<?php

namespace WP_CLI_Secure\Exceptions;

use Exception;

class FileIsNotReadable extends Exception {
    /**
     * @var string
     */
    protected $message = 'File does not have a correct permissions to read its content';
}
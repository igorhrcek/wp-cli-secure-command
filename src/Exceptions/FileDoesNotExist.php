<?php

namespace WP_CLI_Secure\Exceptions;

use Exception;

class FileDoesNotExist extends Exception {
    /**
     * @var string
     */
    protected $message = 'File does not exist';
}
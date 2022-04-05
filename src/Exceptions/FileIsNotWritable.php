<?php

namespace WP_CLI_Secure\Exceptions;

use Exception;

class FileIsNotWritable extends Exception
{
    /**
     * @var string
     */
    protected $message = 'File does not have a correct permissions for writing';
}

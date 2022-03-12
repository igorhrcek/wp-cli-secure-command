<?php

namespace WpCliFileManager\Exceptions;

use Exception;

class FileIsNotWritable extends Exception {
    /**
     * @var string
     */
    protected $message = 'File does not have a correct permissions for writing';
}
<?php

namespace WpCliFileManager\Exceptions;

use Exception;

class RuleAlreadyExist extends Exception {
    /**
     * @var string
     */
    protected $message = 'The rule already exist in the file';
}
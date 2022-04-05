<?php

namespace WP_CLI_Secure\Exceptions;

use Exception;

class RuleAlreadyExist extends Exception
{
    /**
     * @var string
     */
    protected $message = 'The rule already exists in the file';
}

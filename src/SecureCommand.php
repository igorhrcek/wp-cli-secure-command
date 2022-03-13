<?php

namespace WP_CLI_Secure;

use WP_CLI\Utils;

class SecureCommand extends AbstractCommand {
    protected function getCommandName(): string
    {
        return 'secure';
    }

    public function __invoke($arguments, $options)
    {
        \WP_CLI::success( 'This is output for something.' );
    }

    public function getDescription(): string
    {
        return 'Secure your WordPress installation by applying security rule blocks in your .htaccess or nginx.conf files.';
    }
}
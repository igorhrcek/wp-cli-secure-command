<?php

namespace WP_CLI_Secure;

use WP_CLI;
use WP_CLI\Process;
use WP_CLI\Utils;
use WP_CLI_Command;

class Blah extends WP_CLI_Command {
    public function flush($args, $assoc_args) {
        WP_CLI::success( 'Rewrite rules flushed.' );
    }
}
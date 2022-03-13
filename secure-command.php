<?php

namespace WP_CLI_Secure;

if (!class_exists('WP_CLI')) {
    return;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

\WP_CLI::add_command('secure', Blah::class);

//function registerCommand(CommandInterface $command) {
//    if (!defined('WP_CLI') || !WP_CLI || !class_exists('\WP_CLI')) {
//        return;
//    }
//
//    \WP_CLI::add_command($command->getName(), $command, [
//        'shortdesc' => $command->getDescription(),
//        'synopsis'  => $command->getSynopsis()
//    ]);
//}
//
//registerCommand(new SecureCommand());
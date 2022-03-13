<?php

namespace WP_CLI_Secure;

if (!class_exists('WP_CLI')) {
    return;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

\WP_CLI::add_command('secure', SecureCommand::class);

//wp secure disable-directory-browsing
//wp secure block-php-execution-in-uploads
//wp secure block-php-execution-in-themes
//wp secure block-php-execution-in-plugins
//wp secure block-php-execution-in-wp-includes
//wp secure block-access-to-htaccess
//wp secure block-access-to-sensitive-files
//wp secure block-access-to-sensitive-directories
//wp secure block-author-scanning
//wp secure block-access-to-xml-rpc

//wp secure add --rules=disable-directory-browsing,block-php-execution-in-uploads --output --path
//wp secure remove --rules=disable-directory-browsing,block-php-execution-in-uploads
//wp secure flush
//wp secure block-access-to-htaccess --revert
//wp secure block-access-to-htaccess --path=/path/to/htaccess --output
<?php

namespace WP_CLI_Secure\SubCommands;

class BlockPhpExecutionInWpIncludes extends SubCommand {
    public string $ruleTemplate = 'block_php_execution_in_wp_includes';
    public string $ruleName = 'BLOCK PHP EXECUTION IN UPLOADS';
    public string $successMessage = 'Block Execution In wp-includes Directory rule has been deployed.';
}
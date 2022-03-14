<?php

namespace WP_CLI_Secure\SubCommands;

class BlockPhpExecutionInPlugins extends SubCommand {
    public string $ruleTemplate = 'block_php_execution_in_plugins';
    public string $ruleName = 'BLOCK PHP EXECUTION IN PLUGINS';
    public string $successMessage = 'Block Execution In Plugins Directory rule has been deployed.';
    public string $removalMessage= 'Block Execution In Plugins Directory rule has been removed.';
}
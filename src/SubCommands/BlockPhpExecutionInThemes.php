<?php

namespace WP_CLI_Secure\SubCommands;

class BlockPhpExecutionInThemes extends SubCommand {
    public string $ruleTemplate = 'block_php_execution_in_themes';
    public string $ruleName = 'BLOCK PHP EXECUTION IN THEMES';
    public string $successMessage = 'Block Execution In Themes Directory rule has been deployed.';
    public string $removalMessage= 'Block Execution In Themes Directory rule has been removed.';
}
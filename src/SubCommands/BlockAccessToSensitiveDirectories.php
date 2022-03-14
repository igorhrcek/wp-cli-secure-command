<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToSensitiveDirectories extends SubCommand {
    public string $ruleTemplate = 'block_access_to_sensitive_directories';
    public string $ruleName = 'BLOCK ACCESS TO SENSITIVE DIRECTORIES';
    public string $successMessage = 'Block Access to Sensitive Directories rule has been deployed.';
    public string $removalMessage= 'Block Access to Sensitive Directories rule has been removed.';
}
<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToSensitiveFiles extends SubCommand {
    public string $ruleTemplate = 'block_access_to_sensitive_files';
    public string $ruleName = 'BLOCK ACCESS TO SENSITIVE FILES';
    public string $successMessage = 'Block Access to Sensitive Files rule has been deployed.';
}
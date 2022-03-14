<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToHtaccess extends SubCommand {
    public string $ruleTemplate = 'block_access_to_htaccess';
    public string $ruleName = 'BLOCK ACCESS TO HTACCESS';
    public string $successMessage = 'Block Access to .htaccess rule has been deployed.';
    public string $removalMessage= 'Block Access to .htaccess rule has been removed.';
}
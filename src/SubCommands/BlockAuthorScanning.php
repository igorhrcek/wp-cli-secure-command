<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAuthorScanning extends SubCommand
{
    public string $ruleTemplate = 'block_author_scanning';
    public string $ruleName = 'BLOCK AUTHOR SCANNING';
    public string $successMessage = 'Block Author Scanning rule has been deployed.';
    public string $removalMessage = 'Block Author Scanning rule has been removed.';
}

<?php

namespace WP_CLI_Secure\SubCommands;

class DisableDirectoryBrowsing extends SubCommand
{
    public string $ruleTemplate = 'disable_directory_browsing';
    public string $ruleName = 'DISABLE DIRECTORY BROWSING';
    public string $successMessage = 'Disable Directory Browsing rule has been deployed.';
    public string $removalMessage = 'Disable Directory Browsing rule has been removed.';
}

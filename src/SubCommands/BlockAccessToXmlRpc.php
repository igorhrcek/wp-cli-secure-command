<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToXmlRpc extends SubCommand {
    public string $ruleTemplate = 'block_access_to_xmlrpc';
    public string $ruleName = 'BLOCK ACCESS TO XMLRPC';
    public string $successMessage = 'Block Access to xmlrpc rule has been deployed.';
}
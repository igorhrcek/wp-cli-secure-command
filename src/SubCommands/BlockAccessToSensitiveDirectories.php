<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToSensitiveDirectories extends SubCommand {
    public string $ruleTemplate = 'block_access_to_sensitive_directories';
    public string $ruleName = 'BLOCK ACCESS TO SENSITIVE DIRECTORIES';
    public string $successMessage = 'Block Access to Sensitive Directories rule has been deployed.';
    public string $removalMessage= 'Block Access to Sensitive Directories rule has been removed.';

    public function getTemplateVars() {
        $directories = isset( $this->commandArguments['directories'] ) ? $this->commandArguments['directories'] : 'git,svn,vendors,cache';
        if ( ! empty( $directories ) ) {
            $directories = explode( ',', $directories );
            $directories = array_map( 'trim', $directories );
            $directories_array = [];

            return [
                [ 'directories' => implode( '|', array_map( 'preg_quote', $directories ) ) ]
            ];
        }
        return [];
    }
}
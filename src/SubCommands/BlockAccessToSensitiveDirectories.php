<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToSensitiveDirectories extends SubCommand {
    public string $ruleTemplate = 'block_access_to_sensitive_directories';
    public string $ruleName = 'BLOCK ACCESS TO SENSITIVE DIRECTORIES';
    public string $successMessage = 'Block Access to Sensitive Directories rule has been deployed.';
    public string $removalMessage= 'Block Access to Sensitive Directories rule has been removed.';
  
    /**
     * @var string Default directories that we are going to protect
     */
    private string $sensitiveDirectories = '.git,svn,vendors,cache';

    /**
     * @return array
     */
    public function getTemplateVars() {
        $directories = $this->commandArguments['directories'] ?? $this->sensitiveDirectories;
        if ( ! empty( $directories ) ) {
            $directories = explode( ',', $directories );
            $directories = array_map( 'trim', $directories );

          return [
                [ 'directories' => implode( '|', array_map( 'preg_quote', $directories ) ) ]
            ];
        }
      
        return [];
    }
}
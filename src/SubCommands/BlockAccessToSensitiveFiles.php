<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToSensitiveFiles extends SubCommand {
    public string $ruleTemplate = 'block_access_to_sensitive_files';
    public string $ruleName = 'BLOCK ACCESS TO SENSITIVE FILES';
    public string $successMessage = 'Block Access to Sensitive Files rule has been deployed.';
    public string $removalMessage= 'Block Access to Sensitive Files rule has been removed.';

    public function getTemplateVars() {
        $files = isset( $this->commandArguments['files'] ) ? $this->commandArguments['files'] : 'readme.html, readme.txt, wp-config.php, wp-admin/install.php';
        if ( ! empty( $files ) ) {
            $files = explode( ',', $files );
            $files = array_map( 'trim', $files );
            $files_array = [];

            foreach ( $files as $key => $value ) {
                $files_array[] = [ 'file' => $value ];
            }
            
            return $files_array;
        }
        return [];
    }
}
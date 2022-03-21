<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToSensitiveFiles extends SubCommand {
    public string $ruleTemplate = 'block_access_to_sensitive_files';
    public string $ruleName = 'BLOCK ACCESS TO SENSITIVE FILES';
    public string $successMessage = 'Block Access to Sensitive Files rule has been deployed.';
    public string $removalMessage= 'Block Access to Sensitive Files rule has been removed.';

    public function getTemplateVars() {
        $files = isset( $this->commandArguments['files'] ) ? $this->commandArguments['files'] : 'readme.html,readme.txt,wp-config.php,nginx.conf,/wp-admin/install.php,/wp-admin/upgrade.php';
        if ( ! empty( $files ) ) {
            $files = explode( ',', $files );
            $files = array_map( 'trim', $files );
            $files_array = [];

            foreach ( $files as $key => $value ) {
                if ( preg_match( '/.+\/.+/', $value ) ) {
                    $file_with_directory = $this->setRuleContent( false, 'block_access_to_sensitive_files_with_directories' );
                    if ( isset( $this->commandArguments['server'] ) && $this->commandArguments['server'] === 'nginx' ) {
                        $file = $value;
                    } else {
                        $file = preg_quote( ltrim( $value, '/' ) );
                    }
                    $files_array[] = [ $file => $file_with_directory ];
                } else {
                    $files_array[] = [ 'file' => isset( $this->commandArguments['server'] ) && $this->commandArguments['server'] === 'nginx' ? preg_quote( $value ) : $value ];
                }
            }
            
            return $files_array;
        }
        return [];
    }
}
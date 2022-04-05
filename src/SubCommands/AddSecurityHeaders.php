<?php

namespace WP_CLI_Secure\SubCommands;

class AddSecurityHeaders extends SubCommand {
    public string $ruleTemplate = 'add_security_headers';
    public string $ruleName = 'SECURITY HEADERS';
    public string $successMessage = 'Add Security Headers rule has been deployed.';
    public string $removalMessage= 'Add Security Headers rule has been removed.';

    public function getTemplateVars() : array {

        $default_headers = [
            'Strict-Transport-Security' => '"max-age=63072000; includeSubDomains; preload"',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN'
        ];

        $headers = $this->commandArguments['headers'] ?? array_keys($default_headers);
        if ( ! empty( $headers ) ) {
            if ( is_string( $headers ) ) {
                $headers = explode( ',', $headers );
            }
            $headers = array_map( 'trim', $headers );

            foreach ( $headers as $h ) {
                $header = '';
                $value = '';
                foreach ( $default_headers as $key => $v ) {
                    if ( strtolower( $key ) === strtolower( $h ) ) {
                        $header = $key;
                        $value  = $v;
                    }
                }
                if ( empty( $header ) ) {
                    continue;
                }
                $headers_array[] =
                    [
                        'header' => $header,
                        'value' => $value
                    ];
            }
            return $headers_array;
        }

        return [];
    }
}
<?php

namespace WP_CLI_Secure\SubCommands;

class SecurityHeaders extends SubCommand {
  public string $ruleTemplate = 'security_headers';
  public string $ruleName = 'SECURITY HEADERS';
  public string $successMessage = 'Security headers have been deployed.';
  public string $removalMessage= 'Security headers have been removed.';

  // Security headers we are scanning for
  public array $SecurityHeadersWhitelist = [
    'x-content-type-options',
    'x-frame-options',
    'x-xss-protection',
    'Strict-Transport-Security',
    'referrer-policy',
  ];

  // Scan site for security headers
  public function ScanHeaders( $format = 'plain' ){

    // Get site headers
    $headers = get_headers( home_url(), true );

    // Returns the security headers we are looking for
    $headers_found = array_intersect_key( $headers, array_flip( $security_headers_whitelist ) );

    // Format our array into a table for --scan
    if( $table === 'table' ) {
      $items = ['Header', "Value"];
      return WP_CLI\Utils\format_items( 'table', $items, $headers_found );
    }

    // else: return the array
    return $headers_found;
  }

  public function getTemplateVars() {
      $user_headers = isset( $this->commandArguments['headers'] ) ? $this->commandArguments['headers'] : 'x-content-type-options,x-frame-options,x-xss-protection,Strict-Transport-Security,hsts,referrer-policy';

      //$headers = $this->GetHeaders();

      if ( ! empty( $user_headers ) ) {
          $user_headers = explode( ',', $user_headers );
          $user_headers = array_map( 'trim', $user_headers );
          return [
              'header' => $user_headers,

          ];
      }
      return [];
  }

  // Return a list of missing security headers
  public function GetHeaders(){

    $headers = ScanHeaders( 'plain' );

    // Security headers we would like to set
    $security_headers = [
      'x-content-type-options' => 'nosniff',
      'x-frame-options' => 'SAMEORIGIN',
      'x-xss-protection' => '1; mode=block',
      'Strict-Transport-Security' => 'max-age=63072000; includeSubdomains; preload',
      'referrer-policy' => 'strict-origin-when-cross-origin',
    ];

//

//
    // List the missing headers we can implement
    $new_headers = array_diff( $security_headers, $headers );
    return $new_headers;
  }
}

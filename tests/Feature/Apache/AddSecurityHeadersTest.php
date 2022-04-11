<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\AddSecurityHeaders;

class AddSecurityHeadersTest extends BaseTestCase {
    public function setUp(): void {
        parent::setUp();

        $command = new AddSecurityHeaders($this->apacheAssocArgs);
        $command->output();
    }

    public function testItWillContainAllHeadersOnApache() : void {
        $response = $this->apacheHttpClient->get('', ['http_errors' => false]);

        $this->assertNotEmpty($response->getHeaderLine( 'Strict-Transport-Security' ));
        $this->assertNotEmpty($response->getHeaderLine( 'Referrer-Policy' ));
        $this->assertNotEmpty($response->getHeaderLine( 'x-content-type-options' ));
        $this->assertNotEmpty($response->getHeaderLine( 'X-Frame-Options' ));
    }
}
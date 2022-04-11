<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockAccessToHtaccess;

class BlockAccessToHtaccessTest extends BaseTestCase {
    public function setUp(): void{
        parent::setUp();

        $command = new BlockAccessToHtaccess($this->apacheAssocArgs);
        $command->output();
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingApacheConfigFile() : void {
        $response = $this->apacheHttpClient->get('.htaccess', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
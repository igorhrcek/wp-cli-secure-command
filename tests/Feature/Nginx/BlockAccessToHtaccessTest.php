<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockAccessToHtaccess;

class BlockAccessToHtaccessTest extends BaseTestCase {
    public function setUp(): void{
        parent::setUp();

        $command = new BlockAccessToHtaccess($this->nginxAssocArgs);
        $command->output();

        $command = new BlockAccessToHtaccess($this->apacheAssocArgs);
        $command->output();

        exec('cd ' . getcwd() . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . '&& ddev exec nginx -s reload');
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingNginxConfigFile() : void {
        $response = $this->nginxHttpClient->get('nginx.conf', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingApacheConfigFile() : void {
        $response = $this->apacheHttpClient->get('.htaccess', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
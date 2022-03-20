<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockAccessToXmlRpc;

class BlockAccessToXmlRpcTest extends BaseTestCase {
    public function setUp(): void {
        parent::setUp();

        $command = new BlockAccessToXmlRpc($this->nginxAssocArgs);
        $command->output();

        $command = new BlockAccessToXmlRpc($this->apacheAssocArgs);
        $command->output();

        exec('cd ' . getcwd() . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . '&& ddev exec nginx -s reload');
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingXmlRpcFile() : void {
        $response = $this->nginxHttpClient->get('xmlrpc.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingXmlRpcFile() : void {
        $response = $this->apacheHttpClient->get('xmlrpc.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
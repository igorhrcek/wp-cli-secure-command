<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockAccessToSensitiveDirectories;

class BlockAccessToSensitiveDirectoriesTest extends BaseTestCase {
    public function setUp(): void{
        parent::setUp();

        $command = new BlockAccessToSensitiveDirectories($this->nginxAssocArgs);
        $command->output();

        $command = new BlockAccessToSensitiveDirectories($this->apacheAssocArgs);
        $command->output();

        exec('cd ' . getcwd() . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . '&& ddev exec nginx -s reload');
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingGitDirectory() : void {
        $response = $this->nginxHttpClient->get('.git', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingSvnDirectory() : void {
        $response = $this->nginxHttpClient->get('svn', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingCacheDirectory() : void {
        $response = $this->nginxHttpClient->get('cache', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingGitDirectory() : void {
        $response = $this->apacheHttpClient->get('.git', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingSvnDirectory() : void {
        $response = $this->apacheHttpClient->get('svn', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingCacheDirectory() : void {
        $response = $this->apacheHttpClient->get('cache', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

}
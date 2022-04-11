<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockAccessToSensitiveFiles;

class BlockAccessToSensitiveFilesTest extends BaseTestCase {
    public function setUp(): void {
        parent::setUp();

        $command = new BlockAccessToSensitiveFiles($this->nginxAssocArgs);
        $command->output();

        $command = new BlockAccessToSensitiveFiles($this->apacheAssocArgs);
        $command->output();

        exec('cd ' . getcwd() . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . '&& ddev exec nginx -s reload');
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingReadmeFiles() : void {
        $response = $this->nginxHttpClient->get('readme.html', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingWpConfigFile() : void {
        $response = $this->nginxHttpClient->get('wp-config.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingWpInstallFile() : void {
        $response = $this->nginxHttpClient->get('wp-admin/install.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingWpUpgradeFile() : void {
        $response = $this->nginxHttpClient->get('wp-admin/upgrade.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingReadmeFiles() : void {
        $response = $this->apacheHttpClient->get('readme.html', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingWpConfigFile() : void {
        $response = $this->apacheHttpClient->get('wp-config.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingWpInstallFile() : void {
        $response = $this->apacheHttpClient->get('wp-admin/install.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingWpUpgradeFile() : void {
        $response = $this->apacheHttpClient->get('wp-admin/upgrade.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
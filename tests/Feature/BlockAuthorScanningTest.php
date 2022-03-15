<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockAuthorScanning;

class BlockAuthorScanningTest extends BaseTestCase {
    public function setUp(): void {
        parent::setUp();

        $command = new BlockAuthorScanning($this->apacheAssocArgs);
        $command->output();

        exec('cd ' . dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . '&& ddev exec nginx -s reload');
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingPhpFilesInWpIncludesDirectory() : void {
        $response = $this->apacheHttpClient->get('/?author=12', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
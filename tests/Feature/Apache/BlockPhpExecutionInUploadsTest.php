<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInUploads;

class BlockPhpExecutionInUploadsTest extends BaseTestCase {
    public function setUp(): void {
        parent::setUp();

        $command = new BlockPhpExecutionInUploads($this->nginxAssocArgs);
        $command->output();

        $command = new BlockPhpExecutionInUploads($this->apacheAssocArgs);
        $command->output();

        exec('cd ' . getcwd() . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . '&& ddev exec nginx -s reload');
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingPhpFilesInUploadsDirectory() : void {
        $response = $this->nginxHttpClient->get('wp-content/uploads/index.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingPhpFilesInUploadsDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-content/uploads/index.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
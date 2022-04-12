<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInPlugins;

class BlockPhpExecutionInPluginsTest extends BaseTestCase {
    public function setUp(): void {
        parent::setUp();

        $command = new BlockPhpExecutionInPlugins($this->nginxAssocArgs);
        $command->output();

        $command = new BlockPhpExecutionInPlugins($this->apacheAssocArgs);
        $command->output();

        exec('cd ' . getcwd() . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . '&& ddev exec nginx -s reload');
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingPhpFilesInPlugins() : void {
        $response = $this->nginxHttpClient->get('wp-content/plugins/hello.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingPhpFilesInPlugins() : void {
        $response = $this->apacheHttpClient->get('wp-content/plugins/hello.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
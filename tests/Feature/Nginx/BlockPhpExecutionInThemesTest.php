<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInThemes;

class BlockPhpExecutionInThemesTest extends BaseTestCase {
    public function setUp(): void {
        parent::setUp();

        $command = new BlockPhpExecutionInThemes($this->nginxAssocArgs);
        $command->output();

        $command = new BlockPhpExecutionInThemes($this->apacheAssocArgs);
        $command->output();

        exec('cd ' . getcwd() . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . '&& ddev exec nginx -s reload');
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingPhpFilesInThemesDirectory() : void {
        $response = $this->nginxHttpClient->get('wp-content/themes/index.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingPhpFilesInThemesDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-content/themes/index.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
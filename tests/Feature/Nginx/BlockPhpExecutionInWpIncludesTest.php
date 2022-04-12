<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInWpIncludes;

class BlockPhpExecutionInWpIncludesTest extends BaseTestCase {
    public function setUp(): void {
        parent::setUp();

        $command = new BlockPhpExecutionInWpIncludes($this->nginxAssocArgs);
        $command->output();

        $command = new BlockPhpExecutionInWpIncludes($this->apacheAssocArgs);
        $command->output();

        exec('cd ' . getcwd() . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . '&& ddev exec nginx -s reload');
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingPhpFilesInWpIncludesDirectory() : void {
        $response = $this->nginxHttpClient->get('wp-includes', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingPhpFilesInWpIncludesThemeCompatDirectory() : void {
        $response = $this->nginxHttpClient->get('wp-includes/theme-compat', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnNginxWhenAccessingPhpFilesInWpAdminIncludesDirectory() : void {
        $response = $this->nginxHttpClient->get('wp-admin/includes', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingPhpFilesInWpIncludesDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-includes', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingPhpFilesInWpIncludesThemeCompatDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-includes/theme-compat', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403OnApacheWhenAccessingPhpFilesInWpAdminIncludesDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-admin/includes', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
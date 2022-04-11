<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use WP_CLI_Secure\SubCommands\AddSecurityHeaders;
use WP_CLI_Secure\SubCommands\BlockAccessToHtaccess;
use WP_CLI_Secure\SubCommands\BlockAccessToSensitiveDirectories;
use WP_CLI_Secure\SubCommands\BlockAccessToSensitiveFiles;
use WP_CLI_Secure\SubCommands\BlockAccessToXmlRpc;
use WP_CLI_Secure\SubCommands\BlockAuthorScanning;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInPlugins;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInThemes;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInUploads;
use WP_CLI_Secure\SubCommands\BlockPhpExecutionInWpIncludes;

class SecurityRulesApacheTest extends BaseTestCase {
    public function setUp(): void {
        parent::setUp();

//        $command = new AddSecurityHeaders($this->apacheAssocArgs);
//        $command->output();

        $command = new BlockAccessToHtaccess($this->apacheAssocArgs);
        $command->output();

        $command = new BlockAccessToSensitiveDirectories($this->apacheAssocArgs);
        $command->output();

        $command = new BlockAccessToSensitiveFiles($this->apacheAssocArgs);
        $command->output();

        $command = new BlockAccessToXmlRpc($this->apacheAssocArgs);
        $command->output();

        $command = new BlockAuthorScanning($this->apacheAssocArgs);
        $command->output();

        $command = new BlockPhpExecutionInPlugins($this->apacheAssocArgs);
        $command->output();

        $command = new BlockPhpExecutionInThemes($this->apacheAssocArgs);
        $command->output();

        $command = new BlockPhpExecutionInUploads($this->apacheAssocArgs);
        $command->output();

        $command = new BlockPhpExecutionInWpIncludes($this->apacheAssocArgs);
        $command->output();
    }

    /**
     * @group SecurityHeaders
     */
//    public function testItWillContainAllHeadersOnApache() : void {
//        $response = $this->apacheHttpClient->get('', ['http_errors' => false]);
//
//        $this->assertNotEmpty($response->getHeaderLine('Strict-Transport-Security'));
//        $this->assertNotEmpty($response->getHeaderLine('Referrer-Policy'));
//        $this->assertNotEmpty($response->getHeaderLine('x-content-type-options'));
//        $this->assertNotEmpty($response->getHeaderLine('X-Frame-Options'));
//    }

    /**
     * @group Htaccess
     */
    public function testItWillReturnHttp403WhenAccessingApacheConfigFile() : void {
        $response = $this->apacheHttpClient->get('.htaccess', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group SensitiveDirectories
     */
    public function testItWillReturnHttp403WhenAccessingGitDirectory() : void {
        $response = $this->apacheHttpClient->get('.git', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group SensitiveDirectories
     */
    public function testItWillReturnHttp403WhenAccessingSvnDirectory() : void {
        $response = $this->apacheHttpClient->get('svn', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group SensitiveDirectories
     */
    public function testItWillReturnHttp403WhenAccessingCacheDirectory() : void {
        $response = $this->apacheHttpClient->get('cache', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group SensitiveFiles
     */
    public function testItWillReturnHttp403WhenAccessingReadmeFiles() : void {
        $response = $this->apacheHttpClient->get('readme.html', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group SensitiveFiles
     */
    public function testItWillReturnHttp403WhenAccessingWpConfigFile() : void {
        $response = $this->apacheHttpClient->get('wp-config.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group SensitiveFiles
     */
    public function testItWillReturnHttp403WhenAccessingWpInstallFile() : void {
        $response = $this->apacheHttpClient->get('wp-admin/install.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group SensitiveFiles
     */
    public function testItWillReturnHttp403WhenAccessingWpUpgradeFile() : void {
        $response = $this->apacheHttpClient->get('wp-admin/upgrade.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group XmlRpc
     */
    public function testItWillReturnHttp403WhenAccessingXmlRpcFile() : void {
        $response = $this->apacheHttpClient->get('xmlrpc.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group BlockAuthorScanning
     */
    public function testItWillReturn403WhenTryingToScanForAuthors() : void {
        $response = $this->apacheHttpClient->get('/?author=12', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group PhpInPlugins
     */
    public function testItWillReturnHttp403WhenAccessingPhpFilesInPlugins() : void {
        $response = $this->apacheHttpClient->get('wp-content/plugins/hello.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group PhpInThemes
     */
    public function testItWillReturnHttp403WhenAccessingPhpFilesInThemesDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-content/themes/index.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group PhpInUploads
     */
    public function testItWillReturnHttp403WhenAccessingPhpFilesInUploadsDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-content/uploads/index.php', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @group PhpInWpIncludes
     */
    public function testItWillReturnHttp403WhenAccessingPhpFilesInWpIncludesDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-includes', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403WhenAccessingPhpFilesInWpIncludesThemeCompatDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-includes/theme-compat', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testItWillReturnHttp403WhenAccessingPhpFilesInWpAdminIncludesDirectory() : void {
        $response = $this->apacheHttpClient->get('wp-admin/includes', ['http_errors' => false]);
        $this->assertEquals(403, $response->getStatusCode());
    }
}
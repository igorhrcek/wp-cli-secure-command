<?php

namespace Tests;

use GuzzleHttp\Client;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use WP_CLI_Secure\FileManager;

class BaseTestCase extends TestCase {
    /**
     * @var vfsStreamDirectory
     */
    public vfsStreamDirectory $root;

    /**
     * @var array|string[] Params for testing rules on nginx server
     */
    public array $nginxAssocArgs;

    /**
     * @var array|string[] Params for testing rules on apache server
     */
    public array $apacheAssocArgs;

    /**
     * @var Client
     */
    public Client $nginxHttpClient;

    /**
     * @var Client
     */
    public Client $apacheHttpClient;

    /**
     * @var array Files required to be created for feature tests
     */
    public array $testFiles = [
        '.htaccess',
        'nginx.conf',
        'readme.html',
        'readme.txt',
        'error_log'
    ];

    /**
     * @var array Directories required to be created for feature tests
     */
    public array $testDirectories = [
        '.git',
        'svn',
        'vendors',
        'cache'
    ];

    public function setUp(): void {
        $this->root = vfsStream::setup('assets', 0777);
        $this->root->chown(vfsStream::getCurrentUser());
        $this->root->chgrp(vfsStream::getCurrentGroup());

        /**
         * Setup fo WP CLI integration testing
         */
        if (!defined('WP_CLI_ROOT')) {
            define('WP_CLI_ROOT', dirname(__DIR__) . '/vendor/wp-cli/wp-cli');
        }
        require_once WP_CLI_ROOT . '/php/utils.php';
        require_once WP_CLI_ROOT . '/php/dispatcher.php';
        require_once WP_CLI_ROOT . '/php/class-wp-cli.php';
        require_once WP_CLI_ROOT . '/php/class-wp-cli-command.php';

        \WP_CLI\Utils\load_dependencies();

        /**
         * Configuration for nginx and apache testing
         */
        $this->env = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $this->env->load();

        $this->setupEnvironmentForApacheTests();
        $this->setupEnvironmentForNginxTests();

        //Create test files and directories
        $this->createRequiredFiles();
        $this->createRequiredDirectories();
    }

    public function tearDown(): void {
        //Remove all secure rules from the configuration
        if(isset($this->nginxAssocArgs['file-path'])) {
            $fileManager = new FileManager($this->nginxAssocArgs['file-path']);
            $fileManager->clear();
        }
        if(isset($this->apacheAssocArgs['file-path'])) {
            $fileManager = new FileManager($this->apacheAssocArgs['file-path']);
            $fileManager->clear();
        }
    }

    /**
     * Sets required variables for nginx tests
     *
     * @return void
     */
    private function setupEnvironmentForNginxTests() : void {
        //NGINX CONFIGURATION
        $this->nginxAssocArgs = [
            'server'    => 'nginx',
            'file-path' => $_ENV['WORDPRESS_NGINX_PATH'] . DIRECTORY_SEPARATOR . 'nginx.conf'
        ];

        //Clear Secure configuration from previous tests
        $fileManager = new FileManager($this->nginxAssocArgs['file-path']);
        $fileManager->clear();

        $this->nginxHttpClient = new Client([
            'base_uri' => $_ENV['WORDPRESS_NGINX_URL']
        ]);
    }

    /**
     * Sets required variables for Apache tests
     *
     * @return void
     */
    private function setupEnvironmentForApacheTests() : void {
        //APACHE CONFIGURATION
        $this->apacheAssocArgs = [
            'file-path' => $_ENV['WORDPRESS_APACHE_PATH'] . DIRECTORY_SEPARATOR . '.htaccess'
        ];

        $fileManager = new FileManager($this->apacheAssocArgs['file-path']);
        $fileManager->clear();

        $this->apacheHttpClient = new Client([
            'base_uri' => $_ENV['WORDPRESS_APACHE_URL']
        ]);
    }

    /**
     * Creates files required for testing
     *
     * @return void
     */
    private function createRequiredFiles() : void {
        foreach($this->testFiles as $file) {
            if(!file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_APACHE_PATH'] . DIRECTORY_SEPARATOR . $file)) {
                $fp = fopen(dirname(__DIR__) . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_APACHE_PATH'] . DIRECTORY_SEPARATOR . $file, 'w');
                fclose($fp);
            }

            if(!file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . DIRECTORY_SEPARATOR . $file)) {
                $fp = fopen(dirname(__DIR__) . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . DIRECTORY_SEPARATOR . $file, 'w');
                fclose($fp);
            }
        }
    }

    /**
     * Creates directories required for testing
     *
     * @return void
     */
    private function createRequiredDirectories() : void {
        foreach($this->testDirectories as $directory) {
            if(!file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_APACHE_PATH'] . DIRECTORY_SEPARATOR . $directory)) {
                mkdir(dirname(__DIR__) . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_APACHE_PATH'] . DIRECTORY_SEPARATOR . $directory, 0755, true);
            }

            if(!file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . DIRECTORY_SEPARATOR . $directory)) {
                mkdir(dirname(__DIR__) . DIRECTORY_SEPARATOR . $_ENV['WORDPRESS_NGINX_PATH'] . DIRECTORY_SEPARATOR . $directory, 0755, true);
            }
        }
    }

    /**
     * Provides access to protected methods in test scenarios
     *
     * @throws ReflectionException
     */
    public function callMethod(object $object, string $name, array $args = []) {
        $class = new \ReflectionClass($object);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
<?php

namespace Tests;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class BaseTestCase extends TestCase {
    /**
     * @var vfsStreamDirectory
     */
    public vfsStreamDirectory $root;

    public function setUp(): void {
        $this->root = vfsStream::setup('assets', 0777);
        $this->root->chown(vfsStream::getCurrentUser());
        $this->root->chgrp(vfsStream::getCurrentGroup());

        //Load dependencies for the WP_CLI tests
        if (!defined('WP_CLI_ROOT')) {
            define('WP_CLI_ROOT', dirname(__DIR__) . '/vendor/wp-cli/wp-cli');
        }
        require_once WP_CLI_ROOT . '/php/utils.php';
        require_once WP_CLI_ROOT . '/php/dispatcher.php';
        require_once WP_CLI_ROOT . '/php/class-wp-cli.php';
        require_once WP_CLI_ROOT . '/php/class-wp-cli-command.php';

        \WP_CLI\Utils\load_dependencies();
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
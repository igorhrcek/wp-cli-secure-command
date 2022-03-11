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
<?php

namespace Tests\Unit;

use Tests\BaseTestCase;
use Tests\Helpers\FileHelper;
use WpCliFileManager\Exceptions\FileDoesNotExist;
use WpCliFileManager\FileManager;

class CreateFileTest extends BaseTestCase {
    public function testItWillCreateFileOnGivenPath() : void {
        $filePath = $this->root->url() . '/nginx.conf';
        $fileManager = new FileManager($filePath);

        $fileManager->createFile(777);

        $this->assertFileExists($filePath);
        $this->assertEquals(777, $this->root->getChild('assets/nginx.conf')->getPermissions());
    }

    public function testItWillCreateAFullDirectoryPathWithFile() : void {
        $filePath = $this->root->url() . '/this/does/not/exist/nginx.conf';
        $fileManager = new FileManager($filePath);

        $fileManager->createFile(666);
        $this->assertFileExists($filePath);
        $this->assertEquals(666, $this->root->getChild('this/does/not/exist/nginx.conf')->getPermissions());
    }

    public function testItWillNotCreateAFileIfItAlreadyExist() : void {
        $file = FileHelper::create('.htaccess', 0775, 'File content');
        $this->root->addChild($file);

        $fileManager = new FileManager($file->url());
        $fileManager->createFile(666);

        $this->assertNotEquals(666, $this->root->getChild('.htaccess')->getPermissions());
        $this->assertStringMatchesFormatFile($file->url(), 'File content');
    }
}
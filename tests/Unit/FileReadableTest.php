<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\BaseTestCase;
use WpCliFileManager\FileManager;
use Tests\Helpers\FileHelper;

final class FileReadableTest extends BaseTestCase {

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testFileIsWritable(): void {
        $readableFile = FileHelper::create('.htaccess', 0666);
        $this->root->addChild($readableFile);

        $unreadableFile = FileHelper::create('nginx.conf', 0000);
        $this->root->addChild($unreadableFile);

        $fileManager = new FileManager($readableFile->url());
        $this->assertTrue($this->callMethod($fileManager, 'isReadable'));

        $fileManager = new FileManager($unreadableFile->url());
        $this->assertFalse($this->callMethod($fileManager, 'isReadable'));
    }
}
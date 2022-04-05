<?php

declare(strict_types=1);

namespace Tests\Unit\FileManager;

use Tests\BaseTestCase;
use WP_CLI_Secure\Exceptions\FileIsNotReadable;
use WP_CLI_Secure\FileManager;
use Tests\Helpers\FileHelper;

final class FileReadableTest extends BaseTestCase {

    public function setUp(): void {
        parent::setUp();
    }

    public function testFileIsWritable(): void {
        $this->expectException(FileIsNotReadable::class);
        $readableFile = FileHelper::create('.htaccess', 0644);
        $this->root->addChild($readableFile);

        $unreadableFile = FileHelper::create('nginx.conf', 0000);
        $this->root->addChild($unreadableFile);

        $fileManager = new FileManager($readableFile->url());
        $this->assertTrue($this->callMethod($fileManager, 'isReadable'));

        $fileManager = new FileManager($unreadableFile->url());
        $this->assertFalse($this->callMethod($fileManager, 'isReadable'));
    }
}
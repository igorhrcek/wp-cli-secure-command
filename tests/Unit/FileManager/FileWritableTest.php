<?php

declare(strict_types=1);

namespace Tests\Unit\FileManager;

use Tests\BaseTestCase;
use WP_CLI_Secure\FileManager;
use Tests\Helpers\FileHelper;

final class FileWritableTest extends BaseTestCase {

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testFileIsWritable(): void {
        $writableFile = FileHelper::create('.htaccess', 0666);
        $this->root->addChild($writableFile);

        $nonWritableFile = FileHelper::create('nginx.conf', 0755);
        $this->root->addChild($nonWritableFile);

        $fileManager = new FileManager($writableFile->url());
        $this->assertTrue($this->callMethod($fileManager, 'isWritable'));

        $fileManager = new FileManager($nonWritableFile->url());
        $this->assertFalse($this->callMethod($fileManager, 'isWritable'));
    }
}
<?php

declare(strict_types=1);

namespace Tests\Unit;

use org\bovigo\vfs\vfsStreamFile;
use Tests\BaseTestCase;
use WP_CLI_Secure\Exceptions\FileDoesNotExist;
use WP_CLI_Secure\Exceptions\FileIsNotReadable;
use WP_CLI_Secure\FileManager;
use Tests\Helpers\FileHelper;

final class ReadFileTest extends BaseTestCase {

    /**
     * @var vfsStreamFile
     */
    private vfsStreamFile $file;

    private array $fileContent = [
        '<Files wp-config.php>',
        'Require all denied',
        '</Files>'
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->file = FileHelper::create('.htaccess', 0775, implode(PHP_EOL, $this->fileContent));
        $this->root->addChild($this->file);
    }

    public function testItShouldReturnAnArrayFromTheFile() : void {
        $fileManager = new FileManager($this->file->url());

        $fileContent = $fileManager->read();
        $this->assertIsArray($fileContent);
    }

    public function testItShouldReturnExceptionIfFileIsNotReadable() : void {
        $this->expectException(FileIsNotReadable::class);

        $file = FileHelper::create('unreadable.txt', 000);
        $this->root->addChild($file);

        $fileManager = new FileManager($file->url());
        $fileManager->read();
    }

    public function testItShouldReturnContentOfTheFile() : void {
        $fileManager = new FileManager($this->file->url());

        $fileContent = $fileManager->read();
        $this->assertCount(3, $fileContent);
        $this->assertEquals($fileContent, $this->fileContent);
    }

    public function testItShouldReturnReducedContentOfTheFile() : void {
        $fileManager = new FileManager($this->file->url());

        $fileContent = $fileManager->read(1);
        //Remove top row
        unset($this->fileContent[0]);

        $this->assertCount(count($this->fileContent), $fileContent);
        $this->assertEqualsCanonicalizing($fileContent, $this->fileContent);
    }
}
<?php

declare(strict_types=1);

namespace Tests\Unit\FileManager;

use Tests\BaseTestCase;
use Tests\Helpers\FileHelper;
use WP_CLI_Secure\FileManager;

final class ExtractTest extends BaseTestCase {
    /**
     * @var array Test content
     */
    private array $blockContent = [
        '#### marker BLOCK ACCESS TO SENSITIVE FILES start ####',
        '<LocationMatch ".+\\.(?i:psd|log|cmd|exe|bat|csh|ini|sh)$">',
        'Require all denied',
        '</LocationMatch>',
        '#### marker BLOCK ACCESS TO SENSITIVE FILES end ####',
    ];

    /**
     * @var array Test content
     */
    private array $blockContentWithGlobalMarkers = [
        '# BEGIN WP CLI SECURE',
        '#### marker BLOCK ACCESS TO SENSITIVE FILES start ####',
        '<LocationMatch ".+\\.(?i:psd|log|cmd|exe|bat|csh|ini|sh)$">',
        'Require all denied',
        '</LocationMatch>',
        '#### marker BLOCK ACCESS TO SENSITIVE FILES end ####',
        '# END WP CLI SECURE',
    ];

    public function setUp() : void {
        parent::setUp();

        $this->file = FileHelper::create('.htaccess', 0775, implode(PHP_EOL, $this->blockContent));
        $this->file2 = FileHelper::create('.htaccess2', 0775, implode(PHP_EOL, $this->blockContentWithGlobalMarkers));
        $this->root->addChild($this->file);
        $this->root->addChild($this->file2);
    }

    public function testItWillReturnContentBetweenMarkers() : void {
        $fileManager = new FileManager($this->file->url());

        $content = $fileManager->extractRuleBlock('BLOCK ACCESS TO SENSITIVE FILES');
        $this->assertIsArray($content);

        $compareBlock = $this->blockContent;
        unset($compareBlock[0], $compareBlock[4]);

        $this->assertEquals(array_values($compareBlock), $content);
    }

    public function testItWillReturnTrueIfMarkersAreFound() : void {
        $fileManager = new FileManager($this->file->url());

        $result = $fileManager->hasRuleBlock('BLOCK ACCESS TO SENSITIVE FILES');

        $this->assertTrue($result);
    }

    public function testItWillReturnFalseIfMarkerIsNotFound() : void {
        $fileManager = new FileManager($this->file->url());

        $result = $fileManager->hasRuleBlock('THIS BLOCK DOES NOT EXIST');

        $this->assertFalse($result);
    }

    public function testItWillExtractWholeSecureBlock() : void {
        $fileManager = new FileManager($this->file2->url());

        $content = $fileManager->extractSecureBlock();
        $compareBlock = $this->blockContentWithGlobalMarkers;
        unset($compareBlock[0], $compareBlock[6]);

        $this->assertEquals(array_values($compareBlock), $content);
    }

    public function testItWillReturnFalseIfThereIsNoSecureBlockInFile() : void {
        $fileManager = new FileManager($this->file->url());

        $content = $fileManager->extractSecureBlock();
        $compareBlock = $this->blockContent;

        $this->assertNotEquals(array_values($compareBlock), $content);
    }

    public function testItWillReturnTrueIfWpCliBlockExist() : void {
        $fileManager = new FileManager($this->file2->url());

        $this->assertTrue($fileManager->hasSecureBlock());
    }

    public function testItWillReturnFalseIfWpCliBlockDoesNotExist() : void {
        $fileManager = new FileManager($this->file->url());

        $this->assertFalse($fileManager->hasSecureBlock());
    }
}
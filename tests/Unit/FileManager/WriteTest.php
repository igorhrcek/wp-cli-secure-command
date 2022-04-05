<?php

namespace Tests\Unit\FileManager;

use Tests\BaseTestCase;
use Tests\Helpers\FileHelper;
use WP_CLI_Secure\Exceptions\FileIsNotWritable;
use WP_CLI_Secure\Exceptions\RuleAlreadyExist;
use WP_CLI_Secure\FileManager;

class WriteTest extends BaseTestCase {
    protected array $rules = [
        //BLOCK ACCESS TO WP CONFIG
        [
            '<Files wp-config.php>',
            'Require all denied',
            '</Files>',
        ],
        //FORBID PHP EXECUTION IN UPLOADS
        [
            '<Directory "/some/path">',
            '<FilesMatch \.php$>',
            'Require all denied',
            '</FilesMatch>',
            '</Directory>',
        ],
        //FORBID PHP EXECUTION IN WP-INCLUDES
        [
            '<IfModule mod_rewrite.c>',
            '<Directory "/some/path">',
            '<FilesMatch \.php$>',
            'RewriteEngine on',
            'RewriteCond %{REQUEST_FILENAME} !^/some/path/wp\-tinymce\.php$ [NC]',
            'RewriteRule .* - [NC,F,L]',
            '</FilesMatch>',
            '</Directory>',
            '</IfModule>',
        ]
    ];

    public function setUp(): void {
        parent::setUp();

        $content = file_get_contents(getcwd() . '/tests/assets/htaccess-base.txt');
        $content2 = file_get_contents(getcwd() . '/tests/assets/htaccess-secured.txt');
        $this->file = FileHelper::create('.htaccess', 0755, $content);
        $this->file2 = FileHelper::create('.htaccess2', 0777, $content);
        $this->file3 = FileHelper::create('.htaccess-secured', 0644, $content2);
        $this->root->addChild($this->file);
        $this->root->addChild($this->file2);
        $this->root->addChild($this->file3);
    }

    public function testItWillWrapAllRulesWithGlobalMarkers() : void {
        $fileManager = new FileManager($this->file->url());

        $content = $fileManager->wrap($this->rules[0], 'global');
        $this->assertEquals($fileManager::MARKER_GLOBAL_START . $fileManager::SPACE_DELIMITER . $fileManager::MARKER_WP_CLI_SECURE, $content[0]);
        $this->assertEquals($fileManager::MARKER_GLOBAL_END . $fileManager::SPACE_DELIMITER . $fileManager::MARKER_WP_CLI_SECURE, $content[count($content) -
        1]);
    }

    public function testItWillWrapRulesBlockWithBlockMarkers() : void {
        $fileManager = new FileManager($this->file->url());

        $content = $fileManager->wrap($this->rules[0], 'block', 'BLOCK ACCESS TO WP CONFIG');
        $this->assertEquals($fileManager::MARKER_RULE . $fileManager::SPACE_DELIMITER . 'BLOCK ACCESS TO WP CONFIG' . $fileManager::SPACE_DELIMITER .
            $fileManager::MARKER_RULE_START,
            $content[0]);
        $this->assertEquals($fileManager::MARKER_RULE . $fileManager::SPACE_DELIMITER . 'BLOCK ACCESS TO WP CONFIG' . $fileManager::SPACE_DELIMITER .
            $fileManager::MARKER_RULE_END, $content[count($content) -
        1]);
    }

    public function testItWillCreateBackupFileBeforeChanges() : void {
        $fileManager = new FileManager($this->file->url());

        $this->callMethod($fileManager, 'backup');
        $this->assertFileExists($this->file->url() . '.bkp');
    }

    /**
     * @Depends ExtractTest::testItWillReturnTrueIfMarkersAreFound
     */
    public function testItWillReturnExceptionIfFileIsNotWritable() : void {
        $this->expectException(FileIsNotWritable::class);
        $fileManager = new FileManager($this->file->url());
        $fileManager->add($this->rules[1], 'FORBID PHP EXECUTION IN UPLOADS');
    }

    /**
     * @Depends ExtractTest::testItWillReturnTrueIfMarkersAreFound
     */
    public function testItWillWriteRulesBlockIfRulesAreNotPresentInFile() : void {
        $fileManager = new FileManager($this->file2->url());
        $fileManager->add($this->rules[1], 'FORBID PHP EXECUTION IN UPLOADS');
        $a = file_get_contents($this->file2->url());

        $this->assertTrue($fileManager->hasRuleBlock('FORBID PHP EXECUTION IN UPLOADS'));
        $this->assertTrue($fileManager->hasSecureBlock());
    }

    /**
     * @Depends ExtractTest::testItWillReturnTrueIfMarkersAreFound
     */
    public function testItWillAppendRulesBlockIfRulesArePresentInFile() : void {
        $fileManager = new FileManager($this->file3->url());
        $fileManager->add($this->rules[1], 'FORBID PHP EXECUTION IN UPLOADS');

        $this->assertTrue($fileManager->hasRuleBlock('FORBID PHP EXECUTION IN UPLOADS'));
        $this->assertTrue($fileManager->hasRuleBlock('DISABLE WP-CONFIG ACCESS'));
    }

    public function testItWillThrowAnExceptionIfRuleBlockAlreadyExist() : void {
        $this->expectException(RuleAlreadyExist::class);
        $fileManager = new FileManager($this->file2->url());
        $fileManager->add($this->rules[1], 'FORBID PHP EXECUTION IN UPLOADS');
        $fileManager->add($this->rules[1], 'FORBID PHP EXECUTION IN UPLOADS');
    }

    /**
     * @Depends ExtractTest::testItWillReturnTrueIfMarkersAreFound
     */
    public function testItWillRemoveAllRulesFromFile() : void {
        $fileManager = new FileManager($this->file3->url());
        $fileManager->clear();

        $this->assertFalse($fileManager->hasSecureBlock());
    }

    /**
     * @Depends ExtractTest::testItWillReturnTrueIfMarkersAreFound
     */
    public function testItWillRemoveWpCliSecureBlockFromFile() : void {
        $fileManager = new FileManager($this->file3->url());
        $fileManager->remove('DISABLE WP-CONFIG ACCESS');

        $this->assertFalse($fileManager->hasRuleBlock('DISABLE WP-CONFIG ACCESS'));
    }

    public function testItWillFlattenMultidimensionalArray() : void {
        $arr = [1, 2, 3, [10, 20], [50, 70]];
        $fileManager = new FileManager($this->file->url());

        $result = $this->callMethod($fileManager, 'flattenArray', [$arr]);
        $this->assertEquals([1, 2, 3, 10, 20, 50, 70], $result);
    }
}
<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use Tests\BaseTestCase;
use Tests\Unit\FileManager\ExtractTest;
use WP_CLI_Secure\FileManager;
use WP_CLI_Secure\SubCommands\BlockAccessToHtaccess;

class SubCommandTest extends BaseTestCase {

    public function testItWillSetCorrectCommandArguments() : void {
        $assocArgs = [
            'file-path'  => $this->root->url() . '/.htaccess',
            'server'    => 'nginx',
            'output'    => true
        ];

        $command = new BlockAccessToHtaccess($assocArgs);

        $this->assertEquals($assocArgs['file-path'], $command->filePath);
        $this->assertEquals($assocArgs['server'], $command->serverType);
        $this->assertEquals($assocArgs['output'], $command->output);

        $assocArgs = [];

        $command = new BlockAccessToHtaccess($assocArgs);

        $this->assertEquals('.htaccess', $command->filePath);
        $this->assertEquals('apache', $command->serverType);
        $this->assertFalse($command->output);
    }
}
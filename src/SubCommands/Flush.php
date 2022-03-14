<?php

namespace WP_CLI_Secure\SubCommands;

use WP_CLI;
use WP_CLI_Secure\Exceptions\FileDoesNotExist;
use WP_CLI_Secure\Exceptions\FileIsNotReadable;
use WP_CLI_Secure\Exceptions\FileIsNotWritable;
use WP_CLI_Secure\Exceptions\RuleAlreadyExist;
use WP_CLI_Secure\FileManager;

class Flush extends SubCommand {
    public string $ruleTemplate = '';
    public string $ruleName = '';
    public string $successMessage = 'All security rules have been removed. Please note that if you are using nginx you need to restart it manually. Also, if you copied rules manually, this command will have no effect.';

    /**
     * Outputs the result of command execution
     *
     * @return void
     * @throws WP_CLI\ExitException
     */
    public function output() : void {
        try {
            $fileManager = new FileManager($this->filePath);
            $result = $fileManager->clear();

            if($result) {
                WP_CLI::success($this->successMessage);
            }
        } catch(FileDoesNotExist|RuleAlreadyExist|FileIsNotWritable|FileIsNotReadable $e) {
            WP_CLI::error($e->getMessage());
        }
    }
}
<?php

namespace WP_CLI_Secure\SubCommands;

use WP_CLI;
use WP_CLI_Secure\Exceptions\FileDoesNotExist;
use WP_CLI_Secure\Exceptions\FileIsNotWritable;
use WP_CLI_Secure\Exceptions\RuleAlreadyExist;
use WP_CLI_Secure\FileManager;

class SubCommand {
    /**
     * @var bool Defines if output will be written to a file our to a stdout
     */
    public bool $output;

    /**
     * @var string Determines the server type for which we will write the rules
     */
    public string $serverType = 'apache';

    /**
     * @var string Custom file path, if passed as --path argument
     */
    public string $filePath;

    /**
     * @var string Defines a name of the rule which is going to be used to generate rule block
     */
    public string $ruleName;

    /**
     * @var mixed The content of the rule template file
     */
    public mixed $ruleContent;

    /**
     * @var string Rule template name
     */
    public string $ruleTemplate;

    /**
     * @var string Message to output if command execution was successful
     */
    public string $successMessage;

    /**
     * @var array Command line arguments passed as $assoc_args
     */
    private array $commandArguments;

    /**
     * Default file name for Apache server type
     */
    private const APACHE_FILE = '.htaccess';

    /**
     * Default file name for nginx server type
     */
    private const NGINX_FILE = 'nginx.conf';

    /**
     * @param array  $arguments The $assoc_args passed with the command
     */
    public function __construct(array $arguments) {
        $this->commandArguments = $arguments;
        $this->output = $this->setOutput();
        $this->serverType = $this->setServerType();
        $this->filePath = $this->setFilePath();
        $this->ruleContent = $this->setRuleContent();
    }

    /**
     * @return bool
     */
    private function setOutput() : bool {
        return isset($this->commandArguments['output']) && $this->commandArguments['output'] === true;
    }

    /**
     * @return string
     */
    private function setServerType() : string {
        return $this->commandArguments['server'] ?? 'apache';
    }

    /**
     * @return string
     */
    private function setFilePath() : string {
        return $this->commandArguments['path'] ?? (($this->serverType === 'apache') ? self::APACHE_FILE : self::NGINX_FILE);
    }

    /**
     * Reads rule template file. Depending on output type, returns string or an array
     *
     * @return string|array
     */
    private function setRuleContent() : string|array {
        $templateFilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . $this->serverType . DIRECTORY_SEPARATOR .
            $this->ruleTemplate . '.tpl';

        if($this->output) {
            $result = file_get_contents($templateFilePath);
        } else {
            $result = [];
            $file = new \SplFileObject($templateFilePath);
            while(!$file->eof()) {
                $result[] = rtrim($file->current(), "\n");
                $file->next();
            }
            unset($file);
        }

        return $result;
    }

    /**
     * Outputs the result of command execution
     *
     * @return void
     * @throws WP_CLI\ExitException
     */
    public function output() {
        if($this->output) {
            WP_CLI::line($this->ruleContent);
        } else {
            $fileManager = new FileManager($this->filePath);

            try {
                $result = $fileManager->add($this->ruleContent, $this->ruleName);

                if($result) {
                    WP_CLI::success($this->successMessage);
                }
            } catch(FileDoesNotExist $e) {
                WP_CLI::error($e->getMessage());
            } catch(RuleAlreadyExist $e) {
                WP_CLI::error($e->getMessage());
            } catch(FileIsNotWritable $e) {
                WP_CLI::error($e->getMessage());
            }
        }
    }
}
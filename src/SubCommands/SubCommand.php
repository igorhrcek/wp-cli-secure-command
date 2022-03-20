<?php

namespace WP_CLI_Secure\SubCommands;

use WP_CLI;
use WP_CLI_Secure\Exceptions\FileDoesNotExist;
use WP_CLI_Secure\Exceptions\FileIsNotReadable;
use WP_CLI_Secure\Exceptions\FileIsNotWritable;
use WP_CLI_Secure\Exceptions\RuleAlreadyExist;
use WP_CLI_Secure\FileManager;

class SubCommand {
    /**
     * @var bool Defines if output will be written to a file or to the stdout
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
    public $ruleContent;

    /**
     * @var string Rule template name
     */
    public string $ruleTemplate;

    /**
     * @var string Message to output if command execution was successful
     */
    public string $successMessage;

    /**
     * @var string Message to output if --remove command execution was successful
     */
    public string $removalMessage;

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
        return $this->commandArguments['file-path'] ?? (($this->serverType === 'apache') ? self::APACHE_FILE : self::NGINX_FILE);
    }

    /**
     * Reads rule template file. Depending on output type, returns string or an array
     *
     * @return string|array
     */
    private function setRuleContent() {
        //Return an empty array in case when the executed command does not require a template
        if($this->ruleTemplate === '') {
            return [];
        }

        $templateFilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . $this->serverType . DIRECTORY_SEPARATOR .
            $this->ruleTemplate . '.tpl';

        $result = [];
        $file = new \SplFileObject($templateFilePath);
        while(!$file->eof()) {
            $result[] = rtrim($file->current(), "\n");
            $file->next();
        }
        unset($file);

        return $result;
    }

    /**
     * Returns the output message
     *
     * @param string $type
     *
     * @return string
     * @todo I am not too happy about this
     */
    private function getOutputMessage(string $type = 'success') : string {
        $message = $this->{$type. 'Message'} ;

        if($this->serverType === 'nginx') {
            $message .= PHP_EOL . 'Since you are using nginx you need to restart web server manually. If you copied rules manually, this command will have no effect.';
        }

        return $message;
    }

    /**
     * Outputs the result of command execution
     *
     * @return void
     * @throws WP_CLI\ExitException
     */
    public function output() {
	    try {
		    $fileManager = new FileManager( $this->filePath );
		    if ( $this->output ) {
			    $content     = $fileManager->wrap( $this->ruleContent, 'block', $this->ruleName );
			    WP_CLI::line( implode( PHP_EOL, $content ) );
		    } else {
			    if ( isset( $this->commandArguments['remove'] ) && $this->commandArguments['remove'] === true ) {
				    //We need to remove the rule from file
				    $result = $fileManager->remove( $this->ruleName );

				    if ( $result ) {
					    WP_CLI::success( $this->getOutputMessage( 'removal' ) );
				    }
			    } else {
				    //Add the rule
				    $fileManager->add( $this->ruleContent, $this->ruleName );

				    WP_CLI::success( $this->getOutputMessage( 'success' ) );
			    }

		    }
	    } catch ( FileDoesNotExist | FileIsNotWritable | FileIsNotReadable $e ) {
		    WP_CLI::error( $e->getMessage() );
	    } catch ( RuleAlreadyExist $e ) {
		    WP_CLI::warning( $e->getMessage() );
	    }
    }
}
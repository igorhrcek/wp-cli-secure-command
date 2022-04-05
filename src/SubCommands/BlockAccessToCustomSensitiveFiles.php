<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToCustomSensitiveFiles extends SubCommand
{
    public string $ruleTemplate = 'block_access_to_custom_sensitive_files';
    public string $ruleName = 'BLOCK ACCESS TO CUSTOM SENSITIVE FILES';
    public string $successMessage = 'Block Access to Custom Sensitive Files rule has been deployed.';
    public string $removalMessage = 'Block Access to Custom Sensitive Files rule has been removed.';

    /**
     * @return array
     */
    public function getTemplateVars(): array
    {
        $files = $this->commandArguments['files'];

        if (!empty($files)) {
            $files = explode(',', $files);
            $files = array_map('trim', $files);
            $files_array = [];

            foreach ($files as $key => $value) {
                if (preg_match('/.+\/.+/', $value)) {
                    $file_with_directory = $this->setRuleContent(false, 'block_access_to_sensitive_files_with_directories');
                    if (isset($this->commandArguments['server']) && $this->commandArguments['server'] === 'nginx') {
                        $file = $value;
                    } else {
                        $file = preg_quote(ltrim($value, '/'));
                    }
                    $files_array[] = [ $file => $file_with_directory ];
                } else {
                    $file = (isset($this->commandArguments['server']) && $this->commandArguments['server'] === 'nginx') ?
                        preg_quote($value) : $value;
                    $files_array[] = ['file' => $file];
                }
            }

            return $files_array;
        }

        return [];
    }
}

<?php

namespace WP_CLI_Secure\SubCommands;

class BlockAccessToSensitiveFiles extends SubCommand {
    public string $ruleTemplate = 'block_access_to_sensitive_files';
    public string $ruleName = 'BLOCK ACCESS TO SENSITIVE FILES';
    public string $successMessage = 'Block Access to Sensitive Files rule has been deployed.';
    public string $removalMessage= 'Block Access to Sensitive Files rule has been removed.';

    /**
     * @var string A list of files that should be protected by default
     */
    private string $sensitiveFiles = 'readme.html, readme.txt, wp-config.php, wp-admin/install.php';

    /**
     * @return array
     */
    public function getTemplateVars(): array {
        $files = $this->commandArguments['files'] ?? $this->sensitiveFiles;

        if (!empty($files)) {
            $files = explode(',', $files);
            $files = array_map('trim', $files);
            $files_array = [];

            foreach ($files as $key => $value) {
                $file = (isset($this->commandArguments['server']) && $this->commandArguments['server'] === 'nginx') ?
                    preg_quote($value) : $value;

                $files_array[] = ['file' => $file];
            }
            
            return $files_array;
        }

        return [];
    }
}
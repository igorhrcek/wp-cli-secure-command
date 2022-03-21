<?php

namespace WP_CLI_Secure\SubCommands;

class FixFileAndDirPermissions {
    /**
     * @var int Default permission mask for the file
     */
    public int $filePermissions = 0666;

    /**
     * @var int Default permission mask for the directory
     */
    public int $directoryPermissions = 0755;

    /**
     * Execute the shell script to fix the folder and directory permissions
     *
     * @return bool
     */
    public function output() : bool {
        //Stop execution if ABSPATH is not defined to prevent changing permissions in the wrong place
        if(!defined('ABSPATH')) {
            return false;
        }

        $iterator = new \RecursiveDirectoryIterator(ABSPATH);
        foreach($iterator as $file) {
            chmod($file, is_file($file) ? $this->filePermissions : $this->directoryPermissions);
        }

        WP_CLI::success("Permissions were successfully updated.");
    }
}
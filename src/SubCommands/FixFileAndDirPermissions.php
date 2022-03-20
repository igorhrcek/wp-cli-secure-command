<?php

namespace WP_CLI_Secure\SubCommands;

class FixFileAndDirPermissions extends SubCommand
{
    /**
     * Execute the shell script to fix the folder and directory permissions
     *
     * @return void
     */

    function fixPermissions() {

        $iterator = new \RecursiveDirectoryIterator(ABSPATH);

        foreach($iterator as $file)
        {
              if (is_file($file)) {
                  chmod($file, 0666);
              } elseif (is_dir($file)) {
                  chmod($file, 0755);
              }
        }
    }
}
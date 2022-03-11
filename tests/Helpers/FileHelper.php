<?php

namespace Tests\Helpers;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

class FileHelper {

    /**
     * @param string $fileName
     * @param int    $permissions
     * @param string $content
     *
     * @return vfsStreamFile
     */
    public static function create(string $fileName, int $permissions, string $content = ''): vfsStreamFile {
        $file = new vfsStreamFile($fileName, $permissions);
        $file->chown(vfsStream::getCurrentUser() + 1);
        $file->chgrp(vfsStream::getCurrentGroup() + 1);

        if($content !== '') {
            $file->setContent($content);
        }

        return $file;
    }
}
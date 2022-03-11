<?php

namespace WpCliFileManager;

use WpCliFileManager\Exceptions\FileDoesNotExist;
use function PHPUnit\Framework\throwException;

class FileManager {
    /**
     * @var string Full path of managed file
     */
    private string $path;

    public function __construct(string $path) {
        $this->path = $path;
    }

    /**
     * Read data from the file
     *
     * @param int      $startLine
     * @param int|null $lines
     *
     * @return array
     */
    public function read(int $startLine = 0, int $lines = null) : array {
        if(!$this->fileExist()) {
            new Error(Error::FILE_DOES_NOT_EXIST);
        }

        if(!$this->isReadable()) {
            new Error(Error::FILE_IS_NOT_READABLE);
        }

        $result = [];
        $file = new \SplFileObject($this->path);
        $file->seek($startLine);

        if($lines === null) {
            while(!$file->eof()) {
                $result[] = rtrim($file->current(), "\n");
                $file->next();
            }
        } else {
            for($i = 0; $i < $lines; $i++) {
                if ($file->eof()) {
                    break;
                }

                $result[] = rtrim($file->current(), "\n");
                $file->next();
            }
        }

        unset($file);

        return $this->removeZeroSpace($result);
    }

    /**
     * Get if file is writable
     *
     * @return bool
     */
    private function isWritable() : bool {
        return is_writable($this->path);
    }

    /**
     * Get if file is readable
     *
     * @return bool
     */
    private function isReadable(): bool {
        return is_readable($this->path);
    }

    /**
     * Get if file exist
     *
     * @return bool
     */
    private function fileExist() : bool {
        return file_exists($this->path);
    }

    /**
     * Remove ghost Unicode spaces and other unnecessary stuff (<200b><200c>)
     *
     * @param $content
     *
     * @return array|string
     */
    private static function removeZeroSpace($content): array|string
    {
        if(is_array($content)) {
            return array_map([static::class, 'removeZeroSpace'], $content);
        }

        // Remove UTF-8 BOM if present
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        return str_replace(["\xe2\x80\x8b", "\xe2\x80\x8c", "\xe2\x80\x8d"], '', $content);
    }
}
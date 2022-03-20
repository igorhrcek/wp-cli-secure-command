<?php

namespace WP_CLI_Secure;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use WP_CLI_Secure\Exceptions\FileDoesNotExist;
use WP_CLI_Secure\Exceptions\FileIsNotReadable;
use WP_CLI_Secure\Exceptions\FileIsNotWritable;
use WP_CLI_Secure\Exceptions\RuleAlreadyExist;

class FileManager {
    /**
     * Defines the end of each marked block
     * # END WP CLI SECURE
     */
    public const MARKER_GLOBAL_END = '# END';

    /**
     * Defines the start of each marked block
     * # BEGIN WP CLI SECURE
     */
    public const MARKER_GLOBAL_START = '# BEGIN';

    /**
     * Used to mark a start of WP CLI Secure block
     */
    public const MARKER_WP_CLI_SECURE = 'WP CLI SECURE';

    /**
     * This sting is used as a start of every rule block
     * #### marker RULE NAME start ####
     */
    public const MARKER_RULE = '#### marker';

    /**
     * This string is used to mark the end of the starting line of every rule block
     * #### marker RULE NAME start ####
     */
    public const MARKER_RULE_START = 'start ####';

    /**
     * This string is used to mark the end of the ending line of every rule block
     * #### marker RULE NAME end ####
     */
    public const MARKER_RULE_END = 'end ####';

    /**
     * It is just a space
     */
    public const SPACE_DELIMITER = ' ';

    /**
     * @var string Full path of managed file, for example /home/site/www/nginx.conf
     */
    private string $path;

    /**
     * Extension used for backup files
     */
    private const BACKUP_EXTENSION = '.bkp';

    /**
     * @var array File content converted to an array
     */
    private array $file;

    public function __construct(string $path) {
        $this->path = $path;
        $this->file = $this->setFileContent();
    }

    /**
     * Set value of file
     *
     * @return array
     * @throws FileIsNotReadable
     */
    private function setFileContent() : array {
        if(!$this->fileExist()) {
            return [];
        }

        if(!$this->isReadable()) {
            throw new FileIsNotReadable();
        }

        return $this->read();
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

        return self::removeZeroSpace($result);
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
    private static function removeZeroSpace($content)
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

    /**
     * Extracts rules between two rule block markers
     *
     * @param string $marker
     *
     * @return array
     */
    public function extractRuleBlock(string $marker) : array {
        $result = [];

        $found = false;
        foreach($this->file as $line) {
            if(strpos($line, $marker . self::SPACE_DELIMITER . self::MARKER_RULE_END) !== false) {
                $found = false;
            }

            if($found) {
                $result[] = $line;
            }

            if(strpos($line, $marker . self::SPACE_DELIMITER . self::MARKER_RULE_START) !== false) {
                $found = true;
            }
        }

        return array_map('trim', $result);
    }

    /**
     * Returns location of item inside array
     *
     * @param string $needle
     *
     * @return bool|int|string
     */
    private function findInFile(string $needle) {
        return array_search($needle, $this->file);
    }

    /**
     * Extracts whole WP CLI SECURE block if it exist
     *
     * @return array|bool
     */
    public function extractSecureBlock() {
        $start = $this->findInFile(self::MARKER_GLOBAL_START . self::SPACE_DELIMITER . self::MARKER_WP_CLI_SECURE);
        $end = $this->findInFile(self::MARKER_GLOBAL_END . self::SPACE_DELIMITER . self::MARKER_WP_CLI_SECURE);

        if($start === false || $end === false) {
            return false;
        }

        $result = array_slice($this->file, $start + 1, $end - $start - 1);

        if(!$result) {
            return false;
        }

        if(count($result) === 1) {
            return trim($result[0]);
        }

        return array_filter($result);
    }

    /**
     * Used to check if there is a single rule block in file
     *
     * @param string $marker
     *
     * @return bool
     */
    public function hasRuleBlock(string $marker) : bool {
        return count($this->extractRuleBlock($marker)) > 0;
    }

    /**
     * Checks if there is a global WP CLI SECURE present in the file
     *
     * @return bool
     */
    public function hasSecureBlock() : bool {
        return is_array($this->extractSecureBlock()) && count($this->extractSecureBlock()) > 0;
    }

    /**
     * Creates a file on a given path if it does not exist
     *
     * @param int $permissions
     *
     * @return bool
     */
    public function createFile(int $permissions = 0666) : bool {
        if(!$this->fileExist()) {
            $path = explode("/", $this->path);
            $fileName = $path[count($path) - 1];
            $basePath = implode('/', array_slice($path, 0, count($path) - 1));
            $pathInfo = pathinfo($this->path);

            //Create a full directory path if it does not exist
            if(!file_exists($pathInfo['dirname'])) {
                mkdir($pathInfo['dirname'], 0755, true);
            }

            //Create an empty file with given permissions
            touch($this->path);
            chmod($this->path, $permissions);

            return true;
        }

        return true;
    }

    /**
     * Wraps rules blocks with global or rule markers
     *
     * @param array  $content
     * @param string $with
     * @param string $marker
     *
     * @return array
     */
    public function wrap(array $content, string $with = 'block', string $marker = '') : array {
        if($with === 'block') {
            array_unshift($content, self::MARKER_RULE . self::SPACE_DELIMITER . $marker . self::SPACE_DELIMITER . self::MARKER_RULE_START);
            array_push($content, self::MARKER_RULE . self::SPACE_DELIMITER . $marker . self::SPACE_DELIMITER . self::MARKER_RULE_END);
        } else if($with === 'global') {
            array_unshift($content, self::MARKER_GLOBAL_START . self::SPACE_DELIMITER . self::MARKER_WP_CLI_SECURE);
            array_push($content, self::MARKER_GLOBAL_END . self::SPACE_DELIMITER . self::MARKER_WP_CLI_SECURE);
        }

        return $content;
    }

    /**
     * Creates a backup of file before any changes are made to it
     *
     * @return bool
     */
    private function backup() : bool
    {
        if(file_exists($this->path . self::BACKUP_EXTENSION)) {
            unlink($this->path . self::BACKUP_EXTENSION);
        }

        return copy($this->path, $this->path . self::BACKUP_EXTENSION);
    }

    /**
     * Adds a new rule block
     *
     * @param array  $content
     * @param string $marker
     *
     * @return bool
     */
    public function add(array $content, string $marker = ''): bool {
        //If the rule block already exist, there is no reason to add it again
        if($this->hasRuleBlock($marker)) {
            throw new RuleAlreadyExist();
        }

        //Check if file exist?
        if(!$this->fileExist()) {
            $this->createFile();
        }

        if(!$this->isWritable()) {
            throw new FileIsNotWritable();
        }

        //Wrap the rule block with markers
        $content = $this->wrap($content, 'block', $marker);

        if(!$this->hasSecureBlock()) {
            $content = $this->wrap($content, 'global');

            //File does not contain any of our SECURE rules, so we need to prepend our content at the beginning of the file
            array_unshift($this->file, $content);
        } else {
            //There is already the SECURE block in the file, so we need to append new rule block at the end of global block
            $end = $this->findInFile(self::MARKER_GLOBAL_END . self::SPACE_DELIMITER . self::MARKER_WP_CLI_SECURE);

            array_splice($this->file, $end, 0, $content);
        }

        return $this->write();
    }

    /**
     * Removes all rules and global SECURE block from file
     *
     * @return bool
     */
    public function clear() : bool {
        if(!$this->hasSecureBlock()) {
            return true;
        }

        $start = $this->findInFile(self::MARKER_GLOBAL_START . self::SPACE_DELIMITER . self::MARKER_WP_CLI_SECURE);
        $end = $this->findInFile(self::MARKER_GLOBAL_END . self::SPACE_DELIMITER . self::MARKER_WP_CLI_SECURE);

        array_splice($this->file, $start, $end + 1 - $start);

        return $this->write();
    }

    /**
     * Removes one rule block
     *
     * @param string $marker
     *
     * @return bool
     */
    public function remove(string $marker) : bool {
        if(!$this->hasRuleBlock($marker)) {
            return true;
        }

        $start = $this->findInFile(self::MARKER_RULE . self::SPACE_DELIMITER . $marker . self::SPACE_DELIMITER . self::MARKER_RULE_START);
        $end = $this->findInFile(self::MARKER_RULE . self::SPACE_DELIMITER . $marker . self::SPACE_DELIMITER . self::MARKER_RULE_END);

        array_splice($this->file, $start, $end + 1 - $start);

        return $this->write();
    }

    /**
     * Writes changes to a file
     *
     * @return bool
     */
    public function write(): bool {
        //Flatten the array (<= PHP 8.0 compatible)
        $this->file = $this->flattenArray($this->file);

        //Make a backup of a file
        $this->backup();

        $fp = fopen($this->path, 'w');
        //Attempt to get a file lock, if FS has support
        flock($fp, LOCK_EX);

        //Write the content
        $content = implode(PHP_EOL, $this->file);
        fseek($fp, 0);
        $write = fwrite($fp, $content);

        if($write) {
            ftruncate($fp, ftell($fp));
        }
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return (bool) $write;
    }

    /**
     * Flattens the array structure, for example:
     * [1, 2, 3, [5, 6]] => [1, 2, 3, 5, 6]
     *
     * @param array $array
     * @param int   $depth
     *
     * @return array
     */
    private function flattenArray(array $array, int $depth = 1) : array {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && $depth) {
                $result = array_merge($result, $this->flattenArray($value, $depth - 1));
            } else {
                $result = array_merge($result, [$key => $value]);
            }
        }

        return $result;
    }
}

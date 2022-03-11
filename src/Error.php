<?php

namespace WpCliFileManager;

use WpCliFileManager\Exceptions\FileDoesNotExist;
use WpCliFileManager\Exceptions\FileIsNotReadable;

class Error {

    public const THROW_NONE = 0;
    public const FILE_DOES_NOT_EXIST = 1;
    public const FILE_IS_NOT_READABLE = 2;

    /**
     * @var mixed
     */
    private mixed $tmp;

    /**
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     */
    public function __construct($code = self::THROW_NONE) {
        switch($code) {
            case self::FILE_DOES_NOT_EXIST:
                throw new FileDoesNotExist();
                break;

            case self::FILE_IS_NOT_READABLE:
                throw new FileIsNotReadable();
                break;

            default:
                //No exception
                $this->tmp = $code;
                break;
        }
    }
}
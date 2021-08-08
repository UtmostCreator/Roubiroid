<?php

namespace app\core;

use app\core\notification\Message;
use modules\DD\DD;

class Session
{
    public const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        Session::initIfItDoesNotExist();
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function getAll()
    {
        Session::initIfItDoesNotExist();
        return $_SESSION;
    }

    // @TODO php 5.6+ any number of args
    /*
        public function sum(...$numbers) {
            $acc = 0;
            foreach ($numbers as $n) {
                $acc += $n;
            }
            return $acc;
        }
        echo sum(1, 2, 3, 4);
    */


    public function get($key)
    {
        if (!isset($_SESSION[$key])) {
            return false;
        }
        return $_SESSION[$key];
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function destroy()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function initIfItDoesNotExist()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function hasAnyFlash(): bool
    {
        return isset($_SESSION[self::FLASH_KEY]);
    }

    public function setFlash(
        $type,
        $title,
        $desc,
        $visibility = Message::USER_VISIBLE,
        $oneTime = true,
        $closable = true,
        $unique = true
    ) {
        $message = new Message($type, $title, $desc, $visibility, $oneTime, $closable);
        if (self::hasAnyFlash()) {
            foreach ($_SESSION[self::FLASH_KEY] as $key => $msg) {
                if (Message::compare($msg, $message)) {
                    $_SESSION[self::FLASH_KEY][$key]->addXTimes();
                    $unique = false;
                    continue;
                }
            }
        }
        if ($unique) {
            $_SESSION[self::FLASH_KEY][] = $message;
        }
    }

    protected function getFlashByKey($id)
    {
        /* @var Message $msg */
        $msg = $_SESSION[self::FLASH_KEY][$id];
        $result = $msg->show();
        if ($msg->oneTime) {
            unset($_SESSION[self::FLASH_KEY][$id]);
        }
        return $result;
    }

    public function getAllFlashes($where = ['oneTime' => true], $oneTime = true): bool
    {
        if (empty($_SESSION[self::FLASH_KEY])) {
            return false;
        }
        /* @var Message $msg */
        foreach ($_SESSION[self::FLASH_KEY] as $key => $msg) {
            $doNotDisplay = false;

//            if ($msg->oneTime !== $oneTime) {
//                continue;
//            }
            foreach ($where as $fieldName => $value) {
                $doNotDisplayIfNoMatch = property_exists($msg, $fieldName) && $msg->{$fieldName} !== $value;
                if ($doNotDisplayIfNoMatch) {
                    $doNotDisplay = true;
                    break;
                }
            }
            if ($doNotDisplay) {
                continue;
            }
            echo $this->getFlashByKey($key);
        }
        return true;
    }

    public function destroyFlashesWhere($oneTime, $visibility = null)
    {
        if (self::hasAnyFlash()) {
            /* @var Message $msg */
            foreach ($_SESSION[self::FLASH_KEY] as $key => $msg) {
                if ($msg->oneTime === $oneTime) {
                    unset($_SESSION[self::FLASH_KEY][$key]);
                }
                if ($msg->visibility === $visibility) {
                    unset($_SESSION[self::FLASH_KEY][$key]);
                }
            }
        }
    }
}

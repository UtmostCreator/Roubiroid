<?php

namespace Framework\notification;

class Message
{
    public const ADMIN_VISIBLE = 1;
    public const USER_VISIBLE = 2;
    public const SUCCESS = 'success';
    public const WARNING = 'warning';
    public const DANGER = 'danger';
    public const INFO = 'info';
    public const PRIMARY = 'primary';
    public const SECONDARY = 'secondary';
    public const DARK = 'dark';
    public const TYPES = [
        0 => self::DANGER,
        1 => self::SUCCESS,
        2 => self::WARNING,
        3 => self::INFO,
        4 => self::PRIMARY,
        5 => self::SECONDARY
    ];

    public $type = null;
    public int $xTimes = 1;
    /* @TODO remove status -- never used for anything OR create a status definitions */
    public $status = 'undefined';
    public string $desc = '';
    public string $title = '';
    public bool $oneTime = true;
    public bool $closable = true;
    public int $visibility = Message::USER_VISIBLE;

    public function __construct($type, $title, $desc, $visibility, $oneTime, $closable)
    {
        if (!in_array($type, self::TYPES) && !key_exists($type, self::TYPES)) {
            throw new \InvalidArgumentException('Type wrongly specified!');
        }

        $skipArray = ['visibility'];
        $this->oneTime = $oneTime;
        $this->type = strtolower($type);
        $this->title = $title;
        $this->desc = $desc;
        $this->visibility = $visibility;
        $this->closable = $closable;
    }

    public function show()
    {
        if (!isset($this->type) || !isset($this->status) || empty($this->desc) || empty($this->title) || !isset($this->closable)) {
            return false;
        }

        $msg = sprintf('<div class="alert alert-%s alert-dismissible fade show" role="alert"><strong>', $this->type);

        $msg .= $this->getRepeatedCount();
        $msg .= $this->getDescription();
        if ($this->closable) {
            $msg .= $this->getCloseIcon();
        }
        $msg .= '</div>';
        return $msg;
    }

    protected function getRepeatedCount(): string
    {
        return $this->xTimes > 1 ? '<i>x' . $this->xTimes . '</i> ' : '';
    }

    protected function getDescription(): string
    {
        return $this->title . '</strong> <br><p class="notification-text-padding">' . $this->desc . '</p>';
    }

    protected function getCloseIcon(): string
    {
        return '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    }

    public static function compare(&$obj1, &$obj2): int
    {
        if ($obj1->type != $obj2->type) {
            return 0;
        }
        if ($obj1->desc != $obj2->desc) {
            return 0;
        }
        if ($obj1->title != $obj2->title) {
            return 0;
        }
        if ($obj1->oneTime != $obj2->oneTime) {
            return 0;
        }
        if ($obj1->closable != $obj2->closable) {
            return 0;
        }
        return 1;
    }

    public function addXTimes()
    {
        $this->xTimes++;
    }

    public function setVisibility(int $visibility): void
    {
        if ($visibility === self::ADMIN_VISIBLE || $visibility === self::USER_VISIBLE) {
            $this->visibility = $visibility;
        }
    }
}

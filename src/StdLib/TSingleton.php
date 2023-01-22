<?php
namespace Pipe\StdLib;

trait TSingleton
{
    /** @var $this */
    static protected $instance = null;

    /**
     * @return $this
     */
    static public function getInstance()
    {
        return self::$instance ?? self::$instance = (new self());
    }
}
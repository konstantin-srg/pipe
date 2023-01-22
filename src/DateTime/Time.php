<?php

namespace Pipe\DateTime;

use Pipe\String\Numbers;

class Time extends Cache
{
  protected function __construct($data = '00:00:00')
  {
    $this->parse($data);
  }

  /**
   * @param $data
   * @return Time
   * @throws \Exception
   */
  static public function getTime($data = '00:00:00')
  {
    if (is_string($data) || $data instanceof \DateTime) {
      $dt = new self($data);
    } elseif ($data instanceof self) {
      $dt = clone $data;
    } else {
      throw new \Exception('Unknown data type: ' . $data);
    }

    return $dt;
  }

  public function getString()
  {
    $str = '';

    if ($h = $this->getHours()) {
      $str .= Numbers::declensionRu($h, ['час', 'часа', 'часов']);
    }

    if ($m = $this->getMinutes()) {
      $str .= ' ' . Numbers::declensionRu($m, ['минута', 'минуты', 'минут']);
    }

    return ltrim($str);
  }

  /**
   * @param string $format
   * @return string
   */
  public function format($format = 'H:i:s')
  {
    return parent::format($format);
  }

  /**
   * @return int
   */
  public function minutes()
  {
    return (int)$this->format('i');
  }

  /**
   * @return int
   */
  public function hours()
  {
    return (int)$this->format('G');
  }

  /**
   * @return bool
   */
  public function isEmpty()
  {
    if (!$this->dt) {
      return false;
    }

    return $this->dt->format('H:i:s') == '00:00:00';
  }

  /**
   * @param $dt
   * @return $this
   * @throws \Exception
   */
  public function addition($dt)
  {
    $dt = $this->getDtObj();
    $dt->modify('+ ' . $dt->format('H') . ' hours');
    $dt->modify('+ ' . $dt->format('i') . ' minutes');

    return $this;
  }

  /**
   * @param $dt
   * @return $this
   * @throws \Exception
   */
  public function subtraction($dt)
  {
    $dt = $this->getDtObj();
    $dt->modify('- ' . $dt->format('H') . ' hours');
    $dt->modify('- ' . $dt->format('i') . ' minutes');

    return $this;
  }

  /**
   * @param $nbr
   * @return $this
   */
  public function divide($nbr = 2)
  {
    $h = $this->hours();
    $m = $this->minutes() / 2;

    if ($h && ($rem = $h % $nbr)) {
      $m += $rem * 60 / $nbr;
      $h -= $rem;
    }

    $h /= $nbr;

    $this->dt->setTime($h, $m);

    return $this;
  }

  /**
   * @param null $hours
   * @param null $minutes
   * @return $this
   */
  public function setTime($hours = null, $minutes = null)
  {
    $this->dt->setTime(
      $hours === null ? $this->format('G') : $hours,
      $minutes === null ? $this->format('i') : $minutes
    );

    return $this;
  }

  /**
   * @param string $round
   * @param string $direction
   * @return $this
   */
  public function round($direction = 'auto', $round = 'hours')
  {
    switch ($direction) {
      case 'auto':
        if ($this->dt->format('i') > 29) {
          $this->dt->modify('+1 hour');
        }
        break;
      case 'up':
        if ($this->dt->format('i') > 0) {
          $this->dt->modify('+1 hour');
          $this->setTime(null, 0);
        }
        break;
      case 'down':
        if ($this->dt->format('i') > 0) {
          $this->setTime(null, 0);
        }
        break;
    }

    $this->dt->setTime($this->dt->format('H'), 0);

    return $this;
  }

  public function getMultiplier()
  {
    $multiplier = (float)$this->dt->format('G') + ((float)ltrim($this->dt->format('i'), 0) / 60);

    return $multiplier;
  }

  protected function parse($str)
  {
    if ($str instanceof \DateTime) {
      $this->dt = $str;
      return $this;
    }

    $str = str_replace('.', ':', $str);
    if (!$str) $str = '00:00:00';

    $dt = \DateTime::createFromFormat('H:i:s', $str);
    if (!$dt) {
      $dt = \DateTime::createFromFormat('H:i', $str);
      if (!$dt) {
        $dt = \DateTime::createFromFormat('H', $str);
      }
    }

    $this->dt = $dt;

    return $this;
  }
}
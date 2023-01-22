<?php
namespace Pipe\DateTime;

use Pipe\String\Numbers;

class AbstractDt
{
  /**
   * @var \DateTime
   */
  protected $dt;

  /**
   * @return \DateTime
   */
  public function getDtObj()
  {
    return $this->dt;
  }

  /**
   * @return bool
   */
  public function has()
  {
    return (bool) $this->dt;
  }

  /**
   * @param \DateTime $dt
   * @return $this
   */
  public function setDtObj($dt)
  {
    $this->dt = $dt;

    return $this;
  }

  /**
   * @param $modify
   * @return $this
   */
  public function modify($modify)
  {
    $this->dt->modify($modify);

    return $this;
  }

  /**
   * @param string $format
   * @return string
   */
  public function format($format = 'Y-m-d')
  {
    return $this->dt ? $this->dt->format($format) : '';
  }

  public function __clone()
  {
    $this->dt = clone $this->dt;
  }

  public function __toString()
  {
    return $this->format();
  }
}
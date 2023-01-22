<?php
namespace Pipe\DateTime;

class Date extends Cache
{
    protected function __construct($data = '0000-00-00') {
        $this->parse($data);
    }

    /**
     * @param string $data
     * @return Date|string
     * @throws \Exception
     */
    static public function getDate($data = '0000-00-00')
    {
        if(is_string($data) || $data instanceof \DateTime) {
            $dt = new self($data);
            $dt->parse($data);
        } elseif($data instanceof self) {
            $dt = clone $data;
        } else {
            throw new \Exception('Unknown data type');
        }

        return $dt;
    }

    /**
     * @param $str
     * @return $this
     * @throws \Exception
     */
    protected function parse($str)
    {
        if($str instanceof \DateTime) {
            $this->dt = $str;
            return $this;
        }

        if($str == 'NOW') {
            $dt = new \DateTime();
        } else {
            $dt = \DateTime::createFromFormat('d.m.Y', $str);
            if (!$dt) {
                $dt = \DateTime::createFromFormat('Y-m-d', $str);
                if (!$dt) {
                    $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $str);
                    if (!$dt) {
                        $dt = \DateTime::createFromFormat('d.m.Y', $str . '.0000');
                    }
                }
            }
        }

        $this->dt = $dt;

        return $this;
    }

    public function year()
    {
        return $this->format('Y');
    }

    public function month($zero = true)
    {
        $month = $this->format('m');
        return $zero ? $month : ltrim($month, '0');
    }

    public function day($zero = true)
    {
        $day = $this->format('d');
        return $zero ? $day : ltrim($day, '0');
    }

    /**
     * @param string $format
     * @return string
     */
    public function format($format = 'Y-m-d')
    {
      return parent::format($format);
    }
}
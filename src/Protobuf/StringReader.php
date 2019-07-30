<?php

namespace Protobuf;

class StringReader extends  AbstractReader
{
    protected $data = '';
    protected $length = 0;
    protected $position = 0;

    /**
     * StringReader constructor.
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->length = mb_strlen($data, '8bit');
        $this->position = 0;
    }

    /**
     * Read next one byte.
     * @return int
     */
    public function read()
    {
        if ($this->position >= $this->length) {
            return false;
        }
        $this->fireReadListener();
        return ord(mb_substr($this->data, $this->position++, 1, '8bit'));
    }

    /**
     * Read next $l byte into a string
     * @param $l
     * @return string
     */
    public function readRaw($l)
    {
        $r = mb_substr($this->data, $this->position, $l, '8bit');
        $this->position += $l;
        $this->fireReadListener();
        return $r;
    }

    /**
     * Return data length
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Return reading position
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}

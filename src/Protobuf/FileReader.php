<?php

namespace Protobuf;

class FileReader extends  AbstractReader
{
    /** @var resource  */
    private $handle = null;
    private $buffer = '';
    private $bufferSize = 0;
    private $length = 0;
    private $position = 0;

    /**
     * FileReader constructor.
     * @param string $fileName
     * @param int $bufferSize
     */
    public function __construct($fileName, $bufferSize = 1048576)
    {
        $this->length = filesize($fileName);
        $this->position = 0;
        $this->bufferSize = $bufferSize;
        $this->handle = fopen($fileName, 'r');
        $this->buffer = fread($this->handle, $this->length);
    }

    /**
     * Read next one byte.
     * @return int
     */
    public function read()
    {
        if ($this->position >= $this->length) {
            $this->close();
            return false;
        }
        return ord(mb_substr($this->buffer, $this->position++, 1, '8bit'));
    }

    /**
     * Read next $l byte into a string
     * @param $l
     * @return string
     */
    public function readRaw($l)
    {
        $r = mb_substr($this->buffer, $this->position, $l, '8bit');
        $this->position += $l;
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

    /**
     * Close file
     * @return bool
     */
    public function close()
    {
        return fclose($this->handle);
    }
}

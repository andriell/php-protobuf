<?php


namespace Protobuf;


interface ReaderInterface
{
    /**
     * Read next one byte.
     * @return int
     */
    public function read();

    /**
     * Read next $l byte into a string
     * @param $l
     * @return string
     */
    public function readRaw($l);

    /**
     * Read next LittleEndian32 data
     * @return string
     */
    public function readLittleEndian32();

    /**
     * Read next LittleEndian64 data
     * @return mixed
     */
    public function readLittleEndian64();

    /**
     * Read next Varint32 data
     * @return int
     */
    public function readVarint32();

    /**
     * Read next Varint64 data
     * @return int
     */
    public function readVarint64();

    /**
     * Return data length
     * @return int
     */
    public function getLength();

    /**
     * Return reading position
     * @return int
     */
    public function getPosition();
}
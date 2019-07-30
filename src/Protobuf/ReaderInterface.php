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
     * Read next Int64 data
     * @return int
     */
    public function readInt64();

    /**
     * Read next Double data
     * @return int
     */
    public function readDouble();

    /**
     * Read next Float data
     * @return float
     */
    public function readFloat();

    /**
     * Read next Bool data
     * @return bool
     */
    public function readBool();

    /**
     * Read next Sfixed32 data
     * @return int
     */
    public function readSfixed32();

    /**
     * Read next Sfixed64 data
     * @return int
     */
    public function readSfixed64();

    /**
     * Read next Sint32 data
     * @return int
     */
    public function readSint32();

    /**
     * Read next Sint64 data
     * @return int
     */
    public function readSint64();

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
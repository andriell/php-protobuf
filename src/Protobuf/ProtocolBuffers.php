<?php

namespace Protobuf;

class ProtocolBuffers
{
    const TAG_TYPE_BITS = 3;

    const VAR_INT = 0;
    const FIXED64 = 1;
    const LENGTH_DELIMITED = 2;
    const START_GROUP = 3;
    const END_GROUP = 4;
    const FIXED32 = 5;


    /** @var StringReader */
    private $reader = null;
    private $tagMask = '';
    private $typeMask = '';

    /**
     * ProtocolBuffers constructor.
     * @param StringReader $reader
     */
    public function __construct($reader)
    {
        $this->reader = $reader;
        $this->tagMask = hex2bin('F8');
        $this->typeMask = hex2bin('07');
    }

    public static function getTagFieldNumber($tag)
    {
        return ($tag >> self::TAG_TYPE_BITS) &
            (1 << ((PHP_INT_SIZE * 8) - self::TAG_TYPE_BITS)) - 1;
    }

    public static function getTagWireType($tag)
    {
        return $tag & 0x7;
    }

    /**
     * @param array $map
     * @return array
     * @throws \Exception
     */
    public function parse($map = array())
    {
        $map = is_array($map) ? $map : array();
        $r = array();
        while ($bite = $this->reader->readVarint32()) {
            $number = self::getTagFieldNumber($bite);
            $tag = self::getTagWireType($bite);
            //echo dechex($bite) . ' ' . $number . ' - ' . $tag . "\n";
            if ($tag == self::VAR_INT) {
                $r[$number][] = $this->reader->readVarint64();
            } elseif ($tag == self::FIXED32) {
                $r[$number][] = $this->reader->readLittleEndian32();
            } elseif ($tag == self::FIXED64) {
                $r[$number][] = $this->reader->readLittleEndian64();
            } elseif ($tag == self::LENGTH_DELIMITED) {
                $l = $this->reader->readVarint32();
                $val = $this->reader->readRaw($l);
                if (isset($map[$number])) {
                    $pb = new ProtocolBuffers(new StringReader($val));
                    $val = $pb->parse($map[$number]);
                }
                $r[$number][] = $val;
            } else {
                //echo 'Unexpected wire type ' . $tag . ".\n";
                throw new \Exception('Unexpected wire type ' . $tag . '.');
            }
        }
        return $r;
    }

}
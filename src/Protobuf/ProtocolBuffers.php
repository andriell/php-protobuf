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
    protected $reader = null;
    protected $messages = array();

    /**
     * ProtocolBuffers constructor.
     * @param StringReader $reader
     * @param array $messages
     */
    public function __construct($reader, $messages)
    {
        $this->reader = $reader;
        $this->messages = $messages;
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
     * @param string $message
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function parse($message, $limit = 0)
    {
        $limit = empty($limit) ? $this->reader->getLength() : $limit;
        $r = array();
        $message = isset($this->messages[$message]) ? $this->messages[$message] : array();
        while ($this->reader->getPosition() < $limit) {
            $bite = $this->reader->readVarint32();
            $tag = self::getTagWireType($bite);

            $number = self::getTagFieldNumber($bite);
            $field = isset($message[$number]) ? $message[$number] : array();
            $name = isset($field['name']) ? $message[$number]['name'] : $number;

            //echo dechex($bite) . ' ' . $number . ' - ' . $tag . "\n";
            if ($tag == self::VAR_INT) {
                $val = $this->reader->readVarint64();
            } elseif ($tag == self::FIXED32) {
                $val = $this->reader->readLittleEndian32();
            } elseif ($tag == self::FIXED64) {
                $val = $this->reader->readLittleEndian64();
            } elseif ($tag == self::LENGTH_DELIMITED) {
                $l = $this->reader->readVarint32();
                if (isset($field['message'])) {
                    if (isset($field['packed']) && $field['packed']) {
                        $type = $field['type'];
                        $val = $this->parsePacked($type, $this->reader->getPosition() + $l);
                    } else {
                        $val = $this->parse($field['message'], $this->reader->getPosition() + $l);
                    }
                } else {
                    $val = $this->reader->readRaw($l);
                }
            } else {
                //echo 'Unexpected wire type ' . $tag . ".\n";
                throw new \Exception('Unexpected wire type ' . $tag . '.');
            }
            if (isset($field['repeated']) && empty($field['repeated'])) {
                $r[$name] = $val;
            } else {
                $r[$name][] = $val;
            }
        }
        return $r;
    }

    /**
     * @param $type
     * @param $limit
     * @return array
     */
    public function parsePacked($type, $limit)
    {
        $r = array();
        if ($type == GPBType::DOUBLE) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readDouble();
            }
        } elseif ($type == GPBType::FLOAT) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readFloat();
            }
        } elseif ($type == GPBType::INT64 || $type == GPBType::UINT64) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readInt64();
            }
        } elseif ($type == GPBType::INT32) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readVarint32();
            }
        } elseif ($type == GPBType::FIXED64) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readLittleEndian64();
            }
        } elseif ($type == GPBType::FIXED32) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readLittleEndian32();
            }
        } elseif ($type == GPBType::STRING || $type == GPBType::MESSAGE || $type == GPBType::BYTES) {
            while ($this->reader->getPosition() < $limit) {
                $l = $this->reader->readVarint32();
                $r[] = $this->reader->readRaw($l);
            }
        } elseif ($type == GPBType::GROUP) {
            trigger_error("Not implemented.", E_ERROR);
        } elseif ($type == GPBType::UINT32 || $type == GPBType::ENUM) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readVarint32();
            }
        } elseif ($type == GPBType::SFIXED32) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readSfixed32();
            }
        } elseif ($type == GPBType::SFIXED64) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readSfixed64();
            }
        } elseif ($type == GPBType::SINT32) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readSint32();
            }
        } elseif ($type == GPBType::SINT64) {
            while ($this->reader->getPosition() < $limit) {
                $r[] = $this->reader->readSint64();
            }
        } else {
            user_error("Unsupported type.");
        }
        return $r;

    }

}
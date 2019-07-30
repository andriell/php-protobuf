<?php

namespace Protobuf;

abstract class AbstractReader implements ReaderInterface
{
    const MAX_VAR_INT_BYTES = 10;

    /** @var callable */
    protected $readListener = null;
    protected $readListenerNextCall = 0;
    protected $readListenerStep = 1048576;

    public abstract function read();

    public abstract function readRaw($l);

    public abstract function getLength();

    public abstract function getPosition();

    public static function stringListener($position, $length)
    {
        $time = time() - $GLOBALS['start_time'];
        $timeLeft = 0;
        if ($position >= 1024 * 1024) {
            $timeLeft = $time * $length / $position - $time;
        }
        $a1 = round($position / $length * 100, 2);
        $a2 = Pbf::formatBytes($position);
        $a3 = Pbf::formatBytes($length);
        $a4 = Pbf::formatBytes(memory_get_usage());
        $a5 = Pbf::formatTime($time);
        $a6 = Pbf::formatTime($timeLeft);
        return sprintf("Progress: %+6s%%    [ %+10s / %+10s ]    Memory usage: %+10s    Time: %+9s    Time left: %+9s\n", $a1, $a2, $a3, $a4, $a5, $a6);
    }

    public static function echoListener($position, $length)
    {
        echo self::stringListener($position, $length);
    }

    /**
     * @return callable
     */
    public function getReadListener()
    {
        return $this->readListener;
    }

    /**
     * @param callable $readListener
     */
    public function setReadListener($readListener)
    {
        $this->readListener = $readListener;
    }

    /**
     * @return int
     */
    public function getReadListenerStep()
    {
        return $this->readListenerStep;
    }

    /**
     * @param int $readListenerStep
     */
    public function setReadListenerStep($readListenerStep)
    {
        $this->readListenerStep = $readListenerStep;
    }

    /**
     * @return int
     */
    public function getReadListenerNextCall()
    {
        return $this->readListenerNextCall;
    }

    /**
     * @param int $readListenerNextCall
     */
    public function setReadListenerNextCall($readListenerNextCall)
    {
        $this->readListenerNextCall = $readListenerNextCall;
    }

    protected function updateReadListener()
    {
        if ($this->readListenerNextCall < $this->getPosition()) {
            if (is_callable($this->readListener)) {
                call_user_func($this->readListener, $this->getPosition(), $this->getLength());
            }
            $this->readListenerNextCall = min($this->getPosition() + $this->readListenerStep, $this->getLength() - 1);
        }
    }

    public static function combineInt32ToInt64($high, $low)
    {
        $isNeg = $high < 0;
        if ($isNeg) {
            $high = ~$high;
            $low = ~$low;
            $low++;
            if (!$low) {
                $high = (int)($high + 1);
            }
        }
        $result = bcadd(bcmul($high, 4294967296), $low);
        if ($low < 0) {
            $result = bcadd($result, 4294967296);
        }
        if ($isNeg) {
            $result = bcsub(0, $result);
        }
        return $result;
    }

    public static function zigZagDecode32($uint32)
    {
        // Fill high 32 bits.
        if (PHP_INT_SIZE === 8) {
            $uint32 |= ($uint32 & 0xFFFFFFFF);
        }

        $int32 = (($uint32 >> 1) & 0x7FFFFFFF) ^ (-($uint32 & 1));

        return $int32;
    }

    /**
     * Read next LittleEndian32 data
     * @return string
     */
    public function readLittleEndian32()
    {
        $data = $this->readRaw(4);
        if ($data === false) {
            return false;
        }
        $var = unpack('V', $data);
        return $var[1];
    }

    /**
     * Read next LittleEndian64 data
     * @return string
     */
    public function readLittleEndian64()
    {
        $data = $this->readRaw(4);
        if ($data === false) {
            return false;
        }
        $low = unpack('V', $data);
        $low = $low[1];
        $data = $this->readRaw(4);
        if ($data === false) {
            return false;
        }
        $high = unpack('V', $data);
        $high = $high[1];
        if (PHP_INT_SIZE == 4) {
            return self::combineInt32ToInt64($high, $low);
        }
        return ($high << 32) | $low;
    }


    /**
     * Read next Varint32 data
     * @return int
     */
    public function readVarint32()
    {
        $var = $this->readVarint64();

        if ($var === false) {
            return false;
        }

        if (PHP_INT_SIZE == 4) {
            $var = bcmod($var, 4294967296);
        } else {
            $var &= 0xFFFFFFFF;
        }

        // Convert large uint32 to int32.
        if ($var > 0x7FFFFFFF) {
            if (PHP_INT_SIZE === 8) {
                $var = $var | (0xFFFFFFFF << 32);
            } else {
                $var = bcsub($var, 4294967296);
            }
        }
        return intval($var);
    }

    /**
     * Read next Varint64 data
     * @return int
     */
    public function readVarint64()
    {
        $count = 0;

        if (PHP_INT_SIZE == 4) {
            $high = 0;
            $low = 0;
            do {
                if ($count === self::MAX_VAR_INT_BYTES) {
                    return false;
                }
                $b = $this->read();
                if ($b === false) {
                    return false;
                }
                $bits = 7 * $count;
                if ($bits >= 32) {
                    $high |= (($b & 0x7F) << ($bits - 32));
                } else if ($bits > 25) {
                    // $bits is 28 in this case.
                    $low |= (($b & 0x7F) << 28);
                    $high = ($b & 0x7F) >> 4;
                } else {
                    $low |= (($b & 0x7F) << $bits);
                }

                $count += 1;
            } while ($b & 0x80);

            $var = self::combineInt32ToInt64($high, $low);
            if (bccomp($var, 0) < 0) {
                $var = bcadd($var, "18446744073709551616");
            }
        } else {
            $var = 0;
            $shift = 0;

            do {
                if ($count === self::MAX_VAR_INT_BYTES) {
                    return false;
                }
                $byte = $this->read();
                if ($byte === false) {
                    return false;
                }
                $var |= ($byte & 0x7f) << $shift;
                $shift += 7;
                $count += 1;
            } while ($byte > 0x7f);

        }

        return $var;
    }

    public function readInt64()
    {
        $var = $this->readVarint64();
        if ($var === false) {
            return false;
        }
        if (PHP_INT_SIZE == 4 && bccomp($var, "9223372036854775807") > 0) {
            $var = bcsub($var, "18446744073709551616");
        }
        return $var;
    }

    public function readDouble()
    {
        $data = $this->readRaw(8);
        if ($data === false) {
            return false;
        }

        $value = unpack('d', $data);
        return $value[1];
    }

    public function readFloat()
    {
        $data = $this->readRaw(4);
        if ($data === false) {
            return false;
        }
        $value = unpack('f', $data);
        return $value[1];
    }

    public function readBool()
    {
        $var = $this->readVarint64();

        if ($var === false) {
            return false;
        }
        return (bool) $var;
    }

    public function readSfixed32()
    {
        $var = $this->readLittleEndian32();
        if ($var === false) {
            return false;
        }

        if (PHP_INT_SIZE === 8) {
            $var |= (-($var >> 31) << 32);
        }
        return $var;
    }

    public function readSfixed64()
    {
        $var = $this->readLittleEndian32();
        if ($var === false) {
            return false;
        }
        if (PHP_INT_SIZE == 4 && bccomp($var, "9223372036854775807") > 0) {
            $var = bcsub($var, "18446744073709551616");
        }
        return $var;
    }

    public function readSint32()
    {
        $var = $this->readVarint32();
        if ($var === false) {
            return false;
        }
        return self::zigZagDecode32($var);
    }

    public function readSint64()
    {
        $var = $this->readVarint64();
        if ($var === false) {
            return false;
        }
        return self::zigZagDecode32($var);
    }
}

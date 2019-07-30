<?php

namespace Protobuf;

class Pbf
{
    const NAME = 'name';
    const MESSAGE = 'message';
    const REPEATED = 'repeated';
    const TYPE = 'type';
    const PACKED = 'packed';

    public static function formatBytes($bytes) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $i = 0;
        while ($bytes >= 1024) {
            $bytes = $bytes / 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

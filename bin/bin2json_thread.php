<?php

use Protobuf\FileReader;
use Protobuf\ProtocolBuffers;

if (PHP_INT_SIZE != 8) {
    echo 'This is only for x64 systems';
    exit(1);
}

include_once __DIR__ . '/../vendor/autoload.php';

$messages = require __DIR__ . '/../src/osm_messages.php';

if (!isset($argv[1])) {
    echo 'First argument is path to dir with bin files';
    exit(1);
}
$GLOBALS['bin_dir'] = $argv[1];
if (!is_dir($GLOBALS['bin_dir'])) {
    echo 'Is not a dir: ' . $GLOBALS['bin_dir'];
    exit(1);
}
$GLOBALS['thread_count'] = intval($argv[2]);
$GLOBALS['thread_id'] = intval($argv[3]);
$GLOBALS['start_time'] = time();

$fileList = array();
$dh = opendir($GLOBALS['bin_dir']);
while (($file = readdir($dh)) !== false) {
    if (substr($file, 0, 14) == 'PrimitiveBlock' && substr($file, -4) == '.bin') {
        $fileList[$file] = filesize($GLOBALS['bin_dir'] . '/' . $file);
    }
}
closedir($dh);
ksort($fileList);

$GLOBALS['total_size'] = 0;
$GLOBALS['position'] = 0;
$i = $GLOBALS['thread_id'];
foreach ($fileList as $file => $size) {
    if ($i % $GLOBALS['thread_count'] == 0) {
        $GLOBALS['total_size'] += $size;
    } else {
        unset($fileList[$file]);
    }
    $i++;
}

foreach ($fileList as $file => $size) {
    $reader = new FileReader($GLOBALS['bin_dir'] . '/' . $file);
    $reader->setReadListener(function ($position, $length) {
        $str = json_encode(array(
            'pid' => getmypid(),
            'position' => $position + $GLOBALS['position'],
            'total_size' => $GLOBALS['total_size'],
            'start_time' => $GLOBALS['start_time'],
            'memory_usage' => memory_get_usage(),
        ));
        file_put_contents($GLOBALS['bin_dir'] . '/thread_' . $GLOBALS['thread_id'] . '.json', $str);
    });
    $GLOBALS['position'] += $size;

    $pb = new ProtocolBuffers($reader, $messages);
    $primitiveBlock = $pb->parse('PrimitiveBlock');
    file_put_contents($GLOBALS['bin_dir'] . '/' . $file . '.json', json_encode($primitiveBlock));
}

$handle = fopen($GLOBALS['bin_dir'], 'r');
$fileSize = filesize($GLOBALS['bin_dir']);

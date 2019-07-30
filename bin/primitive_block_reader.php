<?php

use Protobuf\FileReader;
use Protobuf\Pbf;
use Protobuf\ProtocolBuffers;

if (PHP_INT_SIZE != 8) {
    echo 'This is only for x64 systems';
    exit(1);
}

include_once __DIR__ . '/../vendor/autoload.php';

$messages = require __DIR__ . '/../src/osm_messages.php';

if (!(isset($argv[1]))) {
    echo 'First argument is path to bin file';
    exit(1);
}
$fileBin = $argv[1];
if (!is_file($fileBin)) {
    echo 'Is not a file: ' . $fileBin;
    exit(1);
}

$errors = ProtocolBuffers::validateMessages($messages);
if ($errors) {
    echo "Errors:\n";
    print_r($errors);
    exit(1);
}

$GLOBALS['start_time'] = time();
$reader = new FileReader($fileBin);
$reader->setReadListener(array('Protobuf\AbstractReader', 'echoListener'));
$reader->setReadListenerStep(1024 * 1024);
$p = new ProtocolBuffers($reader, $messages);
$r = $p->parse('PrimitiveBlock');

echo 'Memory peak usage: ' . Pbf::formatBytes(memory_get_peak_usage()) . "\n";
echo 'Start saving' . "\n";
file_put_contents($fileBin . '.json', json_encode($r));

echo 'End' . "\n";
echo 'Memory peak usage: ' . Pbf::formatBytes(memory_get_peak_usage()) . "\n";

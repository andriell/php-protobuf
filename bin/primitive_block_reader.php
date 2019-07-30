<?php

use Protobuf\FileReader;
use Protobuf\Pbf;
use Protobuf\ProtocolBuffers;

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

$reader = new FileReader($fileBin);
$reader->setReadListener(function ($position, $length) {
    echo 'Progress: ' . round($position / $length * 100, 2) . '% [ ' . Pbf::formatBytes($position) . ' / ' . Pbf::formatBytes($length) . ' ] Memory usage: ' . Pbf::formatBytes(memory_get_usage()) . "\n";
});
$reader->setReadListenerStep(1024 * 1024);
$p = new ProtocolBuffers($reader, $messages);
$r = $p->parse('PrimitiveBlock');

echo 'Memory peak usage: ' . Pbf::formatBytes(memory_get_peak_usage()) . "\n";
echo 'Start saving' . "\n";
file_put_contents($fileBin . '.json', json_encode($r));

echo 'End' . "\n";
echo 'Memory peak usage: ' . Pbf::formatBytes(memory_get_peak_usage()) . "\n";

<?php

use Protobuf\FileReader;
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

$p = new ProtocolBuffers($reader, $messages);
$r = $p->parse('PrimitiveBlock');

file_put_contents($fileBin . '.json', json_encode($r));

<?php

use Protobuf\Pbf;
use Protobuf\ProtocolBuffers;
use Protobuf\StringReader;

if (PHP_INT_SIZE != 8) {
    echo 'This is only for x64 systems';
    exit(1);
}

include_once __DIR__ . '/../vendor/autoload.php';

$messages = require __DIR__ . '/../src/osm_messages.php';

if (!(isset($argv[1]) && isset($argv[2]))) {
    echo 'First argument is path to pbf file, second argument is dir to save';
    exit(1);
}
$filePbf = $argv[1];
if (!is_file($filePbf)) {
    echo 'Is not a file: ' . $filePbf;
    exit(1);
}
$saveDir = $argv[2];
if (!is_dir($saveDir)) {
    echo 'Is not a dir: ' . $saveDir;
    exit(1);
}

$handle = fopen($filePbf, 'r');
$fileSize = filesize($filePbf);

$headerBlockCount = 0;
$primitiveBlockCount = 0;
$position = 0;
$GLOBALS['start_time'] = time();

while (!feof($handle)) {
    $lengthBin = fread($handle, 4);
    $position += 4;

    $lengthInt = hexdec(bin2hex($lengthBin));
    if ($lengthInt == 0) {
        break;
    }

    $blobHeaderBin = fread($handle, $lengthInt);
    $position += $lengthInt;
    $blobHeaderPb = new ProtocolBuffers(new StringReader($blobHeaderBin), $messages);
    $blobHeader = $blobHeaderPb->parse('BlobHeader');

    $blobBin = fread($handle, $blobHeader['datasize']);
    $position += $blobHeader['datasize'];
    $blobPb = new ProtocolBuffers(new StringReader($blobBin), $messages);
    $blob = $blobPb->parse('Blob');

    $data = $blob['zlib_data'];

    if (empty($blob['raw'])) {
        $data = zlib_decode($data);
    }
    if ($blobHeader['type'] == 'OSMHeader') {
        $headerBlockCount++;
        file_put_contents($saveDir . sprintf("/HeaderBlock%06d.bin", $headerBlockCount), $data);
        $headerBlockPb = new ProtocolBuffers(new StringReader($data), $messages);
        $headerBlock = $headerBlockPb->parse('HeaderBlock');
        file_put_contents($saveDir . sprintf("/HeaderBlock%06d.bin.json", $headerBlockCount), json_encode($headerBlock));
    } elseif ($blobHeader['type'] == 'OSMData') {
        $primitiveBlockCount++;
        file_put_contents($saveDir . sprintf("/PrimitiveBlock%06d.bin", $primitiveBlockCount), $data);
    } else {
        echo 'Error: undefined BlobHeader type ' . $blobHeader['type'] . "\n";
    }
    StringReader::echoListener($position, $fileSize);
}

echo "End\n";
echo 'Memory peak usage: ' . Pbf::formatBytes(memory_get_peak_usage()) . "\n";

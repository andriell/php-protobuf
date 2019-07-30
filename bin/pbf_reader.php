<?php


use Protobuf\ProtocolBuffers;
use Protobuf\StringReader;

include __DIR__ . '/../vendor/autoload.php';

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

$headerBlockCount = 0;
$primitiveBlockCount = 0;

while (!feof($handle)) {
    $lengthBin = fread($handle, 4);

    $lengthInt = hexdec(bin2hex($lengthBin));
    if ($lengthInt == 0) {
        echo "End\n";
        break;
    }

    $blobHeaderBin = fread($handle, $lengthInt);
    $blobHeaderPb = new ProtocolBuffers(new StringReader($blobHeaderBin), $messages);
    $blobHeader = $blobHeaderPb->parse('BlobHeader');

    echo 'BlobHeader ' . $blobHeader['type'] . "\n";

    $blobBin = fread($handle, $blobHeader['datasize']);
    $blobPb = new ProtocolBuffers(new StringReader($blobBin), $messages);
    $blob = $blobPb->parse('Blob');

    $data = $blob['zlib_data'];

    if (empty($blob['raw'])) {
        $data = zlib_decode($data);
    }
    if ($blobHeader['type'] == 'OSMHeader') {
        $headerBlockCount++;
        file_put_contents($saveDir . '/HeaderBlock' . $headerBlockCount . '.bin', $data);
        $headerBlockPb = new ProtocolBuffers(new StringReader($data), $messages);
        $headerBlock = $headerBlockPb->parse('HeaderBlock');
        file_put_contents($saveDir . '/HeaderBlock' . $headerBlockCount . '.json', json_encode($headerBlock));
        echo 'Write HeaderBlock' . $headerBlockCount . ' ' . strlen($data) . "\n";
    } elseif ($blobHeader['type'] == 'OSMData') {
        $primitiveBlockCount++;
        file_put_contents($saveDir . '/PrimitiveBlock' . $primitiveBlockCount . '.bin', $data);
        echo 'Write PrimitiveBlock' . $primitiveBlockCount . ' ' . strlen($data) . "\n";
    } else {
        echo 'Error: undefined BlobHeader type ' . $blobHeader['type'] . "\n";
    }
}

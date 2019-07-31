<?php

use Protobuf\Pbf;

if (PHP_INT_SIZE != 8) {
    echo 'This is only for x64 systems';
    exit(1);
}

include_once __DIR__ . '/../vendor/autoload.php';

if (!isset($argv[1])) {
    echo 'First argument is path to dir with bin files';
    exit(1);
}
$binDir = $argv[1];
if (!is_dir($binDir)) {
    echo 'Is not a dir: ' . $binDir;
    exit(1);
}
$threadCount = intval($argv[2]);

function execInBackground($cmd)
{
    if (substr(php_uname(), 0, 7) == "Windows") {
        pclose(popen("start /B " . $cmd, "r"));
    } else {
        exec($cmd . " > /dev/null 2>&1 &");
    }
}

for ($i = 0; $i < $threadCount; $i++) {
    $command = 'C:\server\php7.1\php.exe -d memory_limit=-1 ' . __DIR__ . DIRECTORY_SEPARATOR . 'bin2json_thread.php ' . $binDir . ' ' . $threadCount . ' ' . $i . '';
    echo $command . "\n";
    execInBackground($command);
}

while (true) {
    sleep(1);
    $isEnd = true;
    for ($i = 0; $i < $threadCount; $i++) {
        $file = $binDir . '/thread_' . $i . '.json';
        if (!is_file($file)) {
            $isEnd = false;
            continue;
        }
        $str = file_get_contents($file);
        $array = json_decode($str, true);
        $time = time() - $array['start_time'];
        $timeLeft = 0;
        if ($array['position'] >= 1024 * 1024) {
            $timeLeft = $time * $array['total_size'] / $array['position'] - $time;
        }
        $a0 = $array['pid'];
        $a1 = round($array['position'] / $array['total_size'] * 100, 2);
        $a2 = Pbf::formatBytes($array['position']);
        $a3 = Pbf::formatBytes($array['total_size']);
        $a4 = Pbf::formatBytes(memory_get_usage());
        $a5 = Pbf::formatTime($time);
        $a6 = Pbf::formatTime($timeLeft);
        echo sprintf("%03d    Pid: %+6s    Progress: %+6s%%    [ %+10s / %+10s ]    Memory usage: %+10s    Time: %+9s    Time left: %+9s    \n", $i, $a0, $a1, $a2, $a3, $a4, $a5, $a6);

        if ($array['position'] < $array['total_size']) {
            $isEnd = false;
        }
    }
    echo chr(27) . chr(91) . $threadCount . 'A';
    if ($isEnd) {
        break;
    }
}
for ($i = 0; $i < $threadCount; $i++) {
    $file = $binDir . '/thread_' . $i . '.json';
    unlink($file);
}

echo 'End' . "\n";

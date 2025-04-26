<?php
$directory = "/home/ubuntu/files";
$scanned_dir = array_diff(scandir($directory), array('..', '.'));

$process_id = 0;
foreach ($scanned_dir as $file) {
    echo "Spawning process for file: $file\n";
    shell_exec("/usr/bin/php /var/www/html/process.php $process_id $file > /home/ubuntu/logs/master_process.log 2>&1 &");
    $process_id++;
}

echo "All processes started!\n";
?>

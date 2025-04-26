<?php
if ($argc < 3) {
    die("Usage: php process.php <process_id> <filename>\n");
}

$process_id = $argv[1];
$file = $argv[2];

echo "Starting process $process_id for file: $file\n";

// Call import.php for the given file
shell_exec("/usr/bin/php /var/www/html/import.php $file > /home/ubuntu/logs/process_$process_id.log 2>&1 &");
?>

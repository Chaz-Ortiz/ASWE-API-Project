<?php
// Get the file name from the command line argument
if ($argc < 2) {
    die("Usage: php import.php <filename>\n");
}

$file = $argv[1]; // File passed as an argument
$logFile = "/home/ubuntu/logs/import_" . pathinfo($file, PATHINFO_FILENAME) . ".log";

// Database credentials
$un = "web_user";
$pw = "Q@O2eVlu-/P[vgDH";
$db = "equipment";
$host = "localhost";

$dblink = new mysqli($host, $un, $pw, $db);
if ($dblink->connect_error) {
    die("Connection failed: " . $dblink->connect_error);
}

$fp = fopen("/home/ubuntu/files4/$file", "r");
if (!$fp) {
    die("Error opening file: $file\n");
}

// Log error summary
function logError($dblink, $errorType, $lineNumber, $line) {

    $raw_data = implode(",", $line);
    $raw_data = substr($raw_data, 0, 255);
    $sql = "INSERT INTO `import_log` (`error_type`, `line_number`, `raw_data`) VALUES (?, ?, ?)";
    $stmt = $dblink->prepare($sql);
    $stmt->bind_param("sis", $errorType, $lineNumber, $raw_data);
    $stmt->execute();
}

// ----------------------------
// PERFORMANCE TRACKING
// ----------------------------
$startTime = microtime(true);
$lineNumber = 0;
$validRows = 0;

while (($line = fgetcsv($fp)) !== FALSE) {
    $lineNumber++;
    $line = array_map('trim', $line);

    if (empty(implode("", $line))) {
        if (count($line) > 1) {
            logError($dblink, "Malformed Line", $lineNumber, $line);
        }
        continue;
    }

    if (count($line) !== 3) {
        logError($dblink, "Malformed Line", $lineNumber, $line);
        continue;
    }

    list($device, $manu, $sn) = $line;

    $deviceWithoutSpaces = str_replace(' ', '', $device);
    if (!ctype_alpha($deviceWithoutSpaces)) {
        logError($dblink, "Invalid Device Type", $lineNumber, $line);
        continue;
    }

    $manuWithoutSpaces = str_replace(' ', '', $manu);
    if (!ctype_alpha($manuWithoutSpaces)) {
        logError($dblink, "Invalid Manufacturer", $lineNumber, $line);
        continue;
    }

    $validDevices = ["smart watch", "television", "mobile phone", "vehicle", "tablet", "laptop", "computer"];
    if (!in_array(strtolower($device), $validDevices, true)) {
        logError($dblink, "Misspelled Device Type", $lineNumber, $line);
        continue;
    }

    $validManus = ["samsung", "apple", "google", "lg", "gm", "visio", "ibm", "motorola", "sony", "oneplus", "panasonic", "huawei", "microsoft", "tcl", "ford", "nissan", "toyota", "chevorlet", "dell", "kia", "hisense", "hyundai", "hp", "nokia", "lenovo", "chrysler", "asus", "acer", "gateway", "optoma", "viewsonic", "epson", "westinghouse", "generic", "vizio", "jeep", "insignia"];

    if (!in_array(strtolower($manu), $validManus, true)) {
        logError($dblink, "Misspelled Manufacturer", $lineNumber, $line);
        continue;
    }

    if (strpos($sn, "SN-") !== 0) {
        logError($dblink, "Invalid Serial Format", $lineNumber, $line);
        continue;
    }

    if (empty($device)) {
        logError($dblink, "Device Name Missing", $lineNumber, $line);
        continue;
    }

    if (empty($manu)) {
        logError($dblink, "Manufacturer Missing", $lineNumber, $line);
        continue;
    }

    $check_sql = "SELECT COUNT(*) FROM `devices` WHERE `serial_number` = ?";
    $check_stmt = $dblink->prepare($check_sql);
    $check_stmt->bind_param("s", $sn);
    $check_stmt->execute();
    $check_stmt->store_result();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        logError($dblink, "Duplicate Serial Number", $lineNumber, $line);
        continue;
    }

    $sql = "INSERT INTO `devices` (`device_type`, `manufacturer`, `serial_number`) VALUES (?, ?, ?)";
    $stmt = $dblink->prepare($sql);
    $stmt->bind_param("sss", $device, $manu, $sn);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        logError($dblink, "Database Insert Failed", $lineNumber, $line);
    } else {
        $validRows++;
    }
}

fclose($fp);
$dblink->close();

$endTime = microtime(true);
$duration = $endTime - $startTime;
$rowsPerSecond = $lineNumber > 0 ? round($validRows / $duration, 2) : 0;

// Log summary
file_put_contents($logFile, "File: $file\n", FILE_APPEND);
file_put_contents($logFile, "Total Rows Processed: $lineNumber\n", FILE_APPEND);
file_put_contents($logFile, "Valid Rows Inserted: $validRows\n", FILE_APPEND);
file_put_contents($logFile, "Import Duration (seconds): " . round($duration, 2) . "\n", FILE_APPEND);
file_put_contents($logFile, "Effective Rows Per Second: $rowsPerSecond\n", FILE_APPEND);
?>

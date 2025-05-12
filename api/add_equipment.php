<?php
// Needed: device_type, manufacturer, serial_number
$deviceType    = isset($_REQUEST['device_type']) ? $_REQUEST['device_type'] : null;
$manufacturer  = isset($_REQUEST['manufacturer']) ? $_REQUEST['manufacturer'] : null;
$serialNumber  = isset($_REQUEST['serial_number']) ? $_REQUEST['serial_number'] : null;


$output = [];

if (empty($deviceType)) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Invalid or missing device type.';
    $output[] = 'Action: list_devices';
    echo json_encode($output);
    die();
}

if (empty($manufacturer)) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Invalid or missing manufacturer.';
    $output[] = 'Action: list_manufacturers';
    echo json_encode($output);
    die();
}

if (empty($serialNumber)) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Invalid or missing serial number.';
    $output[] = 'Action: none';
    echo json_encode($output);
    die();
}

// Connect to database
$dblink = db_iconnect("equipment");

// Validate device type
$sql = "SELECT `auto_id` FROM `device_types` WHERE `auto_id`='$deviceType' AND `status`='active'";
$result = $dblink->query($sql) or die("Something went wrong with $sql<br>".$dblink->error);
if ($result->num_rows <= 0) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Invalid or inactive device type.';
    $output[] = 'Action: list_devices';
    echo json_encode($output);
    die();
}

// Validate manufacturer
$sql = "SELECT `auto_id` FROM `manu_types` WHERE `auto_id`='$manufacturer' AND `status`='active'";
$result = $dblink->query($sql) or die("Something went wrong with $sql<br>".$dblink->error);
if ($result->num_rows <= 0) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Invalid or inactive manufacturer.';
    $output[] = 'Action: list_manufacturers';
    echo json_encode($output);
    die();
}

// Check for duplicate serial number
$sql = "SELECT `auto_id` FROM `devices` WHERE `serial_number`='$serialNumber'";
$result = $dblink->query($sql) or die("<p>Error running query:<br>$sql<br>".$dblink->error);
if ($result->num_rows > 0) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Duplicate serial number.';
    $output[] = 'Action: none';
    echo json_encode($output);
    die();
}

// Insert new device
$sql = "INSERT INTO `devices` (`device_type`, `manufacturer`, `serial_number`) 
        VALUES ('$deviceType', '$manufacturer', '$serialNumber')";
$dblink->query($sql) or die("<p>Error running insert:<br>$sql<br>".$dblink->error);

// Success
$output[] = 'Status: Success';
$output[] = 'MSG: New equipment successfully added.';
$output[] = 'Action: home';
echo json_encode($output);
die();
?>

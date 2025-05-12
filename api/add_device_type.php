<?php
$dblink = db_iconnect("equipment");

// 1. Get input safely
$device_name = isset($_POST['device']) ? trim($_POST['device']) : '';  // Changed to $_POST['device']

$output = [];

// Validate: Only allow letters, numbers, spaces, and hyphens
if (empty($device_name) || !preg_match("/^[a-zA-Z0-9\s\-]+$/", $device_name)) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Invalid device type name. Only letters, numbers, spaces, and hyphens are allowed.';
    $output[] = 'Action: add_device_type';  // Consistent with the action name
    echo json_encode($output);
    exit();
}

// Check for duplicates
$sql = "SELECT `auto_id` FROM `device_types` WHERE `name` = ? AND `status` = 'active'";
$check_stmt = $dblink->prepare($sql);
$check_stmt->bind_param("s", $device_name);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Device type already exists.';
    $output[] = 'Action: add_device_type';  // Consistent with the action name
    echo json_encode($output);
    exit();
}

// Insert new device type
$insert_sql = "INSERT INTO `device_types` (`name`, `status`) VALUES (?, 'active')";
$insert_stmt = $dblink->prepare($insert_sql);
$insert_stmt->bind_param("s", $device_name);

if ($insert_stmt->execute()) {
    $output[] = 'Status: Success';
    $output[] = 'MSG: New device type successfully added.';
    $output[] = 'Action: add_device_type';  // Consistent with the action name
} else {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Database insert failed.';
    $output[] = 'Action: add_device_type';  // Consistent with the action name
}

echo json_encode($output);

?>

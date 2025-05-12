<?php
$dblink = db_iconnect("equipment");

// 1. Get input safely
$manu_name = isset($_REQUEST['manu_name']) ? trim($_REQUEST['manu_name']) : '';

$output = [];

// 2. Validate input
if (empty($manu_name)) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Manufacturer name is required.';
    $output[] = 'Action: add_manufacturer';
    echo json_encode($output);
    exit;
}

// Validate manufacturer name (letters, numbers, spaces, hyphens only)
if (empty($manu_name) || !preg_match("/^[a-zA-Z0-9\s\-]+$/", $manu_name)) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Invalid manufacturer name. Only letters, numbers, spaces, and hyphens are allowed.';
    $output[] = 'Action: add_manufacturer';
    echo json_encode($output);
    exit();
}

// 3. Check for duplicate name
$sql = "SELECT `auto_id` FROM `manu_types` WHERE `name` = ?";
$stmt = $dblink->prepare($sql);
$stmt->bind_param("s", $manu_name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Manufacturer already exists.';
    $output[] = 'Action: add_manufacturer';
    echo json_encode($output);
    exit;
}

// 4. Insert new manufacturer
$insert_sql = "INSERT INTO `manu_types` (`name`, `status`) VALUES (?, 'active')";
$insert_stmt = $dblink->prepare($insert_sql);
$insert_stmt->bind_param("s", $manu_name);

if ($insert_stmt->execute()) {
    $output[] = 'Status: Success';
    $output[] = 'MSG: New manufacturer successfully added.';
    $output[] = 'Action: add_manufacturer';
} else {
    $output[] = 'Status: Error';
    $output[] = 'MSG: Database insert failed.';
    $output[] = 'Action: add_manufacturer';
}

echo json_encode($output);
?>

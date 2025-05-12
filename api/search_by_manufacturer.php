<?php
header("Content-Type: application/json");
$dblink = db_iconnect("equipment");
include("../functions.php");


if (!isset($_POST['manufacturer']) || empty($_POST['manufacturer'])) {
    echo json_encode([
        "Status: ERROR",
        "Missing 'manufacturer' parameter",
        "Action: None"
    ]);
    exit;
}

$manufacturer = $dblink->real_escape_string($_POST['manufacturer']);

$sql = "SELECT device, manufacturer, serial_number, status FROM equipment WHERE manufacturer = '$manufacturer'";
$result = $dblink->query($sql);

if (!$result) {
    echo json_encode([
        "Status: ERROR",
        "Query failed: " . $dblink->error,
        "Action: None"
    ]);
    exit;
}

$devices = [];
while ($row = $result->fetch_assoc()) {
    $devices[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $devices
]);

?>

<?php
$dblink = db_iconnect("equipment");
$output = [];

if (isset($_REQUEST['serial'])) {
    $serial = trim($_REQUEST['serial']);
    $serial = mysqli_real_escape_string($conn, $serial);

    $query = "SELECT * FROM equipment WHERE serial_number = '$serial' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $output[] = "Status: Success";
        $output[] = "MSG: Device found";
        $output[] = "Action: Returned device by serial";
        $output[] = $data;
    } else {
        $output[] = "Status: Not Found";
        $output[] = "MSG: No device with that serial number";
        $output[] = "Action: None";
    }
} else {
    $output[] = "Status: ERROR";
    $output[] = "MSG: Missing 'serial' parameter";
    $output[] = "Action: None";
}

echo json_encode($output);
?>

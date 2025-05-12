<?php

function db_iconnect($dbName)
{	
	$un = "web_user";
	$pw = "Q@O2eVlu-/P[vgDH";
	$db = $dbName;
	$host = "localhost";
	$dblink = new mysqli($host, $un, $pw, $db);
	return $dblink;
}

function log_activity($endPoint,$remoteClient,$parameters)
{
	//I am going to log sucessful callss to my endpoints
	// include date/time stamps
	$date = date("Y-m-d H:i:s"); // 24-hour format, leading zero, safe for logs
}

function log_error($endpoint,$remoteClient)
{
	//I am going to log sucessful callss to my endpoints
	// include date/time stamps
	$date = date("Y-m-d H:i:s"); // 24-hour format, leading zero, safe for logs
	// log errors to database
	// return error message to user
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isValidSerialNumber($serial) {
    $pattern = '/^SN-[a-zA-Z0-9-]{1,72}$/'; // "SN-" (3 chars) + up to 72 valid chars
    return preg_match($pattern, $serial) === 1;
}

function isValidName($string) {
    // Reject anything that's not alphabetic or a space
    return preg_match('/^[a-zA-Z ]+$/', $string) === 1;
}

function showBanner($msg) {
    if ($msg == "DeviceExists") {
        echo '<div class="alert alert-danger" role="alert">Serial Number already exists in the database!</div>';
	} elseif ($msg == "EquipmentAdded") {
		echo '<div class="alert alert-success" role="alert">New device was added successfully!</div>';
	} elseif ($msg == "InvalidSerial") {
		echo '<div class="alert alert-warning" role="alert">Invalid Serial Number format. Please enter a valid serial.</div>';
	} elseif ($msg == "DeviceTypeExists") {
		echo '<div class="alert alert-danger">Device type already exists!</div>';
	} elseif ($msg == "DeviceTypeAdded") {
		echo '<div class="alert alert-success">Device type added successfully!</div>';
	} elseif ($msg == "NameRequired") {
		echo '<div class="alert alert-warning">Device type name is required!</div>';
	} elseif ($msg == "InvalidStatus") {
		echo '<div class="alert alert-warning">Invalid status. Choose either "active" or "inactive".</div>';
	} elseif ($msg == "ManufacturerExists") {
		echo '<div class="alert alert-warning" role="alert">Manufacturer already exists in the database.</div>';
	} elseif ($msg == "ManufacturerAdded") {
		echo '<div class="alert alert-success" role="alert">New manufacturer was added successfully!</div>';
	} elseif ($msg == "EmptyManufacturer") {
		echo '<div class="alert alert-danger" role="alert">Please enter a manufacturer name.</div>';
	} elseif ($msg == "InvalidDeviceTypeName") {
		echo '<div class="alert alert-warning" role="alert">Invalid device type format. Please enter a valid device type name.</div>';
	}
}

function validateManufacturer($manufacturer, $eid, $dblink) {
    // Trim and sanitize input
    $manufacturer = trim($manufacturer);

    // Validate length
    $manufacturerLength = strlen($manufacturer);
    if ($manufacturerLength < 1 || $manufacturerLength > 75) {
        return "<div class='alert alert-danger'>Manufacturer must be between 1 and 75 characters.</div>";
    }

    // Validate characters (only allow a-z, A-Z, and spaces)
    if (!preg_match('/^[a-zA-Z\s]+$/', $manufacturer)) {
        return "<div class='alert alert-danger'>Manufacturer must contain only letters (a-z or A-Z) and spaces.</div>";
    }

    // Escape for safe SQL query
    $safeManufacturer = $dblink->real_escape_string($manufacturer);

    // Check if manufacturer already exists for a different device
    $checkSQL = "SELECT * FROM `devices` WHERE `manufacturer`='$safeManufacturer' AND `auto_id` != '$eid'";
    $checkResult = $dblink->query($checkSQL) or
        die("<h2>Error checking manufacturer uniqueness: $checkSQL<br>" . $dblink->error . '</h2>');

    if ($checkResult->num_rows > 0) {
        return "<div class='alert alert-danger'>Manufacturer already exists. Please use a unique one.</div>";
    }

    // Return true if everything is fine
    return true;
}

function deviceTypeExists($name) {
    global $dblink; // Use the existing database link

    $safeName = $dblink->real_escape_string($name);
    $sql = "SELECT * FROM `device_types` WHERE `name` = '$safeName'";
    $rst = $dblink->query($sql) or die("<p>Error checking device type:<br>$sql<br>" . $dblink->error);

    return ($rst->num_rows > 0);
}

function deviceNameExistsInDevices($name) {
    global $dblink;
    $safeName = $dblink->real_escape_string($name);
    $sql = "SELECT * FROM `devices` WHERE `device_type` = '$safeName'";
    $rst = $dblink->query($sql) or die("<p>Error checking device type in devices:<br>$sql<br>" . $dblink->error);

    return ($rst->num_rows > 0);
}

function manufacturerExistsInDevices($name) {
	global $dblink;
	$safeName = $dblink->real_escape_string($name);
	$sql = "SELECT * FROM `devices` WHERE `manufacturer` = '$safeName'";
	$rst = $dblink->query($sql) or die("<p>Error checking manufacturer in devices:<br>$sql<br>" . $dblink->error);
}


?>
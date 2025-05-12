
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Advanced Software Engineering</title>
<link href="../assets/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/font-awesome.min.css">
<link rel="stylesheet" href="../assets/css/owl.carousel.css">
<link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
<!-- MAIN CSS -->
<link rel="stylesheet" href="../assets/css/templatemo-style.css">
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
<!-- MENU - navbar -->
	<section class="navbar custom-navbar navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					 <span class="icon icon-bar"></span>
					 <span class="icon icon-bar"></span>
					 <span class="icon icon-bar"></span>
				</button>
				<!-- lOGO "New Modify" -->
				<a href="#" class="navbar-brand">New Modify</a>
			</div>
			<!-- MENU LINKS -->
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav navbar-nav-first">
					<li><a href="index.php" class="smoothScroll">Home</a></li>
					<li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
					<li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
				</ul>
			</div>
		</div>
	</section>
	<!-- End of MENU - navbar --> 
		
	<!-- HOME (Not currently being used) -->
	<section id="home">
	<!-- No content currently inside -->
	</section>
	
	<!-- FEATURE -->
	<section id="feature">
		<div class="container">
			<div class="row">

			<?php 
			include("../functions.php");
				// Connect to 'equipment' database. db_iconnect() is in functions.php
				$dblink = db_iconnect("equipment");
				
				// Action selection buttons: Add new equipment, add new device type, add new manufacturer
				$action = isset($_GET['action']) ? $_GET['action'] : '';
				// Switch to handle different actions
				switch ($action):
// Default case
					case '': // If no action is set, show the form to choose action
						$eid = $_GET['eid']; // add code to validate
						$sql = "SELECT * FROM `devices` WHERE `auto_id`='$eid'";
						$result = $dblink->query($sql) or die("<h2>Something went wrong with: $sql".$dblink->error.'</h2>');
						$info = $result->fetch_array(MYSQLI_ASSOC);
						// Display "Device Info"
						echo '<h2>Device Info:</h2>';
						echo '<p>Device ID: <b>'.$info['auto_id'].'</b></p>';
						echo '<p>Device Type: <b>'.$info['device_type'].'</b></p>';
						echo '<p>Device Manufacturer: <b>'.$info['manufacturer'].'</b></p>';
						echo '<p>Device Serial Number: <b>'.$info['serial_number'].'</b></p>';
						// Check if device is inactive
						$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '".$info['auto_id']."'";
						$checkStatusResult = $dblink->query($checkStatusSql);
						$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";
						echo '<p>Device Status: <b>'.$status.'</b></p>';
				
						// Check if device type is inactive
						$checkTypeStatusSql = "SELECT 1 FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
						$checkTypeStatusResult = $dblink->query($checkTypeStatusSql);
						$typeStatus = ($checkTypeStatusResult->num_rows > 0) ? "Inactive" : "Active";
						echo '<p>Device Type Status: <b>'.$typeStatus.'</b></p>';

						// Check if manufacturer is inactive
						$checkManufacturerStatusSql = "SELECT 1 FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
						$checkManufacturerStatusResult = $dblink->query($checkManufacturerStatusSql);
						$manufacturerStatus = ($checkManufacturerStatusResult->num_rows > 0) ? "Inactive" : "Active";
						echo '<p>Device Manufacturer Status: <b>'.$manufacturerStatus.'</b></p>';
				
						?>
						<h2>Select what you want to Modify:</h2>
						<form method="get" action="newmodify4.php">
							<input type="hidden" name="eid" value="<?php echo htmlspecialchars($eid); ?>">
							<button type="submit" name="action" value="modifyequipment" class="btn btn-primary">Modify Equipment</button>
							<button type="submit" name="action" value="modifymanufacturer" class="btn btn-primary">Modify Manufacturer</button>
							<button type="submit" name="action" value="modifydevicetype" class="btn btn-primary">Modify Device Type</button>
						</form>
						<?php
						break;
// Case 1: 
					case 'modifyequipment':
					$eid = $_GET['eid'];

					// Fetch current device info
					$sql="SELECT * FROM `devices` WHERE `auto_id`='$eid'";
					$result=$dblink->query($sql) or
						die("<h2>Something went wrong with: $sql".$dblink->error.'</h2>');
					$info=$result->fetch_array(MYSQLI_ASSOC);


					// Check current status from `device_status_inactive`
					$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '$eid'";
					$checkStatusResult = $dblink->query($checkStatusSql);
					$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";
				
					// Check current device type status
					$checkTypeStatusSql = "SELECT 1 FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
					$checkTypeStatusResult = $dblink->query($checkTypeStatusSql);
					$typeStatus = ($checkTypeStatusResult->num_rows > 0) ? "Inactive" : "Active";
				
					// Check current manufacturer status
					$checkManufacturerStatusSql = "SELECT 1 FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
					$checkManufacturerStatusResult = $dblink->query($checkManufacturerStatusSql);
					$manufacturerStatus = ($checkManufacturerStatusResult->num_rows > 0) ? "Inactive" : "Active";


					if (isset($_POST['submit'])) {
						$newDeviceType = $dblink->real_escape_string($_POST['device_type']);
						$newManufacturer = $dblink->real_escape_string($_POST['manufacturer']);
						$newSerial = $dblink->real_escape_string($_POST['serial_number']);
						$newStatus = $_POST['status'];
						$newTypeStatus = $_POST['device_type_status'];
						$newManufacturerStatus = $_POST['device_manufacturer_status'];
						
						// Update device type
						$updateDeviceTypeSql = "UPDATE `devices` SET `device_type` = '$newDeviceType' WHERE `auto_id` = '$eid'";
						$dblink->query($updateDeviceTypeSql) or die("<h2>Update Device Type failed: ".$dblink->error.'</h2>');
						
						// Update manufacturer
						$updateManufacturerSql = "UPDATE `devices` SET `manufacturer` = '$newManufacturer' WHERE `auto_id` = '$eid'";
						$dblink->query($updateManufacturerSql) or die("<h2>Update manufacturer failed: ".$dblink->error.'</h2>');

						// Update serial number
						$updateSerialSql = "UPDATE `devices` SET `serial_number` = '$newSerial' WHERE `auto_id` = '$eid'";
						$dblink->query($updateSerialSql) or die("<h2>Update serial number failed: ".$dblink->error.'</h2>');

						// Update status logic
						if ($newStatus == "Inactive" && $status != "Inactive") {
							$insertStatus = "INSERT INTO `device_status_inactive` (`device_id`) VALUES ('$eid')";
							$dblink->query($insertStatus);
						} elseif ($newStatus == "Active" && $status != "Active") {
							$deleteStatus = "DELETE FROM `device_status_inactive` WHERE `device_id` = '$eid'";
							$dblink->query($deleteStatus);
						}

						// Update device type status logic
						if ($newTypeStatus == "Inactive" && $typeStatus != "Inactive") {
							$insertTypeStatus = "INSERT INTO `device_type_inactive` (`device_type`) VALUES ('".$info['device_type']."')";
							$dblink->query($insertTypeStatus);
						} elseif ($newTypeStatus == "Active" && $typeStatus != "Active") {
							$deleteTypeStatus = "DELETE FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
							$dblink->query($deleteTypeStatus);
						}
						
						// Update device manufacturer status logic
						if ($newManufacturerStatus == "Inactive" && $manufacturerStatus != "Inactive") {
							$insertManufacturerStatus = "INSERT INTO `device_manufacturer_inactive` (`device_manufacturer`) VALUES ('".$info['manufacturer']."')";
							$dblink->query($insertManufacturerStatus) or die("<h2>Insert manufacturer status failed: ".$dblink->error.'</h2>');
						} elseif ($newManufacturerStatus == "Active" && $manufacturerStatus != "Active") {
							// If setting to Active and it was not Active before, DELETE from device_manufacturer_inactive
							$deleteManufacturerStatus = "DELETE FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
							$dblink->query($deleteManufacturerStatus) or die("<h2>Delete manufacturer status failed: ".$dblink->error.'</h2>');
						}

						echo '<div class="alert alert-success">Device updated successfully!</div>';
						// Refresh status after update
						$status = $newStatus;
						$typeStatus = $newTypeStatus;
						$manufacturerStatus = $newManufacturerStatus;
					}

					// Display form
					$eid = $_GET['eid']; // add code to validate

					$sql = "SELECT * FROM `devices` WHERE `auto_id`='$eid'";
					$result = $dblink->query($sql) or die("<h2>Something went wrong with: $sql" . $dblink->error . '</h2>');
					$info = $result->fetch_array(MYSQLI_ASSOC);

					echo '<h2>Device Info:</h2>';
					echo '<p>Device ID: <b>' . $info['auto_id'] . '</b></p>';
					echo '<p>Device Type: <b>' . $info['device_type'] . '</b></p>';
					echo '<p>Device Manufacturer: <b>' . $info['manufacturer'] . '</b></p>';
					echo '<p>Device Serial Number: <b>' . $info['serial_number'] . '</b></p>';

					// Check if device is inactive
					$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '" . $info['auto_id'] . "'";
					$checkStatusResult = $dblink->query($checkStatusSql);
					$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";
					echo '<p>Device Status: <b>' . $status . '</b></p>';
				
					// Check if device type is inactive
					$checkTypeStatusSql = "SELECT 1 FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
					$checkTypeStatusResult = $dblink->query($checkTypeStatusSql);
					$typeStatus = ($checkTypeStatusResult->num_rows > 0) ? "Inactive" : "Active";
					echo '<p>Device Type Status: <b>'.$typeStatus.'</b></p>';

					// Check if manufacturer is inactive
					$checkManufacturerStatusSql = "SELECT 1 FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
					$checkManufacturerStatusResult = $dblink->query($checkManufacturerStatusSql);
					$manufacturerStatus = ($checkManufacturerStatusResult->num_rows > 0) ? "Inactive" : "Active";
					echo '<p>Device Manufacturer Status: <b>'.$manufacturerStatus.'</b></p>';
				
					?>

					<h2>Modify Device</h2>
					<form method="post" action="newmodify4.php?action=modifyequipment&eid=<?php echo $eid; ?>">
						
						<div class="form-group">
							<label for="device_type">Device Type:</label>
							<input type="text" name="device_type" id="device_type" class="form-control" value="<?php echo htmlspecialchars($info['device_type']); ?>" required>
						</div>
						
						<div class="form-group">
							<label for="manufacturer">Manufacturer:</label>
							<input type="text" name="manufacturer" id="manufacturer" class="form-control" value="<?php echo htmlspecialchars($info['manufacturer']); ?>" required>
						</div>
						
						<div class="form-group">
							<label for="serial_number">Device Serial Number:</label>
							<input type="text" name="serial_number" id="serial_number" class="form-control" value="<?php echo htmlspecialchars($info['serial_number']); ?>" required>
						</div>

						<div class="form-group">
							<label for="status">Device Status:</label>
							<select name="status" id="status" class="form-control">
								<option value="Active" <?php if ($status == "Active") echo "selected"; ?>>Active</option>
								<option value="Inactive" <?php if ($status == "Inactive") echo "selected"; ?>>Inactive</option>
							</select>
						</div>
						
						<div class="form-group">
							<label for="device_type_status">Device Type Status:</label>
							<select name="device_type_status" id="device_type_status" class="form-control">
								<option value="Active" <?php if ($typeStatus == "Active") echo "selected"; ?>>Active</option>
								<option value="Inactive" <?php if ($typeStatus == "Inactive") echo "selected"; ?>>Inactive</option>
							</select>
						</div>
						
						<div class="form-group">
							<label for="device_manufacturer_status">Device Manufacturer Status:</label>
							<select name="device_manufacturer_status" id="device_manufacturer_status" class="form-control">
								<option value="Active" <?php if ($manufacturerStatus == "Active") echo "selected"; ?>>Active</option>
								<option value="Inactive" <?php if ($manufacturerStatus == "Inactive") echo "selected"; ?>>Inactive</option>
							</select>
						</div>

						<button type="submit" name="submit" class="btn btn-primary">Update Device</button>
					</form>

					<?php
					break;

// Case 2: 
					case 'modifymanufacturer':
						$eid = $_GET['eid'];

						// Fetch current device info
						$sql="SELECT * FROM `devices` WHERE `auto_id`='$eid'";
						$result=$dblink->query($sql) or
							die("<h2>Something went wrong with: $sql".$dblink->error.'</h2>');
						$info=$result->fetch_array(MYSQLI_ASSOC);


						// Check current status from `device_status_inactive`
						$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '$eid'";
						$checkStatusResult = $dblink->query($checkStatusSql);
						$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";

						// Check current device type status
						$checkTypeStatusSql = "SELECT 1 FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
						$checkTypeStatusResult = $dblink->query($checkTypeStatusSql);
						$typeStatus = ($checkTypeStatusResult->num_rows > 0) ? "Inactive" : "Active";

						// Check current manufacturer status
						$checkManufacturerStatusSql = "SELECT 1 FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
						$checkManufacturerStatusResult = $dblink->query($checkManufacturerStatusSql);
						$manufacturerStatus = ($checkManufacturerStatusResult->num_rows > 0) ? "Inactive" : "Active";


						if (isset($_POST['submit'])) {
							$newDeviceType = $dblink->real_escape_string($_POST['device_type']);
							$newManufacturer = $dblink->real_escape_string($_POST['manufacturer']);
							$newSerial = $dblink->real_escape_string($_POST['serial_number']);
							$newStatus = $_POST['status'];
							$newTypeStatus = $_POST['device_type_status'];
							$newManufacturerStatus = $_POST['device_manufacturer_status'];

							// Update device type
							$updateDeviceTypeSql = "UPDATE `devices` SET `device_type` = '$newDeviceType' WHERE `auto_id` = '$eid'";
							$dblink->query($updateDeviceTypeSql) or die("<h2>Update Device Type failed: ".$dblink->error.'</h2>');

							// Update manufacturer
							$updateManufacturerSql = "UPDATE `devices` SET `manufacturer` = '$newManufacturer' WHERE `auto_id` = '$eid'";
							$dblink->query($updateManufacturerSql) or die("<h2>Update manufacturer failed: ".$dblink->error.'</h2>');

							// Update serial number
							$updateSerialSql = "UPDATE `devices` SET `serial_number` = '$newSerial' WHERE `auto_id` = '$eid'";
							$dblink->query($updateSerialSql) or die("<h2>Update serial number failed: ".$dblink->error.'</h2>');

							// Update status logic
							if ($newStatus == "Inactive" && $status != "Inactive") {
								$insertStatus = "INSERT INTO `device_status_inactive` (`device_id`) VALUES ('$eid')";
								$dblink->query($insertStatus);
							} elseif ($newStatus == "Active" && $status != "Active") {
								$deleteStatus = "DELETE FROM `device_status_inactive` WHERE `device_id` = '$eid'";
								$dblink->query($deleteStatus);
							}

							// Update device type status logic
							if ($newTypeStatus == "Inactive" && $typeStatus != "Inactive") {
								$insertTypeStatus = "INSERT INTO `device_type_inactive` (`device_type`) VALUES ('".$info['device_type']."')";
								$dblink->query($insertTypeStatus);
							} elseif ($newTypeStatus == "Active" && $typeStatus != "Active") {
								$deleteTypeStatus = "DELETE FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
								$dblink->query($deleteTypeStatus);
							}

							// Update device manufacturer status logic
							if ($newManufacturerStatus == "Inactive" && $manufacturerStatus != "Inactive") {
								$insertManufacturerStatus = "INSERT INTO `device_manufacturer_inactive` (`device_manufacturer`) VALUES ('".$info['manufacturer']."')";
								$dblink->query($insertManufacturerStatus) or die("<h2>Insert manufacturer status failed: ".$dblink->error.'</h2>');
							} elseif ($newManufacturerStatus == "Active" && $manufacturerStatus != "Active") {
								// If setting to Active and it was not Active before, DELETE from device_manufacturer_inactive
								$deleteManufacturerStatus = "DELETE FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
								$dblink->query($deleteManufacturerStatus) or die("<h2>Delete manufacturer status failed: ".$dblink->error.'</h2>');
							}

							echo '<div class="alert alert-success">Device updated successfully!</div>';
							// Refresh status after update
							$status = $newStatus;
							$typeStatus = $newTypeStatus;
							$manufacturerStatus = $newManufacturerStatus;
						}

						// Display form
						$eid = $_GET['eid']; // add code to validate

						$sql = "SELECT * FROM `devices` WHERE `auto_id`='$eid'";
						$result = $dblink->query($sql) or die("<h2>Something went wrong with: $sql" . $dblink->error . '</h2>');
						$info = $result->fetch_array(MYSQLI_ASSOC);

						echo '<h2>Device Info:</h2>';
						echo '<p>Device ID: <b>' . $info['auto_id'] . '</b></p>';
						echo '<p>Device Type: <b>' . $info['device_type'] . '</b></p>';
						echo '<p>Device Manufacturer: <b>' . $info['manufacturer'] . '</b></p>';
						echo '<p>Device Serial Number: <b>' . $info['serial_number'] . '</b></p>';

						// Check if device is inactive
						$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '" . $info['auto_id'] . "'";
						$checkStatusResult = $dblink->query($checkStatusSql);
						$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";
						echo '<p>Device Status: <b>' . $status . '</b></p>';

						// Check if device type is inactive
						$checkTypeStatusSql = "SELECT 1 FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
						$checkTypeStatusResult = $dblink->query($checkTypeStatusSql);
						$typeStatus = ($checkTypeStatusResult->num_rows > 0) ? "Inactive" : "Active";
						echo '<p>Device Type Status: <b>'.$typeStatus.'</b></p>';

						// Check if manufacturer is inactive
						$checkManufacturerStatusSql = "SELECT 1 FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
						$checkManufacturerStatusResult = $dblink->query($checkManufacturerStatusSql);
						$manufacturerStatus = ($checkManufacturerStatusResult->num_rows > 0) ? "Inactive" : "Active";
						echo '<p>Device Manufacturer Status: <b>'.$manufacturerStatus.'</b></p>';

						?>

						<h2>Modify Manufacturer</h2>
						<form method="post" action="newmodify4.php?action=modifymanufacturer&eid=<?php echo $eid; ?>">

							<div class="form-group">
								<label for="device_type">Device Type:</label>
								<input type="text" name="device_type" id="device_type" class="form-control" value="<?php echo htmlspecialchars($info['device_type']); ?>" required>
							</div>

							<div class="form-group">
								<label for="manufacturer">Manufacturer:</label>
								<input type="text" name="manufacturer" id="manufacturer" class="form-control" value="<?php echo htmlspecialchars($info['manufacturer']); ?>" required>
							</div>

							<div class="form-group">
								<label for="serial_number">Device Serial Number:</label>
								<input type="text" name="serial_number" id="serial_number" class="form-control" value="<?php echo htmlspecialchars($info['serial_number']); ?>" required>
							</div>

							<div class="form-group">
								<label for="status">Device Status:</label>
								<select name="status" id="status" class="form-control">
									<option value="Active" <?php if ($status == "Active") echo "selected"; ?>>Active</option>
									<option value="Inactive" <?php if ($status == "Inactive") echo "selected"; ?>>Inactive</option>
								</select>
							</div>

							<div class="form-group">
								<label for="device_type_status">Device Type Status:</label>
								<select name="device_type_status" id="device_type_status" class="form-control">
									<option value="Active" <?php if ($typeStatus == "Active") echo "selected"; ?>>Active</option>
									<option value="Inactive" <?php if ($typeStatus == "Inactive") echo "selected"; ?>>Inactive</option>
								</select>
							</div>

							<div class="form-group">
								<label for="device_manufacturer_status">Device Manufacturer Status:</label>
								<select name="device_manufacturer_status" id="device_manufacturer_status" class="form-control">
									<option value="Active" <?php if ($manufacturerStatus == "Active") echo "selected"; ?>>Active</option>
									<option value="Inactive" <?php if ($manufacturerStatus == "Inactive") echo "selected"; ?>>Inactive</option>
								</select>
							</div>

							<button type="submit" name="submit" class="btn btn-primary">Update Device</button>
						</form>
					<?php
						break;
// Case 3: 
					case 'modifydevicetype':
						$eid = $_GET['eid'];

						// Fetch current device info
						$sql="SELECT * FROM `devices` WHERE `auto_id`='$eid'";
						$result=$dblink->query($sql) or
							die("<h2>Something went wrong with: $sql".$dblink->error.'</h2>');
						$info=$result->fetch_array(MYSQLI_ASSOC);


						// Check current status from `device_status_inactive`
						$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '$eid'";
						$checkStatusResult = $dblink->query($checkStatusSql);
						$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";

						// Check current device type status
						$checkTypeStatusSql = "SELECT 1 FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
						$checkTypeStatusResult = $dblink->query($checkTypeStatusSql);
						$typeStatus = ($checkTypeStatusResult->num_rows > 0) ? "Inactive" : "Active";

						// Check current manufacturer status
						$checkManufacturerStatusSql = "SELECT 1 FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
						$checkManufacturerStatusResult = $dblink->query($checkManufacturerStatusSql);
						$manufacturerStatus = ($checkManufacturerStatusResult->num_rows > 0) ? "Inactive" : "Active";


						if (isset($_POST['submit'])) {
							$newDeviceType = $dblink->real_escape_string($_POST['device_type']);
							$newManufacturer = $dblink->real_escape_string($_POST['manufacturer']);
							$newSerial = $dblink->real_escape_string($_POST['serial_number']);
							$newStatus = $_POST['status'];
							$newTypeStatus = $_POST['device_type_status'];
							$newManufacturerStatus = $_POST['device_manufacturer_status'];

							// Update device type
							$updateDeviceTypeSql = "UPDATE `devices` SET `device_type` = '$newDeviceType' WHERE `auto_id` = '$eid'";
							$dblink->query($updateDeviceTypeSql) or die("<h2>Update Device Type failed: ".$dblink->error.'</h2>');

							// Update manufacturer
							$updateManufacturerSql = "UPDATE `devices` SET `manufacturer` = '$newManufacturer' WHERE `auto_id` = '$eid'";
							$dblink->query($updateManufacturerSql) or die("<h2>Update manufacturer failed: ".$dblink->error.'</h2>');

							// Update serial number
							$updateSerialSql = "UPDATE `devices` SET `serial_number` = '$newSerial' WHERE `auto_id` = '$eid'";
							$dblink->query($updateSerialSql) or die("<h2>Update serial number failed: ".$dblink->error.'</h2>');

							// Update status logic
							if ($newStatus == "Inactive" && $status != "Inactive") {
								$insertStatus = "INSERT INTO `device_status_inactive` (`device_id`) VALUES ('$eid')";
								$dblink->query($insertStatus);
							} elseif ($newStatus == "Active" && $status != "Active") {
								$deleteStatus = "DELETE FROM `device_status_inactive` WHERE `device_id` = '$eid'";
								$dblink->query($deleteStatus);
							}

							// Update device type status logic
							if ($newTypeStatus == "Inactive" && $typeStatus != "Inactive") {
								$insertTypeStatus = "INSERT INTO `device_type_inactive` (`device_type`) VALUES ('".$info['device_type']."')";
								$dblink->query($insertTypeStatus);
							} elseif ($newTypeStatus == "Active" && $typeStatus != "Active") {
								$deleteTypeStatus = "DELETE FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
								$dblink->query($deleteTypeStatus);
							}

							// Update device manufacturer status logic
							if ($newManufacturerStatus == "Inactive" && $manufacturerStatus != "Inactive") {
								$insertManufacturerStatus = "INSERT INTO `device_manufacturer_inactive` (`device_manufacturer`) VALUES ('".$info['manufacturer']."')";
								$dblink->query($insertManufacturerStatus) or die("<h2>Insert manufacturer status failed: ".$dblink->error.'</h2>');
							} elseif ($newManufacturerStatus == "Active" && $manufacturerStatus != "Active") {
								// If setting to Active and it was not Active before, DELETE from device_manufacturer_inactive
								$deleteManufacturerStatus = "DELETE FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
								$dblink->query($deleteManufacturerStatus) or die("<h2>Delete manufacturer status failed: ".$dblink->error.'</h2>');
							}

							echo '<div class="alert alert-success">Device updated successfully!</div>';
							// Refresh status after update
							$status = $newStatus;
							$typeStatus = $newTypeStatus;
							$manufacturerStatus = $newManufacturerStatus;
						}

						// Display form
						$eid = $_GET['eid']; // add code to validate

						$sql = "SELECT * FROM `devices` WHERE `auto_id`='$eid'";
						$result = $dblink->query($sql) or die("<h2>Something went wrong with: $sql" . $dblink->error . '</h2>');
						$info = $result->fetch_array(MYSQLI_ASSOC);

						echo '<h2>Device Info:</h2>';
						echo '<p>Device ID: <b>' . $info['auto_id'] . '</b></p>';
						echo '<p>Device Type: <b>' . $info['device_type'] . '</b></p>';
						echo '<p>Device Manufacturer: <b>' . $info['manufacturer'] . '</b></p>';
						echo '<p>Device Serial Number: <b>' . $info['serial_number'] . '</b></p>';

						// Check if device is inactive
						$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '" . $info['auto_id'] . "'";
						$checkStatusResult = $dblink->query($checkStatusSql);
						$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";
						echo '<p>Device Status: <b>' . $status . '</b></p>';

						// Check if device type is inactive
						$checkTypeStatusSql = "SELECT 1 FROM `device_type_inactive` WHERE `device_type` = '".$info['device_type']."'";
						$checkTypeStatusResult = $dblink->query($checkTypeStatusSql);
						$typeStatus = ($checkTypeStatusResult->num_rows > 0) ? "Inactive" : "Active";
						echo '<p>Device Type Status: <b>'.$typeStatus.'</b></p>';

						// Check if manufacturer is inactive
						$checkManufacturerStatusSql = "SELECT 1 FROM `device_manufacturer_inactive` WHERE `device_manufacturer` = '".$info['manufacturer']."'";
						$checkManufacturerStatusResult = $dblink->query($checkManufacturerStatusSql);
						$manufacturerStatus = ($checkManufacturerStatusResult->num_rows > 0) ? "Inactive" : "Active";
						echo '<p>Device Manufacturer Status: <b>'.$manufacturerStatus.'</b></p>';

						?>

						<h2>Modify Device Type</h2>
						<form method="post" action="newmodify4.php?action=modifydevicetype&eid=<?php echo $eid; ?>">

							<div class="form-group">
								<label for="device_type">Device Type:</label>
								<input type="text" name="device_type" id="device_type" class="form-control" value="<?php echo htmlspecialchars($info['device_type']); ?>" required>
							</div>

							<div class="form-group">
								<label for="manufacturer">Manufacturer:</label>
								<input type="text" name="manufacturer" id="manufacturer" class="form-control" value="<?php echo htmlspecialchars($info['manufacturer']); ?>" required>
							</div>

							<div class="form-group">
								<label for="serial_number">Device Serial Number:</label>
								<input type="text" name="serial_number" id="serial_number" class="form-control" value="<?php echo htmlspecialchars($info['serial_number']); ?>" required>
							</div>

							<div class="form-group">
								<label for="status">Device Status:</label>
								<select name="status" id="status" class="form-control">
									<option value="Active" <?php if ($status == "Active") echo "selected"; ?>>Active</option>
									<option value="Inactive" <?php if ($status == "Inactive") echo "selected"; ?>>Inactive</option>
								</select>
							</div>

							<div class="form-group">
								<label for="device_type_status">Device Type Status:</label>
								<select name="device_type_status" id="device_type_status" class="form-control">
									<option value="Active" <?php if ($typeStatus == "Active") echo "selected"; ?>>Active</option>
									<option value="Inactive" <?php if ($typeStatus == "Inactive") echo "selected"; ?>>Inactive</option>
								</select>
							</div>

							<div class="form-group">
								<label for="device_manufacturer_status">Device Manufacturer Status:</label>
								<select name="device_manufacturer_status" id="device_manufacturer_status" class="form-control">
									<option value="Active" <?php if ($manufacturerStatus == "Active") echo "selected"; ?>>Active</option>
									<option value="Inactive" <?php if ($manufacturerStatus == "Inactive") echo "selected"; ?>>Inactive</option>
								</select>
							</div>

							<button type="submit" name="submit" class="btn btn-primary">Update Device</button>
						</form>
					<?php
						break;
				endswitch;
				?>
			</div>
		</div>
	</div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>	
</body>
</html>
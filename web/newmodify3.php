
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
// Default Case: If no action is set, show Device info and three buttons to choose action					
					case '': // If no action is set, show the Device info and three buttons to choose action
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
						?>
						<h2>Select what you want to Modify:</h2>
						<form method="get" action="newmodify3.php">
							<input type="hidden" name="eid" value="<?php echo htmlspecialchars($eid); ?>">
							<button type="submit" name="action" value="modifyequipment" class="btn btn-primary">Modify Equipment</button>
							<button type="submit" name="action" value="modifymanufacturer" class="btn btn-primary">Modify Manufacturer</button>
							<button type="submit" name="action" value="modifydevicetype" class="btn btn-primary">Modify Device Type</button>
						</form>
						<?php
						break;
// Case 1: if the "Modify Equipment" button is selected
					case 'modifyequipment':
					$eid = $_GET['eid'];

					// Fetch current device info
					$sql="SELECT * FROM `devices` WHERE `auto_id`='$eid'";
					$result=$dblink->query($sql) or
						die("<h2>Something went wrong with: $sql".$dblink->error.'</h2>');
					$info=$result->fetch_array(MYSQLI_ASSOC);


					// Check current status
					$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '$eid'";
					$checkStatusResult = $dblink->query($checkStatusSql);
					$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";

					if (isset($_POST['submit'])) {
						$newSerial = $dblink->real_escape_string($_POST['serial_number']);
						$newStatus = $_POST['status'];

						// Update serial number
						$updateSql = "UPDATE `devices` SET `serial_number` = '$newSerial' WHERE `auto_id` = '$eid'";
						$dblink->query($updateSql) or die("<h2>Update failed: ".$dblink->error.'</h2>');

						// Update status logic
						if ($newStatus == "Inactive" && $status != "Inactive") {
							$insertStatus = "INSERT INTO `device_status_inactive` (`device_id`) VALUES ('$eid')";
							$dblink->query($insertStatus);
						} elseif ($newStatus == "Active" && $status != "Active") {
							$deleteStatus = "DELETE FROM `device_status_inactive` WHERE `device_id` = '$eid'";
							$dblink->query($deleteStatus);
						}

						echo '<div class="alert alert-success">Device updated successfully!</div>';
						// Refresh status after update
						$status = $newStatus;
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
					?>

					<h2>Modify Equipment</h2>
					<form method="post" action="newmodify3.php?action=modifyequipment&eid=<?php echo $eid; ?>">
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

						<button type="submit" name="submit" class="btn btn-primary">Update Device</button>
					</form>

					<?php
						break;

// Case 2: modifymanufacturer
					case 'modifymanufacturer':
					$eid = $_GET['eid'];

					// Fetch current device info
					$sql="SELECT * FROM `devices` WHERE `auto_id`='$eid'";
					$result=$dblink->query($sql) or
						die("<h2>Something went wrong with: $sql".$dblink->error.'</h2>');
					$info=$result->fetch_array(MYSQLI_ASSOC);


					// Check current status
					$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '$eid'";
					$checkStatusResult = $dblink->query($checkStatusSql);
					$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";

					if (isset($_POST['submit'])) {
						$newSerial = $dblink->real_escape_string($_POST['serial_number']);
						$newStatus = $_POST['status'];

						// Update serial number
						$updateSql = "UPDATE `devices` SET `serial_number` = '$newSerial' WHERE `auto_id` = '$eid'";
						$dblink->query($updateSql) or die("<h2>Update failed: ".$dblink->error.'</h2>');

						// Update status logic
						if ($newStatus == "Inactive" && $status != "Inactive") {
							$insertStatus = "INSERT INTO `device_status_inactive` (`device_id`) VALUES ('$eid')";
							$dblink->query($insertStatus);
						} elseif ($newStatus == "Active" && $status != "Active") {
							$deleteStatus = "DELETE FROM `device_status_inactive` WHERE `device_id` = '$eid'";
							$dblink->query($deleteStatus);
						}

						echo '<div class="alert alert-success">Device updated successfully!</div>';
						// Refresh status after update
						$status = $newStatus;
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
					?>

					<h2>Modify Manufacturer</h2>
					<form method="post" action="newmodify3.php?action=modifymanufacturer&eid=<?php echo $eid; ?>">
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

						<button type="submit" name="submit" class="btn btn-primary">Update Device</button>
					</form>

					<?php
						break;
					
// Case 3: modifydevicetype 
					case 'modifydevicetype':
						$eid = $_GET['eid'];

					// Fetch current device info
					$sql="SELECT * FROM devices WHERE auto_id='$eid'";
					$result=$dblink->query($sql) or
						die("<h2>Something went wrong with: $sql".$dblink->error.'</h2>');
					$info=$result->fetch_array(MYSQLI_ASSOC);


					// Check current status
					$checkStatusSql = "SELECT 1 FROM device_type_inactive WHERE device_type = '" . $info['device_type'] . "'";
					$checkStatusResult = $dblink->query($checkStatusSql);
					$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";


					if (isset($_POST['submit'])) {
						$newSerial = $dblink->real_escape_string($_POST['serial_number']);
						$newStatus = $_POST['status'];



						// Update status logic
						$checkStatusSql = "SELECT 1 FROM device_type_inactive WHERE device_type = '" . $info['device_type'] . "'";
						$checkStatusResult = $dblink->query($checkStatusSql);
						$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";


						echo '<div class="alert alert-success">Device updated successfully!</div>';
						// Refresh status after update
						$status = $newStatus;
					}

					// Display form
					$eid = $_GET['eid']; // add code to validate

					$sql = "SELECT * FROM devices WHERE auto_id='$eid'";
					$result = $dblink->query($sql) or die("<h2>Something went wrong with: $sql" . $dblink->error . '</h2>');
					$info = $result->fetch_array(MYSQLI_ASSOC);



					// Check if device type status is inactive
					$checkStatusSql = "SELECT 1 FROM device_status_inactive WHERE device_id = '" . $info['auto_id'] . "'";
					$checkStatusResult = $dblink->query($checkStatusSql);
					$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";
					echo '<p>Device Type Status: <b>' . $status . '</b></p>';
					?>

					<h2>Modify Device Type</h2>
					<form method="post" action="newmodify3.php?action=modifydevicetype&eid=<?php echo $eid; ?>">


						<div class="form-group">
							<label for="status">Device Type Status:</label>
							<select name="status" id="status" class="form-control">
								<option value="Active" <?php if ($status == "Active") echo "selected"; ?>>Active</option>
								<option value="Inactive" <?php if ($status == "Inactive") echo "selected"; ?>>Inactive</option>
							</select>
						</div>

						<button type="submit" name="submit" class="btn btn-primary">Update Device Type</button>
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
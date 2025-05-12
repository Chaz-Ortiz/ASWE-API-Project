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
// If no action is set, show the form to choose action. (Device info and three buttons)					
					case '': 
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
						<form method="get" action="newmodify2.php">
							<input type="hidden" name="eid" value="<?php echo htmlspecialchars($eid); ?>">
							<button type="submit" name="action" value="modifyequipment" class="btn btn-primary">Modify Equipment</button>
							<button type="submit" name="action" value="modifymanufacturer" class="btn btn-secondary">Modify Manufacturer</button>
							<button type="submit" name="action" value="modifydevicetype" class="btn btn-info">Modify Device Type</button>
						</form>
					<?php
						break;
					// End of "Device Info" display and three buttons
					
// Case if the "Modify Equipment" button is selected
					case 'modifyequipment':
					$eid = $_GET['eid'];

					// Fetch current device info
					$sql="SELECT * FROM `devices` WHERE `auto_id`='$eid'";
					$result=$dblink->query($sql) or
						die("<h2>Something went wrong with: $sql".$dblink->error.'</h2>');
					$info=$result->fetch_array(MYSQLI_ASSOC);

					// Check current status (device_status_inactive)
					$checkStatusSql = "SELECT 1 FROM `device_status_inactive` WHERE `device_id` = '$eid'";
					$checkStatusResult = $dblink->query($checkStatusSql);
					$status = ($checkStatusResult->num_rows > 0) ? "Inactive" : "Active";

					// HEY I'M WORKING OVER HERE!
					// Check device type status (device_type_inactive)
					$checkTypeStatusSql = "SELECT 1 FROM `device_type_inactive` WHERE `device_id` = '$eid'";
					$checkTypeStatusResult = $dblink->query($checkTypeStatusSql);
					$typeStatus = ($checkTypeStatusResult->num_rows > 0) ? "Inactive" : "Active";
					// HEY I'M WORKING OVER HERE!


			// Check if the form was submitted by looking for 'submit' in POST data (Update Device button)
					if (isset($_POST['submit'])) {
						// Sanitize the new serial number to prevent SQL injection
						$newSerial = $dblink->real_escape_string($_POST['serial_number']);
						$newStatus = $_POST['status'];
						// Get the new status selected by the user ('Active' or 'Inactive')
						$newTypeStatus = $_POST['typeStatus']; // HEY I'M WORKING OVER HERE!


						// Update serial number in the devices table
						$updateSql = "UPDATE `devices` SET `serial_number` = '$newSerial' WHERE `auto_id` = '$eid'";
						$dblink->query($updateSql) or die("<h2>Update failed: ".$dblink->error.'</h2>');

						// Update status logic (active/inactive). A separate table (`device_status_inactive`) tracks inactive devices.
						if ($newStatus == "Inactive" && $status != "Inactive") {
							$insertStatus = "INSERT INTO `device_status_inactive` (`device_id`) VALUES ('$eid')";
							$dblink->query($insertStatus);
						} elseif ($newStatus == "Active" && $status != "Active") {
							$deleteStatus = "DELETE FROM `device_status_inactive` WHERE `device_id` = '$eid'";
							$dblink->query($deleteStatus);
						}

						// HEY I'M WORKING OVER HERE!
						// Update TYPE status logic (active/inactive). A separate table (`device_type_inactive`) tracks inactive types.
						if ($newTypeStatus == "Inactive" && $typeStatus != "Inactive") {
							$insertTypeStatus = "INSERT INTO `device_type_inactive` (`device_id`) VALUES ('$eid')";
							$dblink->query($insertTypeStatus);
						} elseif ($newTypeStatus == "Active" && $typeStatus != "Active") {
							$deleteTypeStatus = "DELETE FROM `device_type_inactive` WHERE `device_id` = '$eid'";
							$dblink->query($deleteTypeStatus);
						}
						// HEY I'M WORKING OVER HERE!

						echo '<div class="alert alert-success">Device updated successfully!</div>';
						// Update the local $status variable so the page reflects the new status
						$status = $newStatus;

						echo '<div class="alert alert-success">Device type status updated successfully!</div>';
						// Update the local $typeStatus variable so the page reflects the new status
						$typeStatus = $newTypeStatus;

					}

	// Display form "Device Info"
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

					<h2>Modify Device</h2>
					<form method="post" action="newmodify2.php?action=modifyequipment&eid=<?php echo $eid; ?>">
						
						<div class="form-group">
							<label for="typeStatus">Device Type Status:</label>
							<select name="typeStatus" id="typeStatus" class="form-control">
								<option value="Active" <?php if ($typeStatus == "Active") echo "selected"; ?>>Active</option>
								<option value="Inactive" <?php if ($typeStatus == "Inactive") echo "selected"; ?>>Inactive</option>
							</select>
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

						<button type="submit" name="submit" class="btn btn-primary">Update Device</button>
					</form>

					<?php
					break;


					case 'modifymanufacturer':
						if (isset($_POST['submit'])) {
							// process form submission for modify manufacturer
						}
						break;

					case 'modifydevicetype':
						if (isset($_POST['submit'])) {
							// process form submission for modify device type
						}
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
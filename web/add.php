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
     <!-- MENU -->
     <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
          <div class="container">
               <div class="navbar-header">
                    <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                    </button>
                    <!-- lOGO TEXT HERE -->
                    <a href="#" class="navbar-brand">Add New Equipment</a>
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
 	 <!-- HOME -->
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
						case '': // If no action is set, show the form to choose action
							?>
							<h2>Select what you want to add:</h2>
							<form method="get" action="add.php">
								<button type="submit" name="action" value="addequipment" class="btn btn-primary">Add New Equipment</button>
								<button type="submit" name="action" value="adddevicetype" class="btn btn-secondary">Add New Device Type</button>
								<button type="submit" name="action" value="addmanufacturer" class="btn btn-info">Add New Manufacturer</button>
							</form>
							<?php
							break;
					   // Show form if "add new equipment" button is selected
					   case 'addequipment':
							if (isset($_POST['submit'])) {
								// Sanitize and trim user input
								$device = trim($_POST['device']);
								$manufacturer = trim($_POST['manufacturer']);
								$serialNumber = trim($_POST['serialnumber']);
								// Check serial number validity
								if (!isValidSerialNumber($serialNumber)) {
									redirect("add.php?action=addequipment&msg=InvalidSerial");
								}
								// Step 1: Check if the serial number already exists
								$sql = "SELECT `auto_id` FROM `devices` WHERE `serial_number` = '$serialNumber'";
								$rst = $dblink->query($sql) or die("<p>Error running query:<br>$sql<br>".$dblink->error);

								if ($rst->num_rows <= 0) {
									// Insert new device
									$sql = "INSERT INTO `devices` (`device_type`, `manufacturer`, `serial_number`) 
											VALUES ('$device', '$manufacturer', '$serialNumber')";
									$dblink->query($sql) or die("<p>Error running insert:<br>$sql<br>".$dblink->error);

									redirect("index.php?msg=EquipmentAdded");
								} else {
									redirect("add.php?msg=DeviceExists");
								}
							}
				   			// Fetch distinct device types and manufacturers for dropdowns
							$deviceTypes = [];
							$manufacturers = [];
							$sqlTypes = "SELECT DISTINCT `device_type` FROM `devices` ORDER BY `device_type`";
							$sqlManus = "SELECT DISTINCT `manufacturer` FROM `devices` ORDER BY `manufacturer`";
							$rstTypes = $dblink->query($sqlTypes) or die("<p>Error fetching device types:<br>$sqlTypes<br>".$dblink->error);
							$rstManus = $dblink->query($sqlManus) or die("<p>Error fetching manufacturers:<br>$sqlManus<br>".$dblink->error);
							while ($row = $rstTypes->fetch_assoc()) {
								$deviceTypes[] = $row['device_type'];
							}
							while ($row = $rstManus->fetch_assoc()) {
								$manufacturers[] = $row['manufacturer'];
							}
							// Show form only if the action is addequipment and no submission yet
							?>
							<!-- Success or error banners -->
							<?php if (isset($_REQUEST['msg'])) {
								showBanner($_REQUEST['msg']);
							} ?>

							<h2>Add a New Device</h2>
							<form action="add.php?action=addequipment" method="post" class="mt-4">
								<div class="form-group">
									<label for="device">Device Type:</label>
									<select name="device" id="device" class="form-control" required>
										<option value="">Select Device Type</option>
										<?php foreach ($deviceTypes as $type): ?>
											<option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="form-group">
									<label for="manufacturer">Manufacturer:</label>
									<select name="manufacturer" id="manufacturer" class="form-control" required>
										<option value="">Select Manufacturer</option>
										<?php foreach ($manufacturers as $manu): ?>
											<option value="<?= htmlspecialchars($manu) ?>"><?= htmlspecialchars($manu) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="form-group">
									<label for="serialnumber">Serial Number:</label>
									<input type="text" name="serialnumber" id="serialnumber" class="form-control" required>
								</div>
								<button type="submit" name="submit" class="btn btn-primary">Add Device</button>
							 </form>
					<?php
					break;
				    case 'adddevicetype':
						if (isset($_POST['submit'])) {
							$name = trim($_POST['devicetype']);

							// Basic validation
							if (empty($name)) {
								redirect("add.php?action=adddevicetype&msg=NameRequired");
							}
							
							// Validate the name to ensure only alphabet letters and spaces
							if (!isValidName($name)) {
								redirect("add.php?action=adddevicetype&msg=InvalidDeviceTypeName");
							}
							
							// Check if the device type already exists in device_types
							$sql = "SELECT * FROM `device_types` WHERE `name` = '$name'";
							$rst = $dblink->query($sql) or die("<p>Error checking device type:<br>$sql<br>".$dblink->error);

							if ($rst->num_rows > 0) {
								redirect("add.php?action=adddevicetype&msg=DeviceTypeExists");
							}

							// Insert new device type
							$sql = "INSERT INTO `device_types` (`name`, `status`) VALUES ('$name', 'active')";
							$dblink->query($sql) or die("<p>Error inserting device type:<br>$sql<br>".$dblink->error);

							redirect("add.php?msg=DeviceTypeAdded");
						}

						// Success or error banners
						if (isset($_REQUEST['msg'])) {
							?>
							<div class="alert alert-info" role="alert">
								<?php
								if ($_REQUEST['msg'] == "DeviceTypeExists") {
									echo "Device Type already exists in the database.";
								} elseif ($_REQUEST['msg'] == "DeviceTypeAdded") {
									echo "New device type was added successfully!";
								} elseif ($_REQUEST['msg'] == "NameRequired") {
									echo "Please enter a device type name.";
								} elseif ($_REQUEST['msg'] == "InvalidDeviceTypeName") {
									echo "Device type name is invalid. Only letters and spaces are allowed.";
								}
								?>
							</div>
							<?php


						}
						?>

						<h2>Add a New Device Type</h2>
						<form action="add.php?action=adddevicetype" method="post">
							<div class="form-group">
								<label for="devicetype">Device Type Name:</label>
								<input type="text" name="devicetype" id="devicetype" class="form-control" required>
							</div>
							<button type="submit" name="submit" class="btn btn-info">Add Device Type</button>
						</form>
						<?php
						break;


				   		case 'addmanufacturer':
							if (isset($_POST['submit'])) {
								$manufacturer = trim($_POST['manufacturer']);

								// Basic validation
								if (empty($manufacturer)) {
									redirect("add.php?action=addmanufacturer&msg=EmptyManufacturer");
								}
								
								// Validate the name to ensure only alphabet letters and spaces
								if (!isValidName($manufacturer)) {
									redirect("add.php?action=adddevicetype&msg=InvalidDeviceTypeName");
								}
								
								// Check if manufacturer already exists in manu_types
								$sql = "SELECT * FROM `manu_types` WHERE `name` = '$manufacturer'";
								$rst = $dblink->query($sql) or die("<p>Error checking manufacturer:<br>$sql<br>".$dblink->error);

								if ($rst->num_rows > 0) {
									redirect("add.php?action=addmanufacturer&msg=ManufacturerExists");
								}
								// Insert new manufacturer with status 'active'
								$sql = "INSERT INTO `manu_types` (`name`, `status`) VALUES ('$manufacturer', 'active')";
								$dblink->query($sql) or die("<p>Error inserting manufacturer:<br>$sql<br>".$dblink->error);

								redirect("add.php?msg=ManufacturerAdded");
							}
							?>
							<!-- Success or error banners -->
							<?php if (isset($_REQUEST['msg'])) { ?>
								<?php if ($_REQUEST['msg'] == "ManufacturerExists") { ?>
									<div class="alert alert-warning" role="alert">Manufacturer already exists in the database.</div>
								<?php } elseif ($_REQUEST['msg'] == "ManufacturerAdded") { ?>
									<div class="alert alert-success" role="alert">New manufacturer was added successfully!</div>
								<?php } elseif ($_REQUEST['msg'] == "EmptyManufacturer") { ?>
									<div class="alert alert-danger" role="alert">Please enter a manufacturer name.</div>
								<?php } ?>
							<?php } ?>
							<h2>Add a New Manufacturer</h2>
							<form action="add.php?action=addmanufacturer" method="post">
								<div class="form-group">
									<label for="manufacturer">Manufacturer Name:</label>
									<input type="text" name="manufacturer" id="manufacturer" class="form-control" required>
								</div>
								<button type="submit" name="submit" class="btn btn-info">Add Manufacturer</button>
							</form>
							<?php
							break;
					endswitch;
					?>
				</div>
          </div>
     </section>
</body>
</html>

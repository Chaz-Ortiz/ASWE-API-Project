<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Advanced Software Engineering</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.dataTables.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
    <script src="../assets/js/jquery-3.7.1.js"></script>
    <script src="../assets/js/dataTables.js"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script> <!-- Bootstrap JS for toggler -->
	
    <!-- DataTable Initialization -->
    <script>
    $(document).ready(function() {
        $('table.display').DataTable(); // Activate DataTables on any table with class "display"
    });
    </script>
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
    <!-- NAVIGATION MENU -->
    <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                </button>

                <!-- LOGO / TITLE -->
                <a href="#" class="navbar-brand">Add Equipment</a>
            </div>
            <!-- MENU LINKS -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-nav-first">
                    <li><a href="index.php" class="smoothScroll">Home</a></li>
                    <li><a href="search-prof.php" class="smoothScroll">Search Equipment</a></li>
                    <li><a href="add-prof.php" class="smoothScroll">Add Equipment</a></li>
                </ul>
            </div>
        </div>
    </section>
    <!-- HOME SECTION (currently empty) -->
    <section id="home">
        <!-- No content currently inside -->
    </section>
    <!-- SEARCH FEATURE SECTION -->
    <section id="feature">
        <div class="container">
            <?php
			include("../functions.php");
			$dblink = db_iconnect("equipment");

// -------- Display the 3 selection buttons:
			if (!isset($_GET['type'])) {
				echo '<h2>Select what you want to add:</h2>';
				echo '<a class="btn btn-primary" href="add-prof.php?type=device">Add New Equipment</a> ';
				echo '<a class="btn btn-primary" href="add-prof.php?type=type">Add New Device Type</a> ';
				echo '<a class="btn btn-primary" href="add-prof.php?type=manufacturer">Add New Manufacturer</a> ';

// -------- if the "Add New Manufacturer" button is selected
			} else if ($_GET['type'] == 'manufacturer') {
			// If the form has been submitted
			if (isset($_POST['submit']) && $_POST['submit'] == 'addmanufacturer') {
				$manufacturer = trim($_POST['manufacturer']);

				// Basic validation
				if (empty($manufacturer)) {
					redirect("add-prof.php?type=manufacturer&msg=EmptyManufacturer");
				}

				// Validate the name to ensure only alphabet letters and spaces
				if (!isValidName($manufacturer)) {
					redirect("add-prof.php?type=manufacturer&msg=InvalidDeviceTypeName");
				}

				// Check if manufacturer already exists in manu_types
				$sql = "SELECT * FROM `manu_types` WHERE `name` = '$manufacturer'";
				$rst = $dblink->query($sql) or die("<p>Error checking manufacturer:<br>$sql<br>".$dblink->error);

				if ($rst->num_rows > 0) {
					redirect("add-prof.php?type=manufacturer&msg=ManufacturerExists");
				}

				// Insert new manufacturer
				$sql = "INSERT INTO `manu_types` (`name`, `status`) VALUES ('$manufacturer', 'active')";
				$dblink->query($sql) or die("<p>Error inserting manufacturer:<br>$sql<br>".$dblink->error);

				redirect("add-prof.php?type=manufacturer&msg=ManufacturerAdded");
			}

			// Show form and banner (this runs if form hasn't been submitted yet or redirect returned user here)
			if (isset($_REQUEST['msg'])) {
				showBanner($_REQUEST['msg']);
			}

			echo '<h3>Add New Manufacturer</h3>';
			echo '<form method="POST" action="add-prof.php?type=manufacturer">';
			echo '<div class="form-group">';
			echo '<label for="manufacturer">Manufacturer Name</label>';
			echo '<input type="text" name="manufacturer" class="form-control" required>';
			echo '</div>';
			echo '<button type="submit" name="submit" value="addmanufacturer" class="btn btn-success">Add Manufacturer</button>';
			echo '</form>'; 
				
// -------- if the "Add New Device Type" button is selected
			} else if ($_GET['type'] == 'type') {
			// If form submitted
			if (isset($_POST['submit']) && $_POST['submit'] == "adddevicetype") {
				$name = trim($_POST['devicetype']);

				if (empty($name)) {
					redirect("add-prof.php?type=type&msg=NameRequired");
				}

				if (!isValidName($name)) {
					redirect("add-prof.php?type=type&msg=InvalidDeviceTypeName");
				}

				$sql = "SELECT * FROM `device_types` WHERE `name` = '$name'";
				$rst = $dblink->query($sql) or die("<p>Error checking device type:<br>$sql<br>" . $dblink->error);

				if ($rst->num_rows > 0) {
					redirect("add-prof.php?type=type&msg=DeviceTypeExists");
				}

				$sql = "INSERT INTO `device_types` (`name`, `status`) VALUES ('$name', 'active')";
				$dblink->query($sql) or die("<p>Error inserting device type:<br>$sql<br>" . $dblink->error);

				redirect("add-prof.php?msg=DeviceTypeAdded");
			}

			// Show form
			if (isset($_REQUEST['msg'])) {
				showBanner($_REQUEST['msg']);
			}

			echo '<h3>Add New Device Type</h3>';
			echo '<form method="POST" action="add-prof.php?type=type">';
			echo '<div class="form-group">';
			echo '<label for="devicetype">Device Type Name</label>';
			echo '<input type="text" name="devicetype" class="form-control" required>';
			echo '</div>';
			echo '<button type="submit" name="submit" value="adddevicetype" class="btn btn-success">Add Device Type</button>';
			echo '</form>';


// -------- Default logic for the Add New Equipment button				
			} else {
				// Process form submission
				if (isset($_POST['submit']) && $_POST['submit'] == "add") {
					$device = trim($_POST['device']);
					$manufacturer = trim($_POST['manufacturer']);
					$serialNumber = trim($_POST['serialnumber']);

					// Check serial number validity
					if (!isValidSerialNumber($serialNumber)) {
						redirect("add-prof.php?action=addequipment&msg=InvalidSerial");
					}

					// Check if the serial number already exists
					$sql = "SELECT `auto_id` FROM `devices` WHERE `serial_number` = '$serialNumber'";
					$rst = $dblink->query($sql) or die("<p>Error running query:<br>$sql<br>" . $dblink->error);

					if ($rst->num_rows <= 0) {
						// Insert new device
						$sql = "INSERT INTO `devices` (`device_type`, `manufacturer`, `serial_number`) 
								VALUES ('$device', '$manufacturer', '$serialNumber')";
						$dblink->query($sql) or die("<p>Error running insert:<br>$sql<br>" . $dblink->error);

						redirect("index.php?msg=EquipmentAdded");
					} else {
						redirect("add-prof.php?msg=DeviceExists");
					}
				}

				// Fetch dropdown options for device types and manufacturers
				$deviceTypes = [];
				$manufacturers = [];

				$sqlTypes = "SELECT DISTINCT d.device_type 
							 FROM devices d 
							 WHERE d.device_type NOT IN 
								 (SELECT device_type FROM equipment.device_type_inactive) 
							 ORDER BY d.device_type ASC";
				$sqlManus = "SELECT DISTINCT d.manufacturer 
							 FROM devices d 
							 WHERE d.manufacturer NOT IN 
								 (SELECT device_manufacturer FROM equipment.device_manufacturer_inactive) 
							 ORDER BY d.manufacturer ASC";

				$rstTypes = $dblink->query($sqlTypes) or die("<p>Error fetching device types:<br>$sqlTypes<br>" . $dblink->error);
				$rstManus = $dblink->query($sqlManus) or die("<p>Error fetching manufacturers:<br>$sqlManus<br>" . $dblink->error);

				while ($row = $rstTypes->fetch_assoc()) {
					$deviceTypes[] = $row['device_type'];
				}

				while ($row = $rstManus->fetch_assoc()) {
					$manufacturers[] = $row['manufacturer'];
				}

				// Show the form for adding new equipment
				if ($_GET['type'] == 'device') {
					if (isset($_REQUEST['msg'])) {
						showBanner($_REQUEST['msg']);
					}

					echo '<h3>Add New Equipment</h3>';
					echo '<form method="POST" action="add-prof.php?type=device">';

					echo '<div class="form-group">';
					echo '<label for="device">Device Type</label>';
					echo '<select name="device" class="form-control">';
					foreach ($deviceTypes as $type) {
						echo "<option value=\"$type\">$type</option>";
					}
					echo '</select>';
					echo '</div>';

					echo '<div class="form-group">';
					echo '<label for="manufacturer">Manufacturer</label>';
					echo '<select name="manufacturer" class="form-control">';
					foreach ($manufacturers as $manu) {
						echo "<option value=\"$manu\">$manu</option>";
					}
					echo '</select>';
					echo '</div>';

					echo '<div class="form-group">';
					echo '<label for="serialnumber">Serial Number</label>';
					echo '<input type="text" name="serialnumber" class="form-control" required>';
					echo '</div>';

					echo '<button type="submit" name="submit" value="add" class="btn btn-success">Add Equipment</button>';
					echo '</form>';
				}
			}
			?>
        </div>
    </section>
</body>
</html>

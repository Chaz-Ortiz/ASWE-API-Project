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
					$dblink = db_iconnect("equipment");

					// Handle form submission for adding a new device
					if (isset($_POST['submit_equipment'])) {
						$device = trim($_POST['device']);
						$manufacturer = trim($_POST['manufacturer']);
						$serialNumber = trim($_POST['serialnumber']);

						if (!isValidSerialNumber($serialNumber)) {
							redirect("add.php?msg=InvalidSerial");
						}

						$sql = "SELECT `auto_id` FROM `devices` WHERE `serial_number` = '$serialNumber'";
						$rst = $dblink->query($sql) or die("<p>Error:<br>$sql<br>".$dblink->error);

						if ($rst->num_rows == 0) {
							$sql = "INSERT INTO `devices` (`device_type`, `manufacturer`, `serial_number`) 
									VALUES ('$device', '$manufacturer', '$serialNumber')";
							$dblink->query($sql) or die("<p>Error:<br>$sql<br>".$dblink->error);
							redirect("add.php?msg=EquipmentAdded");
						} else {
							redirect("add.php?msg=DeviceExists");
						}
					}

					// Get options for dropdowns
					$deviceTypes = [];
					$manufacturers = [];

					$sqlTypes = "SELECT DISTINCT device_type FROM devices 
								 WHERE device_type NOT IN (SELECT device_type FROM device_type_inactive) 
								 ORDER BY device_type ASC";
					$sqlManus = "SELECT DISTINCT manufacturer FROM devices 
								 WHERE manufacturer NOT IN (SELECT device_manufacturer FROM device_manufacturer_inactive) 
								 ORDER BY manufacturer ASC";

					$rstTypes = $dblink->query($sqlTypes) or die("<p>Error:<br>$sqlTypes<br>".$dblink->error);
					$rstManus = $dblink->query($sqlManus) or die("<p>Error:<br>$sqlManus<br>".$dblink->error);

					while ($row = $rstTypes->fetch_assoc()) {
						$deviceTypes[] = $row['device_type'];
					}
					while ($row = $rstManus->fetch_assoc()) {
						$manufacturers[] = $row['manufacturer'];
					}

					// Display feedback message if present
					if (isset($_REQUEST['msg'])) {
						showBanner($_REQUEST['msg']);
					}
					?>

					<h2>Add a New Device</h2>
					<form method="post">
						<div>
							<label for="device">Device Type:</label>
							<select name="device" required>
								<option value="">Select a device type</option>
								<?php foreach ($deviceTypes as $type): ?>
									<option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div>
							<label for="manufacturer">Manufacturer:</label>
							<select name="manufacturer" required>
								<option value="">Select a manufacturer</option>
								<?php foreach ($manufacturers as $manu): ?>
									<option value="<?= htmlspecialchars($manu) ?>"><?= htmlspecialchars($manu) ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div>
							<label for="serialnumber">Serial Number:</label>
							<input type="text" name="serialnumber" required>
						</div>

						<button type="submit" name="submit_equipment">Add Equipment</button>
					</form>
				</div>
          </div>
     </section>
</body>
</html>
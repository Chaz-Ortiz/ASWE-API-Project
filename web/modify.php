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
                    <a href="#" class="navbar-brand">Modify</a>
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
		 <div class="container">
		 </div>
	</section>

     <!-- FEATURE -->
     <section id="feature">
          <div class="container">
               <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <?php
						include("../functions.php");
                        $dblink=db_iconnect("equipment");
                        
						// Get the 'eid' parameter from the URL query string (e.g., modify.php?eid=123)
						$eid=$_GET['eid']; // add code to validate data and make sure its a number, or whatever else to protect against injections, vulnerabilities. 
                        
						// SQL query to check if the device is marked as inactive
						// '$eid' is the device ID we're interested in
						// The query selects all records from the 'device_manufacturer_inactive' table where the device_id matches $eid
						$checkInactive = "SELECT * FROM device_manufacturer_inactive WHERE device_id = '$eid'";
						//$inactiveResult = $dblink->query($checkInactive);
						//$is_inactive = ($inactiveResult->num_rows > 0);
						
						// Check if the form was submitted using the POST method (i.e., a form submission occurred)
						if ($_SERVER['REQUEST_METHOD'] == 'POST') {
						$status = $_POST['status'];

							
							
						// Serial number Validation	
							
						// Retrieve the serial number from the form input, removing any leading or trailing whitespace
						$serial = trim($_POST['serial']);

						// Validate structure
						$pattern = '/^SN-[a-fA-F0-9-]+$/i'; // Max 76 chars (3 + dash + up to 72)
						$serialLength = strlen($serial);

						if ($serialLength < 4 || $serialLength > 75) {
							echo "<div class='alert alert-danger'>Serial number must be between 4 and 75 characters.</div>";
						} elseif (!preg_match($pattern, $serial)) {
							echo "<div class='alert alert-danger'>Serial number must follow format SN-xxxxx (0-9, a-f only).</div>";
						} else {
							// Check uniqueness
							$safeSerial = $dblink->real_escape_string($serial);
							$checkSQL = "SELECT * FROM `devices` WHERE `serial_number`='$safeSerial' AND `auto_id` != '$eid'";
							$checkResult = $dblink->query($checkSQL) or
								die("<h2>Error checking serial number uniqueness: $checkSQL<br>".$dblink->error.'</h2>');

							if ($checkResult->num_rows > 0) {
								echo "<div class='alert alert-danger'>Serial number already exists. Please use a unique one.</div>";
							} else {
								// Sanitize other fields
								$status = $_POST['status'];
								$type = str_replace("_", " ", $_POST['type']);
								$manufacturer = str_replace("_", " ", $_POST['manufacturer']);

								// Proceed with update
								$updateSQL = "UPDATE `devices` 
											  SET `device_type`='$type', 
												  `manufacturer`='$manufacturer', 
												  `serial_number`='$safeSerial' 
											  WHERE `auto_id`='$eid'";
								$dblink->query($updateSQL) or
									die("<h2>Something went wrong with: $updateSQL<br>".$dblink->error.'</h2>');

								// Handle manufacturer status
								if (isset($_POST['submit'])) {
								$eid = $_POST['eid']; // Assuming you're passing this
								$status = $_POST['status']; // Optional if you're toggling manufacturer inactivity separately

								// Manufacturer inactivity update
								if (isset($_POST['manufacturer_inactive'])) {
									$checkSQL = "SELECT * FROM `device_manufacturer_inactive` WHERE `device_id`='$eid'";
									$checkResult = $dblink->query($checkSQL) or
										die("<h2>Something went wrong with: $checkSQL<br>".$dblink->error.'</h2>');

									if ($checkResult->num_rows == 0) {
										$insertSQL = "INSERT INTO `device_manufacturer_inactive` (`device_id`) VALUES ('$eid')";
										$dblink->query($insertSQL) or
											die("<h2>Something went wrong with: $insertSQL<br>".$dblink->error.'</h2>');
									}
								} else {
									$deleteSQL = "DELETE FROM `device_manufacturer_inactive` WHERE `device_id`='$eid'";
									$dblink->query($deleteSQL) or
										die("<h2>Something went wrong with: $deleteSQL<br>".$dblink->error.'</h2>');
								}

								echo "<div class='alert alert-success'>Manufacturer Status Changes saved successfully.</div>";
							}

								// Handle status update
								if ($status == "inactive") {
									$checkSQL = "SELECT * FROM `device_status_inactive` WHERE `device_id`='$eid'";
									$checkResult = $dblink->query($checkSQL) or
										die("<h2>Something went wrong with: $checkSQL<br>".$dblink->error.'</h2>');

									if ($checkResult->num_rows == 0) {
										$insertSQL = "INSERT INTO `device_status_inactive` (`device_id`) VALUES ('$eid')";
										$dblink->query($insertSQL) or
											die("<h2>Something went wrong with: $insertSQL<br>".$dblink->error.'</h2>');
									}
								} else {
									$deleteSQL = "DELETE FROM `device_status_inactive` WHERE `device_id`='$eid'";
									$dblink->query($deleteSQL) or
										die("<h2>Something went wrong with: $deleteSQL<br>".$dblink->error.'</h2>');
								}

								echo "<div class='alert alert-success'>Status Changes saved successfully.</div>";
							}
						}

						$type = str_replace("_", " ", $_POST['type']);
						$manufacturer = str_replace("_", " ", $_POST['manufacturer']);

						// Handle status update
						if ($status == "inactive") {
							// Check if already inactive
							$checkSQL = "SELECT * FROM `device_status_inactive` WHERE `device_id`='$eid'";
							$checkResult = $dblink->query($checkSQL) or
								die("<h2>Something went wrong with: $checkSQL<br>".$dblink->error.'</h2>');

							if ($checkResult->num_rows == 0) {
								$insertSQL = "INSERT INTO `device_status_inactive` (`device_id`) VALUES ('$eid')";
								$dblink->query($insertSQL) or
									die("<h2>Something went wrong with: $insertSQL<br>".$dblink->error.'</h2>');
							}
						} else {
							// If status is active, remove from inactive list if present
							$deleteSQL = "DELETE FROM `device_status_inactive` WHERE `device_id`='$eid'";
							$dblink->query($deleteSQL) or
								die("<h2>Something went wrong with: $deleteSQL<br>".$dblink->error.'</h2>');
						}

						echo "<div class='alert alert-success'>Changes saved successfully.</div>";
					}

						$sql="Select * from `devices` where `auto_id`='$eid'";
                        $result=$dblink->query($sql) or
                            die("<h2>Something went wrong with: $sql".$dblink->error.'</h2>');
                        $info=$result->fetch_array(MYSQLI_ASSOC);
						// Device ID
						echo '<form method="post" action="">';
                        echo '<p>Device ID: <b>'.$info['auto_id'].'</b></p>';
					
						// Modify Equipment
						echo '<h2>Modify Equipment:</h2>';
						// Modify Device Serial Number
						echo '<div class="form-group mb-3">';
						echo '<label for="serial">Modify Device Serial Number:</label><br>';
						echo '<input type="text" class="form-control mt-1" id="serial" name="serial" value="' . htmlspecialchars($info['serial_number']) . '" size="100">';
						echo '</div>';
						// Modify Status 
						echo '<div class="form-group">';
						echo '<label for="exampleDevice">Modify Status:</label>';
						$sql="Select * from `device_status_inactive` where `device_id`='$info[auto_id]'";
						$result = $dblink->query($sql) or
							die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");
						echo '<select class="form-control" name="status">';
						if ($result->num_rows > 0) {
							echo '<option value="active">Active</option>';
							echo '<option value="inactive" selected>Inactive</option>';
						} else {
							echo '<option value="active" selected>Active</option>';
							echo '<option value="inactive">Inactive</option>';
						}
						echo '</select>';
						echo '</div>';

						
						
						
						
						
						
						// Modify Manufacturer 
						echo '<h2>Modify Manufacturer:</h2>';
						// Modify Manufacturer Name field
						echo '<div class="form-group mb-3">';
						echo '<label for="manufacturer_name">Modify Manufacturer Name:</label><br>';
						echo '<input type="text" class="form-control mt-1" id="manufacturer_name" name="manufacturer_name" value="' . htmlspecialchars($info['manufacturer']) . '" size="100">';
						echo '</div>';

						if (isset($_POST['update_manufacturer'])) {
							$eid = $_POST['eid'];
							$newManufacturer = $_POST['manufacturer_name'];

							// Validate manufacturer using the function from functions.php
							$validationResult = validateManufacturer($newManufacturer, $eid, $dblink);

							if ($validationResult === true) {
								// If validation passes, proceed with the update

								// Escape the manufacturer for safe SQL query
								$safeManufacturer = $dblink->real_escape_string($newManufacturer);

								// Update the manufacturer in the database
								$updateManufacturerSQL = "UPDATE devices SET manufacturer = '$safeManufacturer' WHERE auto_id = '$eid'";
								$dblink->query($updateManufacturerSQL) or
									die("<h2>Error updating manufacturer: $updateManufacturerSQL<br>".$dblink->error.'</h2>');

								echo "<div class='alert alert-success'>Manufacturer updated successfully.</div>";
							} else {
								// If validation fails, display the error message
								echo $validationResult;
							}
						}


						if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manufacturer_name'])) {
						$newManufacturer = $_POST['manufacturer_name'];
						$autoId = $_POST['auto_id'];

						$sql = "UPDATE `devices` SET `manufacturer`=? WHERE `auto_id`=?";
						$stmt = $dblink->prepare($sql);
						$stmt->bind_param("si", $newManufacturer, $autoId);

						if ($stmt->execute()) {
							echo "<div class='alert alert-success'>Manufacturer updated successfully!</div>";
						} else {
							echo "<div class='alert alert-danger'>Error updating manufacturer: " . $stmt->error . "</div>";
						}
					}

						
						
						
						// Manufacturer Drop-down
						echo '<div class="form-group">';
						echo '<label for="exampleDevice">Manufacturer:</label>';
						echo '<select class="form-control" name="manufacturer">';
						$sql = "SELECT DISTINCT(`manufacturer`) FROM `devices`";
						$result = $dblink->query($sql) or
							die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");
						while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
							$manufacturer = $data['manufacturer'];
							$value = str_replace(" ", "_", $manufacturer);

							if ($manufacturer == $info['manufacturer']) {
								echo '<option value="'. $value .'" selected>' . $manufacturer . '</option>';
							} else {
								echo '<option value="'. $value .'">' . $manufacturer . '</option>';
							}
						}
						echo '</select>';
                        echo '</div>';
						// Modify Manufacturer status 
						echo '<div class="form-group">';
						echo '<label for="manufacturerStatus">Modify Manufacturer Status:</label>';
						echo '<select class="form-control" id="manufacturer_status" name="manufacturer_status">';
						echo '<option value="active"' . (!$is_inactive ? ' selected' : '') . '>Active</option>';
						echo '<option value="inactive"' . ($is_inactive ? ' selected' : '') . '>Inactive</option>';
						echo '</select>';
						echo '</div>';
						
						// Modify Device Type
						echo '<h2>Modify Device Type:</h2>';
						// Modify Device Type field
						echo '<div class="form-group mb-3">';
						echo '<label for="serial">Modify Device Type:</label><br>';
						
						echo '<input type="text" class="form-control mt-1" id="device_type" name="device_type" value="' . htmlspecialchars($info['device_type']) . '" size="100">';
						echo '</div>';

						// Device Type Drop-down
						echo '<div class="form-group">';
						echo '<label for="exampleDevice">Device Type:</label>';
						echo '<select class="form-control" name="type">';
						$sql = "SELECT DISTINCT(`device_type`) FROM `devices`";
						$result = $dblink->query($sql) or
							die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");
						while ($data = $result->fetch_array(MYSQLI_ASSOC)) 
						{
							$value = str_replace(" ", "_", $data['device_type']);
							if ($data['device_type']==$info['device_type'])
								echo '<option value="'. $value.'"selected>' . $data['device_type'] . '</option>';
							else
								echo '<option value="'. $value.'">' . $data['device_type'] . '</option>';
						}
						echo '</select>';
                        echo '</div>';
						// Modify Device Type Status Drop-down:
						echo '<div class="form-group">';
						echo '<label for="manufacturerStatus">Modify Device Type Status:</label>';
						echo '<select class="form-control" id="device_type_status" name="device_type_status">';
						echo '<option value="active"' . (!$is_inactive ? ' selected' : '') . '>Active</option>';
						echo '<option value="inactive"' . ($is_inactive ? ' selected' : '') . '>Inactive</option>';
						echo '</select>';
						echo '</div>';						
						
						// Submit button 
						echo '<input type="submit" class="btn btn-primary" value="Submit">';
						echo '</form>';
                        ?>
                    </div>
               </div>
          </div>
     </section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>	
</body>
</html>
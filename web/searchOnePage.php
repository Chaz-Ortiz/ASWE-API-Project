<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Advanced Software Engineering</title>

    <!-- CSS Dependencies -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.dataTables.css">

    <!-- Main Site Styling -->
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">

    <!-- JS Dependencies -->
    <script src="../assets/js/jquery-3.7.1.js"></script>
    <script src="../assets/js/dataTables.js"></script>

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
                <a href="#" class="navbar-brand">Search Equipment Database</a>
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

			// ------------------- SEARCH EQUIPMENT FORM -------------------
			echo '<div class="feature-thumb">';
			echo '<form method="post" action="">';
			echo '<h2>Search Equipment</h2>';

			// Device Type dropdown
			echo '<div class="form-group">';
			echo '<label for="device_type">Device Type:</label>';
			echo '<select class="form-control" name="device_type">';
			$sql = "SELECT DISTINCT device_type 
					FROM devices 
					WHERE device_type NOT IN (SELECT device_type FROM equipment.device_type_inactive)
					ORDER BY device_type ASC";
			$result = $dblink->query($sql);
			while ($row = $result->fetch_assoc()) {
				$val = htmlspecialchars($row['device_type']);
				echo "<option value='$val'>$val</option>";
			}
			echo '<option value="all">All Device Types</option>';
			echo '</select>';
			echo '</div>';

			// Manufacturer dropdown
			echo '<div class="form-group">';
			echo '<label for="manufacturer">Manufacturer:</label>';
			echo '<select class="form-control" name="manufacturer">';

			$sql_manufacturers = "SELECT DISTINCT d.manufacturer FROM devices d WHERE d.manufacturer NOT IN (SELECT device_manufacturer FROM equipment.device_manufacturer_inactive) ORDER BY d.manufacturer ASC";

			$result_manufacturers = $dblink->query($sql_manufacturers);
			while ($row = $result_manufacturers->fetch_assoc()) {
				$val = htmlspecialchars($row['manufacturer']);
				echo "<option value='$val'>$val</option>";
			}

			echo '<option value="all">All Manufacturers</option>';
			echo '</select>';
			echo '</div>';
			echo '<div class="form-group mt-3">';
			echo '<button type="submit" class="btn btn-success" name="search_equipment" value="search">Search Equipment</button>';			
			echo '</div>';
			echo '</form>';
			echo '</div>'; // close feature-thumb
			?>

			<?php
			// ------------------- SEARCH SERIAL FORM -------------------
			echo '<div class="feature-thumb">';
			echo '<form method="post" action="">';
			echo '<h2>Search By Serial Number</h2>';
			echo '<div class="form-group">';
			echo '<label for="serial_number">Serial Number:</label>';
			echo '<input type="text" class="form-control" name="serial_number" placeholder="Enter serial number">';
			echo '</div>';
			echo '<div class="form-group mt-3">';
			echo '<button type="submit" class="btn btn-success" name="search_serial">Search by Serial Number</button>';
			echo '</div>';
			echo '</form>';
			echo '</div>'; // close feature-thumb
			?>

			<?php
			$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'active';

			echo '<div class="feature-thumb">';
			echo '<form method="get" action="">';
			echo '<h2 class="mt-4">Search All Devices</h2>';
			echo '<div class="form-row mb-3">';
			echo '<label for="status" class="col-form-label mr-2">Filter By Status:</label>';
			echo '<select name="status" id="status" class="form-control col-auto mr-2">';
			echo '<option value="active"' . ($statusFilter == 'active' ? ' selected' : '') . '>Only Active</option>';
			echo '<option value="inactive"' . ($statusFilter == 'inactive' ? ' selected' : '') . '>Only Inactive</option>';
			echo '<option value="all"' . ($statusFilter == 'all' ? ' selected' : '') . '>All</option>';
			echo '</select>';
			echo '<button type="submit" class="btn btn-primary" name="search_all">Search</button>';
			echo '</div>';
			echo '</form>';
			echo '</div>'; // close feature-thumb
			?>

			<?php
			// ------------------- HANDLE FORM SUBMISSIONS -------------------
			if (isset($_POST['submit']) && $_POST['submit'] == "search") {
				$type = str_replace("_", " ", $_POST['device_type']);
				$manu = str_replace("_", " ", $_POST['manufacturer']);

				$typeStr = ($type == "all") ? "`device_type` LIKE '%'" : "`device_type` = '$type'";
				$manuStr = ($manu == "all") ? "`manufacturer` LIKE '%'" : "`manufacturer` = '$manu'";

				$sql = "SELECT * FROM `devices` WHERE $typeStr AND $manuStr 
						AND `auto_id` NOT IN (SELECT `device_id` FROM `device_status_inactive`)";

				$result = $dblink->query($sql) or
					die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");

				echo "<p><strong>Showing results for:</strong> $type devices from $manu</p>";

				echo '<div class="row">';
				echo '<div class="col-md-10 col-md-offset-1">';
				echo '<table class="display table table-striped table-bordered" cellspacing="0" width="100%">';
				echo '<thead><tr><th>Device Type</th><th>Manufacturer</th><th>Serial Number</th><th>Action</th></tr></thead>';
				echo '<tbody>';
				while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
					echo '<tr>';
					echo '<td>' . htmlspecialchars($data['device_type']) . '</td>';
					echo '<td>' . htmlspecialchars($data['manufacturer']) . '</td>';
					echo '<td>' . htmlspecialchars($data['serial_number']) . '</td>';
					echo '<td><a class="btn btn-success" href="view.php?eid=' . $data['auto_id'] . '">View</a></td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				echo '</div>';
				echo '</div>';
			}

			elseif (isset($_POST['search_serial'])) {
				$serial = trim($_POST['serial_number']);
				echo "<p><strong>Searching for serial number:</strong> $serial</p>";
				// Perform query here...
			}
			elseif (isset($_GET['search_all'])) {
				echo "<p><strong>Showing:</strong> $statusFilter devices</p>";

				if ($statusFilter == 'active') {
					$sql = "SELECT * FROM devices 
							WHERE device_id NOT IN (SELECT device_id FROM equipment.device_status_inactive)
							ORDER BY manufacturer, device_type ASC";
				} else if ($statusFilter == 'inactive') {
					$sql = "SELECT * FROM equipment.device_status_inactive";
				} else {
					$sql = "SELECT * FROM devices 
							ORDER BY manufacturer, device_type ASC";
				}

				// Run the query and display results if needed...
			}
			?>

        </div>
    </section>
</body>
</html>

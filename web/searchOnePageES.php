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
			// Show forms only if no search has been submitted
			if (!isset($_POST['search_equipment']) && !isset($_POST['search_serial']) && !isset($_POST['device_type']) && !isset($_POST['manufacturer'])) {
				// ------------------- SEARCH EQUIPMENT FORM -------------------
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
			

			// ------------------- EQUIPMENT SEARCH HANDLER -------------------
			if (isset($_POST['search_equipment'])) {
				$deviceType = trim($_POST['device_type']);
				$manufacturer = trim($_POST['manufacturer']);
				$serial = trim($_POST['serial_number']);

				$sql = "SELECT * FROM devices 
						WHERE device_type LIKE '%$deviceType%' 
						AND manufacturer LIKE '%$manufacturer%' 
						AND serial_number LIKE '%$serial%' 
						AND auto_id NOT IN (SELECT device_id FROM device_status_inactive)";

				$result = $dblink->query($sql) or
					die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");

				echo "<p><strong>Showing results for:</strong> Type: $deviceType, Manufacturer: $manufacturer, Serial Number: $serial</p>";

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

			// ------------------- SERIAL NUMBER SEARCH HANDLER -------------------
			if (isset($_POST['search_serial'])) {
				$serial = trim($_POST['serial_number']);
				$serialEscaped = $dblink->real_escape_string($serial);

				$sql = "SELECT * FROM devices 
						WHERE serial_number LIKE '%$serialEscaped%' 
						AND auto_id NOT IN (SELECT device_id FROM device_status_inactive)";

				$result = $dblink->query($sql) or
					die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");

				echo "<p><strong>Showing results for serial number:</strong> $serial</p>";

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
			?>

        </div>
    </section>
</body>
</html>

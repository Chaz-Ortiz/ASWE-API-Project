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
                <a href="#" class="navbar-brand">Search Equipment Database</a>
            </div>
            <!-- MENU LINKS -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-nav-first">
                    <li><a href="index.php" class="smoothScroll">Home</a></li>
                    <li><a href="search-prof.php" class="smoothScroll">Search Equipment</a></li>
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
            $dblink=db_iconnect("equipment");
// -------- Display the 4 buttons: "Search by Device", "Search by Manufacturer", "Search by Serial Number", "View All"
            if (!isset($_GET['type'])) {
				echo '<h2>Select what you want to search by:</h2>';
                echo '<a class="btn btn-primary" href="search-prof.php?type=device">Search by Device Type</a> ';
                echo '<a class="btn btn-primary" href="search-prof.php?type=manufacturer">Search by Manufacturer</a> ';
                echo '<a class="btn btn-primary" href="search-prof.php?type=serialNum">Search by Serial Number</a> ';
                echo '<a class="btn btn-primary" href="search-prof.php?type=all">Search All</a>';	
			
// -------- Default logic for the Search by Device button
			} else {
                // Show dropdown form for device & manufacturer
				echo '<h2>Search by Device Type</h2>';
				echo '<form method="post" action="">';
				echo '<div class="row justify-content-center">';
				echo '<div class="col-md-12">';

				// Device dropdown
				echo '<div class="form-group">';
				echo '<label for="exampleDevice">Device:</label>';
				echo '<select class="form-control" name="device">';
				// Get unique device types from the devices table and exclude device types in device_type_inactive 
                $sql = "SELECT DISTINCT d.device_type FROM devices d WHERE d.device_type NOT IN (SELECT device_type FROM equipment.device_type_inactive) ORDER BY d.device_type ASC";
                $result = $dblink->query($sql) or
                    die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");
                while ($data = $result->fetch_array(MYSQLI_ASSOC)) 
				{
                    $value = str_replace(" ", "_", $data['device_type']);
                    echo '<option value="' . $value . '">' . $data['device_type'] . '</option>';
                }
                echo '<option value="all">All Devices</option>';
                echo '</select>';
				echo '</div>';
                
				// Manufacturer Dropdown
                echo '<label for="exampleManufacturer">Manufacturer:</label>';
                echo '<select class="form-control" name="manufacturer">';
                // Only returns manufacturers not listed in device_manufacturer_inactive, sorted alphabetically
				$sql = "SELECT DISTINCT d.manufacturer FROM devices d WHERE d.manufacturer NOT IN (SELECT device_manufacturer FROM equipment.device_manufacturer_inactive) ORDER BY d.manufacturer ASC";
                $result = $dblink->query($sql) or
                    die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");
                while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
                    $value = str_replace(" ", "_", $data['manufacturer']);
                    echo '<option value="' . $value . '">' . $data['manufacturer'] . '</option>';
                }
                echo '<option value="all">All Manufacturers</option>';
                echo '</select>';
                echo '</div>';

                echo '<div class="form-group mt-3">';
				echo '<button type="submit" class="btn btn-success" value="search" name="submit">Search</button>';
				echo '</div>';

				echo '</div>'; // end col-md-12
				echo '</div>'; // end row
				echo '</form>';		
				
            }
			// Search by device
            // If form is submitted
            if (isset($_POST['submit']) && $_POST['submit'] == "search") {
                // Clean up values from form
                $type = str_replace("_", " ", $_POST['device']);
                $manu = str_replace("_", " ", $_POST['manufacturer']);

                // Build WHERE clause dynamically
                $typeStr = ($type == "all") ? "`device_type` LIKE '%'" : "`device_type` = '$type'";
                $manuStr = ($manu == "all") ? "`manufacturer` LIKE '%'" : "`manufacturer` = '$manu'";

                // Query excludes any device that has been added to device_status_inactive
                $sql = "SELECT * FROM `devices` WHERE $typeStr AND $manuStr AND `auto_id` NOT IN (SELECT `device_id` FROM `device_status_inactive`)";

                $result = $dblink->query($sql) or
                    die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");
				// Search by device
                // Display results in a DataTable
                echo '<div class="row">';
				echo '<div class="col-md-8 col-md-offset-2">'; // Or try col-md-10 / col-md-8 if you want it narrower

				echo '<table id="resultsTable" class="display table table-striped table-bordered" cellspacing="0" width="100%">';

				echo '<thead>';
				echo '<tr><th>Device Type</th><th>Manufacturer</th><th>Serial Number</th><th>Action</th></tr>';
				echo '</thead>';
				echo '<tbody>';
				while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
					echo '<tr>';
					echo '<td>' . $data['device_type'] . '</td>';
					echo '<td>' . $data['manufacturer'] . '</td>';
					echo '<td>' . $data['serial_number'] . '</td>';
					echo '<td><a class="btn btn-success" href="view.php?eid=' . $data['auto_id'] . '">View</a></td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';

				echo '</div>'; // end col-md-12
				echo '</div>'; // end row

				} 
            ?>
        </div>
    </section>
</body>
</html>

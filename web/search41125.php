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
            $dblink=db_iconnect("equipment");
            // Show search options if no search type is set
            if (!isset($_GET['type'])) {
                echo '<a class="btn btn-primary" href="search.php?type=device">Search by Device</a> ';
                echo '<a class="btn btn-primary" href="search.php?type=manufacturer">Search by Manufacturer</a> ';
                echo '<a class="btn btn-primary" href="search.php?type=serialNum">Search by Serial Number</a> ';
                echo '<a class="btn btn-primary" href="search.php?type=all">View All</a>';
            } else {
                // Show dropdown form for device & manufacturer
                echo '<form method="post" action="">';
                echo '<div class="form-group">';

                // Device Dropdown
                echo '<label for="exampleDevice">Device:</label>';
                echo '<select class="form-control" name="device">';
                $sql = "SELECT DISTINCT(`device_type`) FROM `devices`";
                $result = $dblink->query($sql) or
                    die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");
                while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
                    $value = str_replace(" ", "_", $data['device_type']);
                    echo '<option value="' . $value . '">' . $data['device_type'] . '</option>';
                }
                echo '<option value="all">All Devices</option>';
                echo '</select>';

                // Manufacturer Dropdown
                echo '<label for="exampleManufacturer">Manufacturer:</label>';
                echo '<select class="form-control" name="manufacturer">';
                $sql = "SELECT DISTINCT(`manufacturer`) FROM `devices`";
                $result = $dblink->query($sql) or
                    die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");
                while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
                    $value = str_replace(" ", "_", $data['manufacturer']);
                    echo '<option value="' . $value . '">' . $data['manufacturer'] . '</option>';
                }
                echo '<option value="all">All Manufacturers</option>';
                echo '</select>';

                echo '</div>';
                echo '<button type="submit" class="btn btn-success" value="search" name="submit">Search</button>';
                echo '</form>';
            }

            // If form is submitted
            if (isset($_POST['submit']) && $_POST['submit'] == "search") {
                // Clean up values from form
                $type = str_replace("_", " ", $_POST['device']);
                $manu = str_replace("_", " ", $_POST['manufacturer']);

                // Build WHERE clause dynamically
                $typeStr = ($type == "all") ? "`device_type` LIKE '%'" : "`device_type` = '$type'";
                $manuStr = ($manu == "all") ? "`manufacturer` LIKE '%'" : "`manufacturer` = '$manu'";

                // Query based on criteria
                $sql = "SELECT * FROM `devices` WHERE $typeStr AND $manuStr";
                $result = $dblink->query($sql) or
                    die("<h2>Something went wrong with $sql<br>" . $dblink->error . "</h2>");

                // Display results in a DataTable
                echo '<table class="display" style="width:100%">';
                echo '<thead>';
                echo '<tr><th>Device Type</th><th>Manufacturer</th><th>Serial Number</th></tr>';

                echo '</thead>';
                echo '<tbody>';
                while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $data['device_type'] . '</td>';
                    echo '<td>' . $data['manufacturer'] . '</td>';
                    echo '<td>' . $data['serial_number'] . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }
            ?>
        </div>
    </section>

</body>
</html>

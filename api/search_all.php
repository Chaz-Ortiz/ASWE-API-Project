<?php
// Assuming db_iconnect function is already defined elsewhere
$dblink = db_iconnect("equipment");
$output = [];

// Check if required GET parameters exist and set defaults if not
$type = isset($_GET['type']) ? $_GET['type'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : 'all'; // Default to 'all' if no status is provided

// You can now use $type and $status to query the database or use them to adjust the query accordingly

// Example for searching by status (this should be adjusted based on your actual database schema and requirements)
if ($status !== 'all') {
    // Perform your query to filter by status, if not 'all'
    $query = "SELECT * FROM devices WHERE status = :status";
    $stmt = $dblink->prepare($query);
    $stmt->execute([':status' => $status]);
    $output = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If the status is 'all', fetch all records
    $query = "SELECT * FROM devices";
    $stmt = $dblink->prepare($query);
    $stmt->execute();
    $output = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Output the result as JSON
echo json_encode($output);
?>

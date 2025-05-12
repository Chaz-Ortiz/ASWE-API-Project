<?php
header("Content-Type: application/json");
$dblink = db_iconnect("equipment");

$response = [
    'success' => false,
    'message' => '',
    'devices' => [],
    'manufacturers' => [],
    'results' => []
];

try {
    // Get unique device types
    $stmt = $pdo->query("SELECT DISTINCT device FROM equipment ORDER BY device ASC");
    $response['devices'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get unique manufacturers
    $stmt = $pdo->query("SELECT DISTINCT manufacturer FROM equipment ORDER BY manufacturer ASC");
    $response['manufacturers'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // If POST request is made with form data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $device = isset($_POST['device']) ? $_POST['device'] : '';
		$manufacturer = isset($_POST['manufacturer']) ? $_POST['manufacturer'] : '';


        $query = "SELECT * FROM equipment WHERE 1=1";
        $params = [];

        if (!empty($device)) {
            $query .= " AND device = :device";
            $params[':device'] = $device;
        }

        if (!empty($manufacturer)) {
            $query .= " AND manufacturer = :manufacturer";
            $params[':manufacturer'] = $manufacturer;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response['results'] = $results;
        $response['success'] = true;
    } else {
        $response['message'] = "No POST data received.";
    }

} catch (PDOException $e) {
    $response['message'] = "Database error: " . $e->getMessage();
}

echo json_encode($response);
?>

<?php
require_once '../connection.php'; // ✅ use your existing DB connection

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$bookId = $input['bookId'] ?? null;
$table = $input['table'] ?? null;

// ✅ Secure table whitelist to prevent SQL injection
$allowedTables = ['mylibrary', 'myfavorites', 'myopencover', 'myclosedcover', 'mydustyshelves'];

if (!$bookId || !$table || !in_array($table, $allowedTables)) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// ✅ Prepare and run the query safely
$query = "SELECT 1 FROM `$table` WHERE bookId = ?";
$stmt = $link->prepare($query);

if ($stmt) {
    $stmt->bind_param('s', $bookId);
    $stmt->execute();
    $stmt->store_result();

    echo json_encode(['exists' => $stmt->num_rows > 0]);

    $stmt->close();
} else {
    echo json_encode(['error' => 'Query preparation failed']);
}

$link->close();
?>

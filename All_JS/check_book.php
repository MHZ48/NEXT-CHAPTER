<?php
session_start();
include_once '../PHP/connection.php';

$allowedTables = ['mylibrary', 'myopencover', 'myclosedcover', 'mydustyshelves', 'myfavorites'];

$data = json_decode(file_get_contents('php://input'), true);
$response = ['exists' => false];

if (isset($data['table'], $data['bookId'])) {
    $table = $data['table'];
    $bookId = $data['bookId'];

    if (!in_array($table, $allowedTables)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid table name']);
        exit;
    }

    $query = "SELECT * FROM `$table` WHERE bookId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $response['exists'] = true;
    }

    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
?>

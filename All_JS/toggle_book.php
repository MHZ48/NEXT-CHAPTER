<?php
session_start();
include_once '../PHP/connection.php';

$allowedTables = ['mylibrary', 'myopencover', 'myclosedcover', 'mydustyshelves', 'myfavorites'];

$data = json_decode(file_get_contents("php://input"), true);
$response = [];

if (isset($data['table'], $data['bookId'], $data['title'], $data['author'])) {
    $table = $data['table'];
    $bookId = $data['bookId'];
    $title = $data['title'];
    $author = $data['author'];
    $thumbnail = isset($data['thumbnail']) ? $data['thumbnail'] : '';

    if (!in_array($table, $allowedTables)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid table name']);
        exit;
    }

    // Optional: block unauthenticated users
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $checkQuery = "SELECT * FROM `$table` WHERE bookId = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $bookId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult && $checkResult->num_rows > 0) {
        // Exists → delete it
        $deleteQuery = "DELETE FROM `$table` WHERE bookId = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("s", $bookId);
        $deleteStmt->execute();
        $deleteStmt->close();
        $response['status'] = 'removed';
    } else {
        // Doesn’t exist → insert
        $insertQuery = "INSERT INTO `$table` (bookId, title, author, thumbnail) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssss", $bookId, $title, $author, $thumbnail);
        $insertStmt->execute();
        $insertStmt->close();
        $response['status'] = 'added';
    }

    $checkStmt->close();
} else {
    $response['error'] = 'Missing data';
    http_response_code(400);
}

header('Content-Type: application/json');
echo json_encode($response);
?>

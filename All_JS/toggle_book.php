<?php
session_start();
header('Content-Type: application/json');

try {
    // Use absolute path to connection.php
require_once '../connection.php';
    
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not logged in');
    }

    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }

    $required = ['bookId', 'table'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    $allowedTables = ['favorites', 'library', 'opencover', 'closedcover', 'dustyshelves'];
    if (!in_array($input['table'], $allowedTables)) {
        throw new Exception('Invalid table specified');
    }

    $user_id = $_SESSION['user_id'];
    $bookId = $input['bookId'];
    $table = $input['table'];

    // Check if book exists for user
    $checkSql = "SELECT id FROM `$table` WHERE bookId = ? AND user_id = ?";
    $stmt = $link->prepare($checkSql);
    if (!$stmt) {
        throw new Exception('Database query preparation failed');
    }
    $stmt->bind_param('si', $bookId, $user_id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if ($exists) {
        // Remove the book
        $delete = $link->prepare("DELETE FROM `$table` WHERE bookId = ? AND user_id = ?");
        $delete->bind_param('si', $bookId, $user_id);
        $delete->execute();
        echo json_encode(['status' => 'removed']);
    } else {
        // Add the book
        $insert = $link->prepare("INSERT INTO `$table` 
            (user_id, bookId) 
            VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param('issss', 
            $user_id,
            $bookId,
        );
        $insert->execute();
        echo json_encode(['status' => 'added']);
    }

} catch (Exception $e) {
    error_log("Toggle Book Error: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>
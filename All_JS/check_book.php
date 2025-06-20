<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Authentication required']));
}
$user_id = $_SESSION['user_id'];

require_once '../connection.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $book_id = $input['book_id'] ?? null;
    $table = $input['table'] ?? null;

    $allowedTables = ['library', 'favorites', 'opencover', 'closedcover', 'dustyshelves'];
    if (!$book_id || !$table || !in_array($table, $allowedTables)) {
        throw new Exception('Invalid input');
    }

    $user_id = 1; // Replace with actual user ID from session
    
    $query = "SELECT 1 FROM `$table` WHERE book_id = ? AND user_id = ?";
    $stmt = $link->prepare($query);
    if (!$stmt) {
        throw new Exception('Query preparation failed');
    }
    
    $stmt->bind_param('si', $book_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    echo json_encode(['exists' => $stmt->num_rows > 0]);
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $link->close();
}
?>
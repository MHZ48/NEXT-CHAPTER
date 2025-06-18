<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Not logged in']));
}

$input = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'];


header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $bookId = $input['bookId'] ?? null;
    $table = $input['table'] ?? null;
    
    // Validate input
    $allowedTables = ['library', 'favorites', 'opencover', 'closedcover', 'dustyshelves'];
    if (!$bookId || !$table || !in_array($table, $allowedTables)) {
        throw new Exception('Invalid input');
    }

    // Check if the book exists
    $checkSql = "SELECT 1 FROM `$table` WHERE bookId = ? AND user_id = ?";
    $checkStmt = $link->prepare($checkSql);
    if (!$checkStmt) {
        throw new Exception('Check query preparation failed');
    }
    
    // You need to get the current user_id from session or token
    $user_id = 1; // Replace with actual user ID from session
    
    $checkStmt->bind_param('si', $bookId, $user_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Remove the book
        $deleteStmt = $link->prepare("DELETE FROM `$table` WHERE bookId = ? AND user_id = ?");
        if ($deleteStmt) {
            $deleteStmt->bind_param('si', $bookId, $user_id);
            $deleteStmt->execute();
            $deleteStmt->close();
            echo json_encode(['status' => 'removed']);
        } else {
            throw new Exception('Delete query failed');
        }
    } else {
        // Add the book
        $insertStmt = $link->prepare("INSERT INTO `$table` (user_id, bookId) VALUES (?, ?)");
        if ($insertStmt) {
            $insertStmt->bind_param('issss', $user_id, $bookId);
            $insertStmt->execute();
            $insertStmt->close();
            echo json_encode(['status' => 'added']);
        } else {
            throw new Exception('Insert query failed');
        }
    }

    $checkStmt->close();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $link->close();
}
?>
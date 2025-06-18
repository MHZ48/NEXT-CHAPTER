<?php
require_once '../connection.php'; // ✅ reuse existing connection

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$bookId = $input['bookId'] ?? null;
$table = $input['table'] ?? null;

// ✅ Validate input
$allowedTables = ['library', 'favorites', 'opencover', 'closedcover', 'dustyshelves'];
if (!$bookId || !$table || !in_array($table, $allowedTables)) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// ✅ Check if the book exists
$checkSql = "SELECT 1 FROM `$table` WHERE bookId = ?";
$checkStmt = $link->prepare($checkSql);
if (!$checkStmt) {
    echo json_encode(['error' => 'Check query preparation failed']);
    exit;
}
$checkStmt->bind_param('s', $bookId);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // ✅ Remove the book
    $deleteStmt = $link->prepare("DELETE FROM `$table` WHERE bookId = ?");
    if ($deleteStmt) {
        $deleteStmt->bind_param('s', $bookId);
        $deleteStmt->execute();
        $deleteStmt->close();
        echo json_encode(['status' => 'removed']);
    } else {
        echo json_encode(['error' => 'Delete query failed']);
    }
} else {
    // ✅ Add the book
    $insertStmt = $link->prepare("INSERT INTO `$table` (bookId, title, author, thumbnail) VALUES (?, ?, ?, ?)");
    if ($insertStmt) {
        $insertStmt->bind_param('ssss', $bookId, $title, $author, $thumbnail);
        $insertStmt->execute();
        $insertStmt->close();
        echo json_encode(['status' => 'added']);
    } else {
        echo json_encode(['error' => 'Insert query failed']);
    }
}

$checkStmt->close();
$link->close();
?>

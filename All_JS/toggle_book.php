<?php
/**
 * toggle_book.php
 *
 * Adds or removes a book from one of the user's lists:
 * favorites, library, opencover, closedcover, dustyshelves.
 * Expects JSON or form data with 'book_id' and 'table'.
 * Returns JSON { status: 'added'|'removed', table: string, book_id: string }.
 */

session_start();
require_once '../connection.php';

header('Content-Type: application/json');

// Ensure user is authenticated
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Parse input (JSON body preferred)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    // Fallback to POST data
    $data = [
        'book_id' => $_POST['book_id'] ?? null,
        'table'  => $_POST['table']  ?? null,
    ];
}

$userId = $_SESSION['user_id'];
$book_id = trim($data['book_id'] ?? '');$table = trim($data['table'] ?? '');

// Validate inputs
$allowedTables = ['favorites','library','opencover','closedcover','dustyshelves'];
if (!$book_id || !in_array($table, $allowedTables, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

// Use prepared statements to prevent SQL injection
$link->set_charset('utf8mb4');

// Check if the book is already in the user's table
$checkSql = "SELECT 1 FROM `$table` WHERE `user_id` = ? AND `book_id` = ? LIMIT 1";
if ($stmt = $link->prepare($checkSql)) {
    $stmt->bind_param('is', $userId, $book_id);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
}

if ($exists) {
    // Remove the book
    $sql = "DELETE FROM `$table` WHERE `user_id` = ? AND `book_id` = ?";
    $action = 'removed';
} else {
    // Add the book
    $sql = "INSERT INTO `$table` (`user_id`,`book_id`,`added_at`) VALUES (?, ?, NOW())";
    $action = 'added';
}

if ($stmt = $link->prepare($sql)) {
    $stmt->bind_param('is', $userId, $book_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'status' => $action,
        'table'  => $table,
        'book_id' => $book_id,
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}

exit;
?>
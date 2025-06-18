<?php
session_start();
header("Content-Type: application/json");
require_once '../connection.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Parse input
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['table'], $input['bookId'], $input['title'], $input['author'], $input['thumbnail'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing data fields"]);
    exit;
}

$table = $input['table'];
$book_Id = $input['bookId'];
$title = $input['title'];
$author = $input['author'];
$thumbnail = $input['thumbnail'];

// ✅ Whitelist target tables
$allowedTables = ['myfavorites', 'mylibrary', 'myopencover', 'myclosedcover', 'mydustyshelves'];
if (!in_array($table, $allowedTables)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid table']);
    exit;
}

// ✅ Check if book already exists
$stmt = $link->prepare("SELECT id FROM `$table` WHERE user_id = ? AND book_Id = ?");
$stmt->bind_param("is", $user_id, $book_Id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Book exists → delete
    $deleteStmt = $link->prepare("DELETE FROM `$table` WHERE user_id = ? AND book_Id = ?");
    $deleteStmt->bind_param("is", $user_id, $book_Id);
    $deleteStmt->execute();
    $deleteStmt->close();
    echo json_encode(["status" => "removed"]);
} else {
    // Book doesn't exist → insert
    $insertStmt = $link->prepare("INSERT INTO `$table` (user_id, book_Id, title, author, thumbnail) VALUES (?, ?, ?, ?, ?)");
    $insertStmt->bind_param("issss", $user_id, $book_Id, $title, $author, $thumbnail);
    $insertStmt->execute();
    $insertStmt->close();
    echo json_encode(["status" => "added"]);
}

$stmt->close();
$link->close();
?>

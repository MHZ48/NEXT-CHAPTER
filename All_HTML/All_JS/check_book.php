<?php
session_start();
header("Content-Type: application/json");
require_once '../connection.php';

// ✅ Ensure session is active
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Read input safely
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['table']) || !isset($input['bookId'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing table or bookId"]);
    exit;
}

$table = $input['table'];
$book_Id = $input['bookId'];

// ✅ Whitelist tables
$allowedTables = ['myfavorites', 'mylibrary', 'myopencover', 'myclosedcover', 'mydustyshelves'];
if (!in_array($table, $allowedTables)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid table']);
    exit;
}

// ✅ Prepare query
$stmt = $link->prepare("SELECT id FROM `$table` WHERE user_id = ? AND book_Id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Query prepare failed"]);
    exit;
}

$stmt->bind_param("is", $user_id, $book_Id);
$stmt->execute();
$res = $stmt->get_result();

echo json_encode([
    "exists" => ($res && $res->num_rows > 0)
]);

$stmt->close();
$link->close();
?>

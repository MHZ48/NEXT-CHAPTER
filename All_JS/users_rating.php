<?php
session_start();
header("Content-Type: application/json");
require_once '../connection.php';

// ✅ Ensure login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Validate input
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['bookId'], $input['book_rating'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing bookId or rating"]);
    exit;
}

$book_Id = $input['bookId'];
$rating = $input['book_rating'];

// ✅ Check if user already rated this book
$stmt = $link->prepare("SELECT id FROM myratings WHERE user_id = ? AND book_Id = ?");
$stmt->bind_param("is", $user_id, $book_Id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Update existing rating
    $updateStmt = $link->prepare("UPDATE myratings SET book_rating = ? WHERE user_id = ? AND book_Id = ?");
    $updateStmt->bind_param("dis", $rating, $user_id, $book_Id);
    $updateStmt->execute();
    $updateStmt->close();
    echo json_encode(["status" => "updated"]);
} else {
    // Insert new rating
    $insertStmt = $link->prepare("INSERT INTO myratings (user_id, book_Id, book_rating) VALUES (?, ?, ?)");
    $insertStmt->bind_param("isd", $user_id, $book_Id, $rating);
    $insertStmt->execute();
    $insertStmt->close();
    echo json_encode(["status" => "inserted"]);
}

$stmt->close();
$link->close();
?>

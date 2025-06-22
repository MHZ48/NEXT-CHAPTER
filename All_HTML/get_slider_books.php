<?php
session_start();
header('Content-Type: application/json');

include_once '../connection.php';

$allowed_tables = ['favorites', 'library', 'opencover', 'closedcover', 'dustyshelves'];

$slider = $_GET['slider'] ?? 'favorites';
if (!in_array($slider, $allowed_tables)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid slider parameter']);
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT book_id FROM `$slider` WHERE user_id = ?";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = [
        'book_id' => $row['book_id']
    ];
}

echo json_encode($books);
$link->close();

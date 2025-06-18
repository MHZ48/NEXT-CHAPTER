<?php
header('Content-Type: application/json');

// DB connection — customize with your credentials
$host = 'localhost';
$db   = 'your_database';
$user = 'your_username';
$pass = 'your_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Input
$data = json_decode(file_get_contents('php://input'), true);
$bookId = $data['bookId'] ?? null;
$table = $data['table'] ?? null;
$title = $data['title'] ?? '';
$author = $data['author'] ?? '';
$thumbnail = $data['thumbnail'] ?? '';

if (!$bookId || !$table) {
    echo json_encode(['error' => 'Missing required fields.']);
    exit;
}

// Sanitize table
$allowedTables = ['mylibrary', 'myfavorites', 'myopencover', 'myclosedcover', 'mydustyshelves'];
if (!in_array($table, $allowedTables)) {
    echo json_encode(['error' => 'Invalid table.']);
    exit;
}

// Check if it already exists
$stmt = $pdo->prepare("SELECT 1 FROM `$table` WHERE bookId = :bookId LIMIT 1");
$stmt->execute(['bookId' => $bookId]);

if ($stmt->fetch()) {
    // Exists — remove
    $stmt = $pdo->prepare("DELETE FROM `$table` WHERE bookId = :bookId");
    $stmt->execute(['bookId' => $bookId]);
    echo json_encode(['status' => 'removed']);
} else {
    // Does not exist — insert
    $stmt = $pdo->prepare("INSERT INTO `$table` (bookId, title, author, thumbnail) VALUES (:bookId, :title, :author, :thumbnail)");
    $stmt->execute([
        'bookId' => $bookId,
        'title' => $title,
        'author' => $author,
        'thumbnail' => $thumbnail
    ]);
    echo json_encode(['status' => 'added']);
}
exit;
?>

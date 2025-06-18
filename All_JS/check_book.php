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

// Get input
$data = json_decode(file_get_contents('php://input'), true);
$bookId = $data['bookId'] ?? null;
$table = $data['table'] ?? null;

if (!$bookId || !$table) {
    echo json_encode(['error' => 'Invalid input.']);
    exit;
}

// Safe table name check (whitelist recommended)
$allowedTables = ['mylibrary', 'myfavorites', 'myopencover', 'myclosedcover', 'mydustyshelves'];
if (!in_array($table, $allowedTables)) {
    echo json_encode(['error' => 'Invalid table.']);
    exit;
}

// Query
$stmt = $pdo->prepare("SELECT 1 FROM `$table` WHERE bookId = :bookId LIMIT 1");
$stmt->execute(['bookId' => $bookId]);
$exists = $stmt->fetch() !== false;

echo json_encode(['exists' => $exists]);
exit;
?>

<?php
$link = mysqli_init();
mysqli_ssl_set($link, NULL, NULL, __DIR__ . "/ca-certificate.crt", NULL, NULL);

// USE ENV VAR INSTEAD
$host = "db-mysql-fra1-...ondigitalocean.com";
$port = 25060;
$user = "doadmin";
$pass = getenv("DB_PASSWORD"); // ← this replaces the hardcoded password
$dbname = "defaultdb";

$link->real_connect($host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}
?>

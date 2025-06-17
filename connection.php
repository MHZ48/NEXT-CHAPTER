<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect without manual SSL setup
$link = mysqli_init();
$link->real_connect(
    "db-mysql-fra1-53075-do-user-23156859-0.k.db.ondigitalocean.com",
    "doadmin",
    "AVNS_3PMBf2wLHehUJTdTyRa",
    "defaultdb",
    25060,
    NULL,
    MYSQLI_CLIENT_SSL  // still uses SSL mode REQUIRED from DO
);

if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
} else {
    echo "Connected to DigitalOcean MySQL";
}
?>

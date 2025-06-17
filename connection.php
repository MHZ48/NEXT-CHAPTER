<?php
$link = mysqli_init();

// Set path to your CA cert file
mysqli_ssl_set($link, NULL, NULL, "/full/path/to/ca-certificate.crt", NULL, NULL);

// Connect to the database
$link->real_connect(
    "db-mysql-fra1-53075-do-user-23156859-0.k.db.ondigitalocean.com", // host
    "doadmin",                     // user
    "AVNS_3PMBf2wLHehUJTdTyRa",    // password
    "defaultdb",                   // database
    25060,                         // port
    NULL,                          // socket
    MYSQLI_CLIENT_SSL              // flags
);

// Verify connection
if ($link->connect_error) {
    die("❌ Connection failed: " . $link->connect_error);
}
?>

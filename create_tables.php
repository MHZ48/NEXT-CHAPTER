<?php
include("connection.php");

// USERS TABLE
$userTable = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    username VARCHAR(30) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(64) NOT NULL,
    profile_img LONGBLOB
)";


  $genresTable ="  CREATE TABLE IF NOT EXISTS user_genres (
    user_id INT PRIMARY KEY,
    genre1 VARCHAR(64),
    genre2 VARCHAR(64),
    genre3 VARCHAR(64),
    genre4 VARCHAR(64),
    genre5 VARCHAR(64),
    genre6 VARCHAR(64),
    FOREIGN KEY (user_id) REFERENCES users(id)
    )";
   
    $forgotpasswordTable = "CREATE TABLE IF NOT EXISTS `forgotpassword` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `rkey` char(32) NOT NULL,
    `time`  DATETIME NOT NULL,
    `status` varchar(7) NOT NULL,
    PRIMARY KEY (`id`)
    )";
    $favoritesTable = "CREATE TABLE IF NOT EXISTS `favorites` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $libraryTable = "CREATE TABLE IF NOT EXISTS `library` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (user_id) REFERENCES users(id)
    )";
   $opencoverTable = "CREATE TABLE IF NOT EXISTS opencover (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
    $closedcoverTable = "CREATE TABLE IF NOT EXISTS closedcover (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
    $dustyshelves = "CREATE TABLE IF NOT EXISTS dustyshelves (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
 $myratings = "CREATE TABLE IF NOT EXISTS myratings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    book_id VARCHAR(32) NOT NULL,
    book_rating DECIMAL(3,1) NOT NULL DEFAULT 0.0,
     PRIMARY KEY (id)
)";


// CREATE TABLE user_genres


// Run queries
if (
    $link->query($userTable) === TRUE &&
    $link->query($genresTable) === TRUE &&
    $link->query($favoritesTable) === TRUE &&
    $link->query($opencoverTable) === TRUE &&
    $link->query($closedcoverTable) === TRUE &&
    $link->query($dustyshelves) === TRUE &&
    $link->query($myratings) === TRUE &&
    $link->query($mylibraryTable) === TRUE &&
    $link->query($forgotpasswordTable) === TRUE
)
 {
    echo "All tables created successfully.";
} else {
    echo "Error: " . $link->error;
}

/*$sql = "ALTER TABLE forgotpassword MODIFY `time` DATETIME NOT NULL";
ALTER TABLE myfavorites ADD COLUMN bookId VARCHAR(255);
ALTER TABLE mylibrary ADD COLUMN bookId VARCHAR(255);
-- وهاكذا لباقي الجداول*/

//$sql = "ALTER TABLE mydustyshelves ADD COLUMN book_Id VARCHAR(255)";
//$sql= "ALTER TABLE mydustyshelves MODIFY thumbnail VARCHAR(255) ";
/*$sql= "ALTER TABLE user_genres
MODIFY genre1 VARCHAR(64),
MODIFY genre2 VARCHAR(64),
MODIFY genre3 VARCHAR(64),
MODIFY genre4 VARCHAR(64),
MODIFY genre5 VARCHAR(64),
MODIFY genre6 VARCHAR(64);
 ";*/
 $sql = "ALTER TABLE myratings
MODIFY COLUMN book_rating DECIMAL(3,1) NOT NULL DEFAULT 0.0;";

if ($link->query($sql) === TRUE) {
    echo "تم تعديل العمود time إلى DATETIME بنجا1ح.";
} else {
    echo "خطأ في تعديل العمود: " . $link->error;
}



$link->close();
?>

<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER','localhost');
define('DB_USERNAME','');
define('DB_PASSWORD','');
define('DB_NAME','');

/* Attempt to connect to MySQL database */
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
mysqli_set_charset($connection,"utf8");
// Check connection
if($connection === false){
    die("ERROR: Sunucu Bağlantı Hatasıı.");
}
?>

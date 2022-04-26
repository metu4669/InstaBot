<?php
include "conn/conn.php";
$date = date("D M d, Y G:i");
$mbt = mysqli_query($connection, "INSERT INTO test (test_text) VALUES('$date')");
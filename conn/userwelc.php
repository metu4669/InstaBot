<?php
if (session_status() == PHP_SESSION_NONE) {
    session_reset();
    session_start();
}

include "conn.php";

function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

$user_name = "";
$user_surname = "";
$user_email = "";
$user_register_time = "";

$admin_id = $_SESSION['admin_id'];


$sql = mysqli_query($connection,"SELECT * FROM insta_admin where ad_id='$admin_id'");
if($sql->num_rows>0){
    while($dataCheck = $sql->fetch_assoc()) {
        if ($dataCheck["ad_active"] === '0') {
            header("location: /insta/index.php");
            session_destroy();
            exit;
        } else {
            $user_name = $dataCheck["ad_name"];
            $user_surname = $dataCheck["ad_surname"];
            $user_email = $dataCheck["ad_email"];
            $user_register_time = $dataCheck["ad_register_date"];
        }
    }
}else{
    header("location: /insta/index.php");
    session_destroy();
    exit;
}



// If session variable is not set it will redirect to login page
if(!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])){
    header("location: /insta/index.php");
    session_destroy();
    exit;
}


// If session variable is not set it will redirect to login page
if(!isset($_SESSION['admin_email']) || empty($_SESSION['admin_email'])){
    header("location: /insta/index.php");
    session_destroy();
    exit;
}

if(!isset($_SESSION['active']) || empty($_SESSION['active'])){
    header("location: /insta/index.php");
    session_destroy();
    exit;
}else{
    if($_SESSION['active'] !== "Active"){
        header("location: /insta/index.php");
        session_destroy();
        exit;
    }
}
?>
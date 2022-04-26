<head>

</head>


<?php
include "../conn/conn.php";
include "../conn/userwelc.php";
use InstagramAPI\Instagram;

if(isset($_POST["http_request"]) && isset($_POST["user_name"]) && isset($_POST["password"])) {
    $x_http_request = (isset($_POST["http_request"]))?mysqli_real_escape_string($connection,$_POST["http_request"]):"";
    $user_name = (isset($_POST["user_name"]))?mysqli_real_escape_string($connection,$_POST["user_name"]):"";
    $password = (isset($_POST["password"]))?mysqli_real_escape_string($connection,$_POST["password"]):"";

    if(!empty($x_http_request) && !empty($user_name) && !empty($password)){
        if($x_http_request === "DA4AH2H2H25T5H52"){

            require_once("../api/vendor/autoload.php");
            Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
            $ig = new Instagram();

            try{
                $ig->login($user_name,$password);
                $user_pk = $ig->account_id;
                $m_check0 = mysqli_query($connection,"SELECT * FROM user_list WHERE user_name='$user_name'");
                if($m_check0->num_rows>0){
                    $m_check1 = mysqli_query($connection,"SELECT * FROM user_list WHERE user_name='$user_name' AND user_password='$password'");
                    if($m_check1->num_rows === 0){
                        $m_update = mysqli_query($connection,"UPDATE user_list SET user_password='$password' WHERE user_name='$user_name'");
                    }
                }else{
                    $m_insert = mysqli_query($connection,"INSERT INTO user_list (user_pk, user_name, user_password) VALUES('$user_pk','$user_name','$password')");
                }


                $_SESSION["user_name"] = $user_name;
                $_SESSION["password"] = $password;
                $_SESSION["instagram_account_connect"] = true;
                $_SESSION["insta_array"] = array();

                $_SESSION["tlm_pk"] = [];
                $_SESSION["tlm_url"] = [];
                $_SESSION["tlm_user"] = [];
                $_SESSION["tlm_user_profile_pic"] = [];
                $_SESSION["tlm_type"] = [];
                $_SESSION["tlm_captions"] = [];
                $_SESSION["tlm_view"] = [];

                echo "Log In Successful. Redirecting...";


            }catch(Exception $e){
                echo $e->getMessage();
            }
        }else{
            echo "Error: Played Request Data";
        }
    }else{
        echo "Error: Empty Input";
    }
}
else if(isset($_POST["http_request"])) {
    $x_http_request = (isset($_POST["http_request"]))?mysqli_real_escape_string($connection,$_POST["http_request"]):"";

    if(!empty($x_http_request)){
        if($x_http_request === "DFASFAFAFAS"){
            $_SESSION["user_name"] = "";
            $_SESSION["password"] = "";
            $_SESSION["instagram_account_connect"] = false;
            $_SESSION["insta_array"] = array();
            echo "Log Out Successful. Redirecting...";
        }else{
            echo "Error: Played Request Data";
        }
    }else{
        echo "Error: Empty Input";
    }
}
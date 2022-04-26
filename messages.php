<?php
include "includes/header.php";

use InstagramAPI\Instagram;

$_username = $_SESSION["user_name"];
$account_connected = $_SESSION["instagram_account_connect"];
$_password = $_SESSION["password"];

if($account_connected){
    require_once("api/vendor/autoload.php");
    Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

    $ig = new Instagram();

    try{
        $ig->login($_username,$_password);
        $_SESSION["insta_array"] = json_decode($ig->account->getCurrentUser(),true);
        $_SESSION["get_sel_info"] = json_decode($ig->people->getSelfInfo(),true);

        $insta_array = $_SESSION["insta_array"];
        $self_insta_array =$_SESSION["get_sel_info"];

        $getUserArray = $insta_array["user"];
        $getSelfArray = $self_insta_array["user"];



    }catch(\Exception $e){
        echo $e->getMessage();
    }
}else{
    echo '<script>window.location.href = "/insta/accountchoose.php";</script>';
}
?>

    <!-- Container Fluid-->
    <div class="container-fluid" id="container-wrapper" style="min-height: 500px; width: 100%;">
        <pre>
            <?php
               // echo print_r(json_decode($ig->direct->getInbox(null, 20),true));
             //   echo $ig->direct->getInbox(null, 20);
            
                $message_array = json_decode($ig->direct->getInbox(null, 20),true)["inbox"]["threads"];
                echo $ig->direct->getThread($message_array[0]["thread_id"]."", null);
            ?>
        </pre>
    </div>

<?php


include "includes/footer.php";
?>
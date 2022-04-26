<?php
include "includes/header.php";

use InstagramAPI\Instagram;

$_username = $_SESSION["user_name"];
$account_connected = $_SESSION["instagram_account_connect"];
$_password = $_SESSION["password"];

$follower_array = [];
if($account_connected){
    require_once("api/vendor/autoload.php");
    Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

    $ranking_token = InstagramAPI\Signatures::generateUUID();
    $ig = new Instagram();

    try{
        $ig->login($_username,$_password);
        $_SESSION["insta_array"] = json_decode($ig->account->getCurrentUser(),true);
        $_SESSION["get_sel_info"] = json_decode($ig->people->getSelfInfo(),true);

        $insta_array = $_SESSION["insta_array"];
        $self_insta_array =$_SESSION["get_sel_info"];

        $getUserArray = $insta_array["user"];
        $getSelfArray = $self_insta_array["user"];

        $follower_array = json_decode($ig->people->getSelfFollowers($ranking_token),true)["users"];
        $following_array = json_decode($ig->people->getSelfFollowing($ranking_token),true)["users"];

    }catch(\Exception $e){
        echo $e->getMessage();
    }
}else{
    echo '<script>window.location.href = "/insta/accountchoose.php";</script>';
}
?>
<style>
    #follower:hover{
        cursor: pointer;
    }
    #following:hover{
        cursor: pointer;
    }
    #user_name_row:hover{
        cursor: pointer;
    }
</style>
<script>
    function display_follower(){
        document.getElementById("following_box").hidden = true;
        document.getElementById("follower_box").hidden = false;
    }
    function display_following(){
        document.getElementById("following_box").hidden = false;
        document.getElementById("follower_box").hidden = true;
    }
</script>
    <!-- Container Fluid-->
    <div class="container-fluid" id="container-wrapper" style="min-height: 500px; width: 100%;">
        <div class="row" style="width:95%; margin:10px auto;padding: 10px; min-height: 150px; background: transparent;">
            <div class="col-4" style="background-image: url(<?php if($account_connected){echo $getUserArray["profile_pic_url"];}else{echo "img/boy.png";} ?>); background-repeat: no-repeat; background-size: contain; background-position: center;">

            </div>
            <div class="col-8">
                <div class="row">
                    <div class="col-12">
                        <b style="font-size: 18px;"><?php if($account_connected){echo $getUserArray["username"];}else{echo "-";} ?></b>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-4">
                        <b><?php if($account_connected){echo $getSelfArray["media_count"];}else{echo "-";} ?></b> Posts
                    </div>
                    <div class="col-4" id="follower" onclick="display_follower()">
                        <b><?php if($account_connected){echo $getSelfArray["follower_count"];}else{echo "-";} ?></b> Follower
                    </div>
                    <div class="col-4" id="following" onclick="display_following()">
                        <b><?php if($account_connected){echo $getSelfArray["following_count"];}else{echo "-";} ?></b> Following
                    </div>
                </div>
                <br>
                <div class="row">
                    <b><?php if($account_connected){echo $getUserArray["full_name"];}else{echo "-";} ?></b>
                </div>
                <div class="row">
                    <?php if($account_connected){echo $getUserArray["biography"];}else{echo "-";} ?>
                </div>
            </div>
        </div>

        <div class="row" style="width:100%; margin:20px auto;border-radius: 5px;min-height: 350px; overflow: auto;" hidden id="following_box">
            <?php
                if($account_connected){
                    $k = -1;

                    $echo_string = "";
                    foreach (array_reverse($following_array) as $follower_user){
                        $k++;
                        $pk = $follower_user["pk"];
                        $follower_user_name = $follower_user["username"];
                        $follower_user_full_name = $follower_user["full_name"];
                        $follower_user_profile_url = $follower_user["profile_pic_url"];
                        $url = "https://tradegaming.net/insta/user.php?user=".$follower_user_name;
                        if($k%3 === 0){
                            $echo_string = $echo_string.'<div class="row" style="width: 100%; padding: 0; margin: auto;">';
                        }
                        $echo_string = $echo_string.'
                <div class="col-4" style="min-height: 200px;">
                    <div class="row" style="height: 80%; margin: auto; width: 100%; background-image: url('.$follower_user_profile_url.'); background-repeat: no-repeat; background-size: contain; background-position: center;">

                    </div>
                    <div class="row" id="user_name_row"  style="height: 20%; margin: auto; width: 100%; text-align: center;" onclick="window.open(\''.$url.'\', \'_blank\')">
                        <p style="width: 100%; text-align: center; height: 100%; padding: 2px; vertical-align: center; margin: auto;">'.$follower_user_full_name.' (<b>'.$follower_user_name.'</b>)</p>
                    </div>
                </div>';
                        if($k%3 === 2){
                            $echo_string = $echo_string.'</div>';
                        }
                    }
                    if($k%3 !== 2){
                        $echo_string = $echo_string.'</div>';
                    }
                    echo $echo_string;
                }
            ?>
        </div>
        <div class="row" style="width:100%; margin:20px auto;border-radius: 5px;min-height: 350px; overflow: auto;" id="follower_box">
            <?php
                if($account_connected){
                    $k = -1;

                    $echo_string = "";
                    foreach (array_reverse($follower_array) as $follower_user){
                        $k++;
                        $pk = $follower_user["pk"];
                        $follower_user_name = $follower_user["username"];
                        $follower_user_full_name = $follower_user["full_name"];
                        $follower_user_profile_url = $follower_user["profile_pic_url"];
                        $url = "https://tradegaming.net/insta/user.php?user=".$follower_user_name;
                        if($k%3 === 0){
                            $echo_string = $echo_string.'<div class="row" style="width: 100%; padding: 0; margin: auto;">';
                        }
                        $echo_string = $echo_string.'
                <div class="col-4" style="min-height: 200px;">
                    <div class="row" style="height: 80%; margin: auto; width: 100%; background-image: url('.$follower_user_profile_url.'); background-repeat: no-repeat; background-size: contain; background-position: center;">

                    </div>
                    <div class="row" id="user_name_row" style="height: 20%; margin: auto; width: 100%; text-align: center;" onclick="window.open(\''.$url.'\', \'_blank\')">
                        <p style="width: 100%; text-align: center; height: 100%; padding: 2px; vertical-align: center; margin: auto;">'.$follower_user_full_name.' (<b>'.$follower_user_name.'</b>)</p>
                    </div>
                </div>';
                        if($k%3 === 2){
                            $echo_string = $echo_string.'</div>';
                        }
                    }
                    if($k%3 !== 2){
                        $echo_string = $echo_string.'</div>';
                    }
                    echo $echo_string;
                }
            ?>
        </div>


    </div>



<?php
include "includes/footer.php";
?>
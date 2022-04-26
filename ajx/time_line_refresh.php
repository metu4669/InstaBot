<head>

</head>


<?php
include "../conn/conn.php";
include "../conn/userwelc.php";
use InstagramAPI\Instagram;

if(isset($_POST["http_request"]) && !isset($_POST["user_name_get"])) {
    $x_http_request = (isset($_POST["http_request"]))?mysqli_real_escape_string($connection,$_POST["http_request"]):"";
    if(!empty($x_http_request)){
        if($x_http_request === "34125g1f"){

            require_once("../api/vendor/autoload.php");
            Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

            $ig = new Instagram();

            try{
                $user_name = $_SESSION["user_name"];
                $password = $_SESSION["password"];

                $ig->login($user_name,$password);



                $media_pk = $_SESSION["tlm_pk"];
                $media_url = $_SESSION["tlm_url"];
                $media_user = $_SESSION["tlm_user"];
                $media_user_profile_pic = $_SESSION["tlm_user_profile_pic"];
                $media_type = $_SESSION["tlm_type"];
                $media_captions = $_SESSION["tlm_captions"];
                $like_view = $_SESSION["tlm_view"];

                if($_SESSION["last_item_index"] !== count($media_pk)) {
                    $_SESSION["last_item_index"] +=5;
                    for ($p =  0; $p < $_SESSION["last_item_index"]; $p++) {
                        $url = "https://tradegaming.net/insta/user.php?user=".$media_user[$p];
                        echo '
                                <div class="row" tlm="media_item" plm="'.$p.'" style="width: 100%; margin: 30px auto;">
                                    <div class="instagram-item" style="background: white; border: 1px solid #dbdbdb">
                                        <div class="row" style="width: 100%;margin: auto; padding: 5px;">
                                            <div class="main" style="height: 50px;width: 50px;">
                                                <img src="' . $media_user_profile_pic[$p] . '" width="50" height="50" class="img-thumbnail" style="border-radius: 25px;">
                                            </div>
                                            <div class="r" style="text-align: center; height: 50px;" id="user_name_header" onclick="window.open(\''.$url.'\', \'_blank\')">
                                                <p style="margin: auto; margin-left: 5px; font-size: 15px; padding: 10px;font-weight: bold; vertical-align: center;">' . $media_user[$p] . '</p>
                                            </div>
                                        </div>
                                        <div class="row" style="margin: auto; width: 100%; background-image: url(' . $media_url[$p] . '); background-repeat: no-repeat; background-size: contain; background-position: center;">';
                        if ($media_type[$p] === 1) {
                            echo '<div class="row" style="min-height: 400px; margin: auto; width: 100%; background-image: url(' . $media_url[$p] . '); background-repeat: no-repeat; background-size: contain; background-position: center;"></div>';
                        } else if ($media_type[$p] === 2) {
                            echo '<video controls src="' . $media_url[$p] . '" style="min-height: 200px; margin: auto; width: 100%;"></video>';
                        }
                        echo '
                                        </div>
                                        <div class="item-caption">
                                        <div class="row" style="width: 100%; margin: auto; padding: 5px 20px;">
                                            <i style="padding: 3px; font-size: 20px;" class="far fa-heart"></i><i style="padding: 3px; font-size: 20px;" class="far fa-comment"></i><i style="padding: 3px; font-size: 20px;" class="far fa-paper-plane"></i>
                                        </div>
                                        <div class="row" style="width: 100%; margin: auto; padding: 5px 20px;">
                                            <b>' . $like_view[$p] . '</b>
                                        </div>';

                        if ($media_captions[$p] !== "") {
                            echo '
                                        <div class="row" style="width: 100%; margin: auto; padding-left: 20px; padding-right: 20px; padding-bottom: 5px;">                        
                                            <b>' . $media_user[$p] . '</b>&nbsp;' . $media_captions[$p] . '
                                        </div>';
                        }

                        echo '
                                        </div>
                                    </div>
                                </div>';
                    }
                }


            }catch(Exception $e){
                echo $e->getMessage();
            }
        }
        else{
            echo "Error: Played Request Data";
        }
    }else{
        echo "Error: Empty Input";
    }
}

if(isset($_POST["http_request"]) && isset($_POST["user_name_get"])) {
    $x_http_request = (isset($_POST["http_request"]))?mysqli_real_escape_string($connection,$_POST["http_request"]):"";
    $user_name_get = (isset($_POST["user_name_get"]))?mysqli_real_escape_string($connection,$_POST["user_name_get"]):"";
    if(!empty($x_http_request)){
        if($x_http_request === "DSAGD5GF"){
            require_once("../api/vendor/autoload.php");
            Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

            $ig = new Instagram();

            try{
                $user_name = $_SESSION["user_name"];
                $password = $_SESSION["password"];

                $ig->login($user_name,$password);

                $_SESSION["user_page_item"] += 6;
                // --------------------------------------------------------------------------
                $user_id_get = $ig->people->getUserIdForName($user_name_get);

                $get_read_user_array = json_decode($ig->people->getInfoById($user_id_get), true)["user"];


                $media_item_url = [];


                $main_stream = json_decode($ig->timeline->getUserFeed($user_id_get), true);
                $media_stream = $main_stream["items"];
                $next_max_id = $main_stream["next_max_id"];

                while(isset($main_stream["next_max_id"]) && count($media_item_url)<=$_SESSION["user_page_item"]) {
                    foreach ($media_stream as $m_items) {
                        if(count($media_item_url)>=$_SESSION["user_page_item"])  break;
                        array_push($media_item_url, $m_items["image_versions2"]["candidates"][0]["url"]);
                    }
                    $main_stream = json_decode($ig->timeline->getUserFeed($user_id_get, $next_max_id), true);
                    $media_stream = $main_stream["items"];
                    $next_max_id = $main_stream["next_max_id"];
                }
                if(count($media_item_url) < $_SESSION["user_page_item"] && !isset($main_stream["next_max_id"])) {
                    foreach ($media_stream as $m_items) {
                        if(count($media_item_url)>=$_SESSION["user_page_item"])  break;
                        array_push($media_item_url, $m_items["image_versions2"]["candidates"][0]["url"]);
                    }
                }

                if(count($media_item_url)>0){
                    $echo_string = "";
                    for ($k=0; $k<count($media_item_url); $k++){
                        if($k%3 === 0){
                            $echo_string = $echo_string.'<div class="row" style="width: 100%; padding: 0; margin: 5px auto;">';
                        }
                        $echo_string = $echo_string.'
                <div class="col-4">
                    <div class="row" bbd="insta-img" style="margin: auto; width: 100%; background-image: url('.$media_item_url[$k].'); background-repeat: no-repeat; background-size: cover; background-position: center;">

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
                }else{
                    echo '
                            <div class="QlxVY" style="margin: 0 auto; width: 100%;">
                                <h2 class="rkEop"  style="margin: auto; width: 100%; text-align: center;">Bu Hesap Gizli</h2>
                                <div class="VIsJD" style="margin: auto; width: 100%; text-align: center;">Fotoğraflarını ve videolarını görmek için kendisini takip et.</div>
                            </div>';
                }

            }catch(Exception $e){
                echo $e->getMessage();
            }
        }
        else{
            echo "Error: Played Request Data";
        }
    }else{
        echo "Error: Empty Input";
    }
}
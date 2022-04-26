<head>

</head>


<?php
include "../conn/conn.php";
include "../conn/userwelc.php";
use InstagramAPI\Instagram;

if(isset($_POST["http_request"])) {
    $x_http_request = (isset($_POST["http_request"]))?mysqli_real_escape_string($connection,$_POST["http_request"]):"";
    if(!empty($x_http_request)){
        if($x_http_request === "1348JDFJ448F"){

            require_once("../api/vendor/autoload.php");
            Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

            $ig = new Instagram();

            try{
                $user_name = $_SESSION["user_name"];
                $password = $_SESSION["password"];

                $ig->login($user_name,$password);


                $media_pk = [];
                $media_url = [];
                $media_user = [];
                $media_user_profile_pic = [];
                $media_type = [];
                $media_captions = [];
                $like_view = [];

                $time_line_feed = json_decode($ig->timeline->getTimelineFeed(),true);
                $time_line_feed_items = $time_line_feed["feed_items"];
                $next_max_id = $time_line_feed["next_max_id"];

                $max_feed_item = 5;
                $total_media_item = 110;
                if(count($_SESSION["tlm_pk"]) <= $total_media_item) {
                    while (count($media_pk) < $max_feed_item) {
                        foreach ($time_line_feed_items as $tl_feed_item) {
                            if ($tl_feed_item["media_or_ad"] !== null) {
                                if (!in_array($tl_feed_item["media_or_ad"]["pk"], $_SESSION["tlm_pk"])) {
                                    array_push($media_pk, $tl_feed_item["media_or_ad"]["pk"]);
                                    array_push($media_user, $tl_feed_item["media_or_ad"]["user"]["username"]);
                                    array_push($media_user_profile_pic, $tl_feed_item["media_or_ad"]["user"]["profile_pic_url"]);
                                    array_push($media_type, $tl_feed_item["media_or_ad"]["media_type"]);

                                    array_push($media_captions, $tl_feed_item["media_or_ad"]["caption"]["text"]);
                                    if ($tl_feed_item["media_or_ad"]["media_type"] === 1) {
                                        array_push($media_url, $tl_feed_item["media_or_ad"]["image_versions2"]["candidates"][0]["url"]);
                                    } else if ($tl_feed_item["media_or_ad"]["media_type"] === 2) {
                                        array_push($media_url, $tl_feed_item["media_or_ad"]["video_versions"][0]["url"]);
                                    }

                                    if ($time_line_feed_items["media_or_ad"]["like_count"] !== "") {
                                        array_push($like_view, $tl_feed_item["media_or_ad"]["like_count"] . " Likes");
                                    }
                                }
                                if (count($media_pk) >= $max_feed_item) break;
                                if (count($_SESSION["tlm_pk"]) >= $total_media_item) break;

                            }
                        }

                        $time_line_feed = json_decode($ig->timeline->getTimelineFeed($next_max_id), true);
                        $time_line_feed_items = $time_line_feed["feed_items"];
                        $next_max_id = $time_line_feed["next_max_id"];
                    }

                    $_SESSION['tlm_pk'] = array_merge($_SESSION['tlm_pk'], $media_pk);
                    $_SESSION['tlm_url'] = array_merge($_SESSION['tlm_url'], $media_url);
                    $_SESSION['tlm_user'] = array_merge($_SESSION['tlm_user'], $media_user);
                    $_SESSION['tlm_user_profile_pic'] = array_merge($_SESSION['tlm_user_profile_pic'], $media_user_profile_pic);
                    $_SESSION['tlm_type'] = array_merge($_SESSION['tlm_type'], $media_type);
                    $_SESSION['tlm_captions'] = array_merge($_SESSION['tlm_captions'], $media_captions);
                    $_SESSION['tlm_view'] = array_merge($_SESSION['tlm_view'], $like_view);

                }

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
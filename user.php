<?php
if(!isset($_GET["user"])) {
    header("location: /insta/dashboard.php");
}
include "includes/header.php";

$user_name_get = mysqli_real_escape_string($connection, $_GET["user"]);

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


    try {
        $ig->login($_username, $_password);
        $_SESSION["insta_array"] = json_decode($ig->account->getCurrentUser(), true);
        $_SESSION["get_sel_info"] = json_decode($ig->people->getSelfInfo(), true);

        $insta_array = $_SESSION["insta_array"];
        $self_insta_array = $_SESSION["get_sel_info"];

        $getUserArray = $insta_array["user"];
        $getSelfArray = $self_insta_array["user"];

// -----------------------------------------------------------------------------------------
        $_SESSION["user_page_item"] = 30;
        $user_id_get = $ig->people->getUserIdForName($user_name_get);

        $get_read_user_array = json_decode($ig->people->getInfoById($user_id_get), true)["user"];
        if(!isset($get_read_user_array)){
            $get_read_user_array = json_decode($ig->people->getInfoByName($user_name_get), true)["user"];
        }

        $media_item_url = [];
        $media_item_types = [];


        $main_stream = json_decode($ig->timeline->getUserFeed($user_id_get), true);
        $media_stream = $main_stream["items"];
        $next_max_id = $main_stream["next_max_id"];

        while(isset($main_stream["next_max_id"]) && count($media_item_url)<=$_SESSION["user_page_item"]) {
            foreach ($media_stream as $m_items) {
                if(count($media_item_url)>=$_SESSION["user_page_item"])  break;

                if($m_items["media_type"] === 1) {
                    array_push($media_item_url, $m_items["image_versions2"]["candidates"][0]["url"]);
                }elseif($m_items["media_type"] === 2){
                    array_push($media_item_url, $m_items["video_versions"][0]["url"]);
                }elseif($m_items["media_type"] === 8){
                    $carousel_media_items = $m_items["carousel_media"];
                    $input_string = "";
                    for($t=0; $t<count($carousel_media_items);$t++){
                        $input_string .= $carousel_media_items[$t]["url"].(($t=== count($carousel_media_items-1))?"":"*");
                    }
                    array_push($media_item_url, $input_string);
                }
            }
            $main_stream = json_decode($ig->timeline->getUserFeed($user_id_get, $next_max_id), true);
            $media_stream = $main_stream["items"];
            $next_max_id = $main_stream["next_max_id"];
        }
        if(count($media_item_url) >! $_SESSION["user_page_item"] && !isset($main_stream["next_max_id"])) {
            foreach ($media_stream as $m_items) {
                if(count($media_item_url)>=$_SESSION["user_page_item"])  break;
                array_push($media_item_url, $m_items["image_versions2"]["candidates"][0]["url"]);
            }
        }

    }catch(Exception $e){
        if($e->getMessage() === "InstagramAPI\Response\UserInfoResponse: User not found." ){
            $except = 1;
        }
    }
}else{
    echo '<script>window.location.href = "/insta/accountchoose.php";</script>';
}

?>

<?php
if($except === 1){
    echo "<div class=\"error-container -cx-PRIVATE-ErrorPage__errorContainer -cx-PRIVATE-ErrorPage__errorContainer__\" style='min-height:500px; margin: 30px auto;width: 100%;'>
    

    <h2 style='width: 100%;text-align: center;'>Üzgünüz, bu sayfaya ulaşılamıyor.</h2>

    <p style='width: 100%;text-align: center;'>
        Tıkladığın bağlantı bozuk olabilir veya sayfa kaldırılmış olabilir.
    </p>



    </div>";
    include "includes/footer.php";
    die();
}
?>
    <script>
        $(document).ready(function(){
            $("div[bbd='insta-img']").height($("div[bbd='insta-img']").width());
            $("div[bds='profile']").height($("div[bds='profile']").width());
        });
        $(window).on('resize', function(){
            $("div[bbd='insta-img']").height($("div[bbd='insta-img']").width());
            $("div[bds='profile']").height($("div[bds='profile']").width());
        });
        var tl_refreshed = false;

        function time_line_viewer(){
            if(tl_refreshed) {
                $(window).scrollTop();
                var temp_height = $(document).height();
                var temp_height2;

                Pace.restart();
                Pace.track(function () {
                    $.ajax({
                        type: 'POST',
                        url: 'ajx/time_line_refresh.php',
                        dataType: 'html',
                        data: {http_request: "DSAGD5GF", user_name_get : "<?php echo $user_name_get?>"},
                        error: function (err) {
                            //alert("Hata oluştu." + err);
                        },
                        success: function (xtthprot) {
                            $('#following_box').html(xtthprot);
                            temp_height2 = $(document).height();

                            $(window).scrollTop(temp_height);
                            $("div[bbd='insta-img']").height($("div[bbd='insta-img']").width());
                        }
                    });

                });
            }
            tl_refreshed = false;
        }

        $(window).scroll(function() {
            if(!tl_refreshed) {
                if ($(window).scrollTop() + $(window).height() == $(document).height()) {
                    tl_refreshed = true;
                    time_line_viewer();
                }
            }
        });
    </script>

    <!-- Container Fluid-->
    <div class="container-fluid" id="container-wrapper" style="min-height: 500px; width: 100%;">

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper" style="min-height: 500px; width: 100%;">
            <div class="row" style="width:95%; margin:10px auto;padding: 10px; min-height: 150px; background: transparent;">
                <div class="col-4" bds="profile" style="max-width:200px; background-image: url(<?php if($account_connected){echo $get_read_user_array["profile_pic_url"];}else{echo "img/boy.png";} ?>); background-repeat: no-repeat; background-size: cover; background-position: center;">

                </div>
                <div class="col-7" style="margin-left: 10px;">
                    <div class="row">
                        <div class="col-12">
                            <b style="font-size: 18px;"><?php if($account_connected){echo $get_read_user_array["username"];}else{echo "-";} ?></b>
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-4">
                            <b><?php if($account_connected){echo $get_read_user_array["media_count"];}else{echo "-";} ?></b> Posts
                        </div>
                        <div class="col-4" id="follower" onclick="display_follower()">
                            <b><?php if($account_connected){echo $get_read_user_array["follower_count"];}else{echo "-";} ?></b> Follower
                        </div>
                        <div class="col-4" id="following" onclick="display_following()">
                            <b><?php if($account_connected){echo $get_read_user_array["following_count"];}else{echo "-";} ?></b> Following
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <b><?php if($account_connected){echo $get_read_user_array["full_name"];}else{echo "-";} ?></b>
                    </div>
                    <div class="row">
                        <?php if($account_connected){echo $get_read_user_array["biography"];}else{echo "-";} ?>
                    </div>
                </div>
            </div>

            <div class="row" style="width:100%; margin:20px auto;border-radius: 5px;min-height: 350px; overflow: auto;" id="following_box">
                <?php
                if($account_connected && count($media_item_url)>0){
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
                ?>
            </div>
        </div>
    </div>


<?php
include "includes/footer.php";
?>
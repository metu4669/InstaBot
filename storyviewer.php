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



        $get_read_user_array = json_decode($ig->people->getInfoById($ig->people->getUserIdForName($user_name_get)), true)["user"];

        $story_items = json_decode($ig->story->getUserStoryFeed($ig->people->getUserIdForName($user_name_get)),true)["reel"]["items"];


    }catch(\Exception $e){
        $except = 0;
        if(
            $e->getMessage() !== "InstagramAPI\Response\UserFeedResponse: Not authorized to view user." &&
            $e->getMessage() !== "Requested resource does not exist." ) {
        }
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
</script>
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
        <?php
        if($account_connected && count($story_items)>0) {
            $p = -1;
            $echo_string = "";
            foreach ($story_items as $story_item) {
                $p++;
                if ($p % 2 === 0) {
                    $echo_string = $echo_string . '<div class="row" style="margin: 3px auto;">';
                }
                if ($story_item["media_type"] === 1) {
                    $media_can = $story_item["image_versions2"]["candidates"][0];

                    $media_width = $media_can["width"];
                    $scaler = 400 / $media_width;
                    $media_height = $media_can["height"] * $scaler;
                    $media_url = $media_can["url"];

                    $echo_string = $echo_string . '<div class="col-6" bbd="insta-img" style="padding: 2px;">
                <img src="' . $media_url . '" style="height: 100%; margin: auto;">
            </div>';
                } elseif ($story_item["media_type"] === 2) {
                    $media_can = $story_item["video_versions"][0];

                    $media_width = $media_can["width"];
                    $scaler = 400 / $media_width;
                    $media_height = $media_can["height"] * $scaler;
                    $media_url = $media_can["url"];

                    $echo_string = $echo_string . '<div class="col-6" bbd="insta-img" style="padding: 2px;">
                <video controls style="width: 100%; height: 100%;">
                    <source src="' . $media_url . '" type="video/mp4">
                    Your browser does not support HTML5 video.
                </video>
            </div>';
                }
                if ($p % 2 === 1) {
                    $echo_string = $echo_string . '</div>';
                }
            }
            if ($p % 2 !== 1) {
                $echo_string = $echo_string . '</div>';
            }
            echo $echo_string;
        }else{
            echo '
                            <div class="QlxVY" style="margin: 0 auto; width: 100%;">
                                <h2 class="rkEop"  style="margin: auto; width: 100%; text-align: center;">Bu Hesap Gizli veya Story Yok.</h2>
                            </div>';
        }
        ?>

    </div>

<?php


include "includes/footer.php";
?>
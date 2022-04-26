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

        $media_pk = $_SESSION["tlm_pk"];
        $media_url = $_SESSION["tlm_url"];
        $media_user = $_SESSION["tlm_user"];
        $media_user_profile_pic = $_SESSION["tlm_user_profile_pic"];
        $media_type = $_SESSION["tlm_type"];
        $media_captions = $_SESSION["tlm_captions"];
        $like_view = $_SESSION["tlm_view"];
        $_SESSION["last_item_index"] = (count($media_pk)>5)?4:(count($media_pk)-1);




    }catch(Exception $e){
        echo $e->getMessage();
    }
}else{
    echo '<script>window.location.href = "/insta/accountchoose.php";</script>';
}
?>
<style>
        @media (min-width: 960px){
            .instagram-item{
                width:800px;
                margin: auto;
            }

        }
        @media (max-width:960px){
            .instagram-item{
                width: 95%;
                margin: auto;
            }

        }
    #user_name_header:hover{
        cursor: pointer;
    }
</style>

    <script>
        <?php
        if($account_connected){
            echo '
    $(document).ready(function(){
        function time_line_retrieve(){
            $.ajax({
                type: \'POST\',
                url: \'ajx/timeline_retrieve.php\',
                dataType:\'html\',
                data: { http_request: "1348JDFJ448F"},
                error: function (err) {
                    alert("Hata oluştu."+err);
                },
                success: function (xtthprot) {
                    setTimeout(time_line_retrieve,3000);
                }
            });
        }
        time_line_retrieve();
    });';
        }
        ?>
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
                        data: {http_request: "34125g1f"},
                        error: function (err) {
                            //alert("Hata oluştu." + err);
                        },
                        success: function (xtthprot) {
                            $('#container-wrapper').html(xtthprot);
                            temp_height2 = $(document).height();

                            $(window).scrollTop(temp_height);
                        }
                    });

                });
            }
            tl_refreshed = false;
        }

        $(window).scroll(function() {
            if(!tl_refreshed) {
                if ($(window).scrollTop() + $(window).height() == $(document).height()) {
                    //alert($("div[tlm=media_item]").length+"");
                    tl_refreshed = true;
                    time_line_viewer();
                }
            }
        });
    </script>
            <!-- Container Fluid-->
            <div class="container-fluid" id="container-wrapper" style="min-height: 500px; width: 100%;">
                <?php
                $p = 0;
                    for($p=0; $p<$_SESSION["last_item_index"];$p++){
                        $url = "https://tradegaming.net/insta/user.php?user=".$media_user[$p];
                        echo '
                <div class="row" tlm="media_item"  plm="'.$p.'"  style="width: 100%; margin: 30px auto;">
                    <div class="instagram-item" style="background: white; border: 1px solid #dbdbdb">
                        <div class="row" style="width: 100%;margin: auto; padding: 5px;">
                            <div class="main" style="height: 50px;width: 50px;">
                                <img src="'.$media_user_profile_pic[$p].'" width="50" height="50" class="img-thumbnail" style="border-radius: 25px;">
                            </div>
                            <div class="r" style="text-align: center; height: 50px;" id="user_name_header" onclick="window.open(\''.$url.'\', \'_blank\')">
                                <p style="margin: auto; margin-left: 5px; font-size: 15px; padding: 10px;font-weight: bold; vertical-align: center;">'.$media_user[$p].'</p>
                            </div>
                        </div>
                        <div class="row" style="margin: auto; width: 100%; background-image: url('.$media_url[$p].'); background-repeat: no-repeat; background-size: contain; background-position: center;">';
                            if($media_type[$p] === 1){
                                echo '<div class="row" style="min-height: 400px; margin: auto; width: 100%; background-image: url('.$media_url[$p].'); background-repeat: no-repeat; background-size: contain; background-position: center;"></div>';
                            }else if($media_type[$p] === 2){
                                echo '<video controls src="'.$media_url[$p].'" style="min-height: 200px; margin: auto; width: 100%;"></video>';
                            }
                        echo '
                        </div>
                        <div class="item-caption">
                        <div class="row" style="width: 100%; margin: auto; padding: 5px 20px;">
                            <i style="padding: 3px; font-size: 20px;" class="far fa-heart"></i><i style="padding: 3px; font-size: 20px;" class="far fa-comment"></i><i style="padding: 3px; font-size: 20px;" class="far fa-paper-plane"></i>
                        </div>
                        <div class="row" style="width: 100%; margin: auto; padding: 5px 20px;">
                            <b>'.$like_view[$p].'</b>
                        </div>';

                        if($media_captions[$p] !== "") {
                            echo '
                        <div class="row" style="width: 100%; margin: auto; padding-left: 20px; padding-right: 20px; padding-bottom: 5px;">                        
                            <b>' . $media_user[$p] . '</b>&nbsp;' . $media_captions[$p] . '
                        </div>';
                        }

                            echo '
                        </div>
                    </div>
                </div>';
                        $p++;
                    }
                ?>
            </div>

<?php
include "includes/footer.php";
?>
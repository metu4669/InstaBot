<?php
 include "includes/header.php";

use InstagramAPI\Instagram;

 $_username = $_SESSION["user_name"];
 $_password = $_SESSION["password"];
 $getUserArray = array();

 $tag_data = "";
 $has_tag_read = false;

 $instagram_account_connected = $_SESSION["instagram_account_connect"];


 if($instagram_account_connected){
     require_once("api/vendor/autoload.php");
     Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

     $ig = new Instagram();
     try{
         $ig->login($_username, $_password);
         $_SESSION["insta_array"] = json_decode($ig->account->getCurrentUser(),true);
         $_SESSION["get_sel_info"] = json_decode($ig->people->getSelfInfo(),true);

         $insta_array = $_SESSION["insta_array"];
         $self_insta_array =$_SESSION["get_sel_info"];

         $getUserArray = $insta_array["user"];
         $getSelfArray = $self_insta_array["user"];


     }catch(Exception $e){
         echo $e->getMessage();
     }

 }
?>


<script>
    <?php
        if($instagram_account_connected){
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
                    $(\'#ajax-append\').html(xtthprot);
                    setTimeout(time_line_retrieve,5000);
                }
            });
        }
        time_line_retrieve();
    });';
        }
    ?>

    function set_user_info(){
        Pace.start();
        Pace.track(function () {
            document.getElementById("submit-button").disabled = true;
            var user_name = document.getElementById("insta_username").value;
            var password = document.getElementById("insta_password").value;
            $('#exampleModal').modal('toggle');
            $.ajax({
                type: 'POST',
                url: 'ajx/set_user_info.php',
                data: { http_request: "DA4AH2H2H25T5H52", user_name: user_name, password: password },
                error: function (err) {
                    alert("Hata oluştu."+err);
                },
                success: function (xtthprot) {
                    document.getElementById("submit-button").disabled = false;
                    $('#ajax-append').append(xtthprot);
                    setTimeout(function () {
                        window.location.reload();
                    },1000);
                }
            });
        });
    }

    function log_out(){
        Pace.restart();
        Pace.track(function () {
            $.ajax({
                type: 'POST',
                url: 'ajx/set_user_info.php',
                dataType:'html',
                data: { http_request: "DFASFAFAFAS"},
                error: function (err) {
                    alert("Hata oluştu."+err);
                },
                success: function (xtthprot) {
                    $('#ajax-append').append(xtthprot);
                    setTimeout(function () {
                        window.location.reload();
                    },1000);
                }
            });
        });
    }

</script>
<!-- Container Fluid-->
    <div class="container-fluid" id="container-wrapper" style="min-height: 100%;">
        <div class="row">
            <div class="col-6">
                <h1 class="h3 mb-0 text-gray-800">Instagram Control Panel </h1><p id="ajax-append"></p>
            </div>
            <?php
                if($instagram_account_connected){
                    echo '<div class="col-6">
                <button class="btn btn-primary" onclick="log_out()" style="float:right;">Log Out</button>
            </div>';
                }
            ?>
        </div>
        <br>

        <div class="row" style="border:1px solid lightgrey; border-radius: 5px; padding: 5px;">
            <div class="col-2">
                <img src="<?php if($instagram_account_connected){echo $getUserArray["profile_pic_url"];}else{echo "img/boy.png";} ?>" style="width: 100%; margin: auto;" class="img-thumbnail">
            </div>
            <div class="col-6">
                <div class="row">
                    <div class="col-12">
                        <p style="display:inline-block; font-size: 20px;" class="h3 mb-0 text-gray-800">Username: <b><?php if($instagram_account_connected){echo $getUserArray["username"];}else{echo "-";} ?></b></p>
                        <br>
                        <p style="display:inline-block; font-size: 20px;" class="h3 mb-0 text-gray-800">IG ID: <b><?php if($instagram_account_connected){echo $getUserArray["pk"];}else{echo "-";} ?></b></p>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-4">
                        Post Number: <?php if($instagram_account_connected){echo $getSelfArray["media_count"];}else{echo "-";} ?>
                    </div>
                    <div class="col-4">
                        Follower: <?php if($instagram_account_connected){echo $getSelfArray["follower_count"];}else{echo "-";} ?>
                    </div>
                    <div class="col-4">
                        Following: <?php if($instagram_account_connected){echo $getSelfArray["following_count"];}else{echo "-";} ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12">
                        <b><?php if($instagram_account_connected){echo $getUserArray["full_name"];}else{echo "-";} ?></b>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?php if($instagram_account_connected){echo $getUserArray["biography"];}else{echo "-";} ?>
                    </div>
                </div>
            </div>
            <br>
            <?php
            if(!$instagram_account_connected){
                echo '<button type="button" style="margin-top:10px; width: 100%;"  class="btn btn-primary"  data-toggle="modal" data-target="#exampleModal">Sign In</button>';
            }
            ?>


        </div>
    </div>

<?php
if(!$instagram_account_connected){
    echo '<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Log In</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Log Into Your Account to SetUp Control Bot</p>
                <label>Username: <input type="text" class="form-control" placeholder="Username" id="insta_username" name="insta_username"></label>
                <label>Password: <input type="password" class="form-control" placeholder="Password" id="insta_password" name="insta_password"></label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="submit-button" name="submit_account" onclick="set_user_info()" class="btn btn-primary">Log In</button>
            </div>
        </div>
    </div>
</div>';
}


include "includes/footer.php";
?>
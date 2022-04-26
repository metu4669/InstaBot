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


        $_user_id_at_tags = [];
        $tag_queried = false;

        if(isset($_POST["post-tag"])) {
            if ($account_connected && !empty($_POST["tag-text"])) {
                $tag_text_array = explode(',', $_POST["tag-text"]);
                $has_tag_read = true;

                $number_of_tags = count($tag_text_array);

                $main_hash_tag_search_link = json_decode($ig->hashtag->getFeed($tag_text_array[0], InstagramAPI\Signatures::generateUUID()), true);
                $initial_hash_tag_items = $main_hash_tag_search_link["items"];

                $account_id = $getUserArray["pk"];
                $tag_print_string = "";
                $tt = "";
                foreach ($tag_text_array as $tags) {
                    // Check Existence at Database
                    $check_sql_query = mysqli_query($connection,"SELECT * FROM tag_list WHERE requested_user_name='$_username' AND tag_text='$tags'");
                    if($check_sql_query->num_rows === 0){
                        $insert_into = mysqli_query($connection,"INSERT INTO tag_list (tag_text, requested_user_name, admin_id) VALUES('$tags', '$_username', '$admin_id')");

                        $tag_print_string = $tag_print_string."* <b>".$tags."</b> has been inserted into database.<br>";
                    }else{
                        $tag_print_string = $tag_print_string."* <b>".$tags."</b> has been already in database.<br>";
                    }
                }

                $tag_queried = true;
            } else {
                $tag_queried = false;
                echo "<script>alert('Please dont left empty.');</script>";
            }
        }
    }catch(\Exception $e){
        echo $e->getMessage();
    }
}else{
    echo '<script>window.location.href = "/insta/accountchoose.php";</script>';
}
?>
<script>
    function disableUnits(){
        setTimeout(function () {
            document.getElementById("post-tag").disabled = true;
            document.getElementById("tag-text").disabled = true;
        },1000);
    }
</script>
    <!-- Container Fluid-->
    <div class="container-fluid" id="container-wrapper" style="min-height: 500px; width: 100%;">
        <div class="form-control" style="width:95%; margin:10px auto;border-radius: 10px; padding: 10px; min-height: 150px; overflow: auto;">
            <b>Rules for Generating Follow Account:</b><br>
            *Enter tags your tags into "Enter tag" box. (Put ',' between your tags)<br>
            *Dont left any empty space.<br>
            *After you start generating, you should see results in result section.<br>
            *If you don't see any result. Please check previous rules before you contact with developer :)<br>
        </div>
        <div class="row" style="width:95%; margin:10px auto;border-radius: 10px;">
            <form class="form-control" action="" style="width: 100%; height: 100%;" method="post">
                    <label style="width: 100%;">
                        <textarea class="form-control" id="tag-text" name="tag-text" placeholder="Enter tag" style="width: 100%;"></textarea>
                    </label>
                    <button name="post-tag" type="submit" onclick="disableUnits()" style="width: 100%;" id="post-tag" class="btn btn-primary">Generate Users</button>
            </form>
        </div>
        <div class="form-control" style="width:95%; margin:10px auto;border-radius: 10px; padding: 10px; min-height: 350px; overflow: auto;">
            <b>Results</b><br>
            <pd class="form-control" disabled name="result-text" style="background:wheat;width: 100%; min-height:300px; overflow: auto;">
                <?php
                    if($account_connected && $tag_queried){
                        echo "All tags saved in database. It will be processed automatically. (150 for each tag/run)<br>".$tag_print_string;
                    }
                ?>
            </pd>
        </div>
    </div>

<?php


include "includes/footer.php";
?>
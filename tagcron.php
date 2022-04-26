<?php
include "conn/conn.php";

use InstagramAPI\Instagram;
$admin_id_array = [1 ,2];
foreach ($admin_id_array as $aia) {
    $read_all_tags = mysqli_query($connection, "SELECT * FROM tag_list WHERE admin_id='$aia'");

    $total_counter = 0;

    if ($read_all_tags->num_rows > 0) {
        $max_allowed = 50;
        $item_per_tag = (int)$max_allowed / $read_all_tags->num_rows;
        while ($read_tag_data = $read_all_tags->fetch_assoc()) {
            $requested_user_name = $read_tag_data["requested_user_name"];
            $user_name_check = mysqli_query($connection, "SELECT * FROM user_list WHERE user_name='$requested_user_name'");
            if ($user_name_check->num_rows > 0) {
                while ($pt = $user_name_check->fetch_assoc()) {
                    $tag_text = $read_tag_data["tag_text"];
                    $admin_id = $read_tag_data["admin_id"];
                    $requested_user_password = $pt["user_password"];
                    require_once("api/vendor/autoload.php");
                    Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

                    $ig = new Instagram();

                    try {
                        $ig->login($requested_user_name, $requested_user_password);
                        $user_pk = $ig->account_id;

                        $tag_data = json_decode($ig->hashtag->getFeed($tag_text, \InstagramAPI\Signatures::generateUUID()), true);
                        $tag_data_items = $tag_data["items"];
                        $tag_data_next_max_id = $tag_data["next_max_id"];
                        $i = 0;
                        $counter = 0;
                        $inner_counter = 0;
                        while ($i <= $item_per_tag) {
                            if ($counter > $item_per_tag) break;
                            $counter++;

                            if ($total_counter >= $max_allowed) break;
                            foreach ($tag_data_items as $tag_data_item) {
                                if ($total_counter >= $max_allowed) break;
                                if ($i >= $item_per_tag) break;

                                $inner_counter++;

                                $pk = $tag_data_item["user"]["pk"];

                                $check_for_pk = mysqli_query(
                                    $connection,
                                    "SELECT * FROM follow_pending WHERE follow_pk='$pk' AND user_pk='$user_pk'");

                                if ($check_for_pk->num_rows === 0) {
                                    $insert_item = mysqli_query(
                                        $connection,
                                        "INSERT INTO follow_pending (follow_pk,user_pk, user_name, user_password, tag_obtained, admin_id) VALUES('$pk','$user_pk','$requested_user_name','$requested_user_password','$tag_text','$admin_id')");
                                    $i++;
                                    $total_counter++;
                                }
                            }

                            if ($inner_counter === count($tag_data_items) - 1) {
                                $tag_data = json_decode($ig->hashtag->getFeed($tag_text, \InstagramAPI\Signatures::generateUUID(), $tag_data_next_max_id), true);
                                $tag_data_items = $tag_data["items"];
                                $tag_data_next_max_id = $tag_data["next_max_id"];
                                $inner_counter = 0;
                            }
                        }

                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }

                }
            }
        }
    }
}
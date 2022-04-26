<?php
include "conn/conn.php";

$all_pk_list = array();
$more_follow_list = false;

use InstagramAPI\Instagram;
$all_user_list_get = mysqli_query($connection, "SELECT * FROM user_list");

if($all_user_list_get->num_rows){
    while($mpt = $all_user_list_get->fetch_assoc()){
        $read_user_name = $mpt["user_name"];
        $read_password = $mpt["user_password"];
        $read_pk = $mpt["user_pk"];

        $m_get_pk = mysqli_query($connection,"SELECT * FROM follow_pending WHERE user_pk='$read_pk'");
        if($m_get_pk->num_rows>0){
            $t_array = array();
            while($m_data = $m_get_pk->fetch_assoc()){
                array_push($t_array,$m_data["follow_pk"]);
            }
            array_push($all_pk_list, array('user_pk'=>$read_pk, 'user_name'=>$read_user_name, 'password'=>$read_password, 'follow_pk_list'=>$t_array));
            $more_follow_list = true;
        }
    }

    if($more_follow_list){
        require_once("api/vendor/autoload.php");
        Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
        $ig = new Instagram();
        foreach ($all_pk_list as $pk_array){
            $pk_user= $pk_array["user_pk"];
            $pk_user_name = $pk_array["user_name"];
            $pk_user_password = $pk_array["password"];
            $pk_list = $pk_array["follow_pk_list"];
            $i=0;
            try{
                $ig->login($pk_user_name,$pk_user_password);
                $user_pk = $ig->account_id;

                while($i<10){
                    $follow_pk = $pk_list[$i];
                    $ig->people->follow($follow_pk);

                    $delete_ = mysqli_query($connection,"DELETE FROM follow_pending WHERE follow_pk='$follow_pk' AND user_pk='$pk_user'");
                    $i++;
                }

            }catch(Exception $e){
                echo $e->getMessage();
            }
        }
    }
}

?>
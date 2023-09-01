<?php

function is_current_user_profile(Database $db_users_instance): bool{
    $username = $_GET['username'];
    $uid = $_COOKIE['UID'];
    $db_username = $db_users_instance->all_where("id=$uid")[0]['username'];
    if ($username == $db_username){
        return true;
    }
    return false;
}

function get_user_data(Database $db_users_instance){
    $username = $_GET['username'];
    $data = $db_users_instance->all_where("username='$username'");
    if (empty($data)){
        return array();
    }
    unset($data['password']);
    return $data[0];
}

function get_user_by_id(int $uid, Database $users_db_instance){
    $db = $users_db_instance;
    $u = $db->all_where("id=$uid");
    if (empty($u)){
        return array();
    }
    unset($u[0]['password']);
    return $u[0];
}


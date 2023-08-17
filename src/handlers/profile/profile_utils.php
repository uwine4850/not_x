<?php

function is_current_user_profile(): bool{
    $username = $_GET['username'];
    $db = new Database('users');
    $uid = $_COOKIE['UID'];
    $db_username = $db->all_where("id=$uid")[0]['username'];
    if ($username == $db_username){
        return true;
    }
    return false;
}

function get_user_data(){
    $username = $_GET['username'];
    $db = new Database('users');
    $data = $db->all_where("username='$username'")[0];
    unset($data['password']);
    return $data;
}

function get_user_by_id(int $uid){
    $db = new Database('users');
    $u = $db->all_where("id=$uid");
    if (empty($u)){
        return array();
    }
    unset($u[0]['password']);
    return $u[0];
}


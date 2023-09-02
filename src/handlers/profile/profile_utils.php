<?php

/**
 * Checks if the user at the path /profile/{username} is the current user.
 * @param Database $db_users_instance An instance of the users table connection database.
 * @return bool
 */
function is_current_user_profile(Database $db_users_instance): bool{
    $username = $_GET['username'];
    $uid = $_COOKIE['UID'];
    $db_username = $db_users_instance->all_where("id=$uid")[0]['username'];
    if ($username == $db_username){
        return true;
    }
    return false;
}

/**
 * Retrieving user data on the /profile/{username} page.
 * @param Database $db_users_instance An instance of the users table connection database.
 * @return array
 */
function get_user_data(Database $db_users_instance): array{
    $username = $_GET['username'];
    $data = $db_users_instance->all_where("username='$username'");
    if (empty($data)){
        return array();
    }
    unset($data['password']);
    return $data[0];
}

/**
 * Returns the user data by ID.
 * @param int $uid User ID.
 * @param Database $users_db_instance An instance of the users table connection database.
 * @return array
 */
function get_user_by_id(int $uid, Database $users_db_instance): array{
    $db = $users_db_instance;
    $u = $db->all_where("id=$uid");
    if (empty($u)){
        return array();
    }
    unset($u[0]['password']);
    return $u[0];
}

